<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class BecomeACreatorTranslation extends Model
{
    protected $table = "become_a_creator_translation";

    public function BecomeACreator () {
        return $this->belongsTo(BecomeACreator::Class, 'id');
    }

}