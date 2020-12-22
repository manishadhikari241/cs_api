<?php

namespace App\Utilities\Filters;

class AddressFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'nation' => 'nation.translations',
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
