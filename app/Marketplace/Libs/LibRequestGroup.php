<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibRequestGroup extends Model
{
    use SoftDeletes;
    
    protected $table = 'lib_request_group';

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function requests()
    {
        return $this->hasMany(LibRequest::class);
    }
}
