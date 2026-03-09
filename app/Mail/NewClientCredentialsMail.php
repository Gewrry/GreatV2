<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewClientCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $clientName,
        public readonly string $businessName,
        public readonly string $email,
        public readonly string $tempPassword,
        public readonly string $portalUrl,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[GReAT System] Your Client Portal Account — ' . $this->businessName,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.client.new-credentials',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}