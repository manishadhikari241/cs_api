<?php

namespace App\Utilities\Filters;

class LibMonthUserFilter  extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
          // 'designs' => 'designs.design',
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
