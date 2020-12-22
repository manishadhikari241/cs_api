<?php

namespace App\Utilities\Filters;

class EmbassadorFilter extends QueryFilter
{
    public function isActive($value)
    {
        return $this->builder->where('is_active', $value);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'inspirations'               => 'inspirations.translations',
            'inspirations.category'      => 'inspirations.category.translations',
            'inspirations.moodboards'    => 'inspirations.moodboards',
            'inspirations.month.season'  => 'inspirations.month.season.translations',
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
