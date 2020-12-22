<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class FeedsSimulation extends Model
{
    protected $table = 'feed_simulation';

    public function feed()
    {
        return $this->belongsTo(Feed::class, 'id');
    }

    public function getUploadPath()
    {
        return 'uploads/lib/feed/simulation';
    }
}
