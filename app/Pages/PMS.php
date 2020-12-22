<?php

namespace App\Pages;
use App\Pages\PMSTranslation;
use Illuminate\Database\Eloquent\Model;

class PMS extends Model
{
    protected $table = "pms";

    public function translation () {
        return $this->hasMany(PMSTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(PMSTranslation::class, 'id', 'id');
    }
    public function getUploadPath()
    {
      return "uploads/contents/";
    }

}