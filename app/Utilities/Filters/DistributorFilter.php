<?php

namespace App\Utilities\Filters;

use App\User;

class DistributorFilter extends QueryFilter
{
    public function email($email = '')
    {
        $this->builder->whereHas('user', function ($query) use ($email) {
            return $query->where('email', 'LIKE', "%{$email}%");
        });
    }

    // public function isActive($bool)
    // {
    //     return $this->builder->where('is_active', $bool);
    // }

    public function createdFrom($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }

    public function createdTo($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function distributorGroupId($distributor_group_id)
    {
        return $this->builder->where('distributor_group_id', $distributor_group_id);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user' => 'user',
            'users' => 'users',
            'group' => 'group',
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
