<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CorporateDeals extends Model
{
    protected $table = "corporate_deals";

    public function translation () {
        return $this->hasMany(CorporateDealsTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(CorporateDealsTranslation::class, 'id', 'id');
    }
}