<?php

namespace App\Marketplace\Goods;

use Illuminate\Database\Eloquent\Model;

class GoodsTranslation extends Model
{
    protected $table = "good_translation";

    protected $fillable = ['name', 'lang', 'description'];

    public $timestamps = false;

    public function Good()
    {
        return $this->belongsTo(Good::class, 'id');
    }

}
