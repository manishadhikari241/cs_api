<?php

namespace App\Marketplace\Libs;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Designs\Design;

class Applyon extends Model
{
    protected $table = 'applyon';

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(ApplyonsTranslation::class, 'id', 'id');
    }
}
