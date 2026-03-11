<?php

namespace App\Mail;

use App\Models\onlineBPLS\BplsOnlineApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BplsPaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly BplsOnlineApplication $application
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[GReAT System] Payment Reminder: Application #' . $this->application->application_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.client.payment-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
