<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CompanyTranslation extends Model
{
    protected $table = "company_translation";

    public function company () {
        return $this->belongsTo(Company::Class, 'id');
    }

}