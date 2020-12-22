<?php

namespace App\Marketplace\Common;

use App\Currency;
use Illuminate\Database\Eloquent\Model;

class CurrencyTranslation extends Model
{
    protected $table = "currency_translation";

    public $timestamps = false;

    protected $fillable = ['name', 'lang'];

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'id');
    }

}
