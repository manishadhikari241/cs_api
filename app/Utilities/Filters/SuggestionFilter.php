<?php

namespace App\Utilities\Filters;

class SuggestionFilter extends QueryFilter
{
    public function name($name)
    {
        return $this->builder->where('name', $name);
    }

    public function email($email)
    {
        return $this->builder->where('email', $email);
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
}
