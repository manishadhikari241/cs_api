<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;

class ProjectsTranslation extends Model
{
    protected $table = "project_translation";

    protected $fillable = [ 'name', 'lang' ];
    
    public function project () {
        return $this->belongsTo(Project::Class, 'id');
    }

}