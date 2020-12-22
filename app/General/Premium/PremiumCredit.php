<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class PremiumCredit extends Model
{
    protected $table = "premium_credit";

    // only order, not payable instance!
    public function settle($order, $input)
    {
        // $telexUser = \Auth::user()->telex()->first();
        // if (!$telexUser->is_active) {abort(422, 'TELEX_USER_INACTIVE');}
        $this->ensureHasCredit(\Auth::user(), $order->total);
        $order->save();
        $usage = PremiumCreditUsage::forceCreate([
            'user_id'        => \Auth::user()->id,
            'value'          => $order->total,
            'usage_id'       => $order->id,
            'usage_type'     => "App\Marketplace\Shopping\Order",
            'transaction_id' => str_random(10),
        ]);
        // $this->order_id       = $order->id;
        // $this->telex_user_id  = $telexUser->id;
        // $this->save();
        return (object) [
            'transaction_id' => $usage->transaction_id,
        ];
    }

    protected function ensureHasCredit($user, $additionalAmount)
    {
        // $outstandingTransfer = Order::whereHas('telexTransfer', function ($query) use ($telexUser) {
        //   return $query->where([
        //     'status'        => TelexTransfer::IS_OUTSTANDING,
        //     'telex_user_id' => $telexUser->id
        //   ]);
        // })->sum('total');
        if ($additionalAmount > $user->credit()->sum('value') - $user->creditUsages()->sum('value')) {
            abort(422, 'CREDIT_AMOUNT_EXCESS_CAPACITY');
        }
    }

}
