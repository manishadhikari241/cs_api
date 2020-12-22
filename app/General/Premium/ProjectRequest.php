<?php

namespace App\General\Premium;

use App\Marketplace\Studio\Studio;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectRequest extends Model
{
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

    use SoftDeletes;

    protected $table = 'project_request';

    protected $fillable = [
        'name', 'message', 'expected_at', 'status', 'message', 'reason'
    ];

    public function getUploadPath()
    {
        return "uploads/user/project/{$this->user_id}/";
    }

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

    public function projectPackage()
    {
        return $this->belongsTo(ProjectPackage::class);
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'request_id');
    }

    public function payment()
    {
        return $this->hasOne(ProjectPayment::class);
    }

    public function files()
    {
        return $this->hasMany(ProjectRequestFile::class, 'request_id');
    }

    public function startable()
    {
        return array_search($this->status, [self::IS_WAITING_APPROVAL]) !== false;
    }

    public function completable()
    {
        return array_search($this->status, [self::IS_STARTED, self::IS_READY]) !== false;
    }

    public function removable()
    {
        return array_search($this->status, [self::IS_REJECTED, self::IS_COMPLETED]) !== false;
    }
}
