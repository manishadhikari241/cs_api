<?php

namespace App\Utilities\Filters;

class UnsubscribeReasonFilter extends QueryFilter
{
    public function scope($scopes = [])
    {
        $relatable = [
            'user'              => 'user',
            'user.libPlanUsers' => 'user.libPlanUsers.libPlan',
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
