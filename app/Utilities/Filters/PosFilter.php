<?php

namespace App\Utilities\Filters;

class PosFilter extends QueryFilter
{
    public function code($code = null)
    {
        if ($code) {
            return $this->builder->where('code', $code);
        }
    }

    public function user($name)
    {
        if($name){
            return $this->builder->whereHas('user', function ($query) use ($name) {
                return $query->where('email', $name);
            });
        }
    }

    public function value($value)
    {
        if ($value) {
            return $this->builder->where('value', $value);
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

    public function dateFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('created_at', '>=', $date);
        }
    }
    public function dateTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('created_at', '<=', $date);
        }
    }
    public function dateBetween($data)
    {
        return $this->builder->WhereDate('created_at', '>=', $data['0'])->WhereDate('created_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'    => 'user',
            'cashier' => 'cashier',
            'manager' => 'manager',
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
