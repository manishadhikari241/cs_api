<?php

namespace App\Marketplace\Common;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table    = "tag";
    protected $fillable = ['is_active', 'is_exclusive'];
    protected $casts    = ['is_active' => 'boolean'];

    public function designs()
    {
        return $this->belongsToMany('App\Marketplace\Designs\Design', 'product_tag', 'tag_id', 'product_id');
    }

    public function category()
    {
        return $this->hasOne('App\Marketplace\Common\Category', 'tag_id');
    }

    public function translation()
    {
        return $this->hasOne('App\Marketplace\Common\TagsTranslation', 'id', 'id');
    }

    public function translations()
    {
        return $this->hasMany('App\Marketplace\Common\TagsTranslation', 'id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
