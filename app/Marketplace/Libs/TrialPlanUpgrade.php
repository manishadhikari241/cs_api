<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrialPlanUpgrade extends Model
{
    use SoftDeletes;

    protected $table = 'trial_plan_upgrade';


    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class);
    }
}