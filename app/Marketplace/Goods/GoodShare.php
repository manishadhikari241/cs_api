<?php

namespace App\Marketplace\Goods;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodShare extends Model
{

    protected $table = 'good_share';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sharee()
    {
        return $this->belongsTo(User::class, 'sharee_id', 'id');
    }

    public function goods()
    {
        return $this->hasMany(Good::class, 'user_id', 'user_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
