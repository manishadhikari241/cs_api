<?php

namespace App\Marketplace\Goods;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    protected $table = 'good';

    const REGION = [
        1 => 'China',
        2 => 'India',
    ];

    protected $fillable = ['is_active', 'is_purchasable', 'image', 'region'];

    protected $casts = [
        'is_active'      => 'boolean',
        'is_purchasable' => 'boolean',
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function translations()
    {
        return $this->hasMany(GoodsTranslation::class, 'id', 'id');
    }

    public function getUploadPath()
    {
        return 'uploads/good/';
    }

    public function photos()
    {
        return $this->hasMany(GoodPhoto::class);
    }

    public function prices()
    {
        return $this->hasMany(GoodPrice::class);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
