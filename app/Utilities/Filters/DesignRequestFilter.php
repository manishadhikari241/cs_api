<?php

namespace App\Utilities\Filters;

class DesignRequestFilter extends QueryFilter
{
    public function status($codes = [0])
    {
        return $this->builder->whereIn('status', $codes);
    }
    public function code($code)
    {
        if ($code) {
            return $this->builder->where('code', $code);
        }
    }
    public function creatorID($id)
    {
        if ($id) {
            return $this->builder->whereHas('user.profile', function ($query) use ($id) {
                return $query->where('code', $id);
            });
        }
    }
    public function customId($id)
    {
        return $this->builder->where('custom_id', 'LIKE', '%'.$id.'%');
    }

    public function designName($name)
    {
        return $this->builder->where('design_name', 'LIKE', '%' . $name . '%');
    }

    public function dateFrom($date)
    {
        if ($date) {
            return $this->builder->whereDate('created_at', '>=', $date);
        }
    }
    public function dateTo($date)
    {
        if ($date) {
            return $this->builder->whereDate('created_at', '<=', $date);
        }

    }
    public function dateBetween($data)
    {
        return $this->builder->WhereDate('created_at', '>=', $data['0'])->WhereDate('created_at', '<=', $data['1']);
    }

    public function scope($scopes = [])
    {
        $relatable = [
            'user'         => 'user',
            'user.profile' => 'user.profile',
            'design'       => 'design',
            'user.studio'  => 'user.studios.translations',
            'libMonth'     => 'libMonth',
            'libCategory'  => 'libCategory.translations',
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
