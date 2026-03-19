<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;
    public $actionType; // 'change-password' or 'reset-password'
    public $resetUrl;

    public function __construct($user, $token, $actionType = 'reset-password')
    {
        $this->user = $user;
        $this->token = $token;
        $this->actionType = $actionType;
        
        // Generate reset URL
        $this->resetUrl = route('password.reset-form', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }

    public function envelope(): Envelope
    {
        $subject = $this->actionType === 'change-password' 
            ? 'Confirm Your Password Change' 
            : 'Reset Your Password';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'user' => $this->user,
                'resetUrl' => $this->resetUrl,
                'actionType' => $this->actionType,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
