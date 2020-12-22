<?php

namespace App\Utilities\Filters;

class StudioPermitFilter extends QueryFilter
{
    public function isConsumed($value)
    {
      return $this->builder->where('is_consumed', $value);
    }

    // take those whose coupon not used / used
    public function hasHistory($value)
    {
      return $this->builder->whereHas('coupon', function ($q) use ($value) {
        $value ? $q->whereHas('histories') : $q->whereDoesntHave('histories');
      });
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'coupon'            => 'coupon',
            'voucher'            => 'voucher',
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