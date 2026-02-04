<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceSent extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice; // Public property so the view can access it

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        return $this
            ->from(
                config('mail.from.address'),
                config('mail.from.name')
            )
            ->subject(
                'Invoice #' . str_pad($this->invoice->id, 4, '0', STR_PAD_LEFT) . ' from Tanaman'
            )
            ->view('emails.invoice');
    }
}
