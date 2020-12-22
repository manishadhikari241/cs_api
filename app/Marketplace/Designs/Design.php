<?php

namespace App\Marketplace\Designs;

use App\User;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Libs\LibRequest;
use App\Marketplace\Libs\Feed;

class Design extends Model
{
    const IS_PENDING            = 0;
    const IS_WAITING_APPROVAL   = 1;
    const IS_APPROVED           = 2;
    const IS_SOLD               = 3;
    const IS_UNPUBLISH          = 4;
    const IS_PREMIUM_ONLY       = 5;
    const IS_PROJECT            = 6;
    const IS_PROJECT_DOWNLOADED = 7;
    const IS_REJECTED           = 8;
    const IS_DELETED            = 9;
    const IS_DOWNLOAD_ONLY      = 10;
    const IS_TREND              = 11;
    const IS_LIBRARY_ONLY       = 12;

    const licence_type = [
        0 => 'all',
        1 => 'exclusive',
        2 => 'non-exclusive',
    ];
    protected $table = 'product';

    // protected $hidden = ['pseudo_downloads'];

    protected $casts = [
        'is_licensing'      => 'boolean',
        'is_premium'        => 'boolean',
        'is_exclusive_view' => 'boolean',
    ];

    protected $fillable = ['is_exclusive_view', 'is_licensing', 'is_onshow', 'design_name', 'price', 'licence_price', 'has_eps', 'has_pdf', 'has_ai', 'has_jpg', 'has_psd', 'custom_file'];

    public function getDownloadsAttribute($value)
    {
        return $value + $this->pseudo_downloads;
    }

    public function tags()
    {
        return $this->belongsToMany('App\Marketplace\Common\Tag', 'product_tag', 'product_id');
    }

    public function feeds()
    {
        return $this->belongsToMany(Feed::class, 'feed_design', 'design_id');
    }

    public function colors()
    {
        return $this->belongsToMany('App\Marketplace\Designs\Color', 'product_color', 'product_id');
    }

    public function designer()
    {
        return $this->belongsTo('App\User');
    }

    public function request()
    {
        return $this->belongsTo(DesignRequest::class);
    }

    // may not work because category's primary key is tag_id in this table
    // public function categories()
    // {
    //     return $this->belongsTomany('App\Marketplace\Common\Category', 'product_tag', 'product_id', 'tag_id');
    // }

    public function projects()
    {
        return $this->belongsToMany('App\General\Premium\Project', 'project_design', 'design_id', 'project_id');
    }

    public function projectItems()
    {
        return $this->belongsToMany('App\General\Premium\ProjectItem', 'project_design', 'design_id', 'project_item_id');
    }

    public function trends()
    {
        return $this->belongsToMany('App\General\Premium\Trend', 'trend_design', 'design_id', 'trend_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'owner_id');
    }

    public function orders()
    {
        return $this->belongsToMany('App\Marketplace\Shopping\Order', 'orders_product', 'product_id');
    }

    public function keywords()
    {
        return $this->belongsToMany('App\Marketplace\Common\Tag', 'product_tag', 'product_id');
    }

    public function collectionItem()
    {
        return $this->morphMany('App\Marketplace\Collection\CollectionItem', 'item');
    }

    public function free()
    {
        return $this->hasOne('App\Marketplace\Designs\FreeDesign');
    }

    public function freeDesign()
    {
        return $this->hasOne('App\Marketplace\Designs\FreeDesign');
    }

    public function libMonth()
    {
        return $this->hasOne('App\Marketplace\Libs\LibMonthDesign');
    }

    public function libCategory()
    {
        return $this->hasOne('App\Marketplace\Libs\LibMonthDesign');
    }

    public function studio()
    {
        return $this->belongsTo('App\Marketplace\Studio\Studio', 'studio_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function getUploadPath()
    {
        $prefix = app()->environment('production', 'staging') ? '' : 'uploads/designer/design/';
        return $prefix . "CS{$this->code}/";
    }

    /*
     * get the design instance Directly by code
     */
    public static function code($code)
    {
        return self::where('code', $code)->first();
    }

    /*
     * convert design code to id
     */
    public static function codeToId($codes = [])
    {
        $designs = self::whereIn('code', $codes)->get();
        return $designs->pluck('id');
    }

    /*
     * convert design id to code
     */
    public static function idToCode($ids = [])
    {
        $designs = self::whereIn('id', $ids)->get();
        return $designs->pluck('code');
    }

    /*
     * protect exclusive designs from non-admin users
     */
//    public function scopeProtect($query, $user = null)
//    {
//        if (!$user || !$user->is_super_admin) {
//            $query->where('is_exclusive_view', 0);
//        }
//
//        if (!$user || (!$user->is_premium && !$user->is_super_admin)) {
//            $query->whereIn('status', [2, 10, 12]);
//        }
//    }

    public function libRequests()
    {
        return $this->belongsToMany(LibRequest::class, 'lib_request_design');
    }

    public function isExclusive() {
        return $this->licence_type == 'exclusive';
    }

    public function isSold() {
        return $this->buyer_id || $this->owner_id;
    }
}
