<?php

namespace App\Utilities\Filters;

class LibPlanCouponFilter extends QueryFilter
{
    public function code($code)
    {
        if ($code) {
            return $this->builder->where('code','like','%' .$code. '%');
        }
    }

    public function startFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('created_at', '>=', $fromdate);
        }
    }
    public function startTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('created_at', '<=', $enddate);
        }
    }
    public function startBetween($data)
    {
        return $this->builder->WhereDate('created_at', '>=', $data['0'])->WhereDate('created_at', '<=', $data['1']);
    }

    public function endFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('expired_at', '>=', $fromdate);
        }
    }
    public function endTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('expired_at', '<=', $enddate);
        }
    }
    public function endBetween($data)
    {
        return $this->builder->WhereDate('expired_at', '>=', $data['0'])->WhereDate('expired_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'representative'      => 'representative',
            'representative.user' => 'representative.user',
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
