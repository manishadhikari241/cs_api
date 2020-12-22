<?php

namespace App\Utilities\Filters;

use App\Marketplace\Libs\LibPlanUser;

class LibPlanUserFilter extends QueryFilter
{
    public function user($value = '')
    {
        $this->builder->whereHas('user', function ($query) use ($value) {
            return $query->where('email', 'LIKE', "%{$value}%");
        });
    }

    public function subscriptionId($id = null)
    {
        if (!$id) {
            return;
        }
        return $this->builder->where('subscription_id', 'LIKE', "%{$id}%");
    }

    public function plan($plan = null)
    {
        if (!$plan) {
            return;
        }
        if ($plan == 'free') {
            return $this->builder->where(function($query){
                    $query->where('is_granted', false)->whereNull('subscription_id');
                });
        } else if ($plan == 'starter' || $plan == 'pro') {
            return $this->builder->whereHas('libPlan', function ($q) use ($plan) {
                    $q->where('group', $plan);
                })->where(function($q) {
                    $q->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        } else {
            return $this->builder->where('lib_plan_id', $plan)->where(function($q) {
                    $q->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        }
    }

    public function planCycle($cycle = null)
    {
        if (!$cycle) {
            return;
        }
        if ($cycle == 'month') {
            return $this->builder->whereHas('libPlan', function ($q) {
                    $q->where('month_cycle', 1);
                })->where(function($q) {
                    $q->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        } else if ($cycle == 'year') {
            return $this->builder->whereHas('libPlan', function ($q) {
                    $q->where('month_cycle', 12);
                })->where(function($q) {
                    $q->whereNotNull('subscription_id')->orWhere('is_granted', true);
                });
        } else if ($cycle == 'free') {
            return $this->builder->where(function($query){
                    $query->where('is_granted', false)->whereNull('subscription_id');
                });
        }
    }

    public function status($status = 0)
    {
        if ($status == 'is_granted') {
            return $this->builder->where('is_granted', true);
        }
        if (!is_array($status)) {
            $status = [$status];
        }
        return $this->builder->whereIn('status', $status);
    }

    public function startFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('started_at', '>=', $fromdate);
        }
    }

    public function startTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('started_at', '<=', $enddate);
        }
    }

    public function startBetween($data)
    {
        return $this->builder->whereDate('started_at', '>=', $data['0'])->whereDate('started_at', '<=', $data['1']);
    }

    public function endFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('ended_at', '>=', $fromdate);
        }
    }

    public function endTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('ended_at', '<=', $enddate);
        }
    }

    public function endBetween($data)
    {
        return $this->builder->whereDate('ended_at', '>=', $data['0'])->whereDate('ended_at', '<=', $data['1']);
    }

    public function updatedFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('lib_plan_user.updated_at', '>=', $fromdate);
        }
    }

    public function updatedTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('lib_plan_user.updated_at', '<=', $enddate);
        }
    }

    public function reason($reason)
    {
        if ($reason) {
            return $this->builder->whereHas('unsubscribeReason', function ($q) use ($reason) {
                $q->where('reason', $reason);
            });
        }
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'plan'                    => 'libPlan.translations',
            'libPlanChange.plan'      => 'libPlanChange.libPlan.translations',
            'repSubscription'         => 'repSubscription',
            'user'                    => 'user',
            'unsubscribeReason'       => 'unsubscribeReason',
            'user.unsubscribeReasons' => 'user.unsubscribeReasons',
            'payment'                 => 'payment',
            'address'                 => 'address',
            'address.nation'          => 'address.nation',
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
