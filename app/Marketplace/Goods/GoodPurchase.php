<?php

namespace App\Marketplace\Goods;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class GoodPurchase extends Model
{
    protected $table = "good_purchase";

    protected $fillable = ['quantity', 'message', 'contact_no', 'name', 'status', 'transaction_id', 'settled_at', 'is_tuned', 'style'];

    protected $casts = [
        'style'    => 'json',
        'is_tuned' => 'boolean',
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function design()
    {
        return $this->belongsTo('App\Marketplace\Designs\Design');
    }

    public function product()
    {
        return $this->belongsTo('App\Marketplace\Goods\Good', 'good_id');
    }

}
