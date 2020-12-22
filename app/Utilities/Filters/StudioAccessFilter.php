<?php

namespace App\Utilities\Filters;

class StudioAccessFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'user'            => 'user',
            'studio'          => 'studio',
            'premiumRequest'  => 'user.premiumRequest',
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
