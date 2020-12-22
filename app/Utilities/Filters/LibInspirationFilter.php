<?php

namespace App\Utilities\Filters;

class LibInspirationFilter extends QueryFilter
{
    public function seasonId(int $seasonId)
    {
        return $this->builder->whereHas('month', function ($q) use ($seasonId) {
            $q->where('season_id', $seasonId);
        });
    }

    public function libCategoryId(int $catId)
    {
        return $this->builder->where('lib_category_id', $catId);
    }

    public function monthId(int $catId)
    {
        return $this->builder->where('lib_month_id', $catId);
    }

    public function isActive(int $bool)
    {
        return $this->builder->where('is_active', $bool);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'translations'          => 'translations',
            'moodboards'            => 'moodboards',
            'month'                 => 'month.translations',
            'month.season'          => 'month.season.translations',
            'category'              => 'category.translations',
            'embassador'            => 'embassador.translations',
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
