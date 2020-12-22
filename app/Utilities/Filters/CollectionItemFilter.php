<?php

namespace App\Utilities\Filters;

class CollectionItemFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'item'               => 'item',
            'group'              => 'group',
            'commentsCount'      => 'commentsCount',
            'good'               => 'good',
            'confirmations'      => 'confirmations',
            'confirmations.user' => 'confirmations.user',
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
