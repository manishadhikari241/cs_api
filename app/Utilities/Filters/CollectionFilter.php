<?php

namespace App\Utilities\Filters;

class CollectionFilter extends QueryFilter
{
    public function granted()
    {
        return $this->builder->orWhereHas('accesses', function ($query) {
            $query->where('user_id', \Auth::id());
        });
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'       => 'user',
            'items'      => 'items',
            // 'colors'     => 'colors',
            'items.item' => 'items.item',
            'items.good' => 'items.good',
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
