<?php

namespace App\Marketplace\Studio;

use Illuminate\Database\Eloquent\Model;

class StudiosTranslation extends Model
{
    protected $table = 'studio_translation';

    protected $fillable = ['name', 'lang','description'];

    public function studio()
    {
        return $this->belongsTo(Studio::class, 'id');
    }
    
    // public function setNameAttribute($value)
    // {
    //     $this->attributes['name'] = self::tagify($value);
    // }

    public static function tagify($name)
    {
        return urldecode(trim(mb_strtolower($name)));
    }

    public static function removeChineseSpace($name)
    {
        return preg_replace('/\s+/', '', $name);
    }

}
