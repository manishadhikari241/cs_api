<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;
use App\Utilities\Filters\QueryFilter;

class ProjectRequestFile extends Model
{

    protected $table = "project_request_file";

    protected $fillable = [ "name" ];

    public function request ()
    {
      return $this->belongsTo(ProjectRequest::class, 'request_id');
    }

}