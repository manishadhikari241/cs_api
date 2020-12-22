<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibPlanChange extends Model
{
    protected $table = 'lib_plan_change';

    public function libPlanUser()
    {
        return $this->belongsTo(LibPlanUser::class);
    }

    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class);
    }
}