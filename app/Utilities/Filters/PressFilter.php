<?php

namespace App\Utilities\Filters;

class PressFilter extends QueryFilter
{
    public function title($title = null)
    {
        //return $this->builder->Where('name',$name);
        if (!$title) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($title) {
            return $query->where('title', 'like', '%' . $title . '%');
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
