<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class TrendsTranslation extends Model
{
    protected $table = "trend_translation";

    protected $fillable = ['name', 'lang', 'description'];

    public function trend()
    {
        return $this->belongsTo(Trend::class, 'id');
    }

}
