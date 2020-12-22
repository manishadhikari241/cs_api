<?php

namespace App\Utilities\Filters;

class CountryFilter extends QueryFilter
{
    public function name($name = null)
    {
        //return $this->builder->Where('name',$name);
        if (!$name) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', $name);
        });
    }

}
