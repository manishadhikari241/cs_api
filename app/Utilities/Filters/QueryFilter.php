<?php

namespace App\Utilities\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class QueryFilter
{
    public $request;

    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->filters() as $name => $value) {
            $name = Str::camel($name);
            if (method_exists($this, $name)) {
                // call_user_func_array([$this, $name], array_filter([$value]));
                call_user_func_array([$this, $name], [$value]);
            }
        }

        return $this->builder;
    }

    public function random()
    {
        return $this->builder->inRandomOrder();
    }

    public function latest()
    {
        return $this->builder->latest();
    }

    public function recent()
    {
        return $this->builder->orderBy('updated_at', 'desc');
    }

    public function filters()
    {
        return $this->request->all();
    }

    public function ajax()
    {
        return $this->request->ajax();
    }

    public function all()
    {
        return $this->request->all();
    }

    public function input($string)
    {
        return $this->request->input($string);
    }

    public function has($string)
    {
        return $this->request->has($string);
    }

    public function userId($id = null)
    {
        return $this->builder->where('user_id', $id);
    }

    public function ids($ids = [])
    {
        return $this->builder->whereIn('id', $ids);
    }

    public function sort($qString = '') // "id,-status,+buyer_id"
    {
        $fields = explode(',', $qString);
        foreach ($fields as $field) {
            $sign  = substr($field, 0, 1);
            $order = 'asc';
            if (in_array($sign, ['-', '+'])) {
                $order = $sign === '-' ? 'desc' : 'asc';
                $field = substr($field, 1);
            }
            $this->builder->orderBy($field, $order);
        }
        return $this->builder;
    }

    public function status($status = 0)
    {
        if (!is_array($status)) {
            $status = [$status];
        }
        return $this->builder->whereIn('status', $status);
    }

    public function createdFrom($date)
    {
        return $this->builder->whereDate('created_at', '>', $date);
    }

    public function createdTo($date)
    {
        return $this->builder->whereDate('created_at', '<', $date);
    }

    public function withTrashed()
    {
        return $this->builder->withTrashed();
    }

    public function randomKey(int $key = 1)
    {
        return $this->builder->orderBy(DB::raw("RAND({$key})"));
    }
}
