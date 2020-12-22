<?php

namespace App\Marketplace\Collection;

use App\Utilities\Filters\QueryFilter;
use App\Marketplace\Collection\CollectionItem;
use Illuminate\Database\Eloquent\Model;

class CollectionItemComment extends Model
{
    protected $table = "collection_item_comment";

    public function collectionItem()
    {
        return $this->belongsTo(CollectionItem::class);
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
