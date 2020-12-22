<?php

namespace App\General\Premium;

class PremiumUser
{
    public $user;

    public function __construct($user)
    {
        $this->user  = $user;
        $this->total = $this->user->credit()->sum('value');
        $this->usage = $this->user->creditUsages()->sum('value');
    }

    public function status()
    {
        return (object) [
            'credit'  => $this->total,
            'balance' => $this->total - $this->usage,
            // 'plan'    => $this->user->plan()->first(),
        ];
    }

    public function detailStatus()
    {
        return (object) [
            'credit'  => $this->total,
            'balance' => $this->total - $this->usage,
            'project' => $this->user->projects()->count(),
            'plan'    => $this->user->premiumHistory()->latest()->first()->plan()->with('translations')->first(),
        ];
    }
}
