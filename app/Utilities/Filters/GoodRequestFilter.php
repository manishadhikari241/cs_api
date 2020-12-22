<?php

namespace App\Utilities\Filters;

class GoodRequestFilter extends QueryFilter
{
    public function approved($bol = 1)
    {
        return $bol ? $this->builder->whereNotNull('approved_at') : $this->builder->whereNull('approved_at');
    }

    public function isHidden($bol = 1)
    {
        return $this->builder->where('is_hidden', $bol);
    }

    public function withTrashed()
    {
        return $this->builder->withTrashed();
    }

    public function email($email)
    {
        return $this->builder->whereHas('user', function ($u) use ($email) {
            $u->where('email', 'like', "%{$email}%");
        });
    }
}
