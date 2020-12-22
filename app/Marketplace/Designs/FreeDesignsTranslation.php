<?php

namespace App\Marketplace\Designs;

use Illuminate\Database\Eloquent\Model;

class FreeDesignsTranslation extends Model
{
    protected $table = "free_design_translation";

    public $timestamps  = false;

    protected $fillable = ['name', 'lang'];

    public function FreeDesign()
    {
        return $this->belongsTo(FreeDesign::class, 'id');
    }

}
