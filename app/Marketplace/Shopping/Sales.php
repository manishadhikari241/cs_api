<?php

namespace App\Marketplace\Shopping;

use App\User;
use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class Sales extends Model
{

    protected $table = "orders_product";

    public function order () {
      return $this->belongsTo('App\Marketplace\Shopping\Order');
    }

    public function product () {
      return $this->belongsTo('App\Marketplace\Designs\Design', 'product_id');
    }

    public function voucher () {
      return $this->belongsTo('App\Marketplace\Shopping\Voucher', 'code', 'code');
    }

    public function owner () {
      return $this->belongsTo('App\User', 'owner_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
