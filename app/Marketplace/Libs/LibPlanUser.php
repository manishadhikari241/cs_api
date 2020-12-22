<?php

namespace App\Marketplace\Libs;

use App\General\Address;
use App\General\Representative\RepresentativeSubscription;
use Illuminate\Support\Facades\Auth;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LibPlanUser extends Model
{
    protected $table    = 'lib_plan_user';

    const IS_STARTED               = 2;
    const IS_TRIAL                 = 4;
    const IS_GRACE_PERIOD          = 8;
    const IS_ENDED                 = 9;
    const IS_ENDING                = 10; // user clicked unsubscrbe

    protected $fillable = ['address_id', 'trial_ends_at'];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function libPlan()
    {
        return $this->belongsTo(LibPlan::class);
    }

    public function libPlanUser()
    {
        return $this->belongsTo(LibPlanUser::class);
    }

    public function libPlanChange()
    {
        return $this->hasOne(LibPlanChange::class);
    }

    public function repSubscription()
    {
        return $this->hasOne(RepresentativeSubscription::class);
    }

    public function trialPlanUpgrade()
    {
        return $this->hasOne(TrialPlanUpgrade::class);
    }

    public function unsubscribeReason()
    {
        return $this->hasOne(UnsubscribeReason::class)->latest();
    }

    public function payment()
    {
        return $this->hasOne(LibPlanUserPayment::class);
    }

    public function payments()
    {
        return $this->hasMany(LibPlanUserPayment::class);
    }

    // get current users latest lib plan
    public static function currentPlan()
    {
        return self::latest()->where('user_id', Auth::id())->first();
    }

    public function isOldMonthlyPlan () {
        return $this->created_at->lessThan(Carbon::parse('2018-10-19')) && in_array($this->lib_plan_id, [1,3]);
    }

    public function getRemainingDays () {
        $now = Carbon::now();
        // return $this->payment_method === 'credit_card' ? $now->diffInDays(Carbon::parse($this->next_billing_at)) : $now->diffInDays(Carbon::parse($this->payment_required_until));
        // return $now->diffInDays(Carbon::parse($this->next_billing_at));
        $days = $now->diffInDays(Carbon::parse($this->next_billing_at ?: $this->payment_required_until));
        
        if (in_array($days, [364, 366])) { // if there is only oneday diff. Calculate it as full year
            $days = 365;
        }
        return $days;
    }
}
