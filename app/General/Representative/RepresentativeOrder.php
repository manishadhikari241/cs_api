<?php

namespace App\General\Representative;

use App\Marketplace\Shopping\Order;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class RepresentativeOrder extends Model
{
    protected $table = "representative_order";

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
