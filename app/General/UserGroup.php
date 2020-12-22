<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{

    protected $table = "sf_guard_user_group";

    protected $fillable = [ 'user_id', 'group_id' ];

    public function users()
    {
      return $this->belongsTo('App\User');
    }

    public function group()
    {
      return $this->belongsTo(Group::class);
    }

}