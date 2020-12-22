<?php

namespace App\Utilities\Filters;

class PromotionFilter extends QueryFilter
{
    public function title($title = null)
    {
        if (!$title) {
            return null;
        }
        $this->builder->whereHas('translations', function ($query) use ($title) {
            return $query->where('title', 'like', '%' . $title . '%');
        });
    }

    public function code($value)
    {
        if ($value) {
            return $this->builder->where('code', $value);
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

    public function startFrom($date)
    {

        if ($date) {
            return $this->builder->whereDate('started_at', '>=', $date);
        }

    }
    public function startTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('started_at', '<=', $date);
        }

    }
    public function startDateBetween($data)
    {
        return $this->builder->WhereDate('started_at', '>=', $data['0'])->WhereDate('started_at', '<=', $data['1']);
    }
    public function endFrom($date)
    {

        if ($date) {
            return $this->builder->whereDate('expired_at', '>=', $date);
        }

    }
    public function endTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('expired_at', '<=', $date);
        }

    }
    public function endDateBetween($data)
    {
        return $this->builder->WhereDate('expired_at', '>=', $data['0'])->WhereDate('expired_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'translations' => 'translations',
            'coupon'       => 'coupon',
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
