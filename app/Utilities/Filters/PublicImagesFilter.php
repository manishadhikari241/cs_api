<?php

namespace App\Utilities\Filters;

class PublicImagesFilter extends QueryFilter
{

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
