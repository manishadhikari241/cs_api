<?php

namespace App\Utilities\Filters;

class PremiumPlanHistoryFilter extends QueryFilter
{
    public function user($useremail)
    {
        $userid = User::where('email', $useremail)->pluck('id');
        return $this->builder->where('user_id', $userid[0]);
    }

    public function plan($value)
    {
        if ($value) {
            return $this->builder->where('premium_plan_id', $value);
        }
    }

    public function createdFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('created_at', '>=', $fromdate);
        }
    }
    public function createdTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('created_at', '<=', $enddate);
        }
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'plan.translations' => 'plan.translations',
            'plan'              => 'plan.translations',
            'user'              => 'user'
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
