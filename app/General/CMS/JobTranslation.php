<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;

class JobTranslation extends Model
{
    protected $table = "job_translation";
    protected $fillable = ['title','content','lang'];
    public $timestamps = false;
    
    public function job () {
        return $this->belongsTo(Job::Class, 'id');
    }

}