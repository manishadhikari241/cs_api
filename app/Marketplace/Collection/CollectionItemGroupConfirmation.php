<?php

namespace App\Marketplace\Collection;

use App\Marketplace\Collection\Collection;

use App\Marketplace\Collection\CollectionItem;use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CollectionItemGroupConfirmation extends Model
{
    protected $table = "collection_item_group_confirmation";

    public function item()
    {
        return $this->hasMany(CollectionItem::class, 'group_id');
    }

    public function group()
    {
        return $this->belongsTo(CollectionItemGroup::class, 'group_id');
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function collection()
    {
        return $this->belongs(Collection::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
