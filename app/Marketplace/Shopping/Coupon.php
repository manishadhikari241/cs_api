<?php

namespace App\Marketplace\Shopping;

use App\Utilities\Filters\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Studio\Studio;

class Coupon extends Model
{
    protected $table = 'coupon';

    protected $fillable = ['is_active', 'start_date', 'end_date', 'name', 'code', 'discount_type', 'amount', 'uses_per_user', 'uses_per_coupon', 'min_total'];

    protected $casts    = ['is_active' => 'boolean'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function discount($subTotal)
    {
        if ($this->discount_type === 1) {
            // dd($subTotal, $this->amount, 100);
            return (float) $subTotal * ((float) $this->amount) / (float) 100;
        }
        return (float) min($this->amount, $subTotal);
    }

    public function histories()
    {
        return $this->hasMany(CouponHistory::class);
    }

    public function promotion()
    {
        return $this->hasOne('App\General\Promotion', 'code', 'code');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function validate($user_id)
    {
        if (!$this->is_active) {
            abort(422, 'COUPON_INACTIVE');
        }
        if ($this->discount_type === 1 && ($this->amount < 1 || $this->amount > 99)) {
            abort(422, 'COUPON_INVALID');
        }
        if (!Carbon::now()->between(Carbon::parse($this->start_date), Carbon::parse($this->end_date))) {
            abort(422, 'COUPON_INVALID_DATE');
        }
        // usage
        if ($this->histories()->where('user_id', $user_id)->count() >= $this->uses_per_user) {
            abort(422, 'COUPON_INVALID_USER_USAGE');
        }
        // usage
        if ($this->histories()->count() >= $this->uses_per_coupon) {
            abort(422, 'COUPON_INVALID_COUPON_USAGE');
        }
        return $this;
    }

    public function apply($discount, $order)
    {
        $history = new CouponHistory([
            'order_id' => $order->id,
            'user_id'  => $order->user_id,
            'amount'   => $discount,
        ]);
        $this->histories()->save($history);
        return $history;
    }

    public function data($discount)
    {
        return [
            'code'   => $this->code,
            'type'   => $this->discount_type,
            'amount' => $this->amount,
            'cost'   => $discount,
        ];
    }

    public function isUsed()
    {
        return $this->hasOne(CouponHistory::class)
            ->selectRaw('coupon_id, count(*) as aggregate')
        // ->where('user_id', \Auth::id())
            ->groupBy('coupon_id');
    }

    public function getisUsedAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (!array_key_exists('isUsed', $this->relations)) {
            $this->load('isUsed');
        }

        $related = $this->getRelation('isUsed');

        // then return the count directly
        return ($related) ? (int) 1 : 0;
    }

    public function setAmountAttribute($value)
    {
        if ($this->discount_type === 1 && $value > 100) {
            abort(422, 'COUPON_PERCENTAGE_CANNOT_BE_GREATER_THAN_100');
        } else {
            $this->attributes['amount'] = $value;
        }
    }

    // public function products () {
    //     return $this
    //           ->belongsToMany('App\Marketplace\Designs\design', 'coupon_product', 'coupon_id', 'product_id')
    //           ->withPivot('product_id')
    //           ->withTimestamps();
    // }
}
