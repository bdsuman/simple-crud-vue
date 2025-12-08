<?php

namespace App\Mail\Mobile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;
    public $expiryTime;
    public $lang;

    public function __construct($user, $otp, $expiryTime)
    {
        $this->user       = $user;
        $this->otp        = (string) $otp;
        $this->expiryTime = (int) $expiryTime;
        $this->lang = app('language');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('password_reset_otp_email_subject', [], $this->lang),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.mobile.passwordResetEmail',
            with: [
                'data' => [
                    'user'       => $this->user,
                    'otp'        => $this->otp,
                    'expiryTime' => $this->expiryTime,
                ],
                'email_subject' => __('password_reset_otp_email_subject', [], $this->lang),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
