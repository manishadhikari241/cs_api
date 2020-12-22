<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibMonthUser extends Model
{
    protected $table    = 'lib_month_user';

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class);
    }

    public function libMonth()
    {
        return $this->belongsTo(LibMonth::class);
    }
}
