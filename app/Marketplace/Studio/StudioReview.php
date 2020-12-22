<?php

namespace App\Marketplace\Studio;

use Illuminate\Database\Eloquent\Model;

class StudioReview extends Model
{
    protected $table    = 'studio_review';
    protected $fillable = ['body', 'studio_id', 'user_id', 'marks'];

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }
}
