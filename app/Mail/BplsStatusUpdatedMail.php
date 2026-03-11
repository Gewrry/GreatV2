<?php

namespace App\Mail;

use App\Models\onlineBPLS\BplsOnlineApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BplsStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly BplsOnlineApplication $application,
        public readonly string $statusLabel,
        public readonly ?string $customMessage = null
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->application->workflow_status) {
            'verified' => 'Documents Verified — ' . $this->application->application_number,
            'assessed' => 'Payment Required: Assessment Ready — ' . $this->application->application_number,
            'returned' => 'Action Required: Application Returned — ' . $this->application->application_number,
            'approved' => 'Congratulations! Your Business Permit is Approved — ' . $this->application->application_number,
            'rejected' => 'Application Update — ' . $this->application->application_number,
            default    => 'Application Status Update — ' . $this->application->application_number,
        };

        return new Envelope(
            subject: '[GReAT System] ' . $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.client.status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
