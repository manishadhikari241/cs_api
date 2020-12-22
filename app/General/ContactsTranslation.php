<?php

namespace App\General;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ContactsTranslation extends Model
{
    protected $table = "contact_translation";

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id');
    }
}