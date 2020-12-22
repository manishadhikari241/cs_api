<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibInspirationsMoodboard extends Model
{
    protected $table = 'lib_inspiration_moodboard';

    public function LibInspiration()
    {
        return $this->belongsTo(LibInspiration::class, 'id');
    }

    public function getUploadPath()
    {
        return 'uploads/lib/inspiration/';
    }
}
