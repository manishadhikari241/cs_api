<?php

namespace App\General\Premium;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ProjectItemComment extends Model
{
    protected $table = 'project_item_comment';

    protected $fillable = ['body'];

    public function item()
    {
        return $this->belongsTo(ProjectItem::class, 'project_item_id');
    }

    // public function project()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
