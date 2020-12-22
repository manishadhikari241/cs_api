<?php

namespace App\Marketplace\Libs;

use Carbon\Carbon;
use App\General\Address;
use Braintree\Subscription;
use App\Utilities\Emails\Email;
use App\Exceptions\PaymentException;
use Illuminate\Support\Facades\Auth;
use App\Marketplace\Libs\TrialPlanUpgrade;
use App\Marketplace\Payments\Gateways\Gateway;
use App\General\Distributor\DistributorInvoice;
use App\General\Representative\RepresentativeSubscription;

class UserLibraryManager
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    // LibPlan $plan, PlanSubscription $subscription
    // return PlanSubscription Object
    public function subscribe($info)
    {
        $plan               = LibPlan::find($info['lib_plan_id']);
        // $plan               = LibPlan::find(1); // testing purpose only

        $previousPlan       = LibPlanUser::where('user_id', $this->user->id)->latest()->first();

        if ($previousPlan && $previousPlan->status !== LibPlanUser::IS_ENDED) {
            if ($previousPlan->status === LibPlanUser::IS_TRIAL) {
                PlanSubscription::endFreeTrial($previousPlan, $skipMail = true);
            } elseif ($previousPlan->status !== LibPlanUser::IS_ENDED) {
                throw new PaymentException('USER_CANNOT_DOUBLE_SUBSCRIBE');
            }
        }

        $address = Address::find($info['address_id']);

        $info['user']       = $this->user;
        $info['first_name'] = $this->user->first_name ?: $address->first_name ?? null;
        $info['last_name']  = $this->user->last_name ?: $address->last_name   ?? null;
        $info['company']    = $address->company                               ?? null;
        $info['source']     = $this->user->source                             ?? null;

        // find or create a new chargeable customer
        $customer = Gateway::via($info['payment_method'])->findOrCreateCustomer($info);

        // never free trial
        $coupon         = $this->getCoupon($info, $plan);

        $discount       = 0;
        if ($coupon) {
            $discount                      = $coupon->getPlanDiscount($plan);
            $this->user->representative_id = $coupon->representative_id;
            $this->user->source            = 'via_coupon';
            $this->user->save();
            $info['source']                = 'via_coupon';
        }
        // $hasTrialBefore = $this->user->libPlanUsers()->count();

        // @todo use percentage to give representative correct commission
        // if (!$discount && !$hasTrialBefore) {
        if (!$discount) {
            // ++ reped people
            $discount = $this->getRepresentativeDiscount($plan);
        }

        $subscriptionData = Gateway::via($info['payment_method'])->subscribe($customer, $plan, [
            'discount' => $discount
        ]);

        // store on our server
        $subscription         = new PlanSubscription($customer);
        $subscription         = $subscription->fromGateway($info['payment_method'], $subscriptionData);

        $subscription->startPlan($plan, $info);

        //  representative only get commission when user has no subscription before
        // @todo trial then rep -> still got subscription?
        // if (!$hasTrialBefore && ($info['payment_method'] !== 'credit_card')) {
        // if ($info['payment_method'] !== 'credit_card') {
        //     self::recordRepresentativeSubscription($subscription->userPlan, [
        //         'discount' => $discount,
        //         'subscription_years' => $this->user->subscription_years
        //     ]);
        // }

        if ($coupon) {
            $this->recordCouponHistory($subscription->userPlan, $coupon, $discount);
        }

        // Only manual record if not credit card, let webhook record credit card instead
        if ($info['payment_method'] !== 'credit_card') {
            $subscription->recordPayment($subscription->transactionData, $subscription->userPlan, [ 'update_date' => true ]);
        }

        // mark user as customer
        $this->user->is_customer         = 1;
        $this->user->short_trial_enabled = 0;
        $this->user->customer_id         = $customer->csCustomer->id;

        // remove user trial from previous free trial plan
        if ($this->user->is_trial) {
            $this->user->is_trial      = 0;
            $this->user->trial_ends_at = Carbon::now()->toDateTimeString();
        }
        $this->user->save();
        return $subscription;
    }

    // unsubscribe a trial / paying / grace period plan
    public function unsubscribe()
    {
        // find latest plan
        $currentPlan = LibPlanUser::where('user_id', $this->user->id)->where('is_active', 1)->latest()->first();

        \Log::info("# {$currentPlan->id} {$currentPlan->subscription_id} ({$this->user->email})unsubscribed.");

        if (!$currentPlan || !$currentPlan->is_active) {
            throw new PaymentException('NO_PLAN_SUBSCRIBED');
        }

        if (!in_array($currentPlan->status, [LibPlanUser::IS_STARTED, LibPlanUser::IS_TRIAL, LibPlanUser::IS_GRACE_PERIOD])) {
            throw new PaymentException('PLAN_CANNOT_BE_UNSUBSCRIBED');
        }

        if ($currentPlan->status === LibPlanUser::IS_TRIAL) {
            return PlanSubscription::endFreeTrial($currentPlan, $this->user, $isFree = true);
        }

        // hey, do not unsubscribe here, because he still paid the plan!
        // $this->user->is_customer = false;
        // $this->user->save();
        if ($currentPlan->status === $currentPlan::IS_GRACE_PERIOD) {
            Gateway::via($currentPlan->payment_method)->unsubscribe($currentPlan);
            return PlanSubscription::endPlan($currentPlan);
        }

        // if its distributor, send email to inform distributor
        if ($currentPlan->payment_method === 'distributor') {
            (new Email('distributor-subscription-ending'))->send($this->user, [
                'lib_plan_user_id' => $currentPlan->id
            ]);
        }

        // paying user, let system end it later. Since he might regret.
        return PlanSubscription::endingPlan($currentPlan);
    }

    public function getUpgradeFee (array $options) {
        $currentPlan = $this->user->libPlanUsers()->latest()->first();
        $oldPlan = $currentPlan->libPlan;
        $planToUpgrade = LibPlan::findOrFail($options['lib_plan_id']);
        $remainingDays = $currentPlan->getRemainingDays();
        // dd($remainingDays, $currentPlan->next_billing_at);
        // $totalDays = max(365, $remainingDays);

        $totalDays = 365; // or 364, 366?!?!?
        $multiplier = $oldPlan->month_cycle === 1 ? 12 : 1;
        $priceDiff = ($planToUpgrade->price - $oldPlan->price * $multiplier); // normalize the price difference
        $upgradeFee = $priceDiff * ($remainingDays / $totalDays);
        return (object) [
            'amount' => (int) $upgradeFee,
            // 'commission' => 100,
            // 'distributor_fee' => 23, // let say, user registered by distributor and upgrade. It should also calculate the fee to distributor
            'payment_method' => $currentPlan->payment_method,
        ];
    }

    public function changePlan(LibPlanUser $currentPlan, array $info)
    {
        // if (LibPlanChange::where('lib_plan_user_id', $currentPlan->id)->exists()) {
        //     throw new PaymentException('ALREADY_CHANGE_PLAN_IN_PROGRESS');
        // }
        // if ($currentPlan->payment_method !== 'credit_card' && !app()->environment('testing')) {
        //     throw new PaymentException('CREDIT_CARD_USER_ONLY');
        // }
        $newPlan = LibPlan::find($info['lib_plan_id']);
        $oldPlan = $currentPlan->libPlan;
        if ($currentPlan->lib_plan_id === $newPlan->id) {
            throw new PaymentException('SAME_LIB_PLAN');
        }
        // if ($currentPlan->libPlan->group === $newPlan->group) {
        //     throw new PaymentException('PLAN_GROUP_MUST_NOT_BE_THE_SAME');
        // }

        
        $fee = (new UserLibraryManager($currentPlan->user))->getUpgradeFee([
            'lib_plan_id' => $info['lib_plan_id']
        ]);
        
        // dd($currentPlan->payment_required_until, $fee);
            
        // subscribe to the new plan
        $info['payment_method'] = $info['payment_method'] ?? $currentPlan->payment_method;
        $info['user']           = $currentPlan->user;
        $info['amount']         = $fee->amount;

        $customer               = Gateway::via($currentPlan->payment_method)->findOrCreateCustomer($info);

        // no, now customer will supply a token
        if ($info['payment_method'] === 'credit_card') {
            $info['token'] = $customer->getToken();
        }

        // unsubscribe the old plan after. To prevent cancelled the old plan
        if ($newPlan->month_cycle !== $oldPlan->month_cycle && $info['payment_method'] === 'credit_card') {
            $updateDate = true;
            $subscriptionData   = Gateway::via($currentPlan->payment_method)->subscribe($customer, $newPlan);
            Gateway::via($currentPlan->payment_method)->unsubscribe($currentPlan);
        } else {
            $updateDate = false;
            $info['payment_required_until'] = $currentPlan->payment_required_until;
            $info['next_billing_at']        = $currentPlan->next_billing_at;
            $info['subscription_id']        = $currentPlan->subscription_id;
            $subscriptionData               = Gateway::via($currentPlan->payment_method)->chargeDifference($newPlan, $info);
        }

        $subscription           = new PlanSubscription($customer);
        $subscription           = $subscription->fromGateway($currentPlan->payment_method, $subscriptionData);
        $info['source']         = $currentPlan->source;
        $subscription->switchPlan($newPlan, $info);

        // there wouldn't be webhook: it's direct charge
        // dd($subscription->transactionData, $this->user->subscription_years, $fee, $info['lib_plan_id']);
        $subscription->recordPayment($subscription->transactionData, $subscription->userPlan, [
            'update_date' => $updateDate,
            'status' => 2
        ]);
        PlanSubscription::endPlan($currentPlan, $isFree = false, $skipMail = true);

        if ($this->user->distributor_id) {
            // @todo, it does not take account into discount yet, same for rep.
            self::recordDistributorInvoice($subscription->transactionData, $subscription->userPlan, [
                'subtotal'    => $subscription->chargeAmount,
                'total'       => $subscription->chargeAmount,
                'status'      => 2,
                'subscription_years' => $this->user->subscription_years - 1
            ]);
        }

        $this->user->is_customer = 1;
        $this->user->customer_id = $customer->csCustomer->id;
        $this->user->save();

        return $subscription;
    }

    public function recharge(LibPlanUser $currentPlan, array $info)
    {
        // @todo recharge if plan not same class, forbid. use upgrade / downgrade instead
        $plan = LibPlan::find($currentPlan->lib_plan_id);

        // $address      = Address::find($currentPlan->address_id ?: $info['address_id']);
        $info['user'] = $this->user ;

        // find or create a new chargeable
        $customer         = Gateway::via($info['payment_method'])->findOrCreateCustomer($info);
        // $discount         = $this->getRepresentativeDiscount($plan);
        $discount         = 0;

        $subscriptionData = Gateway::via($info['payment_method'])->subscribe($customer, $plan, [
            'discount' => $discount
        ]);

        $subscription     = new PlanSubscription($customer);
        $subscription     = $subscription->fromGateway($info['payment_method'], $subscriptionData);

        // was credit card, change subscription (NOT tested yet)
        if ($currentPlan->payment_method !== $info['payment_method']) {
            if ($currentPlan->payment_method === 'credit_card') {
                Gateway::via('credit_card')->unsubscribe($currentPlan);
                PlanSubscription::discardPlan($currentPlan);
                $subscription->start($plan, $info);
            } else {
                $currentPlan->payment_method = $info['payment_method'];
                $currentPlan->save();
                $subscription->rechargePlan($currentPlan, $info);
            }
            // $currentPlan      = $subscription->userPlan;
        } else {
            $subscription->rechargePlan($currentPlan, $info);
        }

        // self::recordRepresentativeSubscription($subscription->userPlan, [
        //     'discount' => 0,
        //     'subscription_years' => $this->user->subscription_years
        // ]);

        // Only manual record if not credit card, let webhook record credit card instead
        // if ($info['payment_method'] !== 'credit_card') {
        $payment = $subscription->recordPayment($subscription->transactionData, $subscription->userPlan, [
            'status' => 1,
            'update_date' => true
        ]);
        // dd($payment);
        // }

        // mark user to new customer id if any
        $this->user->is_customer = 1;
        $this->user->customer_id = $customer->csCustomer->id;

        $this->user->save();
        return $subscription;
    }

    public function resume()
    {
        $endingPlan = LibPlanUser::where('user_id', $this->user->id)->where('is_active', 1)->latest()->first();
        if (!in_array($endingPlan->status, [$endingPlan::IS_ENDING])) {
            throw new PaymentException('PLAN_CANNOT_BE_RESUMED');
        }

        \Log::info("# {$endingPlan->id} {$endingPlan->subscription_id} ({$this->user->email}) resumed.");

        $resumedPlan           = $endingPlan;
        $resumedPlan->status   = $resumedPlan::IS_STARTED;
        $resumedPlan->ended_at = null;
        $resumedPlan->save();
        return $resumedPlan;
    }

    public function retry()
    {
        $currentPlan = LibPlanUser::where('user_id', $this->user->id)->where('is_active', 1)->latest()->first();

        if (!in_array($currentPlan->payment_method, ['credit_card', 'testing'])) {
            throw new PaymentException('PLAN_CANNOT_BE_RETRYED');
        }
        Gateway::via($currentPlan->payment_method)->retry($currentPlan);
        $currentPlan->status             = $currentPlan::IS_STARTED;
        $currentPlan->grace_period_until = null;
        $currentPlan->save();

        return $currentPlan;
    }

    public function updateCard(array $data)
    {
        $currentPlan = LibPlanUser::where('user_id', $this->user->id)->where('is_active', 1)->latest()->first();

        if (!in_array($currentPlan->payment_method, ['credit_card', 'testing'])) {
            throw new PaymentException('CANNOT_UPDATE_NON_CREDIT_CARD_PAYMENT');
        }
        $data['user'] = $this->user;

        $result                = Gateway::via($currentPlan->payment_method)->update($currentPlan, $data);
        $paymentMethod         = $result['payment_method'];

        // @todo retry subscription here

        // $currentPlan->status             = $currentPlan::IS_STARTED;
        // $currentPlan->grace_period_until = null;
        $currentPlan->card_brand         = $paymentMethod->cardType;
        $currentPlan->card_tail          = $paymentMethod->last4;
        $currentPlan->save();

        return $currentPlan;
    }

    /* Only register the customer */
    public function freeTrial(array $info)
    {
        // if ($this->user->trial_ends_at) {
        if ($this->user->is_trial || $this->user->free_ends_at) {
            throw new PaymentException('USER_CAN_FREE_TRIAL_ONLY_ONCE');
        }
        if ($this->user->is_customer) {
            throw new PaymentException('PAID_CUSTOMER_CANNOT_FREE_TRIAL');
        }

        // $paymentMethod = isset($info['payment_method']) && Auth::user()->is_representative ? $info['payment_method'] : 'credit_card' ;
        // $paymentMethod = isset($info['payment_method']) && $this->user->short_trial_enabled ? $info['payment_method'] : 'credit_card' ;
        $paymentMethod = "representative_granted";
        
        // create customer (braintree)
        // store subscription
        // use subscription_id -> paid -> make the free trial plan paying plan
        // this id!
        // this customer id!

        // $address = Address::find($info['address_id'] ?? 0);

        $info['user']       = $this->user;
        $info['first_name'] = $this->user->first_name ?: null;
        $info['last_name']  = $this->user->last_name ?: null;
        // $info['company']    = $address->company                               ?? null;

        // find or create a new chargeable customer
        $customer = Gateway::via($paymentMethod)->findOrCreateCustomer($info);

        $plan                 = LibPlan::where('group', 'starter')->where('is_active', 1)->first();
        $subscription         = new PlanSubscription($customer);
        // $trialEndsAt = $paymentMethod === 'representative_granted' ? Carbon::now()->addDays(3) : Carbon::now()->addMonth(1) ;
        $trialEndsAt = Carbon::now()->addYears(10);
        $info['trial_ends_at'] = $trialEndsAt;
        $info['payment_method'] = $paymentMethod;

        $subscription         = $subscription->startFreeTrial($plan, $info);


        $this->user->is_trial            = 1;
        $this->user->short_trial_enabled = 0;
        // $this->user->customer_id         = $customer->csCustomer->id;
        $this->user->trial_ends_at       = $trialEndsAt;
        $this->user->save();

        TrialPlanUpgrade::forceCreate([
            'lib_plan_user_id' => $subscription->userPlan->id,
            'lib_plan_id'      => $plan->id,
        ]);

        return $subscription->userPlan;
    }

    /* Start plan of the customer */
    public function freeTrialStartCharging(array $info)
    {
        $userPlan = LibPlanUser::where([
            'status'    => LibPlanUser::IS_TRIAL,
            'user_id'   => $this->user->id,
            'is_active' => 1
        ])->latest()->first();

        if ($userPlan->payment_method !== 'credit_card') {
            throw new PaymentException('REQUIRE_NEW_SUBSCRIPTION');
        }

        // Old users will upgrade to depreciated starter plans.
        $upgrade  = TrialPlanUpgrade::where('lib_plan_user_id', $userPlan->id)->first();

        // override the 
        if (isset($info['lib_plan_id'])) {
            $upgrade->lib_plan_id = LibPlan::where('is_active', 1)->find($info['lib_plan_id'])->id;
            $upgrade->save();
        }
        $libPlan  = LibPlan::where('is_active', 1)->where('group', 'starter');
        $plan     = $upgrade ? $upgrade->libPlan : $libPlan->first();

        // $discount = $this->getRepresentativeDiscount($plan);
        // no discount for free trial
        $discount = 0;

        $info['user']                 = $this->user;
        $info['payment_method']       = 'credit_card';

        $customer = Gateway::via('credit_card')->findOrCreateCustomer($info);

        $subscriptionData = Gateway::via('credit_card')->subscribe($customer, $plan, [
            'discount'   => $discount
        ]);

        // store on our server
        $subscription         = new PlanSubscription($customer);
        $subscription         = $subscription->fromGateway('credit_card', $subscriptionData);

        $userPlan->started_at      = Carbon::now()->toDateTimeString();
        $userPlan->billing_day     = min(Carbon::now()->day, 28);
        $userPlan->subscription_id = $subscription->id;
        $userPlan->lib_plan_id     = $plan->id;
        $userPlan->source          = $this->user->source;
        $userPlan->payment_method  = $info['payment_method'];
        $userPlan->status          = $userPlan::IS_STARTED;
        $userPlan->next_billing_at = Carbon::now()->day($userPlan->billing_day)
                                   ->addMonths($plan->month_cycle);
        $userPlan->save();

        $subscription->recordPayment($subscription->transactionData, $userPlan);

        // $subscription->startPlan($plan, $info);
        // it will be recorded when charged successfully
        // self::recordRepresentativeSubscription($userPlan, $discount);

        $this->user->is_customer      = true;
        $this->user->is_trial         = false;
        $this->user->trial_ends_at    = Carbon::now()->toDateTimeString();
        $this->user->save();

        if ($upgrade) {
            $upgrade->delete();
        }

        return $subscription->userPlan;
    }

    public function testingUser($options = [])
    {
        $customer = Customer::forceCreate([
            'status'            => Customer::IS_PAYING,
            'user_id'           => $this->user->id,
            'customer_id'       => 'fake_cus_' . str_random(14),
            'payment_method'    => 'alipay',
        ]);
        $plan         = LibPlan::where('key', $options['key'] ?? 'pro_yearly')->where('is_active', 1)->where('month_cycle', 12)->first();
        $info         = [
            'user'              => $this->user,
            'payment_method'    => 'alipay',
            'is_granted'        => true
        ];

        $subscription = new PlanSubscription($customer);
        $subscription->startPlan($plan, $info);
        $this->user->is_customer = 1;
        $this->user->customer_id = null;
        $this->user->save();
        return $subscription;
    }

    /** Distributor send invoice */
    public function invoiceUser($options = [])
    {
        $ongoingPlan = LibPlanUser::where('user_id', $this->user->id)->where('status', '<>', LibPLanUser::IS_ENDED)->first();
        if ($ongoingPlan) {
            throw new PaymentException('CUSTOMER_HAS_PLAN');
        }

        $plan         = LibPlan::findOrFail($options['lib_plan_id']);
        $group        = $this->user->referDistributor->group;
        
        $info         = [
            'user'              => $this->user,
            'address_id'        => $options['address_id'] ?? null,
            'payment_method'    => 'distributor'
        ];

        $discount = $this->getDistributorDiscount($plan);

        $customer = Gateway::via('distributor')->findOrCreateCustomer($info);
        $subscriptionData = Gateway::via('distributor')->subscribe($customer, $plan, [
            'discount' => $discount
        ]);
        $subscription = new PlanSubscription($customer);
        $subscription = $subscription->fromGateway('distributor', $subscriptionData);
        $subscription->startPlan($plan, $info);
        
        $subscription->userPlan->billing_day     = min(Carbon::now()->day, 28);
        $subscription->userPlan->payment_required_until = Carbon::now()->day($subscription->userPlan->billing_day)
        ->addMonths($plan->month_cycle);
        $subscription->userPlan->subscription_id = $subscription->transactionId;
        $subscription->userPlan->save();
        
        $invoice = self::recordDistributorInvoice($subscription->transactionData, $subscription->userPlan, [
            'discount'           => $discount,
            'status'             => 0,
            'subscription_years' => $this->user->subscription_years
        ]);

        $this->user->is_customer = 1;
        $this->user->subscription_years += 1;
        $this->user->customer_id = $customer->csCustomer->id;
        $this->user->save();

        (new Email('distributor-invoice'))->send($this->user, [
            'distributor_invoice_id' => $invoice->id
        ]);
        // @todo queue send email
        return $subscription;
    }

    /** User want to recharge using distributor invoice method  */
    public function invoiceRecharge(LibPlanUser $currentPlan, array $info)
    {
        // @todo recharge if plan not same class, forbid. use upgrade / downgrade instead
        $plan = LibPlan::find($currentPlan->lib_plan_id);
        $group = $this->user->referDistributor->group;

        // $address      = Address::find($currentPlan->address_id ?: $info['address_id']);
        $info['user'] = $this->user;

        $discount = $this->getDistributorDiscount($plan);

        // find or create a new chargeable
        $customer = Gateway::via('distributor')->findOrCreateCustomer($info);
        $subscriptionData = Gateway::via('distributor')->subscribe($customer, $plan);

        $subscription = new PlanSubscription($customer);
        $subscription = $subscription->fromGateway('distributor', $subscriptionData);
        $subscription->rechargePlan($currentPlan, $info);

        $subscription->userPlan->billing_day = min(Carbon::now()->day, 28);
        $subscription->userPlan->payment_required_until = Carbon::parse($subscription->userPlan->payment_required_until)
            ->addMonths($plan->month_cycle);
        $subscription->userPlan->save();

        $invoice = self::recordDistributorInvoice($subscription->transactionData, $subscription->userPlan, [
            // 'update_date' => $updateDate,
            'discount'           => $discount,
            'subscription_years' => $this->user->subscription_years,
            'status' => 1
        ]);

        $this->user->subscription_years += 1;
        $this->user->save();
        // (new Email('distributor-invoice'))->send($this->user, [
        //     'distributor_invoice_id' => $invoice->id
        // ]);

        return $subscription;
    }

    // (normally from cron job)
    public function endFreeTrial()
    {
        if (!$this->user->is_trial) {
            abort('USER_NOT_TRIAL');
        }
        $currentPlan = LibPlanUser::where('status', LibPlanUser::IS_TRIAL)->where('user_id', $this->user->id)->where('is_active', 1)->latest()->first();

        // inactivate the plan
        PlanSubscription::endFreeTrial($currentPlan);
    }

    protected function getRepresentativeDiscount(LibPlan $plan)
    {
        if (!$this->user->representative_id) {
            return 0;
        }
        $rep      = $this->user->referrer;
        $group    = $rep->group;
        $discount = $group->getPlanDiscount($plan, $this->user->subscription_years);
        return $discount;
    }

    protected function getDistributorDiscount(LibPlan $plan)
    {
        if (!$this->user->distributor_id) {
            return 0;
        }
        $rep      = $this->user->referDistributor;
        $group    = $rep->group;
        $discount = $group->getPlanDiscount($plan, $this->user->subscription_years);
        return $discount;
    }

    protected function getCoupon(array $info, LibPlan $plan)
    {
        if (!isset($info['code']) || !$info['code']) {
            return 0;
        }
        $coupon = LibPlanCoupon::apply($info['code'], [
            'skip_date' => $info['payment_method'] === 'telex_transfer'
        ]);
        return $coupon;
    }

    protected function recordCouponHistory(LibPlanUser $userPlan, $coupon, $discount)
    {
        return LibPlanCouponHistory::forceCreate([
            'lib_plan_user_id'   => $userPlan->id,
            'coupon_id'          => $coupon->id,
            'user_id'            => $userPlan->user_id,
            'value'              => $discount,
        ]);
    }

    public static function recordDistributorInvoice($transactionData, LibPlanUser $userPlan, $options = [])
    {
        $dist = $userPlan->user->referDistributor;
        if (!$dist) {
            return null;
        }

        $discount = $options['discount'] ?? 0;
        $subtotal = $options['subtotal'] ?? $userPlan->libPlan->price;
        $total    = $subtotal - $discount;

        
        $compensation = $dist->group->distributorFee($userPlan->libPlan, [
            'subscription_years' => $options['subscription_years'],
            'total'              => $total
        ]);
        
        // dd($discount, $subtotal, $total, $compensation);
            
        return DistributorInvoice::forceCreate([
            'status'           => $options['status'] ?? 0,
            'subscription_years' => $options['subscription_years'],
            'lib_plan_user_id' => $userPlan->id,
            'user_id'          => $userPlan->user->id,
            'distributor_id'   => $userPlan->user->distributor_id,
            'price'            => $subtotal,
            'discount'         => $discount,
            'total'            => $total,
            'commission'       => $total - $compensation,
            'distributor_fee'  => $compensation
        ]);
    }

    public static function recordRepresentativeSubscription(LibPlanUser $userPlan, $options = [])
    {
        $rep = $userPlan->user->referrer;
        if (!$rep) {
            return null;
        }

        $discount = $options['discount'] ?? 0;
        $subtotal = $options['subtotal'] ?? $userPlan->libPlan->price;
        $total    = $subtotal - $discount;
        // dd($discount, $subtotal, $total);

        // dd($discount, $subtotal, $total);

        // if user 
        // if ($this->user->subscription_years && $rep->group->rates->count()) {
        // if ($rep->group->rates->count()) {
        //     $compensation = $rep->group->getPlanCompensation($userPlan->libPlan, $userPlan->user->subscription_years);
        // } else {
        //     $compensation = $rep->group->getPlanCompensation($userPlan->libPlan);
        // }
        $compensation = $rep->group->getPlanCompensation($userPlan->libPlan, [
            'subscription_years' => $options['subscription_years'],
            'total'              => $total
        ]);

        // dd($discount, $subtotal, $total, $rep->group->percentage, $compensation);

        return RepresentativeSubscription::forceCreate([
            'status'             => $options['status'],
            'lib_plan_user_id'   => $userPlan->id,
            'representative_id'  => $rep->id,
            'subscription_years' => $options['subscription_years'], // not $userPlan->user, its changed
            'amount'             => $total,
            'discount'           => $discount,
            'commission'         => $total - $compensation,
            'representative_fee' => $compensation,
        ]);
    }
}
