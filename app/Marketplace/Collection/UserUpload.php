<?php

namespace App\Marketplace\Collection;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserUpload extends Model
{
    protected $table = "user_upload";

    use SoftDeletes;

    public function collectionItem()
    {
        return $this->morphMany(CollectionItem::class, 'item');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function getUploadPath()
    {
        $prefix = app()->environment('production') ? "uploads/user-upload/" : "uploads/user-upload/";
        return "{$prefix}{$this->user_id}/";
    }
}
