<?php

namespace App\General;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\Marketplace\Shopping\Coupon;

class Promotion extends Model
{
    protected $table = "promotion";

    protected $fillable = ['is_active', 'sort_order', 'code', 'started_at', 'expired_at'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(PromotionsTranslation::class, 'id', 'id');
    }

    public function coupon() {
        return $this->belongsTo(Coupon::class, 'code', 'code');
    }

}
