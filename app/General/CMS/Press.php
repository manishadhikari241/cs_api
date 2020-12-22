<?php

namespace App\General\CMS;
use App\Utilities\Filters\QueryFilter;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Press extends Model
{
    protected $table = "press";
    protected $fillable = ['image','permalink','sort_order','is_active'];
    protected $casts    = ['is_active' => 'boolean'];
    public function translation () {
        return $this->hasMany(PressTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(PressTranslation::class, 'id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
    public function getUploadPath()
    {
      return "uploads/press/";
    }
}