<?php

namespace App\Utilities\Filters;

use App\User;

class RepresentativeOrderFilter extends QueryFilter
{
    public function user($useremail)
    {
        $user_id = User::where('email', $useremail)->pluck('id');
        return $this->builder->where('user_id', $user_id->first);
    }

    public function month($m = 1)
    {
        return $this->builder->whereMonth('created_at', '=', (int) $m);
    }

    public function year($y = 2016)
    {
        return $this->builder->whereYear('created_at', '=', $y);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'order'               => 'order',
            'order.user'          => 'order.user',
            'order.products'      => 'order.products',
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
