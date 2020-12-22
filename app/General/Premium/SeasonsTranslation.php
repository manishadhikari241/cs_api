<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class SeasonsTranslation extends Model
{
    protected $table = "season_translation";

    protected $fillable = ['name', 'description', 'lang'];

    public function season()
    {
        return $this->belongsTo(Trend::class, 'id');
    }

}
