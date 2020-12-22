<?php

namespace App\Utilities\Filters;

class ProjectItemFilter extends QueryFilter
{

    public function scope($scopes = [])
    {
        $relatable = [
            'designs'        => 'designs',
            'designs.studio' => 'designs.studio.translations',
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
