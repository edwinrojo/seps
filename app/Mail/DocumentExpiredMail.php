<?php

namespace App\Mail;

use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentExpiredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Attachment $attachment
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Document Expired: {$this->attachment->document->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.document-expired',
            with: [
                'attachment' => $this->attachment,
                'document' => $this->attachment->document,
                'supplier' => $this->attachment->supplier,
                'validityDate' => $this->attachment->validity_date?->format('F d, Y'),
            ],
        );
    }
}
