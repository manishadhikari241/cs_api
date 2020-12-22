<?php

namespace App\Utilities\Filters;

class LibMonthDesignFilter extends QueryFilter
{
    public function pro($bool)
    {
        $this->builder->where('pro', $bool);
    }

    public function basic($bool)
    {
        $this->builder->where('basic', $bool);
    }

    public function scope($scopes)
    {
        $relatable = [
            'design'     => 'design',
            'category'   => 'category.translations',
            'libMonth'   => 'libMonth',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
}
