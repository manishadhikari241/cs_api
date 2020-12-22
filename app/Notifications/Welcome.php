<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Welcome extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Welcome to Collectionstock!')
                    ->line('You are now having full access to:')
                    ->line('+ Our Market and Trend aware Collections')
                    ->line('+ Our Prints, Patterns and Graphics')
                    ->line('+ The Product Simulator')
                    ->line('+ The Complimentary Design Request Service')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
