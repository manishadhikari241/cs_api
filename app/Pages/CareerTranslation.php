<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CareerTranslation extends Model
{
    protected $table = "career_translation";

    public function career () {
        return $this->belongsTo(Career::Class, 'id');
    }

}