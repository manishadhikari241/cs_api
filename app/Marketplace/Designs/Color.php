<?php

namespace App\Marketplace\Designs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = "color";

    protected $fillable = ['is_active', 'code', 'sort_order'];

    // protected $casts = [
    //     'is_active' => 'boolean',
    // ];

    public function designs()
    {
        return $this->belongsToMany(Design::class, 'product_color');
    }

    public function translations()
    {
        return $this->hasMany(ColorsTranslation::class, 'id', 'id');
    }
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
