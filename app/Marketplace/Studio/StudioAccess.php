<?php

namespace App\Marketplace\Studio;

use App\User;
use App\Utilities\Emails\Email;
use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;

class StudioAccess extends Model
{
    protected $table    = 'studio_access';
    protected $fillable = ['is_active'];
    protected $cast     = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }

    public static function request($user, $studio, $mail = true)
    {
        if (!$user) {
            throw new \Exception('USER_NOT_FOUND', 1);
        }
        $requestedAccess = $studio->accesses()->where(['user_id' => $user->id])->first();
        if ($requestedAccess) {
            return $requestedAccess;
        }
        $access = StudioAccess::forceCreate([
            'user_id'       => $user->id,
            'studio_id'     => $studio->id,
            'is_active'     => false,
        ]);

        if ($mail) {
            $request              = \Auth::user()->premiumRequest;
            $request              = $request ? $request->toArray() : [];
            $request['studio_id'] = $studio->id;
            (new Email('premium-applied'))->send($user, $request)->message;
        }

        return $access;
    }

    public static function grant($access)
    {
        $access->is_active = true;
        $access->save();
        User::where(['id' => $access->user_id])->update(['is_premium' =>true]);
        return $access;
    }

    // public static function sendRequestedEmail($access)
    // {
    //     $request              = $access->user->premiumRequest;
    //     $request              = $request ? $request->toArray() : [];
    //     $request['studio_id'] = $access->studio_id;
    //     (new Email('premium-applied'))->send($user, $request)->message;

    //     return $access;
    // }

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
