<?php

namespace App\Notifications;

use App\Marketplace\Libs\LibRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExclusiveRequestReceived extends Notification implements ShouldQueue
{
    use Queueable;

    private $request;

    public function __construct(LibRequest $request)
    {
        $this->request = $request;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Your order to create an exclusive design')
                    ->line('Hi,')
                    ->line('')
                    ->line($this->request->files)
                    ->line(__('emails.we_received_to_create_design'))
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
