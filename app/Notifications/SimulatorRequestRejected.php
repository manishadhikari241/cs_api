<?php

namespace App\Notifications;

use App\Marketplace\Goods\GoodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SimulatorRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    private $request;

    public function __construct(GoodRequest $request)
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
                    ->subject('Your Product could not be added in the Simulator')
                    ->line('Hi, unfortunately Your Product could not be added in the Simulator')
                    ->line('Reason: '.$this->request->message)
                    ->line('Your quota will not be used and you can re-try it anytime')
                    ->action('Collectionstock', env('APP_PUBLIC_URL'));
    }
}
