<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your Free Request is ready')
                    ->line('Hi,')
                    ->line('We created your requested style of designs and you can find them now as a mini collection on our website!')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
