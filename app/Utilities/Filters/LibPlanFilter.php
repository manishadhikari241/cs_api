<?php

namespace App\Utilities\Filters;

class LibPlanFilter extends QueryFilter
{
    public function isActive($bool)
    {
        return $this->builder->where('is_active', $bool);
    }
}
