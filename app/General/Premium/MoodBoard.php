<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class MoodBoard extends Model
{
    protected $table = "mood_board";

    protected $fillable = ['name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function trend()
    {
        return $this->belongsTo(Trend::class);
    }
}
