<?php

namespace App\General;

use Illuminate\Database\Eloquent\Model;

class PromotionsTranslation extends Model
{
    protected $table = "promotion_translation";

    protected $fillable = ['title', 'lang', 'content'];

    public $timestamps = false;

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'id');
    }

}
