<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibCategoryTranslation extends Model
{
    protected $table = 'lib_category_translation';

    public $timestamps  = false;

    protected $fillable = ['name', 'lang'];

    public function LibCategory()
    {
        return $this->belongsTo(LibCategory::class, 'id');
    }
}
