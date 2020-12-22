<?php

namespace App\General\Representative;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class RepresentativePayment extends Model
{

    protected $table = "representative_payment";

    protected $fillable = [ 'transaction_id', 'amount', 'month', 'year', 'representative_id' ];

    public function representative () {
        return $this->belongsTo(Representative::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

}
