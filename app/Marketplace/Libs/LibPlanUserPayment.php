<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibPlanUserPayment extends Model
{
    protected $table    = 'lib_plan_user_payment';

    const STATUS = [
        0 => 'is_subscription',
        1 => 'is_continue',
        2 => 'is_upgrade'
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function libPlanUser()
    {
        return $this->belongsTo(LibPlanUser::class);
    }
}
