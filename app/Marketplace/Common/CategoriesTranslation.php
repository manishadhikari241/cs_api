<?php

namespace App\Marketplace\Common;

use Illuminate\Database\Eloquent\Model;

class CategoriesTranslation extends Model
{
    protected $table = "categories_translation";

    public $timestamps = false;

    protected $fillable = ['tags', 'lang'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'id');
    }

}
