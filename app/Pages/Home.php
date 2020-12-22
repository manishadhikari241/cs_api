<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    protected $table = "homepage";

    public function translation () {
        return $this->hasMany(HomeTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(HomeTranslation::class, 'id', 'id');
    }
}