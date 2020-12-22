<?php

namespace App\Utilities\Filters;

use App\User;

class ProjectFilter extends QueryFilter
{
    public function name($name = null)
    {
        $this->builder->whereHas('translations', function ($query) use ($name) {
            return $query->where('name', $name);
        });
    }

    public function user($useremail)
    {
        $userid = User::where('email', 'LIKE', $useremail)->pluck('id');
        return $this->builder->where('user_id', $userid->first());
    }

    public function studioId($id = null)
    {
        return $this->builder->where('studio_id', $id);
    }

    public function expiredFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('expired_at', '>=', $fromdate);
        }
    }

    public function expiredTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('expired_at', '<=', $enddate);
        }
    }

    public function expiredBetween($data)
    {
        return $this->builder->WhereDate('expired_at', '>=', $data['0'])->WhereDate('expired_at', '<=', $data['1']);
    }

    public function granted()
    {
        return $this->builder->orWhereHas('accesses', function ($query) {
            $query->where('user_id', \Auth::id());
        });
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'translations'                   => 'translations',
            'designs'                        => 'designs',
            'items.comments'                 => 'items.comments',
            'items.designs'                  => 'items.designs',
            'items.designs.designer.profile' => 'items.designs.designer.profile',
            'designs.designer.profile'       => 'designs.designer.profile',
            'request'                        => 'request',
            'projectPackage'                 => 'projectPackage',
            'comments'                       => 'comments.user',
            'review'                         => 'review',
            'user'                           => 'user',
            'moodBoards'                     => 'moodBoards',
            'studio'                         => 'studio.translations',
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
