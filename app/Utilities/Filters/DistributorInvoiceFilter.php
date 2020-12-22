<?php

namespace App\Utilities\Filters;

class DistributorInvoiceFilter extends QueryFilter
{
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
