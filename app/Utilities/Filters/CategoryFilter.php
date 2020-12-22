<?php

namespace App\Utilities\Filters;

class CategoryFilter extends QueryFilter
{
    public function name($name = null)
    {
        if (!$name) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('tags', $name);
        });
    }
    public function isActive($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
    }

    public function type($type)
    {
        return $this->builder->where('type', $type);
    }
}
