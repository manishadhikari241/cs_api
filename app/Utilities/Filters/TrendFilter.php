<?php

namespace App\Utilities\Filters;

class TrendFilter extends QueryFilter
{
    public function name($name = null)
    {
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', $name);
        });
    }

    public function isActive($value)
    {
        if ($value == 'true') {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
    }

    public function studioId($id = null)
    {
        return $this->builder->where('studio_id', $id);
    }

    public function expiredFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('expired_at', '>=', $fromdate);
        }
    }

    public function expiredTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('expired_at', '<=', $enddate);
        }
    }

    public function expiredBetween($data)
    {
        return $this->builder->WhereDate('expired_at', '>=', $data['0'])->WhereDate('expired_at', '<=', $data['1']);
    }

    public function startedFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('started_at', '>=', $fromdate);
        }
    }

    public function startedTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('started_at', '<=', $enddate);
        }
    }

    public function startedBetween($data)
    {
        return $this->builder->WhereDate('started_at', '>=', $data['0'])->WhereDate('started_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'designs'      => 'designs',
            'studio'       => 'studio.translations',
            'translations' => 'translations',
            'moodBoards'   => 'moodBoards',
            'season'       => 'season.translations',
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
