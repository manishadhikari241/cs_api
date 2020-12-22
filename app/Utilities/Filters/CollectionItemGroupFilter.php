<?php

namespace App\Utilities\Filters;

class CollectionItemGroupFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'items'      => 'items',
            'items.good' => 'items.good',
            'items.item' => 'items.item',
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
