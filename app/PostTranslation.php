<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model {

    public function Post() {
        return $this->belongsTo(Post::class, 'id');
    }

}
