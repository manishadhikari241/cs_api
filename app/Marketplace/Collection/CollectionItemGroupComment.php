<?php

namespace App\Marketplace\Collection;

use App\Utilities\Filters\QueryFilter;
use App\Marketplace\Collection\CollectionItemGroup;
use Illuminate\Database\Eloquent\Model;

class CollectionItemGroupComment extends Model
{
    protected $table = "collection_item_group_comment";

    protected $fillable = [ 'body' ];

    public function group()
    {
        return $this->belongsTo(CollectionItemGroup::class);
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
