<?php

namespace App\Utilities\Filters;

class SalesFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'product.designer.profile' => 'product.designer.profile',
            'order'                    => 'order',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }

    public function month($m = 1)
    {
        return $this->builder->whereMonth('created_at', '=', (int) $m);
    }

    public function year($y = 2016)
    {
        return $this->builder->whereYear('created_at', '=', $y);
    }
}
