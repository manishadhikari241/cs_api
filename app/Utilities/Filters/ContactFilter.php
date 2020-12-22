<?php

namespace App\Utilities\Filters;

class ContactFilter extends QueryFilter
{

    public function scope($scopes = [])
    {
        $relatable = [
            'translation' => 'feedTranslations',
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