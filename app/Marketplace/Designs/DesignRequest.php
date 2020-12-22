<?php

namespace App\Marketplace\Designs;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class DesignRequest extends Model
{
    protected $table = 'product_request';

    protected $fillable = ['project_item_id', 'design_name', 'price', 'lang', 'tags', 'colors', 'status', 'message', 'reason', 'licence_type', 'licence_price', 'custom_id', 'lib_category_id', 'lib_month_id', 'project_code', 'has_eps', 'has_pdf', 'has_ai', 'has_jpg', 'has_psd', 'custom_file'];

    const IS_PENDING          = 0;
    const IS_WAITING_APPROVAL = 1;
    const IS_APPROVED         = 2;
    const IS_SOLD             = 3;
    const IS_UNPUBLISH        = 4;
    const IS_REJECTED         = 8;
    const IS_DELETED          = 9;

    const licence_type = [
        0 => 'all',
        1 => 'exclusive',
        2 => 'non-exclusive',
    ];

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function design()
    {
        return $this->hasOne(Design::class, 'request_id');
    }

    public function libMonth()
    {
        return $this->belongsTo('App\Marketplace\Libs\LibMonth');
    }

    public function libCategory()
    {
        return $this->belongsTo('App\Marketplace\Libs\LibCategory');
    }

    public function queueable()
    {
        return array_search($this->status, [self::IS_PENDING, self::IS_APPROVED, self::IS_UNPUBLISH]) !== false;
    }

    public function approvable()
    {
        return array_search($this->status, [self::IS_WAITING_APPROVAL, self::IS_APPROVED, self::IS_UNPUBLISH]) !== false;
    }

    public function rejectable()
    {
        return array_search($this->status, [self::IS_PENDING, self::IS_WAITING_APPROVAL, self::IS_APPROVED, self::IS_UNPUBLISH]) !== false;
    }

    public function destroyable()
    {
        return array_search($this->status, [self::IS_PENDING, self::IS_WAITING_APPROVAL, self::IS_REJECTED, self::IS_UNPUBLISH]) !== false;
    }

    public function getUploadPath($type = '')
    {
        $prefix = app()->environment('production', 'staging') ? '' : 'uploads/designer/design/';
        return "{$prefix}{$this->user_id}/";
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
