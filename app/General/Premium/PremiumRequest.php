<?php

namespace App\General\Premium;

use App\Marketplace\Studio\Studio;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class PremiumRequest extends Model
{
    protected $table = 'premium_request';

    protected $fillable = [
        'name', 'company_name', 'design_per_year', 'business', 'specify', 'website', 'employees', 'internal_designers', 'motivate_short', 'studio_id', 'country_id', 'user_id'
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo(App\User::class);
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
