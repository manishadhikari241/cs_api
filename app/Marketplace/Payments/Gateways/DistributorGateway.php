<?php

namespace App\Marketplace\Payments\Gateways;

use Carbon\Carbon;
use App\Marketplace\Libs\LibPlan;
use App\Marketplace\Libs\Customer as CsCustomer;
use App\Marketplace\Payments\ChargeableCustomer;

class DistributorGateway
{
    public function __construct()
    {
    }

    // //  payableInstance: $order, $premiumPlan
    // public function settle($payableInstance, $input)
    // {
    //     $charge = Charge::create([
    //         'amount' => (float)$payableInstance->total() * 7.8 * 100, // in cent
    //         'source' => $input['token'],
    //         'currency' => 'hkd',
    //     ]);
    //     \Log::notice('User Alipay Payment:', (array)$charge);
    //     // dd($result, json_encode($result));
    //     if ($charge->status !== 'succeeded') {
    //         throw new \Exception($charge->status, 1);
    //     }
    //     return (object)[
    //         'transaction_id' => $charge->id,
    //     ];
    // }

    public function findOrCreateCustomer($input)
    {
        $csCustomer = CsCustomer::where('user_id', $input['user']->id)->where('payment_method', 'distributor')->first();

        if (!$csCustomer) {
            $result = $this->createAndStoreCustomer($input);
            $customer = (object)[];
            $csCustomer = $result->cs_customer;
        } else {
            $customer = (object)[];
        }

        return new ChargeableCustomer($customer, $csCustomer, $input);
    }

    public function createAndStoreCustomer($input)
    {
        if (!$input['user']->distributor_id) {
            throw new PaymentException('REQUIRE_DISTRIBUTOR_CREATED_USER', 1);
        }
        $csCustomer = CsCustomer::forceCreate([
            'status' => CsCustomer::IS_PAYING,
            'user_id' => $input['user']->id,
            'customer_id' => 'distributor_' . str_random(10),
            'payment_method' => 'distributor',
        ]);
        return (object)[
            'customer' => null,
            'cs_customer' => $csCustomer
        ];
    }

    public function subscribe(ChargeableCustomer $customer, $plan, array $options = [])
    {
        $tmpId = 'trans_invoice_' . str_random(10);
        \Log::notice('User Distributor Subscription: ' . $tmpId);
        return (object) [
            'id' => $tmpId,
            'transaction_id' => $tmpId,
            'amount' => $plan->price
        ];
    }

    public function chargeDifference(LibPlan $plan, $input)
    {
        return (object) [
            'id'     => 'dist_' . str_random(10),
            'amount' => $input['amount']
        ];
    }

    // public function unsubscribe($plan)
    // {
    //     \Log::info('Stripe plan canceled (no further actions required)');
    //     return true;
    // }

    public function nextBillingDate($plan)
    {
        return Carbon::parse($plan->payment_required_until);
    }
}
