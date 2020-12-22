<?php

namespace App\Marketplace\Libs;

use Carbon\Carbon;
use App\Exceptions\CouponException;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\General\Representative\Representative;

class LibPlanCoupon extends Model
{
    protected $table    = 'lib_plan_coupon';

    protected $fillable = ['discount_starter', 'discount_starter_yearly', 'discount_pro', 'discount_pro_yearly', 'representative_id', 'expired_at'];

    public $timestamps = ['created_at', 'updated_at', 'expired_at'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getPlanDiscount(LibPlan $plan)
    {
        return $this["discount_{$plan->key}"];
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public static function apply($code, $options = [])
    {
        $coupon = self::where('code', $code)->first();
        if (!$coupon) {
            throw new CouponException('COUPON_INVALID');
        }
        if (isset($options['skip_date']) && $options['skip_date']) {
            // skip checking (for tt delayed payment)
            // @todo should check with source created date
        } elseif ($coupon->expired_at && Carbon::now()->gt(Carbon::parse($coupon->expired_at))) {
            throw new CouponException('COUPON_INVALID');
        }
        return $coupon;
    }
}
