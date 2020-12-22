<?php

namespace App\General\Representative;

use App\Marketplace\Libs\LibPlan;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class RepresentativeGroupRate extends Model
{
    protected $table    = 'representative_group_rate';
    protected $fillable = [
        'percentage',
        'representative_group_id',
        'subscription_years',
        'discount_starter',
        'discount_starter_yearly',
        'discount_pro',
        'discount_pro_yearly',
        'compensation_starter',
        'compensation_starter_yearly',
        'compensation_pro',
        'compensation_pro_yearly',
    ];
}