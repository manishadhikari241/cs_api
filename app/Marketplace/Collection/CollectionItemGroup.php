<?php

namespace App\Marketplace\Collection;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CollectionItemGroup extends Model
{
    protected $table = "collection_item_group";

    protected $fillable = ['name', 'description'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function confirmations()
    {
        return $this->hasMany(CollectionItemGroupConfirmation::class, 'group_id');
    }

    public function items()
    {
        return $this->hasMany(CollectionItem::class, 'group_id');
    }

    public function comments()
    {
        return $this->hasMany(CollectionItemGroupComment::class, 'group_id');
    }

    public function commentsCount()
    {
        return $this->hasOne(CollectionItemGroupComment::class, 'group_id')
            ->selectRaw('group_id, count(*) as aggregate')
            ->groupBy('group_id');
    }

    public function getCommentsCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if (!array_key_exists('commentsCount', $this->relations)) {
            $this->load('commentsCount');
        }

        $related = $this->getRelation('commentsCount');

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function swapOrder($group)
    {
        $swap             = $this->sort_order;
        $this->sort_order = $group->sort_order;
        $this->save();
        $group->sort_order = $swap;
        $group->save();
        return $this;
    }
}
