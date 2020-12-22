<?php

namespace App\Marketplace\Libs;

use App\User;
use Carbon\Carbon;
use App\Utilities\Emails\Email;
use App\Marketplace\Libs\TrialPlanUpgrade;
use App\Marketplace\Libs\UserLibraryManager;
use App\Marketplace\Payments\Gateways\Gateway;
use App\General\Distributor\DistributorInvoice;

class PlanSubscription
{
    public $id;
    public $customer;
    public $payment_method;
    public $gateway;
    public $chargeAmount;
    public $transactionId;
    public $transactionData;
    public $userPlan;

    public function __construct($customer = null)
    {
        $this->customer = $customer;
    }

    public function fromGateway($payment_method, $rawData)
    {
        switch ($payment_method) {
            case 'credit_card':
                return $this->fromCreditCard($rawData);
                break;
            case 'alipay':
                return $this->fromAlipay($rawData);
                break;
            case 'wechatpay':
                return $this->fromWechatpay($rawData);
                break;
            case 'distributor':
                return $this->fromDistributor($rawData);
                break;
            case 'telex_transfer':
                return $this->fromTelexTransfer($rawData);
                break;
            case 'testing':
                return $this->fromTesting($rawData);
                break;
        }
    }

    public function fromCreditCard($rawSubscription)
    {
        $transaction           = $rawSubscription->transactions[0] ?? null;
        $this->id              = $rawSubscription->id;
        $this->transactionData = $transaction;
        $this->transactionId   = $transaction->id     ?? null;
        $this->chargeAmount    = $transaction->amount ?? null;
        $this->payment_method  = 'credit_card';
        $this->gateway         = 'braintree';
        return $this;
    }

    public function fromTesting($rawSubscription)
    {
        $transaction           = $rawSubscription->transactions[0] ?? null;
        $this->id              = $rawSubscription->id;
        $this->transactionData = $transaction;
        $this->transactionId   = $transaction->id     ?? null;
        $this->chargeAmount    = $transaction->amount ?? null;
        $this->payment_method  = 'credit_card';
        $this->gateway         = 'braintree';
        return $this;
    }

    public function fromAlipay($rawCharge)
    {
        $this->id              = $rawCharge->id;
        $this->transactionData = $rawCharge;
        $this->transactionId   = $rawCharge->id;
        $this->chargeAmount    = $rawCharge->amount / 7.8 / 100; // HKD to USD, cents to dollar
        $this->payment_method  = 'alipay';
        $this->gateway         = 'stripe';
        return $this;
    }

    public function fromWechatpay($rawCharge)
    {
        $this->id              = $rawCharge->id;
        $this->transactionData = $rawCharge;
        $this->transactionId   = $rawCharge->id;
        $this->chargeAmount    = $rawCharge->amount / 7.8 / 100; // HKD to USD, cents to dollar
        $this->payment_method  = 'wechatpay';
        $this->gateway         = 'stripe';
        return $this;
    }

    public function fromTelexTransfer($rawCharge)
    {
        // dd($rawCharge);
        $this->id              = $rawCharge->id;
        $this->transactionData = $rawCharge;
        $this->transactionId   = $rawCharge->id;
        $this->chargeAmount    = $rawCharge->amount; // HKD to USD, cents to dollar
        $this->payment_method  = 'telex_transfer';
        $this->gateway         = 'collectionstock';
        return $this;
    }

    public function fromDistributor($rawCharge)
    {
        $this->id              = $rawCharge->id;
        $this->transactionData = $rawCharge;
        $this->transactionId   = $rawCharge->id;
        $this->chargeAmount    = $rawCharge->amount; // HKD to USD, cents to dollar
        $this->payment_method  = 'distributor';
        $this->gateway         = 'distributor';
        return $this;
    }

