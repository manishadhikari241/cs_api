<?php

namespace App\Pages;

use App\User;
use App\Pages\PMS;
use Illuminate\Database\Eloquent\Model;

class PMSTranslation extends Model
{
    protected $table = "pms_translation";
    protected $fillable = [ 'lang', 'content' ];
    public function cms () {
        return $this->belongsTo(PMS::Class, 'id');
    }

   public function getUploadPath($type = "content")
    {
    return "uploads/contents/";
    }
}