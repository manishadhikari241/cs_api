<?php

namespace App\Marketplace\Collection;

use App\Marketplace\Collection\CollectionItemComment;
use App\Marketplace\Collection\CollectionItemConfirmation;
use App\Marketplace\Goods\Good;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CollectionItem extends Model
{
    protected $table    = "collection_item";
    protected $fillable = ['good_id', 'style', 'item_id'];
    protected $casts    = [
        'style'          => 'json',
        'is_transferred' => 'boolean',
    ];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function group()
    {
        return $this->belongsTo(CollectionItemGroup::class);
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function comments()
    {
        return $this->hasMany(CollectionItemComment::class);
    }

    public function commentsCount()
    {
        return $this->hasOne(CollectionItemComment::class)
            ->selectRaw('collection_item_id, count(*) as aggregate')
            ->groupBy('collection_item_id');
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


    public function confirmations()
    {
        return $this->hasMany(CollectionItemConfirmation::class);
    }
    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function swapOrder($item)
    {
        $swap = $this->sort_order;
        $this->sort_order = $item->sort_order;
        $this->save();
        $item->sort_order = $swap;
        $item->save();
        return $this;
    }
}
