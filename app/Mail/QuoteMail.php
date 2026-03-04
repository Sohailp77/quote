<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Quote;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
        public $pdfBinary,
        public $customMessage = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Quotation {$this->quote->reference_id} from " . (config('app.provider_name') ?? config('app.name')),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.quote',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfBinary, "Quotation_{$this->quote->reference_id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
