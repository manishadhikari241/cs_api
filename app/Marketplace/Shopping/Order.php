<?php

namespace App\Marketplace\Shopping;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Studio\StudioPermit;

class Order extends Model
{
    protected $table = "orders";

    protected $fillable = [ "order_id", "order_data" ];

    protected $casts = [ "order_data" => 'json' ];

    public function products()
    {
        return $this
              ->belongsToMany('App\Marketplace\Designs\Design', 'orders_product', 'order_id', 'product_id')
              ->withPivot('product_id', 'price', 'type', 'creator_fee', 'commission')
              ->withTimestamps();
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function giftCard()
    {
        return $this->hasMany(Voucher::class, 'order_id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function sales()
    {
        return $this->hasMany(Sales::class);
    }
    
    public function permits () {
        return $this->hasMany(StudioPermit::class);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function nation()
    {
        return $this->belongsTo('App\Marketplace\Common\Country', 'payment_country');
    }

    public function telexTransfer()
    {
        return $this->hasOne('App\Marketplace\Payments\Gateways\TelexTransfer', 'order_id');
    }

    public function representativeOrder () {
        return $this->hasOne('App\General\Representative\RepresentativeOrder', 'order_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    /* should not account for the gift card / premium, becuase otherwise, the next buy will also calculated twice when use it. */
    /* note, might should use the commission sum to calculate, depend on the scope */
    public function subTotal()
    {
        return (float) $this->sales()->sum('price');
    }

    public function total()
    {
        return $this->total;
    }
}
