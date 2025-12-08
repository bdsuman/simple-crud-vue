<?php

namespace App\Mail\Mobile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $lang;

    public function __construct($user)
    {
        $this->user  = $user;
        $this->lang = app('language');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('welcome_email_subject', [], $this->lang),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mobile.welcome-email',
            with: [
                'data' => [
                    'user'   => $this->user
                ],
                'email_subject' => __('welcome_email_subject', [], $this->lang),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
