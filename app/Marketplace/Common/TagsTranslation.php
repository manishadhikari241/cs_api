<?php

namespace App\Marketplace\Common;

use Illuminate\Database\Eloquent\Model;

class TagsTranslation extends Model
{
    protected $table = "tag_translation";

    public $timestamps  = false;

    protected $fillable = ['name', 'lang'];
    
    public function designs()
    {
        return $this->belongsToMany('App\Marketplace\Designs\Design', 'product_tag', 'tag_id', 'product_id');
    }
    public function Tag()
    {
        return $this->belongsTo(Tag::class, 'id');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = self::tagify($value);
    }

    public static function tagify($name)
    {
        return urldecode(trim(mb_strtolower($name)));
    }

    public static function removeChineseSpace($name)
    {
        return preg_replace('/\s+/', '', $name);
    }

}
