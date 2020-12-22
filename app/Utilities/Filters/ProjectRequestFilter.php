<?php

namespace App\Utilities\Filters;

use App\User;

class ProjectRequestFilter extends QueryFilter
{
    public function name($name)
    {
        return $this->builder->where('name', $name);
    }

    public function user($useremail)
    {
        $userid = User::where('email', 'LIKE', $useremail)->pluck('id');
        return $this->builder->where('user_id', $userid->first());
    }

    public function dateFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('created_at', '>=', $fromdate);
        }
    }

    public function dateTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('created_at', '<=', $enddate);
        }
    }

    public function dateBetween($data)
    {
        return $this->builder->WhereDate('created_at', '>=', $data['0'])->WhereDate('created_at', '<=', $data['1']);
    }

    public function expectedFrom($fromdate)
    {
        if ($fromdate) {
            return $this->builder->whereDate('expected_at', '>=', $fromdate);
        }
    }

    public function expectedTo($enddate)
    {
        if ($enddate) {
            return $this->builder->whereDate('expected_at', '<=', $enddate);
        }
    }

    public function expectedBetween($data)
    {
        return $this->builder->WhereDate('expected_at', '>=', $data['0'])->WhereDate('expected_at', '<=', $data['1']);
    }

    public function userId($id = null)
    {
        return $this->builder->where('user_id', $id);
    }

    public function studioId($id = null)
    {
        return $this->builder->where('studio_id', $id);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'                => 'user',
            'user.plan'           => 'user.plan',
            'user.projects'       => 'user.projects',
            'user.premiumRequest' => 'user.premiumRequest',
            'files'               => 'files',
            'project'             => 'project.translations',
            'project.designs'     => 'project.designs',
            'payment'             => 'payment',
            'projectPackage'      => 'projectPackage',
            'studio'              => 'studio.translations',
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
