<?php

namespace App\General\Representative;

use App\Marketplace\Libs\LibPlanUser;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $table    = 'representative';
    protected $fillable = ['is_active', 'representative_group_id', 'record_order', 'record_subscription'];

    // (my self)
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function parent()
    {
        return $this->belongsTo(Representative::class, 'parent_id');
    }

    // (owned)
    public function users()
    {
        return $this->hasMany('App\User');
    }

    public function child()
    {
        return $this->hasMany(Representative::class, 'parent_id');
    }

    // (my self)

    public function group()
    {
        return $this->belongsTo(RepresentativeGroup::class, 'representative_group_id');
    }

    public function representativeGroupLog()
    {
        return $this->hasMany(RepresentativeGroupLog::class, 'representative_id');
    }

    public function representativePayments()
    {
        return $this->hasMany(RepresentativePayment::class, 'representative_id');
    }

    public function representativeSubscriptions()
    {
        return $this->hasMany(RepresentativeSubscription::class, 'representative_id');
    }

    // (owned)
    public function orders()
    {
        return $this->hasMany(RepresentativeOrder::class);
    }

    public function lists()
    {
        return $this->hasMany(UserRepresentativeList::class);
    }

    public static function initiate($user, $data)
    {
        $representative = self::where('user_id', $user->id)->first();
        if (!$representative) {
            $group          = RepresentativeGroup::find($data['representative_group_id']);
            $representative = self::forceCreate([
                'user_id'                 => $user->id,
                'representative_group_id' => $group->id,
                'code'                    => self::findUniqueCode(),
            ]);
            RepresentativeGroupLog::forceCreate([
                'percentage'        => $group->percentage,
                'representative_id' => $representative->id,
            ]);
        }
        return $representative;
    }

    public function assignHandle ($user) {
        $user->representative_id = $this->id;
        if (!$user->source) {
            $user->source = 'via_rep';
            $plan = $user->libPlanUsers()->latest()->first();
            if ($plan && $plan->status !== LibPlanUser::IS_ENDED) {
                $plan->source = 'via_rep';
                $plan->save();
            }
        }
        $user->save();
        return $user;
    }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public static function findUniqueCode()
    {
        $isUnique = false;
        while (!$isUnique) {
            $code = rand(100000, 999999);
            if (!self::where('code', $code)->exists()) {
                return $code;
            }
        }
    }
}
