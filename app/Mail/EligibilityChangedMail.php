<?php

namespace App\Mail;

use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EligibilityChangedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Supplier $supplier,
        public readonly bool $isNowEligible,
        public readonly array $reasons
    ) {}

    public function envelope(): Envelope
    {
        $status = $this->isNowEligible ? 'Eligible' : 'Ineligible';

        return new Envelope(
            subject: "Your Supplier Eligibility Status Changed - Now {$status}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.eligibility-changed',
            with: [
                'supplier' => $this->supplier,
                'isNowEligible' => $this->isNowEligible,
                'reasons' => $this->reasons,
                'status' => $this->isNowEligible ? 'eligible' : 'ineligible',
            ],
        );
    }
}
