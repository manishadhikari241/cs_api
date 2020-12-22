<?php

namespace App\Marketplace\Libs;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class LibRequestFile extends Model
{

    protected $table = "lib_request_file";

    protected $fillable = ["name"];

    public function request()
    {
        return $this->belongsTo(LibRequest::class, 'lib_request_id');
    }

}