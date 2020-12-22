<?php

namespace App\Utilities\Filters;

class UserRepresentativeListFilter extends QueryFilter
{
    public function representativeId($repId)
    {
        return $this->builder->where('representative_id', $repId);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'                => 'user',
            'representative'      => 'representative',
            'representative.user' => 'representative.user',
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
