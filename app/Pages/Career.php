<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    protected $table = "career";

    public function translation () {
        return $this->hasMany(CareerTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(CareerTranslation::class, 'id', 'id');
    }
}