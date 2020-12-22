<?php

namespace App\Marketplace\Common;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table    = "country";
    protected $fillable = ['sort_order'];
    public $timestamps  = false;

    public function translations()
    {
        return $this->hasMany('App\Marketplace\Common\CountriesTranslation', 'id', 'id');
    }
    public function studio()
    {
        return $this->hasOne(Marketplace\Studio\Studio::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
