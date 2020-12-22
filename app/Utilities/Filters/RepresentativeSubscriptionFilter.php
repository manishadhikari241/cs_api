<?php

namespace App\Utilities\Filters;

use App\Marketplace\Libs\LibPlanUser;

class RepresentativeSubscriptionFilter extends QueryFilter
{
    public function subscribed()
    {
        return $this->builder->whereHas('libPlanUser', function ($q) {
            $q->whereIn('status', [LibPlanUser::IS_STARTED, LibPlanUser::IS_ENDING]);
        });
    }

    public function representativeId(int $id)
    {
        return $this->builder->where('representative_id', $id);
    }

    public function activePlans()
    {
        return $this->builder->whereHas('libPlanUser', function ($q) {
            $q->whereIn('status', [LibPlanUser::IS_STARTED, LibPlanUser::IS_ENDING]);
        });
    }

    public function plan($plan)
    {
        if ($plan) {
            return $this->builder->whereHas('libPlanUser', function ($q) use ($plan) {
                $q->where('lib_plan_id', $plan);
            });
        }
    }

    public function source($source)
    {
        if ($source) {
            return $this->builder->whereHas('libPlanUser', function ($q) use ($source) {
                if ($source == "none") {
                    $q->whereNull('source');
                } else {
                    $q->where('source', $source);
                }
            });
        }
    }

    public function type($type)
    {
        if ($type) {
            if ($type == 'new') {
                return $this->builder->where('status', '=', 0);
            }
            if ($type == 'ext') {
                return $this->builder->where('status', '=', 1);
            }
            if ($type == 'upgrade') {
                return $this->builder->where('status', '=', 2);
            }
        }
    }

    public function month(int $month)
    {
        return $this->builder->whereHas('libPlanUser', function ($q) use ($month) {
            $q->whereMonth('started_at', $month);
        });
    }

    public function year(int $year)
    {
        return $this->builder->whereHas('libPlanUser', function ($q) use ($year) {
            $q->whereYear('started_at', $year);
        });
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'representative.user' => 'representative.user',
            'libPlanUser'         => 'libPlanUser',
            'libPlanUser.user'    => 'libPlanUser.user',
            'libPlanUser.libPlan' => 'libPlanUser.libPlan.translations',
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
