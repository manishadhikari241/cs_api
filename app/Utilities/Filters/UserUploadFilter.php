<?php

namespace App\Utilities\Filters;

class UserUploadFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'user'         => 'user',
            'owner'            => 'owner',
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
