<?php

namespace App\General\Representative;

use App\Utilities\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Marketplace\Libs\LibPlanUser;

class RepresentativeSubscription extends Model
{
    protected $table = 'representative_subscription';

    protected $fillable = [ 'is_active' ];

    const STATUS = [
        0 => 'is_subscription',
        1 => 'is_continue',
        2 => 'is_upgrade'
    ];

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function libPlanUser()
    {
        return $this->belongsTo(LibPlanUser::class);
    }
}
