<?php

namespace App\Utilities\Filters;

class LibCollectionFilter extends QueryFilter
{
    public function isActive($value)
    {
        if (in_array($value, [1, 'true'])) {
            return $this->builder->where('is_active', true);
        } else {
            return $this->builder->where('is_active', false);
        }
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

    public function scope($scopes = [])
    {
        $relatable = [
            'embassador' => 'embassador.translations',
            'season'     => 'season.translations',
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
