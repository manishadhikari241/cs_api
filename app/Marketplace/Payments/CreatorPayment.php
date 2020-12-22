<?php

namespace App\Marketplace\Payments;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class CreatorPayment extends Model
{

    protected $table = "creator_payment";

    protected $fillable = [ 'transaction_id', 'amount', 'month', 'year', 'user_id' ];

    public function user () {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
