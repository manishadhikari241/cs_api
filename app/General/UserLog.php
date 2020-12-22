<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{

    protected $table = "sf_guard_user_log";

    protected $fillable = ['user_id', 'remarks'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function handler()
    {
        return $this->belongsTo('App\User', 'handler_id');
    }

}