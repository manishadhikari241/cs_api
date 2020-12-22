<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class FAQ extends Model
{
    protected $table = "faq";
    protected $fillable = ['is_active','sort_order','type'];
    public $timestamp = false;

    public function translation() {
        return $this->hasMany(FAQTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(FAQTranslation::class, 'id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}