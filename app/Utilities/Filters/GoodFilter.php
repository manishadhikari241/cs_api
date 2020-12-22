<?php

namespace App\Utilities\Filters;

class GoodFilter extends QueryFilter
{
    public function name($name)
    {
        if (!$name) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', $name);
        });
    }

    public function isPurchasable($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_purchasable', true);
        } else {
            return $this->builder->where('is_purchasable', false);
        }
    }

    public function isActive($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }

    }

    public function scope($scopes = [])
    {
        $relatable = [
            'prices' => 'prices',
            'photos' => 'photos',
            'user'   => 'user',
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
