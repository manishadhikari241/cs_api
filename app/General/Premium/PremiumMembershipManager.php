<?php

namespace App\General\Premium;

use Auth;
use App\Marketplace\Payments\Gateways\Gateway as PaymentGateway;

class PremiumMembershipManager
{
    /**
     * @depreciated
     */
    public function checkout($info = [])
    {
        $plan = PremiumPlan::find($info['premium_plan_id']);

        $payment = PaymentGateway::via($info['payment_method'])->settle($plan, $info);

        $history = $plan->subscribe(Auth::user(), $payment);
        return $this->handleCheckoutSuccess($history);
    }

    public function handleCheckoutSuccess($history)
    {
        // send email
        return $history;
    }
}
