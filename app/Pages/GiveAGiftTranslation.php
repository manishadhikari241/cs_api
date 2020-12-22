<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class GiveAGiftTranslation extends Model
{
    protected $table = "give_a_gift_translation";

    public function giveAGift () {
        return $this->belongsTo(GiveAGift::Class, 'id');
    }

}