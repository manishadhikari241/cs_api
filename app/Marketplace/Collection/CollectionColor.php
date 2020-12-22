<?php

namespace App\Marketplace\Collection;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CollectionColor extends Model
{
    protected $table = "collection_color";

    protected $fillable = [ 'tpx', 'code', 'sort_order'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function collectionItem()
    {
        return $this->morphMany('App\Marketplace\Collection\CollectionItem', 'item');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
