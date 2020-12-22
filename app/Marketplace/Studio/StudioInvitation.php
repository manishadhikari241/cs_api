<?php

namespace App\Marketplace\Studio;

use App\User;
use Illuminate\Database\Eloquent\Model;

class StudioInvitation extends Model
{
    protected $table    = 'studio_invitation';
    protected $fillable = ['email', 'studio_id', 'first_name', 'last_name'];

    public function user()
    {
        return $this->belongsTo('App\User', 'email', 'email');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class);
    }
}
