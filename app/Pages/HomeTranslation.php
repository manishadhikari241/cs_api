<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class HomeTranslation extends Model
{
    protected $table = "homepage_translation";

    public function home () {
        return $this->belongsTo(Home::Class, 'id');
    }

}