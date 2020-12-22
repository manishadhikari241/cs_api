<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table    = 'customer';

    const IS_CREATED        = 1;
    const IS_TRIAL          = 2;
    const IS_PAYING         = 3;
    const IS_ENDED          = 4;

    // public function scopeFilter($query, QueryFilter $filters)
    // {
    //     return $filters->apply($query);
    // }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
