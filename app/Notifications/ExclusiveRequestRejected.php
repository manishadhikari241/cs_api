<?php

namespace App\Notifications;

use App\Marketplace\Goods\GoodRequest;
use App\Marketplace\Libs\LibRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExclusiveRequestRejected extends Notification implements ShouldQueue
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
                    ->subject('Your exclusive design could not be created')
                    ->line('We are sorry to inform you we could not create your exclusive design')
                    ->line('Reason: '.$this->request->message)
                    ->line('Your quota will not be used and you can re-try it anytime')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
