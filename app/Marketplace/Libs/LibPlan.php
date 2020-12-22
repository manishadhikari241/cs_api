<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibPlan extends Model
{
    protected $table    = 'lib_plan';

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function users()
    {
        return $this->hasMany(LibPlanUser::class);
    }

    public function translations()
    {
        return $this->hasMany(LibPlansTranslation::class, 'id');
    }

    public function isYearly()
    {
        return $this->month_cycle === 12;
    }
}
