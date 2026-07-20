<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewChatSessionUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public $targetUser;
    public $admin;
    public $firstMessage;

    public function __construct(User $targetUser, User $admin, string $firstMessage)
    {
        $this->targetUser = $targetUser;
        $this->admin = $admin;
        $this->firstMessage = $firstMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Message from ' . $this->admin->name . ' - UCC-ERS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-chat-session-user',
        );
    }
}