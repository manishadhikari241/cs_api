<?php

namespace App;

use App\Marketplace\Goods\GoodRequest;
use App\Marketplace\Libs\LibRequest;
use App\Marketplace\Libs\LibUserDownload;
use App\Notifications\CollectionRequestApproved;
use App\Notifications\CollectionRequestRejected;
use App\Notifications\ExclusiveRequestApproved;
use App\Notifications\ExclusiveRequestReceived;
use App\Notifications\ExclusiveRequestRejected;
use App\Notifications\ResetPassword;
use App\Notifications\SimulatorRequestApproved;
use App\Notifications\SimulatorRequestReceived;
use App\Notifications\SimulatorRequestRejected;
use App\Notifications\VerifyEmail;
use App\Notifications\Welcome;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'mobile',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'salt', 'algorithm', 'remember_token', 'settings'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function isAdmin() {
        return $this->role_id == 1;
    }

    public function isCreator() {
        return $this->role_id == 2;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getUploadPath() {
        return 'uploads/user/';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function sendWelcomeNotification()
    {
        $this->notify(new Welcome);
    }




    // Simulator
    public function sendSimulatorRequestReceivedNotification()
    {
        $this->notify(new SimulatorRequestReceived);
    }

    public function sendSimulatorRequestApprovedNotification()
    {
        $this->notify(new SimulatorRequestApproved);
    }

    public function sendSimulatorRequestRejectedNotification(GoodRequest $request)
    {
        $this->notify(new SimulatorRequestRejected($request));
    }

    // Collection
    public function sendCollectionRequestApprovedNotification()
    {
        $this->notify(new CollectionRequestApproved);
    }

    public function sendCollectionRequestRejectedNotification(LibRequest $request)
    {
        $this->notify(new CollectionRequestRejected($request));
    }

    // Exclusive
    public function sendExclusiveRequestReceivedNotification(LibRequest $request)
    {
        $this->notify(new ExclusiveRequestReceived($request));
    }

    public function sendExclusiveRequestApprovedNotification()
    {
        $this->notify(new ExclusiveRequestApproved);
    }

    public function sendExclusiveRequestRejectedNotification(LibRequest $request)
    {
        $this->notify(new ExclusiveRequestRejected($request));
    }







    public function profile()
    {
        return $this->hasOne(General\Profile::class);
    }

    public function memberLists()
    {
        return $this->hasMany(Marketplace\Shopping\MemberList::class);
    }

    public function designs()
    {
        return $this->hasMany(Marketplace\Designs\Design::class, 'designer_id');
    }

    public function carts()
    {
        return $this->hasMany(Marketplace\Shopping\MemberCart::class);
    }

    public function studio()
    {
        return $this->hasOne(Marketplace\Studio\Studio::class, 'user_id');
    }

    // As Designer
    public function studios()
    {
        return $this->belongsToMany('App\Marketplace\Studio\Studio', 'studio_user');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function quota()
    {
        return $this->hasOne(Quota::class, 'user_id');
    }

    public function libDownloads()
    {
        return $this->hasMany(LibUserDownload::class, 'user_id');
    }

}
