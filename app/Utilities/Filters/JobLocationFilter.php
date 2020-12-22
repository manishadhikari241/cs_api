<?php

namespace App\Utilities\Filters;

class JobLocationFilter extends QueryFilter
{
    public function name($name = null)
    {
        //return $this->builder->Where('name',$name);
        if (!$name) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', 'like', '%' . $name . '%');
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

}
