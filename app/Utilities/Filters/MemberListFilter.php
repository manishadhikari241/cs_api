<?php

namespace App\Utilities\Filters;

class MemberListFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'user'                      => 'user',
            'designs'                   => 'products',
            'products'                  => 'products',
            'collection'                => 'collection',
            'products.designer'         => 'products.designer',
            'products.designer.profile' => 'products.designer.profile',
        ];
        $relations = [];
        foreach ($scopes as $key => $value) {
            if (isset($relatable[$value])) {
                array_push($relations, $relatable[$value]);
            }
        }
        return $this->builder->with($relations);
    }
    public function createdFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('member_list.created_at', '>=', $date);
        }
    }
    public function createdTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('member_list.created_at', '<=', $date);
        }
    }
    public function createdBetween($data)
    {
        return $this->builder->whereDate('member_list.created_at', '>=', $data['0'])->whereDate('member_list.created_at', '<=', $data['1']);
    }
}
