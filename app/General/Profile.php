<?php

namespace App\General;

use App\Marketplace\Common\Country;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    const IS_CUSTOMER         = 0;
    const IS_PENDING          = 1;
    const IS_APPROVED         = 2;
    const IS_APPROVED_NOTICED = 3;
    const IS_REJECTED         = 9;
    const IS_SUSPENDED        = 8;

    protected $table = 'sf_guard_user_profile';

    protected $fillable = ['passport_type', 'paypal', 'portfolio_website', 'subscribe', 'subscribe_premium', 'name', 'description', 'country_id'];

    protected $hidden = ['passport_type', 'paypal', 'portfolio_website'];

    protected $casts = [
        'subscribe'         => 'boolean',
        'subscribe_premium' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function creatorGroup()
    {
        return $this->belongsTo(CreatorGroup::class);
    }

    public function concentHistories()
    {
        return $this->hasMany(ConcentHistory::class, 'user_id', 'user_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getUploadPath($type = 'portfolio')
    {
        $table = [
            'passport_image' => 'passport',
            'portfolio'      => 'portfolio',
        ];
        $type = isset($table[$type]) ? $table[$type] : $type;
        return "uploads/designer/${type}/";
    }
}
