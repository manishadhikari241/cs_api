<?php

namespace App\Marketplace\Admin;

// use Carbon\Carbon;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class CreateGroup extends Model
{

    protected $table = "sf_guard_user_creator_group";

    public $timestamps = false;

    protected $fillable = ['name', 'percentage'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
