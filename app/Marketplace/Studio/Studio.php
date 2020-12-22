<?php

namespace App\Marketplace\Studio;

use App\General\Premium\Trend;
use App\General\Premium\Project;
use App\Marketplace\Common\Country;
use App\Marketplace\Designs\Design;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\General\Premium\ProjectRequest;
use App\General\Premium\PremiumRequest;
use App\General\Premium\ProjectPackage;
use App\General\Premium\ProjectPayment;

class Studio extends Model
{
    protected $table = 'studio';

    // this code is visible to only the owner / access user
    protected $hidden = ['invitation_code'];

    protected $fillable = ['logo', 'banner', 'is_active', 'is_project_provider', 'mobile_banner', 'country_id', 'permit_price', 'website', 'project_days', 'project_designs', 'show_new'];

    public function reviews()
    {
        return $this->hasMany(StudioReview::class);
    }

    public function user()
    {
        return $this->belongsTo("App\User");
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function translations()
    {
        return $this->hasMany(StudiosTranslation::class, 'id');
    }

    public function accesses()
    {
        return $this->hasMany(StudioAccess::class);
    }

    public function invitations()
    {
        return $this->hasMany(StudioInvitation::class);
    }

    public function projectPackages()
    {
        return $this->hasMany(ProjectPackage::class);
    }

    public function projectPayments()
    {
        return $this->hasMany(ProjectPayment::class);
    }

    public function permits()
    {
        return $this->hasMany(StudioPermit::class);
    }

    public function requests()
    {
        return $this->hasMany(ProjectRequest::class);
    }

    public function premiumrequests()
    {
        return $this->hasMany(PremiumRequest::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function trends()
    {
        return $this->hasMany(Trend::class);
    }

    public function designer()
    {
        return $this->belongsToMany('App\User', 'studio_user', 'studio_id', 'user_id');
    }

    public function designs()
    {
        // $this->primaryKey ='id';
        // $relation         = $this->belongsToMany('App\Marketplace\Designs\Design', 'studio_user', 'studio_id', 'user_id');
        // $this->primaryKey ='designer_id';
        // return $relation;
        return $this->hasMany(Design::class);
    }

    public function designers()
    {
        return $this->belongsToMany('App\User', 'studio_user', 'studio_id', 'user_id');
    }

    public function marketCountries()
    {
        return $this->belongsToMany('App\Marketplace\Common\Country', 'studio_market_country', 'studio_id', 'country_id');
    }

    public function studio()
    {
        return $this->belongsTo('App\Marketplace\Studio\Studio', 'studio_id');
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function isGrantedTo($user)
    {
        return $this->accesses()->where('user_id', $user->id)->exists();
    }

    public function getUploadPath($type = 'logo')
    {
        $table = [
            'logo'   => 'logo',
            'banner' => 'banner',
        ];
        $type   = isset($table[$type]) ? $table[$type] : $type;
        $prefix = app()->environment('production') ? 'uploads/studio/' : 'uploads/studio/';
        return "{$prefix}{$this->user_id}/${type}/";
    }
}
