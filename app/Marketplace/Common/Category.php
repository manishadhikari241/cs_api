<?php

namespace App\Marketplace\Common;

// use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = "categories";

    protected $fillable = ['is_active', 'sort_order', 'type'];

    public function designs()
    {
        $this->primaryKey = 'tag_id';
        $relation = $this->belongsToMany('App\Marketplace\Designs\Design', 'product_tag', 'tag_id', 'product_id');
        $this->primaryKey = 'id';
        return $relation;
    }

    public function tag()
    {
        return $this->belongsTo('App\Marketplace\Common\Tag', 'tag_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoriesTranslation::class, 'id', 'id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
