<?php

namespace App\Marketplace\Shopping;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CouponHistory extends Model
{

    protected $table = "coupon_history";

    protected $fillable = [ 'order_id', 'user_id', 'amount' ];

    public function coupon () {
      $this->belongsTo(Coupon::class);
    }

}