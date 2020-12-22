<?php

namespace App\Marketplace\Goods;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    const IS_PENDING   = 1;
    const IS_APPROVED  = 2;
    const IS_COMPLETED = 3;
    const IS_EXPIRED   = 7;
    const IS_REJECTED  = 8;
    const IS_DELETED   = 9;

    protected $table = "inquiry";

    protected $fillable = ['quantity', 'message', 'contact_no', 'name', 'status', 'transaction_id', 'settled_at'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function purchases()
    {
        return $this->hasMany(GoodPurchase::class);
    }
}
