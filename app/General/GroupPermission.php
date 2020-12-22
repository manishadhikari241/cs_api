<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class GroupPermission extends Model
{

    protected $table = "sf_guard_group_permission";

    protected $fillable = [ 'group_id', 'permission_id' ];

    public function groups()
    {
      return $this->belongsTo('App\Groups');
    }

    public function permission()
    {
      return $this->belongsTo(Permission::class);
    }

}