<?php

namespace App\Marketplace\Libs;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class DemoRequest extends Model
{
    protected $table    = 'demo_request';
    protected $fillable = ['first_name', 'last_name', 'mobile', 'country_id', 'wechat', 'email', 'skype_id'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
