<?php

namespace App\General\Representative;

use App\Marketplace\Libs\LibPlan;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class RepresentativeGroup extends Model
{
    protected $table    = 'representative_group';
    protected $fillable = ['name', 'percentage',
        'discount_starter',
        'discount_starter_yearly',
        'discount_pro',
        'discount_pro_yearly',
        'compensation_starter',
        'compensation_starter_yearly',
        'compensation_pro',
        'compensation_pro_yearly',
    ];

    public function representatives()
    {
        return $this->hasMany(Representative::class);
    }

    public function rates()
    {
        return $this->hasMany(RepresentativeGroupRate::class);
    }

    public function logs()
    {
        return $this->hasMany(RepresentativeGroupRateLog::class);
    }

    public function commission($total, $years = 0)
    {
        $rate = $this->rates->where('subscription_years', '<=', $years)->sortByDesc('subscription_years')->first();
        $instance = (isset($rate) && $rate) ? $rate : $this;
        return $total * (float) (100 - $instance->percentage) / 100;
    }

    public function representativeFee($total, $years = 0)
    {
        return $total - $this->commission($total, $years);
    }

    public function getPlanDiscount(LibPlan $plan, $years = 0)
    {
        $rate = $this->rates->where('subscription_years', '<=', $years)->sortByDesc('subscription_years')->first();
        $instance = (isset($rate) && $rate) ? $rate : $this;
        return $instance["discount_{$plan->key}"];
    }

    // @todo how about upgrade recharge
    // @todo minus discount first
    // @todo test webhook post to get plan upgrade (no discount but commission)
    public function getPlanCompensation(LibPlan $plan, $options = [])
    {
        $rate = $this->rates->where('subscription_years', '<=', $options['subscription_years'])->sortByDesc('subscription_years')->first();
        $instance = (isset($rate) && $rate) ? $rate : $this;
        return $options['total'] * $instance->percentage / 100;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
