<?php

namespace App\General\Representative;

use Illuminate\Database\Eloquent\Model;

class RepresentativeGroupLog extends Model
{

    protected $table    = "representative_group_log";
    protected $fillable = ['percentage', 'representative_id'];

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function group()
    {
        return $this->belongsTo(RepresentativeGroup::class);
    }

}
