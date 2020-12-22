<?php

namespace App\Pages;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Concept extends Model
{
    protected $table = "concept";

    public function translation () {
        return $this->hasMany(ConceptTranslation::class, 'id', 'id');
    }
    public function translations () {
        return $this->hasMany(ConceptTranslation::class, 'id', 'id');
    }
}