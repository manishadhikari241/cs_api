<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Studio\Studio;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trend extends Model
{
    use SoftDeletes;

    protected $table = 'trend';

    protected $fillable = ['expired_at', 'is_active', 'season_id'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(TrendsTranslation::class, 'id', 'id');
    }

    public function designs()
    {
        return $this->belongsToMany('App\Marketplace\Designs\Design', 'trend_design', 'trend_id', 'design_id');
    }

    public function getUploadPath()
    {
        return 'uploads/trend/';
    }

    public function moodBoards()
    {
        return $this->hasMany(MoodBoard::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
