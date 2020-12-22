<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{

    protected $table    = "sf_guard_permission";
    protected $fillable = ['keyword', 'description'];
    public $timestamps  = false;

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'sf_guard_group_permission', 'permission_id', 'group_id')->withTimestamps();
    }


}
