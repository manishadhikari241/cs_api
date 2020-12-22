<?php

namespace App\Utilities\Filters;

class CouponFilter extends QueryFilter
{
    public function name($name)
    {
        if ($name) {
            return $this->builder->where('name','like','%' .$name. '%');
        }
    }

    public function code($code)
    {
        if ($code) {
            return $this->builder->where('code','like','%' .$code. '%');
        }
    }

    public function discountType($dis)
    {
        if ($dis) {
            return $this->builder->where('discount_type', $dis);
        }
    }
    public function amount($amount)
    {
        if ($amount) {
            return $this->builder->where('amount', $amount);
        }
    }

    public function isActive($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
    }

    public function isUsed($value)
    {
        if ($value == "true") {
            return $this->builder->whereHas('histories', null);
        } else {
            return $this->builder->whereDoesntHave('histories', null);
        }
    }

    public function startFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('start_date', '>=', $fromdate);
        }
    }
    public function startTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('start_date', '<=', $enddate);
        }
    }
    public function startBetween($data)
    {
        return $this->builder->WhereDate('start_date', '>=', $data['0'])->WhereDate('start_date', '<=', $data['1']);
    }

    public function endFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('end_date', '>=', $fromdate);
        }
    }
    public function endTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('end_date', '<=', $enddate);
        }
    }
    public function endBetween($data)
    {
        return $this->builder->WhereDate('end_date', '>=', $data['0'])->WhereDate('end_date', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'histories' => 'histories',
            'isUsed'    => 'isUsed',
            'promotion' => 'promotion',
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
