<?php

namespace App\Utilities\Filters;

class FAQFilter extends QueryFilter
{
    public function question($question = null)
    {
        //return $this->builder->Where('name',$name);
        if (!$question) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($question) {
            return $query->where('question', 'like', '%' . $question . '%');
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
        if (!$type) {
            return null;
        }
        return $this->builder->Where('type', $type);
    }

}
