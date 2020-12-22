<?php

namespace App\Utilities\Filters;

class ProjectPaymentFilter extends QueryFilter
{
    public function isOutstanding()
    {
        return $this->builder->whereNull('project_request_id');
    }

    public function studioId($id)
    {
        return $this->builder->where('studio_id', $id);
    }

    public function scope($scopes = [])
    {
        $relatable = [
          'studio'  => 'studio.translations',
          'address' => 'address.nation.translations',
          'request' => 'request',
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
