<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CorporateDealsTranslation extends Model
{
    protected $table = "corporate_deals_translation";

    public function corporateDeals () {
        return $this->belongsTo(CorporateDeals::Class, 'id');
    }

}