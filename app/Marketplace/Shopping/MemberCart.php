<?php

namespace App\Marketplace\Shopping;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class MemberCart extends Model
{
    protected $table = "member_cart";

    protected $fillable = [ 'type' ];

    public function product () {
        return $this->belongsTo('App\Marketplace\Designs\Design', 'item');
    }

    public function coupon () {
        return $this->belongsTo(Coupon::class, 'item');
    }

    public function voucher () {
        return $this->belongsTo(Voucher::class, 'item');
    }

    public function user () {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getDiscount ($subTotal) {
      $coupon = Coupon::find($this->item);
      // return $coupon ? $coupon->validate($this->user_id)->discount($subTotal) : 0;
      return $coupon ? $coupon->discount($subTotal) : 0;
    }

    public function getUsage ($subTotal) {
      $voucher = Voucher::find($this->item);
      return $voucher ? $voucher->validate($this->user_id)->getUsage($subTotal) : 0;
    }

    public function consume ($usage, $order) {
      return Voucher::find($this->item)->consume($usage, $order);
    }

    public function apply ($discount, $order) {
      return Coupon::find($this->item)->apply($discount, $order);
    }

}