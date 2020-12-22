<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PressTranslation extends Model
{
    protected $table = "press_translation";
    public $timestamps = false;
    protected $fillable =['title','short_desc','content','meta_description','meta_keyword','meta_title'];
    public function press () {
        return $this->belongsTo(Press::Class, 'id');
    }

}