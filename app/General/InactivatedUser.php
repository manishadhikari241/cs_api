<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class InactivatedUser extends Model
{
    protected $table    = 'inactivated_user';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
