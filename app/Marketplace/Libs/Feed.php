<?php

namespace App\Marketplace\Libs;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Designs\Design;
use Carbon\Carbon;
use App\General\Premium\Season;

class Feed extends Model
{
    protected $table    = 'feed';
    protected $fillable = ['is_active', 'designer_id', 'published_at', 'season_id'];

    public function setIsActiveAttribute($value)
    {
        if ($value && !$this->is_active) {
            $this->published_at = Carbon::now();
        }
        $this->attributes['is_active'] = $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(FeedsTranslation::class, 'id', 'id');
    }

    public function moodboards()
    {
        return $this->hasMany(FeedsMoodboard::class);
    }

    public function applyons()
    {
        return $this->belongsToMany(Applyon::class, 'feed_applyon', 'feed_id', 'applyon_id');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Marketplace\Libs\LibCategory', 'feed_category', 'feed_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Marketplace\Common\Tag', 'feed_tag', 'feed_id', 'tag_id');
    }

    public function goods()
    {
        return $this->belongsToMany('App\Marketplace\Goods\Good', 'feed_good', 'feed_id', 'good_id');
    }

    public function simulations()
    {
        return $this->hasMany(FeedsSimulation::class);
    }

    public function designs()
    {
        $designs = $this->belongsToMany(Design::class, 'feed_design')->withTimestamps();
        return $designs->where('licence_type', '!=', 'exclusive');
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    // public function designs()
    // {
    //     return $this->hasMany(FeedDesign::class, 'lib_month_id', 'lib_month_id');
    // }
}
