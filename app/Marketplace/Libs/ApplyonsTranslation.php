<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class ApplyonsTranslation extends Model
{
    protected $table = 'applyon_translation';

    public $timestamps = false;

    protected $fillable = ['name', 'lang'];

    public function applyon()
    {
        return $this->belongsTo(Applyon::class, 'id');
    }
}
