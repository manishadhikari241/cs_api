<?php

namespace App\Utilities\Filters;

class RepresentativeGroupFilter extends QueryFilter
{
    public function name($name)
    {
        if ($name) {
            return $this->builder->where('name', $name);
        }
    }
    public function percentage($per)
    {
        if ($per) {
            return $this->builder->where('percentage', $per);
        }

    }
    public function scope($scopes = [])
    {
        $relatable = [
            'rates'                    => 'rates',
            'logs'                     => 'logs',
            'representatives.user'     => 'representatives.user'
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
