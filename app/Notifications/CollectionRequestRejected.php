<?php

namespace App\Notifications;

use App\Marketplace\Libs\LibRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CollectionRequestRejected extends Notification implements ShouldQueue
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
                    ->subject('Your Free Request has been rejected')
                    ->line('Hi,')
                    ->line('We are sorry to inform you that we could not process your Free Request to create your style of designs')
                    ->line('Reason: '. $this->request->message)
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
