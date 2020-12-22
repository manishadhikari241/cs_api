<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ConceptTranslation extends Model
{
    protected $table = "concept_translation";

    public function concept () {
        return $this->belongsTo(Concept::Class, 'id');
    }

}