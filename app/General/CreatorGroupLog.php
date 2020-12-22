<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class CreatorGroupLog extends Model
{

    protected $table = "creator_group_log";

    protected $fillable = [ "user_id", "percentage" ];

}
