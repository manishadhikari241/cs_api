<?php

namespace App\Utilities\Filters;

use Illuminate\Support\Facades\Auth;

class LibRequestFilter extends QueryFilter
{
    public function userId($id = null)
    {
        if (!$id) {
            $id = Auth::guard('api')->id();
        }

        $this->builder->whereHas('group', function ($group) use ($id) {
            $group->where('user_id', $id);
        });
    }

    public function name($name)
    {
        return $this->builder->where('name', 'like', "%{$name}%");
    }

    public function isHidden($bol = 1)
    {
        return $this->builder->where('is_hidden', $bol);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'group'       => 'group',
            'group.user'  => 'group.user',
            'designs'     => 'designs',
            'files'       => 'files',

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