    public function recordPayment($transactionData, LibPlanUser $userPlan, $options = [])
    {
        $address = $userPlan->address ?: $userPlan->user->addresses()->first();
        // store to database
        $alreadyRecorded = LibPlanUserPayment::where([
            'gateway'            => $this->gateway,
            'subscription_id'    => $userPlan->subscription_id,
            'transaction_id'     => $this->transactionId,
        ])->first();

        $status    = $options['status'] ?? 0; // upgrade or new
        $isUpgrade = $status === 2;
        $isAutoPay = $this->payment_method === 'credit_card';
        $discount = $isUpgrade ? 0 : ($userPlan->libPlan->price - $this->chargeAmount);

        // @todo handle alipay/credit card upgrade from to date (since they do not ++year)
        $fromDate = Carbon::now();
        $toPayDate = Carbon::now()->addMonths($userPlan->libPlan->month_cycle);
        if (!$isAutoPay && !$isUpgrade) {
            if ($userPlan->payment_required_until) { // it is recharge
                $fromDate = Carbon::parse($userPlan->payment_required_until);
                $toPayDate = Carbon::parse($userPlan->payment_required_until)->addMonths($userPlan->libPlan->month_cycle);
            }
        }

        $payment = $alreadyRecorded ?: LibPlanUserPayment::forceCreate([
            'lib_plan_user_id'   => $userPlan->id,
            'status'             => $status,
            'user_id'            => $userPlan->user_id,
            'amount'             => $this->chargeAmount + $discount,
            'discount'           => $discount,
            'total'              => $this->chargeAmount,
            'from_date'          => $fromDate,
            'to_date'            => $toPayDate,
            'payment_method'     => $this->payment_method,
            'gateway'            => $this->gateway,
            'subscription_id'    => $userPlan->subscription_id,
            'transaction_id'     => $this->transactionId,
            'transaction_data'   => json_encode((array)$transactionData),
            'payment_vat_code'   => $address->vat_number ?? null,
            'payment_post_code'  => $address->post_code ?? null,
            'payment_address2'   => $address->address2 ?? null,
            'payment_address1'   => $address->address1 ?? null,
            'payment_country'    => $address && $address->nation
                                    ? $address->nation->translations->first()->name
                                    : $address->country ?? null,
            'payment_last_name'  => $address->last_name ?? null,
            'payment_first_name' => $address->first_name ?? null,
        ]);
        // make sure none-ending plan (e.g. grace period status) become normal
        if ($userPlan->status !== $userPlan::IS_ENDING) {
            $userPlan->status = $userPlan::IS_STARTED;
        }

        if ($userPlan->grace_period_until) {
            $userPlan->grace_period_until = null;
        }

        if ($options['update_date'] ?? false) {
            $userPlan->next_billing_at = $toPayDate;

            // alipay is forced to manually pay at year base
            // next payment notified by cron job within certain months
            // overwrite next_billing_at as well
            if (!$isAutoPay) {
                // $payDate = $userPlan->payment_required_until
                // ? Carbon::parse($userPlan->payment_required_until)->addMonths($userPlan->libPlan->month_cycle)->toDateTimeString()
                // : Carbon::now()->addMonths($userPlan->libPlan->month_cycle)->toDateTimeString();
                $userPlan->payment_required_until = $toPayDate;
                // $userPlan->next_billing_at        = $payDate;
            }
        }
        if ($payment->payment_method === 'credit_card') {
            \Log::info('transaction Data:', [$transactionData]);
            $userPlan->card_tail  = $transactionData->creditCardDetails->last4;
            $userPlan->card_brand = $transactionData->creditCardDetails->cardType;
        }
        $userPlan->save();

        if (!$alreadyRecorded) {
            UserLibraryManager::recordRepresentativeSubscription($userPlan, [
                'status'             => $status,
                'subtotal'           => $isUpgrade ? $this->chargeAmount : $userPlan->libPlan->price,
                'discount'           => $discount,
                'subscription_years' => $isUpgrade ? $userPlan->user->subscription_years - 1 : $userPlan->user->subscription_years
            ]);
            if (!$isUpgrade) {
                $userPlan->user->subscription_years += 1;
                $userPlan->user->save();
            }
            if (!app()->environment('staging')) {
                (new Email('recurring-invoice'))->send($userPlan->user, ['lib_plan_user_payment_id' => $payment->id]);
            }
        }
        return $payment;
    }

