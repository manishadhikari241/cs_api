<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SimulatorRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Product has been added in the Simulator')
                    ->line('Congratulations, Your Product has been successfully added in the Simulator!')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
