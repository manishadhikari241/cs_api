<?php

namespace App\Utilities\Filters;

class PremiumPlanFilter extends QueryFilter
{

    public function isActive($value)
    {
        if ($value) {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }

    }

}
