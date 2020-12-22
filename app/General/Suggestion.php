<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class Suggestion extends Model
{

    protected $table = "suggestion";

    protected $fillable = [ 'name', 'email', 'message', 'user_id' ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
