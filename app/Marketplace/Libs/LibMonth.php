<?php

namespace App\Marketplace\Libs;

use Carbon\Carbon;
use App\General\Premium\Season;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class LibMonth extends Model
{
    protected $table    = 'lib_month';
    protected $fillable = ['season_id', 'year', 'month'];

    // public static function current()
    // {
    //     return self::where('month', Carbon::now()->month)->where('year', Carbon::now()->year)->with('season.translations')->first();
    // }

    // @edit. No more by month. Now default the month is 2018/12 until the feeds ready
    public static function current()
    {
        // return self::where('month', 12)->where('year', 2018)->with('season.translations')->first();
        return LibInspiration::where('is_active', 1)->orderBy('lib_month_id', 'desc')->first()->month;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(LibMonthsTranslation::class, 'id', 'id');
    }

    public function designs()
    {
        return $this->hasMany(LibMonthDesign::class);
    }

    public function inspirations()
    {
        return $this->hasMany(LibInspiration::class);
    }

    public function users()
    {
        return $this->hasMany(LibMonthUser::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
