<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CSCoupon extends Model {

    protected $table = 'cscoupons';

    public function activate($user) {
        if (is_null($this->user_id)) {
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->channel = 'coupon';
            $payment->amount = 0;
            $payment->package = $this->package;
            $payment->quantity = $this->quantity;
            $payment->save();

            $quota = $user->quota;
            if (is_null($quota))
                $quota = Quota::createEmpty($user->id);
            $quota->{$this->package} += $this->quantity;
            $quota->{$this->package.'_expiry'} = Carbon::now()->addYear();
            $quota->save();

            $this->user_id = $user->id;
            $this->save();
        }
    }

}
