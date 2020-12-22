<?php

namespace App\Utilities\Filters;

class PriceFilter extends QueryFilter
{
    public function price($price = null)
    {

        if ($price) {

            return $this->builder->where('price', $price);
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
}
