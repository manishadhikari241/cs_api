<?php

namespace App\Marketplace\Admin;

// use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{

    protected $table = "price";

    public $timestamps = false;

    protected $casts = ['is_active' => 'boolean', 'price'];

    protected $fillable = ['is_active', 'price' => 'decimal'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
