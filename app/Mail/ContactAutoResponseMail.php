<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactAutoResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We have received your message - VESTA Support',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-auto-response', // Mengarah ke template balasan kustom
        );
    }

    public function attachments(): array
    {
        return [];
    }
}