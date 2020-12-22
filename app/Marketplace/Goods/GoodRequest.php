<?php

namespace App\Marketplace\Goods;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodRequest extends Model
{
    use SoftDeletes;
    
    protected $fillable = ['name', 'remarks', 'is_hidden'];
    protected $table = 'good_request';

    public function getUploadPath()
    {
        return 'uploads/good-request/';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function good()
    {
        return $this->belongsTo(Good::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
