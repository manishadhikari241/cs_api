<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $table = "sf_guard_group";
    protected $fillable = ['name', 'description'];

    public function users()
    {
      return $this->belongsToMany('App\User', 'sf_guard_user_group', 'group_id', 'user_id')->withTimestamps();
    }

    public function permissions()
    {
      return $this->belongsToMany(Permission::class, 'sf_guard_group_permission', 'group_id', 'permission_id')->withTimestamps();
    }

}