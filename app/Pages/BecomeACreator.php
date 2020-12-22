<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class BecomeACreator extends Model
{
    protected $table = "become_a_creator";

    public function translation () {
        return $this->hasMany(BecomeACreatorTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(BecomeACreatorTranslation::class, 'id', 'id');
    }
}