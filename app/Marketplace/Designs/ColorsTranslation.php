<?php

namespace App\Marketplace\Designs;

use Illuminate\Database\Eloquent\Model;

class ColorsTranslation extends Model
{
    protected $table = "color_translation";

    protected $fillable = ['name', 'lang'];

    public $timestamps = false;

    public function Color()
    {
        return $this->belongsTo(Color::class, 'id');
    }

}
