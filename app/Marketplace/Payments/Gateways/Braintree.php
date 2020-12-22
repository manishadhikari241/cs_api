<?php

namespace App\Marketplace\Payments\Gateways;

use Carbon\Carbon;
use Braintree\Customer;
use Braintree\ClientToken;
use Braintree\Transaction;
use Braintree\Subscription;
use Braintree\Configuration;
use Braintree\PaymentMethod;
use App\Marketplace\Libs\LibPlan;
use App\Exceptions\PaymentException;
use App\Marketplace\Libs\LibPlanUser;
use App\Marketplace\Libs\Customer as CsCustomer;
use App\Marketplace\Payments\ChargeableCustomer;

class Braintree
{
    public function __construct()
    {
        Configuration::environment(getenv('BRAINTREE_ENV'));
        Configuration::merchantId(getenv('BRAINTREE_MERCHANT_ID'));
        Configuration::publicKey(getenv('BRAINTREE_PUBLIC_KEY'));
        Configuration::privateKey(getenv('BRAINTREE_PRIVATE_KEY'));
    }

    // $payableInstance: $order / $premium plan
    public function settle($payableInstance, $input)
    {
        $result = Transaction::sale([
            'amount'             => $payableInstance->total(),
            'paymentMethodNonce' => $input['token'],
            'options'            => [
                'submitForSettlement' => true,
            ],
        ]);
        \Log::notice('User Credit card Payment:', (array)$result);
        // dd($result, json_encode($result));
        if (!$result->success) {
            throw new PaymentException($result->transaction->status, 1);
        }
        $transaction = $result->transaction;
        return (object) [
            'transaction_id' => $transaction->id,
            'card_tail'      => $transaction->creditCard['last4'],
            'card_brand'     => $transaction->creditCard['cardType'],
            'payment_method' => 'credit_card'
        ];
    }

    // $payableInstance: $order / $premium plan
    public function refund($payableInstance)
    {
        $transaction = Transaction::find($payableInstance->transaction_id);

        \Log::notice('User Credit card Refund Transaction (before):', (array)$transaction);

        if (in_array($transaction->status, ['settled', 'settling'])) {
            $result = Transaction::refund($payableInstance->transaction_id);
        } else {
            $result = Transaction::Void($payableInstance->transaction_id);
        }
        \Log::notice('User Credit card Refund:', (array)$result);
        // dd($result, json_encode($result));
        if (!$result->success) {
            \Log::error('User Credit card Refund Failed:', (array)$result);
            throw new \Exception($result->transaction->type, 1);
        }
        $transaction = $result->transaction;
        return (object) [
            'type'   => $transaction->type,
            'amount' => $transaction->amount,
        ];
    }

    public function findOrCreateCustomer($input)
    {
        $user = $input['user'];
        $csCustomer = CsCustomer::where('user_id', $user->id)->where('payment_method', $input['payment_method'])->first();
        if (!$csCustomer) {
            $payload    = $this->createAndStoreCustomer($input);
            $customer   = $payload->result->customer;
            $csCustomer = $payload->cs_customer;
        } else {
            $customer = Customer::find($csCustomer->customer_id);
            \Log::info('Customer found', (array)$customer);
            // if user is not yet customer, and has token, always try to update a new one! in case customer provided a wrong info at first place
            if (isset($input['token']) && $input['token']) {
                $result = PaymentMethod::create([
                    'customerId'         => $csCustomer->customer_id,
                    'paymentMethodNonce' => $input['token'],
                    'options'            => [
                        'makeDefault' => true
                    ]
                ]);
                \Log::info('braintree payment method updated (during find customer, user is not customer yet)', (array) $result);
                if (!$result->success) {
                    throw new PaymentException('BRAINTREE_CREATE_PAYMENT_METHOD_ERROR');
                }
                $method = $result->paymentMethod;
                $customer = Customer::find($csCustomer->customer_id);
                if ($user->is_customer && $user->libPlanUser && $user->libPlanUser->payment_method === 'credit_card' && $user->libPlanUser->status !== LibPlanUser::IS_ENDED) {
                    $result        = Subscription::update($user->libPlanUser->subscription_id, [
                        'paymentMethodToken' => $method->token
                    ]);
                    \Log::notice('braintree plan founded and updated:', (array)$result);
                    if (!$result->success) {
                        throw new PaymentException('BRAINTREE_UPDATE_SUBSCRIPTION_ERROR', 1);
                    }
                }
            }
        }

        return new ChargeableCustomer($customer, $csCustomer, $input);
    }

    public function createAndStoreCustomer($input)
    {
        $result = Customer::create([
            'firstName'          => $input['first_name'],
            'lastName'           => $input['last_name'],
            'company'            => $input['company'] ?? 'N/A',
            'paymentMethodNonce' => $input['token']
        ]);
        \Log::info('customer create result', (array) $result);
        if (!$result->success) {
            foreach ($result->errors->deepAll() as $error) {
                \Log::info('Braintree Create Customer Error:', [$error->code . ': ' . $error->message . "\n"]);
            }
            throw new PaymentException('BRAINTREE_CREATE_CUSTOMER_ERROR');
        }

        $csCustomer = CsCustomer::forceCreate([
            'status'         => CsCustomer::IS_PAYING,
            'user_id'        => $input['user']->id,
            'customer_id'    => $result->customer->id,
            'payment_method' => $input['payment_method'],
        ]);
        return (object)[
            'result'      => $result,
            'cs_customer' => $csCustomer
        ];
    }

