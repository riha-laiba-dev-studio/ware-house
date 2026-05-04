<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentDueAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $records, public string $type) {}

    public function envelope(): Envelope
    {
        $label = $this->type === 'sales' ? 'Sales' : 'Purchases';
        return new Envelope(
            subject: "💰 Outstanding {$label} Due — " . $this->records->count() . ' records',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-due',
        );
    }
}
