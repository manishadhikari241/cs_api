<?php

namespace App\Utilities\Filters;

class DistributorPaymentFilter extends QueryFilter
{
    public function month($m = 1)
    {
        return $this->builder->where('month', (int)$m);
    }

    public function year($y = 2016)
    {
        return $this->builder->where('year', $y);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'products.designer.profile' => 'products.designer.profile'
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