    /**
     *
     */
    public function startPlan(LibPlan $plan, $info)
    {
        if (!$this->customer) {
            abort('CUSTOMER_REQUIRED');
        }
        $bDay     = min(Carbon::now()->day, 28);
        $userPlan = LibPlanUser::forceCreate([
          'user_id'          => $info['user']->id,
          'lib_plan_id'      => $plan->id,
          'address_id'       => $info['address_id'] ?? null,
          'source'           => $info['source'] ?? null,
          'is_granted'       => $info['is_granted'] ?? false,
          'subscription_id'  => $this->id,
          'started_at'       => Carbon::now()->toDateTimeString(),
          'billing_day'      => $bDay,
          'customer_id'      => $this->customer->id,
          'payment_method'   => $info['payment_method'],
          'next_billing_at'  => Carbon::now()->day($bDay)
                                ->addMonths($plan->month_cycle)
                                ->toDateTimeString(),
        ]);
        // @todo if yearly plan, give all previous month as well
        if ($plan->isYearly()) {
            $this->createPreviousMonths($plan, $info['user'], $months = 12);
        }
        $this->createFirstAndCurrentMonth($plan, $info['user']);

        (new Email('subscription-start'))->send($userPlan->user, ['lib_plan_user_id' => $userPlan->id]);

        $this->userPlan = $userPlan;
        return $this;
    }

    /**
     *
     */
    public function switchPlan(LibPlan $plan, $info)
    {
        if (!$this->customer) {
            abort('CUSTOMER_REQUIRED');
        }
        $bDay = min(Carbon::now()->day, 28);
        $userPlan = LibPlanUser::forceCreate([
            'user_id' => $info['user']->id,
            'lib_plan_id' => $plan->id,
            'address_id' => $info['address_id'] ?? null,
            'is_granted' => $info['is_granted'] ?? false,
            'subscription_id' => $this->id,
            'started_at' => Carbon::now()->toDateTimeString(),
            'billing_day' => $bDay,
            'customer_id' => $this->customer->id,
            'source'      => $info['source'],
            'payment_method' => $info['payment_method'],
            'next_billing_at' => $info['next_billing_at'] ?? null,
            'payment_required_until' => $info['payment_required_until'] ?? null,
        ]);

        $this->userPlan = $userPlan;
        return $this;
    }

    /**
     * Plan is ended when cron job find out grace period passed, or called by end free trial
     */
    public static function endPlan(LibPlanUser $userPlan, $isFree = false, $skipMail = false)
    {
        $userPlan = self::discardPlan($userPlan);

        LibMonthUser::where([
          'user_id'      => $userPlan->user_id,
          'lib_plan_id'  => $userPlan->lib_plan_id,
        ])->update(['is_active' => 0]);

        // unless want to retain downloadable after lib month
        LibUserDownload::where([
          'user_id'      => $userPlan->user_id
        ])->update(['is_active' => 0]);
        
        User::where('id', $userPlan->user_id)->update(['is_customer' => false, 'is_trial' => false]);

        TrialPlanUpgrade::where('lib_plan_user_id', $userPlan->id)->delete();

        // @todo if credit_card, also end the subscription from braintree as well

        if (!$skipMail) {
            (new Email('subscription-end'))->send($userPlan->user, ['lib_plan_user_id' => $userPlan->id, 'is_free' => $isFree]);
        }

        return $userPlan;
    }

