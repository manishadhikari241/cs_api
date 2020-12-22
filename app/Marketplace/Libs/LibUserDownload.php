<?php

namespace App\Marketplace\Libs;

use App\Marketplace\Designs\Design;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibUserDownload extends Model
{
    use SoftDeletes;

    protected $table    = 'lib_user_download';

    public function design()
    {
        return $this->belongsTo(Design::class);
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
