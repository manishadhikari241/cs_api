<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CMS extends Model
{
    protected $table = "cms";

    public function translation () {
        return $this->hasMany(CMSTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(CMSTranslation::class, 'id', 'id');
    }
}