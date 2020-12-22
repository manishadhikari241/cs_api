<?php

namespace App\Utilities\Filters;

class CommissionFilter extends QueryFilter
{
    public function name($name)
    {
        if ($name) {
            return $this->builder->where('name', $name);
        }
    }
    public function percentage($per)
    {
        if ($per) {
            return $this->builder->where('percentage', $per);
        }

    }
}
