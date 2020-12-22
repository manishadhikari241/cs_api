<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Designs\Design;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibRequest extends Model
{
    use SoftDeletes;

    protected $table = 'lib_request';

    protected $fillable = [ 'is_hidden' ];

    const IS_PENDING   = 0;
    const IS_APPROVED  = 2;
    const IS_REJECTED  = 8;

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function group()
    {
        return $this->belongsTo(LibRequestGroup::class, 'lib_request_group_id');
    }

    public function getUploadPath()
    {
        return "uploads/user/lib-request/{$this->user_id}/";
    }

    public function files()
    {
        return $this->hasMany(LibRequestFile::class, 'lib_request_id');
    }

    public function designs()
    {
        return $this->belongsToMany(Design::class, 'lib_request_design');
    }

    public function collections()
    {
        return $this->belongsToMany(Feed::class, 'lib_request_collection', 'lib_request_id', 'collection_id');
    }
}
