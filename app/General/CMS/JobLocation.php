<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class JobLocation extends Model
{
    protected $table = "job_location";
    protected $fillable = ['image','sort_order','is_active'];
    protected $casts =['is_active' => 'boolean'];

    public $timestamps = false;

    public function translation () {
        return $this->hasMany(JobLocationTranslation::class, 'id', 'id');
    }
    
    public function translations () {
        return $this->hasMany(JobLocationTranslation::class, 'id', 'id');
    }

    public function jobs () {
      return $this->hasMany(Job::class, 'location_id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
    public function getUploadPath()
    {
      return "uploads/career/location/";
    }
}