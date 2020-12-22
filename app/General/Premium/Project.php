<?php

namespace App\General\Premium;

use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Studio\StudioReview;
use App\Utilities\Filters\QueryFilter;
use App\Marketplace\Studio\Studio;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    const IS_WAITING_APPROVAL   = 1;
    const IS_DEPOSIT            = 1;

    // adding designs to first draft
    // user should see expected date of ready
    const IS_STARTED            = 2;
    const IS_IN_PROGRESS        = 2;
    const IS_ACCEPTED_PAYMENT   = 2;

    // no more uploads / revision
    const IS_COMPLETED          = 3;

    // project starts showing to customer, in revision, no more new uploads
    const IS_READY              = 6;

    const IS_EXPIRED            = 7;
    const IS_REJECTED           = 8;
    const IS_REFUNDED           = 8;
    const IS_DELETED            = 9;

    protected $table = 'project';

    protected $fillable = ['expired_at', 'status', 'code'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public function request()
    {
        return $this->belongsTo(ProjectRequest::class, 'request_id');
    }

    public function projectPackage()
    {
        return $this->belongsTo(ProjectPackage::class);
    }

    public function translations()
    {
        return $this->hasMany(ProjectsTranslation::class, 'id');
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class, 'project_id');
    }

    public function reviews()
    {
        return $this->hasMany(StudioReview::class, 'project_id');
    }

    public function review()
    {
        return $this->hasOne(StudioReview::class, 'project_id');
    }

    public function accesses()
    {
        return $this->hasMany(ProjectAccess::class);
    }

    public function designs()
    {
        return $this->belongsToMany('App\Marketplace\Designs\Design', 'project_design', 'project_id', 'design_id')->withPivot('project_item_id');
    }

    public function items()
    {
        return $this->hasMany(ProjectItem::class);
    }

    public function getUploadPath()
    {
        return "uploads/user/project/{$this->user_id}/";
    }

    public function moodBoards()
    {
        return $this->hasMany(MoodBoard::class);
    }
}
