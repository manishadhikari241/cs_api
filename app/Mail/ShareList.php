<?php

namespace App\Mail;

use App\Marketplace\Shopping\MemberList;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShareList extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $list;
    public $user;
    public $email;
    public $to_name;
    public $bodyMessage;
    public $username;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($list, User $user, $email, $name, $message)
    {
        $this->list = MemberList::where('id', $list)->first();
        // \Log::info('share member list', $this->list->toArray());
        $this->user        = $user;
        $this->email       = $email;
        $this->to_name     = $name;
        $this->bodyMessage = $message;
        $this->username    = $user->username ?? $user->email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \App::setLocale($this->user ? $this->user->lang_pref : 'en');
        $subject = __('emails.share.list.subject');
        return $this->view('emails.lists.share')
            ->subject($subject);
    }
}
