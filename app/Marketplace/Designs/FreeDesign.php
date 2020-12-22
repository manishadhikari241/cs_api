<?php

namespace App\Marketplace\Designs;

use App\Utilities\Filters\QueryFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FreeDesign extends Model
{
    protected $table = 'free_design';

    protected $fillable = ['started_at', 'expired_at', 'design_id', 'code', 'color', 'popup_start', 'popup_end'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'sf_guard_user_free_design', 'free_design_id', 'user_id', 'licence_type')->withTimestamps();
    }

    public static function code($code)
    {
        return self::where('code', $code)->first();
    }

    public function translations()
    {
        return $this->hasMany('App\Marketplace\Designs\FreeDesignsTranslation', 'id', 'id');
    }

    public function valid()
    {
        // @depreciated for lib, lib free user can always download them
        // return Carbon::now()->between(Carbon::parse($this->started_at), Carbon::parse($this->expired_at));
        return true;
    }

    public function setStartedAtAttribute($date)
    {
        $freeDesignLatest = Self::latest()->first();
        // \Log::info(Carbon::parse($date));
        // \Log::info(Carbon::parse($freeDesignLatest->expired_at));
        // \Log::info(Carbon::parse($date)->toDateString() < Carbon::parse($freeDesignLatest->expired_at)->toDateString());
        if ($freeDesignLatest && !$this->started_at && Carbon::parse($date)->toDateString() < Carbon::parse($freeDesignLatest->expired_at)->toDateString()) {
            abort(422, 'INVALID_STARTED_AT');
        } else {
            $this->attributes['started_at'] = Carbon::parse($date)->toDateString();
        }
    }
}
