<?php

namespace App\Marketplace\Payments\Gateways;

use Carbon\Carbon;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Customer;
use App\Marketplace\Libs\LibPlan;
use App\Marketplace\Libs\Customer as CsCustomer;
use App\Marketplace\Payments\ChargeableCustomer;

class StripeWechatpay
{
    public function __construct()
    {
        Stripe::setApiKey(getenv('STRIPE_SECRET'));
    }

    //  payableInstance: $order, $premiumPlan
    public function settle($payableInstance, $input)
    {
        if ($result = $this->testSettle()) {
            return $result;
        }
        $charge = Charge::create([
          'amount'   => (float)$payableInstance->total() * 7.8 * 100, // in cent
          'source'   => $input['token'],
          'currency' => 'hkd',
        ]);
        \Log::notice('User Wechat Payment:', (array)$charge);
        // dd($result, json_encode($result));
        if ($charge->status !== 'succeeded') {
            throw new \Exception($charge->status, 1);
        }
        return (object)[
          'transaction_id' => $charge->id,
        ];
    }

    public function findOrCreateCustomer($input)
    {
        $csCustomer = CsCustomer::where('user_id', $input['user']->id)->where('payment_method', $input['payment_method'])->first();

        if (!$csCustomer) {
            $result = $this->createAndStoreCustomer($input);
            if (!$result->customer) {
                abort(422, 'STRIPE_CREATE_CUSTOMER_ERROR');
            }
            $customer   = $result->customer;
            $csCustomer = $result->cs_customer;
        } else {
            $customer = Customer::retrieve($csCustomer->customer_id);
        }

        return new ChargeableCustomer($customer, $csCustomer, $input);
    }

    public function createAndStoreCustomer($input)
    {
        $customer = Customer::create([
            'email'  => $input['user']->email,
            // 'source' => $input['token']
        ]);
        $csCustomer = CsCustomer::forceCreate([
            'status'         => CsCustomer::IS_PAYING,
            'user_id'        => $input['user']->id,
            'customer_id'    => $customer->id,
            'payment_method' => $input['payment_method'],
        ]);
        return (object)[
            'customer'      => $customer,
            'cs_customer'   => $csCustomer
        ];
    }

    public function subscribe(ChargeableCustomer $customer, $plan, array $options = [])
    {
        $discount = isset($options['discount']) ? (float) $options['discount'] : 0;
        $charge   = Charge::create([
            'amount'     => (float) ($plan->price - $discount) * 7.8 * 100, // in cent
            'source'     => $customer->getToken(),
            // 'customer'   => $customer->id,
            'currency'   => 'hkd',
        ]);

        \Log::notice('User wechatpay Subscription:', (array)$charge);

        if ($charge->status !== 'succeeded') {
            throw new \Exception($charge->status, 1);
        }
        // return (object)[
        //     'transaction_id' => $charge->id,
        // ];
        return $charge;
    }

    public function unsubscribe($plan)
    {
        \Log::info('Stripe plan canceled (no further actions required)');
        return true;
    }

    //  payableInstance: $order, $premiumPlan
    public function chargeDifference(LibPlan $plan, $input)
    {
        $charge = Charge::create([
            'amount' => $input['amount'] * 7.8 * 100, // in cent
            'source' => $input['token'],
            'currency' => 'hkd',
        ]);
        \Log::notice('User wechatpay Payment Charge difference:', (array)$charge);
        // dd($result, json_encode($result));
        if ($charge->status !== 'succeeded') {
            throw new \Exception($charge->status, 1);
        }
        return $charge;
    }

    public function nextBillingDate($plan)
    {
        return Carbon::parse($plan->payment_required_until);
    }

    public static function test()
    {
        return config(['testing.wechatpay' => true]);
    }

    // Test payment
    public function testSettle()
    {
        if (config('testing.wechatpay')) {
            return (object)[
              'transaction_id' => 'test_this.is.test.trans.because.you.called::test'
            ];
        }
        // this is not a test
        return false;
    }
}
