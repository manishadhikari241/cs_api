<?php

namespace App\Marketplace\Studio;

use App\General\Premium\Project;
use App\General\Premium\Trend;
use App\Marketplace\Common\Country;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\General\Premium\ProjectRequest;
use App\General\Premium\PremiumRequest;
use App\Marketplace\Shopping\Voucher;

class StudioPermit extends Model
{
    protected $table = 'studio_permit';

    public function voucher () {
        return $this->belongsTo(Voucher::class);
    }

    public function studio () {
        return $this->belongsTo(Studio::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}