<?php

namespace App\Utilities\Filters;

class GoodShareFilter extends QueryFilter
{

    public function scope($scopes = [])
    {
        $relatable = [
            'goods' => 'goods.translations',
            'user' => 'user',
            'sharee' => 'sharee',
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
