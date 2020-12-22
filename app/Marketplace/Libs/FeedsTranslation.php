<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class FeedsTranslation extends Model
{
    protected $table = 'feed_translation';

    public $timestamps  = false;

    protected $fillable = ['title', 'description', 'lang'];

    public function feed()
    {
        return $this->belongsTo(Feed::class, 'id');
    }
}
