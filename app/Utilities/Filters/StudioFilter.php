<?php

namespace App\Utilities\Filters;

use App\General\Premium\ProjectRequest;
use App\Marketplace\Studio\StudioAccess;
use Carbon\Carbon;

class StudioFilter extends QueryFilter
{
    public function accessorId($id)
    {
        $studios = StudioAccess::where('user_id', \Auth::id())->where('is_active', 1)->pluck('studio_id');
        return $this->builder->whereIn('id', $studios);
    }

    // if using a auth token, show each user's access status on each studios
    public function showAccess()
    {
        if (!\Auth::check()) {
            return;
        }
        return $this->builder->with(['accesses' => function ($q) {
            $q->where('user_id', \Auth::id());
        }]);
    }

    // if using a auth token, show each user's latest permit on each studios
    public function showAvailablePermit()
    {
        if (!\Auth::check()) {
            return;
        }
        return $this->builder->with(['permits' => function ($q) {
            $q->where('user_id', \Auth::id())->where('is_consumed', 0);
        }]);
    }

    // if using a auth token, show each user's latest permit on each studios
    public function showAvailablePayment()
    {
        if (!\Auth::check()) {
            return;
        }
        return $this->builder->with(['projectPayments' => function ($q) {
            $q->where('user_id', \Auth::id())->where('status', ProjectRequest::IS_WAITING_APPROVAL)->whereNull('project_request_id');
        }]);
    }

    public function isActive($value)
    {
        return $this->builder->where('is_active', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function isProjectProvider($value)
    {
        return $this->builder->where('is_project_provider', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function hasActiveProjectPackages($value)
    {
        return $this->builder->whereHas('projectPackages', function ($q) {
            $q->where('is_active', 1);
        });
    }

    public function hasOngoingTrends($value)
    {
        return $this->builder->with(['trends' => function ($q) {
            $q->where('is_active', 1)->whereDate('expired_at', '>=', Carbon::now());
        }]);
    }

    public function scope($scopes = [])
    {
        // never add filter all designs in scope
        // 1) dangerous
        // 2) non-practical -> to much result
        $relatable = [
            'user'               => 'user',
            'designer'           => 'designer',
            'translations'       => 'translations',
            'trends'             => 'trends.translations',
            'projects'           => 'projects.translations',
            'projectPackages'    => 'projectPackages',
            'premiumrequests'    => 'premiumrequests',
            'country'            => 'country.translations',
            'market_countries'   => 'marketCountries.translations',
            'user.public_images' => 'user.publicImages',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
}
