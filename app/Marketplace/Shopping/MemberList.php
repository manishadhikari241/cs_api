<?php

namespace App\Marketplace\Shopping;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class MemberList extends Model
{
    protected $table = "member_list";

    protected $fillable = [ 'name', 'user_id' ];

    public function products () {
        return $this->belongsToMany('App\Marketplace\Designs\Design', 'member_list_product', 'list_id', 'product_id')->withPivot('usage');
    }

    // public function collection () {
    //     return $this->hasOne('App\Marketplace\Collection\Collection', 'list_id');
    // }

    public function user () {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