    public function subscribe(ChargeableCustomer $customer, LibPlan $plan, array $options = [])
    {
        $paymentInfo = [
            'paymentMethodToken' => $customer->getToken(),
            'planId'             => $plan->key . ($plan->version ? "_v{$plan->version}" : '')
        ];
        if (isset($options['discount'])) {
            // add first time discount
            $paymentInfo['discounts'] = [
                'add' => [
                    [
                        'amount'                => $options['discount'],
                        'inheritedFromId'       => 'first_subscription',
                        'numberOfBillingCycles' => 1
                    ]
                ]
            ];
        }
        \Log::info('Braintree Payment Info:', $paymentInfo);
        $result = Subscription::create($paymentInfo);

        if (!$result->success) {
            \Log::notice('Braintree Create Subscription Error:', (array) $result);
            throw new PaymentException('BRAINTREE_CREATE_SUBSCRIPTION_ERROR', 1);
        }

        return $result->subscription;
    }

    public function unsubscribe($plan)
    {
        $result = Subscription::cancel($plan->subscription_id);
        \Log::notice("{$plan->subscription_id} braintree plan cancelling:", (array)$result);
        if (!$result->success) {
            \Log::notice("{$plan->subscription_id} braintree plan cancel failed");
            throw new PaymentException('BRAINTREE_CANCEL_SUBSCRIPTION_ERROR', 1);
        }
        return true;
    }

    /* Return a Carbon Date */
    public function nextBillingDate($plan)
    {
        $subscription = Subscription::find($plan->subscription_id);
        \Log::notice('braintree plan find next billing date:', (array)$subscription);
        if (!$subscription) {
            abort(422, 'some error. occurs');
        }
        return Carbon::parse($subscription->nextBillingDate->format('Y-m-d'));
    }

    public function token()
    {
        return ClientToken::generate();
    }

    public function retry(LibPlanUser $plan)
    {
        $result = Subscription::retryCharge($plan->subscription_id, $plan->libPlan->price);
        \Log::notice('braintree plan founded for retry:', (array)$result);
        if (!$result->success) {
            abort(422, $result->message);
        }
        return true;
    }

    public function update(LibPlanUser $plan, $data)
    {
        $csCustomer = CsCustomer::where('user_id', $data['user']->id)->where('payment_method', 'credit_card')->firstOrFail();

        $result = PaymentMethod::create([
            'customerId'         => $csCustomer->customer_id,
            'paymentMethodNonce' => $data['token'],
            'options'            => [
                'makeDefault'        => true
            ]
        ]);
        \Log::notice('braintree customer updated payment method:', (array)$result);
        if (!$result->success) {
            throw new PaymentException('BRAINTREE_UPDATE_SUBSCRIPTION_ERROR', 1);
        }
        $paymentMethod = $result->paymentMethod;
        if ($plan->subscription_id) {
            $result        = Subscription::update($plan->subscription_id, [
                'paymentMethodToken' => $paymentMethod->token
            ]);
            \Log::notice('braintree plan founded and updated:', (array)$result);
            if (!$result->success) {
                throw new PaymentException('BRAINTREE_UPDATE_SUBSCRIPTION_ERROR', 1);
            }
        }
        return [
            'subscription'   => $result->subscription ?? null,
            'payment_method' => $paymentMethod
        ];
    }

    //  payableInstance: $order, $premiumPlan
    public function chargeDifference(LibPlan $plan, $input)
    {
        $transaction = null;
        if ($input['amount']) {
            $result = Transaction::sale([
                'amount'             => $input['amount'],
                'paymentMethodToken' => $input['token'],
                'options' => [
                    'submitForSettlement' => true,
                ],
            ]);
            \Log::notice('User Credit card Payment due to upgrade:', (array)$result);
            // dd($result, json_encode($result));
            if (!$result->success) {
                throw new PaymentException($result->message, 1);
            }
            $transaction = $result->transaction;
        }

        // update the price and plan here
        $result = Subscription::update($input['subscription_id'], [
            'planId' => $plan->key . ($plan->version ? "_v{$plan->version}" : ''),
            'price' => $plan->price,
            'options' => [
                'prorateCharges' => false,
            ],
        ]);
        \Log::notice('braintree plan upgrade charged and updated:', (array)$result);
        if (!$result->success) {
            \Log::error('FATAL ERROR! Braintree charged difference but failed to update the subscription!', (array)$result);
        }

        return (object)[
            'id'           => $input['subscription_id'], // it uses the previous plan subscription id,
            'transactions' => $transaction ? [$transaction] : []
        ];
    }

    // public function changePlan(LibPlanUser $currentPlan, $data)
    // {
    //     $csCustomer = CsCustomer::where('user_id', $data['user']->id)->where('payment_method', 'credit_card')->firstOrFail();

    //     // cancel and re-subscribe the plan!

    //     $result        = Subscription::update($currentPlan->subscription_id, [
    //         'planId' => $data['lib_plan']['key'],
    //         'price'  => $data['lib_plan']['price'],
    //     ]);

    //     \Log::info('Braintree change plan result', (array) $result);
    //     if (!$result->success) {
    //         throw new PaymentException('BRAINTREE_CHANGE_PLAN_ERROR', 1);
    //     }

    //     return $result->subscription;
    // }
}
