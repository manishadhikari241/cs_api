<?php

namespace App\Utilities\Filters;

class DistributorGroupInvoiceFilter extends QueryFilter
{

    public function month(int $month)
    {
        return $this->builder->whereMonth('created_at', $month);
    }

    public function year(int $year)
    {
        return $this->builder->whereYear('created_at', $year);
    }

    public function scope($scopes = [])
    {
        $relatable = [
          'user'                => 'user',
          'distributor.user'    => 'distributor.user',
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
