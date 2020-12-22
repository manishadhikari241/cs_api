<?php

namespace App\Marketplace\Common;

use Illuminate\Database\Eloquent\Model;

class CountriesTranslation extends Model
{
    protected $table    = "country_translation";
    protected $fillable = ['name', 'lang'];
    public $timestamps  = false;
    public function Country()
    {
        return $this->belongsTo(Country::class, 'id');
    }

}
