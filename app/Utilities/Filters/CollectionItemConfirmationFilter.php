<?php

namespace App\Utilities\Filters;

class CollectionItemConfirmationFilter extends QueryFilter
{

    public function collectionId($id)
    {
        if($id){
            return $this->builder->whereHas('collectionItem', function ($query) use ($id) {
                return $query->where('collection_id', $id);
            });
        }
    }
    public function scope($scopes = [])
    {
        $relatable = [
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
