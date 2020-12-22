<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class FeedsMoodboard extends Model
{
    protected $table = 'feed_moodboard';

    public function feed()
    {
        return $this->belongsTo(Feed::class, 'id');
    }

    public function getUploadPath()
    {
        return 'uploads/lib/feed/moodboard';
    }
}
