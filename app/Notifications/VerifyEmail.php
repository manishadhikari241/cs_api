<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;

class VerifyEmail extends VerifyEmailBase implements ShouldQueue {

    use Queueable;

    protected function verificationUrl($notifiable) {
        return URL::temporarySignedRoute('verification.verifyemail', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]);
    }

}
