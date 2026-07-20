<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewChatSessionAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chatUser;
    public $firstMessage;

    public function __construct(User $chatUser, ?string $firstMessage = null)
    {
        $this->chatUser = $chatUser;
        $this->firstMessage = $firstMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Chat Message from ' . $this->chatUser->name . ' - UCC-ERS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-chat-session-admin',
        );
    }
}