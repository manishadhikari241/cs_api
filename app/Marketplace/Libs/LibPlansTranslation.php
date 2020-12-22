<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;

class LibPlansTranslation extends Model
{
    protected $table = 'lib_plan_translation';

    protected $fillable = ['name', 'description', 'lang'];

    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class, 'id');
    }
}
