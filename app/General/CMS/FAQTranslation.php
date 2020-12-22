<?php

namespace App\General\CMS;

use App\User;
use Illuminate\Database\Eloquent\Model;

class FAQTranslation extends Model
{
    protected $table = "faq_translation";
    protected $fillable = ['question', 'lang', 'answer'];
    public $timestamps =  false;
    public function faq () {
        return $this->belongsTo(FAQ::Class, 'id');
    }

}