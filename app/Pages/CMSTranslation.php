<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CMSTranslation extends Model
{
    protected $table = "cms_translation";

    public function cms () {
        return $this->belongsTo(CMS::Class, 'id');
    }

}