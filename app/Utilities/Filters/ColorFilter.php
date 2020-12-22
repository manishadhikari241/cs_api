<?php

namespace App\Utilities\Filters;

class ColorFilter extends QueryFilter
{
    public function name($name = null)
    {

        if ($name) {
            $this->builder->whereHas('translations', function ($query) use ($name) {
                return $query->where('name', $name);
            });
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
    public function sortOrder($value = null)
    {

        if ($value) {
            return $this->builder->where('sort_order', $value);

        }

    }

}
