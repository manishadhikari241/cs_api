<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class PremiumPlansTranslation extends Model
{
    protected $table = "premium_plan_translation";

    protected $fillable = ['name', 'description', 'lang'];

    public function plan()
    {
        return $this->belongsTo(PremiumPlan::class, 'id');
    }

}
