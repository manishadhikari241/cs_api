<?php

namespace App\Utilities\Filters;

class FreeDesignFilter extends QueryFilter
{

    public function code($code)
    {
        if ($code) {
            return $this->builder->where('code', $code);
        }
    }

    public function startDateFrom($date)
    {

        if ($date) {
            return $this->builder->whereDate('started_at', '>=', $date);
        }

    }
    public function startDateTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('started_at', '<=', $date);
        }

    }
    public function startDateBetween($data)
    {
        return $this->builder->WhereDate('started_at', '>=', $data['0'])->WhereDate('started_at', '<=', $data['1']);
    }
     public function endDateFrom($date)
    {

        if ($date) {
            return $this->builder->whereDate('expired_at', '>=', $date);
        }

    }
    public function endDateTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('expired_at', '<=', $date);
        }

    }
    public function endDateBetween($data)
    {
        if ($data) {
            return $this->builder->WhereDate('expired_at', '>=', $data['0'])->WhereDate('expired_at', '<=', $data['1']);
        }
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'design'  => 'design',
            'design.tags'  => 'design.tags.translations',
            'translations' => 'translations',
            'users'   => 'users',
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
