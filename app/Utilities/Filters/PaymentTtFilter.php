<?php

namespace App\Utilities\Filters;

class PaymentTtFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'libPlan' => 'libPlan',
            'user'    => 'user',
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
