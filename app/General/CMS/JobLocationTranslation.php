<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;

class JobLocationTranslation extends Model
{
	public $timestamps = false;
    protected $table = "job_location_translation";

    public function location () {
        return $this->belongsTo(JobLocation::Class, 'id');
    }

}