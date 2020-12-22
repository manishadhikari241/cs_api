<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibCategory extends Model
{
    protected $table    = 'lib_category';

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(LibCategoryTranslation::class, 'id', 'id');
    }

    public function designs()
    {
        return $this->hasMany(LibMonthDesign::class);
    }

    public function inspirations()
    {
        return $this->hasMany(LibInspiration::class);
    }
}