    /**
     * Plan is ending when use throw away his started plan
     */
    public static function endingPlan(LibPlanUser $userPlan, $isFree = false)
    {
        $nextBillingDate = Gateway::via($userPlan->payment_method)->nextBillingDate($userPlan);
        $now             = Carbon::now();
        // $now             = Carbon::now()->addMonths(1); // uncomment me to test unsubscribe that will end immediately
        $daysToEnd       = $now->diffInDays($nextBillingDate);

        // the day is very close to end, make him end now
        if ($userPlan->isOldMonthlyPlan() && $daysToEnd <= 2 || ($daysToEnd < 4 && $userPlan->billing_day === 28)) {
            \Log::info("#{$userPlan->id} of {$userPlan->subscription_id} ended immediately plans because the days to end is {$daysToEnd}, very close to end. And it is not old monthly plans.");
            Gateway::via($userPlan->payment_method)->unsubscribe($userPlan);
            return self::endPlan($userPlan);
        }

        \Log::info("#{$userPlan->id} of {$userPlan->subscription_id} will end later because the days to end is {$daysToEnd}");

        $oneYearFixed = false;
        if (!$userPlan->isOldMonthlyPlan()) {
            // @todo, if its second year contract, should fix?
            $oneYearFixed = Carbon::parse($userPlan->started_at)->addYear();
        }

        $userPlan->forceFill([
            'status'            => $userPlan::IS_ENDING,
            'pressed_ending_at' => $now,
            'ended_at'          => $oneYearFixed ?: $userPlan->next_billing_at,
        ])->save();

        return $userPlan;
    }

    /* assume user discard previous plan but it does not remove user download yet */
    public static function discardPlan(LibPlanUser $userPlan)
    {
        $userPlan->forceFill([
            'status'                  => $userPlan::IS_ENDED,
            'is_active'               => 0,
            'ended_at'                => Carbon::now()->toDateTimeString(),
            'grace_period_until'      => null,
            'payment_required_until'  => null
        ])->save();

        return $userPlan;
    }

    public static function gracePeriod(LibPlanUser $userPlan)
    {
        if (!$userPlan->grace_period_until) {
            $userPlan->grace_period_until = Carbon::now()->addDays(2)->toDateTimeString();
            $userPlan->status             = $userPlan::IS_GRACE_PERIOD;
            $userPlan->save();
            (new Email('grace-period'))->send($userPlan->user, ['lib_plan_user_id' => $userPlan->id]);
        }

        return $userPlan;
    }

    public function rechargePlan(LibPlanUser $userPlan)
    {
        // plan not rechargeable state

        // recharge epiring Aliapy with alipay
        // perform charge
        // update current record
        // recharge epiring Aliapy with credit card
        // find / create customer
        // perform charge
        // create a new record
        // inactivate old record
        // recharge failed credit card with same credit card
        // -->> update customer payment method
        // -->> attempt subscription charge
        // recharge failed credit card with different credit card
        // -->> update customer payment method
        // -->> attempt subscription charge
        // all ->>
        // remove grace Period

        // if (!$userPlan->grace_period_until) {
        //     $userPlan->grace_period_until = Carbon::now()->addMonth()->toDateTimeString();
        //     $userPlan->save();
        //     (new Email('grace-period'))->send($userPlan->user, ['lib_plan_user_id' => $userPlan->id]);
        // }

        if (!$this->customer) {
            abort('CUSTOMER_REQUIRED');
        }

        $this->userPlan = $userPlan;

        return $this;
    }

    public function startFreeTrial(LibPlan $plan, $info)
    {
        \Log::info('cusomter object', (array)$this->customer);
        $card     = $this->customer->customer ? $this->customer->customer->creditCards[0] ?? [] : [];
        $userPlan = LibPlanUser::forceCreate([
          'status'                    => LibPlanUser::IS_TRIAL,
          'user_id'                   => $info['user']->id,
          'lib_plan_id'               => $plan->id,
          'address_id'                => $info['address_id'] ?? null,
          'subscription_id'           => $this->id,
          'source'                    => $info['user']->representative_id ? 'via_rep' : null,
          'started_at'                => Carbon::now()->toDateTimeString(),
          'trial_ends_at'             => $info['trial_ends_at'],
          'customer_id'               => $this->customer->id,
          'payment_method'            => $info['payment_method'],
          'card_brand'                => $card->cardType ?? null,
          'card_tail'                 => $card->last4 ?? null,
        ]);

        $this->createFirstAndCurrentMonth($plan, $info['user']);

        (new Email('subscription-start'))->send($userPlan->user, ['lib_plan_user_id' => $userPlan->id, 'is_free' => 1]);

        $this->userPlan = $userPlan;
        return $this;
    }

