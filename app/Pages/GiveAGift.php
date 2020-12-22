<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class GiveAGift extends Model
{
    protected $table = "give_a_gift";

    public function translation () {
        return $this->hasMany(GiveAGiftTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(GiveAGiftTranslation::class, 'id', 'id');
    }
}