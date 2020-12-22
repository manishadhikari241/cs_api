<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class PublicImage extends Model
{
    protected $table = 'user_public_image';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUploadPath()
    {
        return "uploads/user/public-image/{$this->user_id}/";
    }
}
