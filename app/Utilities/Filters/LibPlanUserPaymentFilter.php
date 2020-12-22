<?php

namespace App\Utilities\Filters;

class LibPlanUserPaymentFilter extends QueryFilter
{
    public function user($value='')
    {
        $this->builder->whereHas('user', function ($query) use ($value) {
            return $query->where('email', 'LIKE', "%{$value}%");
        });
    }

    public function transactionId($id = null)
    {
        if (!$id) {
            return;
        }
        return $this->builder->where('transaction_id', 'LIKE', "%{$id}%");
    }

    public function month($m = 1)
    {
        return $this->builder->whereMonth('created_at', '=', (int) $m);
    }

    public function year($y = 2016)
    {
        return $this->builder->whereYear('created_at', '=', $y);
    }

    public function scope($scopes = [])
    {
        $relatable = [
          'user'                => 'user',
          'libPlanUser.libPlan' => 'libPlanUser.libPlan'
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
