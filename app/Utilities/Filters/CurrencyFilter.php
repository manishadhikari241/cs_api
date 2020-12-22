<?php

namespace App\Utilities\Filters;

class CurrencyFilter extends QueryFilter
{
    public function code($code)
    {
        //return $this->builder->Where('name',$name);
        if ($code) {
            return $this->builder->where('code', $code);
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

    public function symbol($symbol)
    {
        if ($symbol) {
            return $this->builder->where('symbol', $symbol);
        }

    }

}
