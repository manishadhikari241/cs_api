<?php

namespace App\General\Distributor;

use Illuminate\Database\Eloquent\Model;

class DistributorGroupLog extends Model
{

    protected $table = "distributor_group_log";
    protected $fillable = ['percentage', 'distributor_id'];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function group()
    {
        return $this->belongsTo(DistributorGroup::class);
    }

}
