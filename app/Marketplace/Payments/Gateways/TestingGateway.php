<?php

namespace App\Marketplace\Payments\Gateways;

use Carbon\Carbon;
use Braintree\Subscription;
use App\Marketplace\Libs\LibPlan;
use App\Marketplace\Libs\Customer;
use App\Marketplace\Libs\LibPlanUser;
use App\Marketplace\Payments\ChargeableCustomer;

class TestingGateway
{
    public function settle($payableInstance, $input)
    {
        return (object) [
            'transaction_id'     => str_random(10),
            'card_tail'          => rand(1000, 9999),
            'card_brand'         => 'visa',
            'payment_method'     => 'testing',
        ];
    }

    public function chargeDifference(LibPlan $plan, $input)
    {
        return (object) [
            'id'                 => $input['subscription_id'],
            'amount'             => $input['amount'],
            'transactions' => [
                (object)[
                    'id' => 'fake_' . str_random(6),
                    'amount' => $input['amount'],
                    'creditCardDetails' => (object)[
                        'bin' => 123456,
                        'cardType' => 'Visa',
                        'cardholderName' => 'testing Chan',
                        'last4' => 1234,
                    ]
                ]
            ]
            // 'transaction_id'     => str_random(10),
            // 'card_tail'          => rand(1000, 9999),
            // 'card_brand'         => 'visa',
            // 'payment_method'     => 'testing',
        ];
    }

    public function findOrCreateCustomer($input)
    {
        return new ChargeableCustomer((object) [
          'id'             => 'fake_cus_' . str_random(10),
          'source'         => 'braintree',
          'paymentMethods' => [
            (object) [
              'token' => 'fake-valid-token'
            ]
          ]
        ], factory(Customer::class)->create(), $input);
    }

    public function subscribe(ChargeableCustomer $customer, $plan, $options = [])
    {
        return (object)[
          'id'            => 'fake_sub_' . str_random(12),
          'plan_id'       => $plan->key,
          'status'        => Subscription::ACTIVE,
          'transactions'  => [
              (object) [
                  'id'                => 'fake_' . str_random(6),
                  'amount'            => $plan->price - ($options['discount'] ?? 0),
                  'creditCardDetails' => (object)[
                      'bin'            => 123456,
                      'cardType'       => 'Visa',
                      'cardholderName' => 'testing Chan',
                      'last4'          => 1234,
                  ]
              ]
          ]
      ];
    }

    public function unsubscribe($plan)
    {
        return [];
    }

    public function nextBillingDate($plan)
    {
        return $plan->payment_required_until ? Carbon::parse($plan->payment_required_until) : Carbon::now()->addMonth();
    }

    public function retry(LibPlanUser $plan)
    {
        return [];
    }

    public function update(LibPlanUser $plan, array $data)
    {
        return [
            'subscription'   => [],
            'payment_method' => (object) [
                'last4'    => 1234,
                'cardType' => 'Visa'
            ]
        ];
    }

    // public function changePlan(LibPlanUser $plan, $data)
    // {
    //     return $plan;
    // }

    public function refund($refundableInstance)
    {
        $refundAmount = $refundableInstance->price;
        // assume done
        return $refundableInstance;
    }
}
