<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model {

    public function translations() {
        return $this->hasMany('App\PostTranslation', 'id', 'id');
    }

    public function author() {
        return $this->belongsTo('App\User', 'user_id');
    }

}
