<?php

namespace App\General\Distributor;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class DistributorPayment extends Model
{

    protected $table = "distributor_payment";

    protected $fillable = ['transaction_id', 'amount', 'month', 'year', 'distributor_id'];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