    public static function endFreeTrial(LibPlanUser $plan, $skipMail = false)
    {
        $plan->user->is_trial      = 0;
        $plan->user->trial_ends_at = Carbon::now()->toDateTimeString();
        $plan->user->save();

        return self::endPlan($plan, $isFree = true, $skipMail = false);
    }

    protected function createFirstAndCurrentMonth(LibPlan $plan, User $user)
    {
        $libMonthUser = LibMonthUser::where([
          'lib_month_id' => 1,
          'user_id'      => $user->id
        ])->first();

        // if user has first month
        if ($libMonthUser) {
            // update it to current plan
            $libMonthUser->forceFill([
              'is_active'     => 1,
              'lib_plan_id'   => $plan->id
            ])->save();
        } else {
            // every new user gets the first month
            $libMonthUser = LibMonthUser::forceCreate([
              'lib_month_id'     => 1,
              'lib_plan_id'      => $plan->id,
              'user_id'          => $user->id,
              'is_active'        => true,
            ]);

            // every new user gets the fifth month
            $libMonthUser = LibMonthUser::forceCreate([
                'lib_month_id' => 2,
                'lib_plan_id'  => $plan->id,
                'user_id'      => $user->id,
                'is_active'    => true,
            ]);
        }

        // every new user gets the current month
        $now              = Carbon::now();
        $monthId          = LibMonth::where('year', $now->year)->where('month', $now->month)->first()->id;
        $currentMonthUser = $libMonthUser = LibMonthUser::where([
          'lib_month_id' => $monthId,
          'user_id'      => $user->id
        ])->first();
        if ($currentMonthUser) {
            $currentMonthUser->forceFill([
              'is_active'     => 1,
              'lib_plan_id'   => $plan->id
            ])->save();
        } else {
            $currentMonthUser = LibMonthUser::forceCreate([
                'lib_month_id'     => $monthId,
                'lib_plan_id'      => $plan->id,
                'user_id'          => $user->id,
                'is_active'        => true,
            ]);
        }
    }

    // gets 12 months (2 seasons)
    protected function createPreviousMonths(LibPlan $plan, User $user, int $monthsCount = 12)
    {
        $now                    = Carbon::now();
        $libMonths              = LibMonth::where('year', '<=', $now->year)->get();

        foreach ($libMonths as $month) {
            $dt        = Carbon::now();
            $dt->year  = $month->year;
            $dt->month = $month->month;
            $between   = $dt->diffInMonths($now, false);
            $data      = 1;
            // do not create if this months until now is smaller than 0
            // or until now is larger than 13 months (is 1 yr + 1 month ago)
            if ($dt->diffInMonths($now, false) < 0 || $dt->diffInMonths($now, false) > $monthsCount) {
                continue;
            }
            $libMonthUser = LibMonthUser::where([
                'lib_month_id' => $month->id,
                'user_id'      => $user->id
            ])->first();

            // if user has that month
            if ($libMonthUser) {
                // update it to current plan
                $libMonthUser->forceFill([
                    'is_active'   => 1,
                    'lib_plan_id' => $plan->id
                ])->save();
            } else {
                // every new user gets the first month
                $libMonthUser = LibMonthUser::forceCreate([
                    'lib_month_id' => $month->id,
                    'lib_plan_id'  => $plan->id,
                    'user_id'      => $user->id,
                    'is_active'    => true,
                ]);
            }
        }
    }
}
