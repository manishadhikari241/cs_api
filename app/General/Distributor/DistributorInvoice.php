<?php

namespace App\General\Distributor;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Marketplace\Libs\LibPlanUser;

class DistributorInvoice extends Model
{

    protected $table = "distributor_invoice";

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libPlanUser()
    {
        return $this->belongsTo(LibPlanUser::class);
    }
}
