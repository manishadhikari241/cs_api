<?php

namespace App\Utilities\Filters;

class CollectionCommentFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'user' => 'user',
            'item' => 'item',
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
