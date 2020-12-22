<?php

namespace App\Marketplace\Common;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class ProductTuning extends Model
{
    protected $table = "product_tuning";
    protected $fillable = ['good_id', 'design_id','is_tuned','style'];
    protected $casts = [
        'style'    => 'json',
        'is_tuned' => 'boolean',
    ];

    public function design()
    {
        return $this->belongsTo('App\Marketplace\Designs\Design');
    }

    public function product()
    {
        return $this->belongsTo('App\Marketplace\Goods\Good', 'good_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
