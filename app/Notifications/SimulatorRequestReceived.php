<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SimulatorRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your order to add a Product into the Simulator')
                    ->line('Hi,')
                    ->line('')
                    ->line('We received your order to add a Product into the Simulator. Within 5 working days Your Product will be evaluated and uploaded into the Simulator. We will be in contact with you soon!')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
