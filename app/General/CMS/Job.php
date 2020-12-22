<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class Job extends Model
{
    protected $table = "job";
    protected $fillable = ['location_id','is_active','sort_order'];
    public $timestamps = false;
    public function translation () {
        return $this->hasMany(JobTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(JobTranslation::class, 'id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
