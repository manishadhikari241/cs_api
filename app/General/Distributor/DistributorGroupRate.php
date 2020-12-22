<?php

namespace App\General\Distributor;

use Illuminate\Database\Eloquent\Model;

class DistributorGroupRate extends Model
{
    protected $table    = 'distributor_group_rate';
    protected $fillable = [
        'distributor_group_id',
        'subscription_years',
        'discount_starter',
        'discount_starter_yearly',
        'discount_pro',
        'discount_pro_yearly',
        'percentage',
    ];
}
