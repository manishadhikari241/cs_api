<?php

namespace App\Utilities\Filters;

use App\Marketplace\Libs\LibMonth;

class LibMonthFilter extends QueryFilter
{
    public function month($month)
    {
        return $this->builder->where('month', $month);
    }

    public function year($year)
    {
        return $this->builder->where('year', $year);
    }

    public function isList()
    {
        $current = LibMonth::current();
        return $this->builder->whereBetween('id', [4, $current->id]);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'designs'       => 'designs.design',
            'season'        => 'season.translations',
            'translations'  => 'translations'
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
