<?php

namespace App\Utilities\Filters;

use App\User;

class RepresentativeFilter extends QueryFilter
{

    public function email($email = '')
    {
        $this->builder->whereHas('user', function ($query) use ($email) {
            return $query->where('email', 'LIKE', "%{$email}%");
        });
    }
    public function isActive($bool)
    {
        return $this->builder->where('is_active', $bool);
    }
    public function createdFrom($date)
    {
        return $this->builder->whereDate('created_at', '>=', $date);
    }
    public function createdTo($date)
    {
        return $this->builder->whereDate('created_at', '<=', $date);
    }

    public function representativeGroupId($representative_group_id)
    {
        return $this->builder->where('representative_group_id', $representative_group_id);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'users'                 => 'users',
            'group'                 => 'group.rates',
            'group.representatives' => 'group.representatives.user',
            
            'root'                  => 'parent.parent.user',
            'parent'                => 'parent.user',
            'child'                 => 'child.user',
            'child.child'           => 'child.child.user'
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
