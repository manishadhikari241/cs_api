<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibMonthsTranslation extends Model
{
    protected $table = 'lib_month_translation';

    public $timestamps  = false;

    protected $fillable = ['title', 'description', 'lang'];

    public function LibMonth()
    {
        return $this->belongsTo(LibMonth::class, 'id');
    }
}
