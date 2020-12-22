<?php

namespace App\General\Distributor;

use App\Marketplace\Libs\LibPlan;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class DistributorGroup extends Model
{

    protected $table = "distributor_group";

    protected $fillable = [ 'name', 'percentage', 'discount_starter', 'discount_starter_yearly', 'discount_pro', 'discount_pro_yearly' ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function distributors()
    {
        return $this->hasMany(Distributor::class);
    }

    public function logs()
    {
        return $this->hasMany(DistributorGroupRateLog::class);
    }

    public function rates()
    {
        return $this->hasMany(DistributorGroupRate::class);
    }

    // public function commission(LibPlan $libPlan, $years = 0)
    // {
    //     $rate = $this->rates->where('subscription_years', '<=', $years)->sortByDesc('subscription_years')->first();
    //     $instance = (isset($rate) && $rate) ? $rate : $this;
    //     $total = $libPlan->price;
    //     $discount = $instance["discount_{$libPlan->key}"];
    //     $subtotal = $total - $discount;
    //     return $subtotal * (float)(100 - $instance->percentage) / 100;
    // }

    public function distributorFee(LibPlan $libPlan, array $options)
    {
        $rate     = $this->rates->where('subscription_years', '<=', $options['subscription_years'])->sortByDesc('subscription_years')->first();
        $instance = (isset($rate) && $rate) ? $rate : $this;
        return $options['total'] * $instance->percentage / 100;

        // $rate = $this->rates->where('subscription_years', '<=', $years)->sortByDesc('subscription_years')->first();
        // $instance = (isset($rate) && $rate) ? $rate : $this;
        // $total = $libPlan->price;
        // $discount = $instance["discount_{$libPlan->key}"];
        // $subtotal = $total - $discount;
        // return $subtotal - $this->commission($libPlan, $years);
    }

    public function getPlanDiscount(LibPlan $plan, $years = 0)
    {
        $rate = $this->rates->where('subscription_years', '<=', $years)->sortByDesc('subscription_years')->first();
        $instance = (isset($rate) && $rate) ? $rate : $this;
        return $instance["discount_{$plan->key}"];
    }
}
