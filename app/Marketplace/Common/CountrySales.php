<?php

namespace App\Marketplace\Common;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CountrySales extends Model
{
    protected $table    = 'country_sales';

    public function country()
    {
        return $this->belongsTo('App\Marketplace\Common\Country', 'country_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
