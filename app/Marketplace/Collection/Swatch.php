<?php

namespace App\Marketplace\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Swatch extends Model
{
    use SoftDeletes;
    
    protected $table = "swatch";

    public function collectionItem()
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function getUploadPath()
    {
        $prefix = app()->environment('production') ? "uploads/swatch/" : "uploads/swatch/";
        return "{$prefix}{$this->user_id}/";
    }

}
