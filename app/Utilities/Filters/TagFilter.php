<?php

namespace App\Utilities\Filters;

class TagFilter extends QueryFilter
{

    public function idFrom($id)
    {
        return $this->builder->where('id', '>=', $id);
    }

    public function idTo($id)
    {
        return $this->builder->where('id', '<=', $id);
    }

    public function name($name = null)
    {
        //return $this->builder->Where('name',$name);
        if (!$name) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', $name);
        });
    }

    public function isActive($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
    }

    public function isExclusive($value)
    {
        if ($value == "true") {
            return $this->builder->where('is_exclusive', true);
        } else {
            return $this->builder->where('is_exclusive', false);
        }
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'tags'         => 'tags.translations',
            'translations' => 'translations',
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
