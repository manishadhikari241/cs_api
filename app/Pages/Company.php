<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = "company";

    public function translation () {
        return $this->hasMany(CompanyTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(CompanyTranslation::class, 'id', 'id');
    }
}