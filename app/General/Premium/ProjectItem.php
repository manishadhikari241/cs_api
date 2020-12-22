<?php

namespace App\General\Premium;

use App\Marketplace\Designs\Design;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ProjectItem extends Model
{
    protected $table    = 'project_item';

    protected $fillable = ['project_id'];

    public function designs()
    {
        return $this->belongsToMany(Design::class, 'project_design')->withPivot('project_id');
    }

    public function lastDesign()
    {
        return $this->belongsTo(Design::class, 'last_design_id');
    }

    public function comments()
    {
        return $this->hasMany(ProjectItemComment::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
