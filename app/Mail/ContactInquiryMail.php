<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // Menampung array kiriman form input customer

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 VESTA TIKET BARU: [' . $this->data['subject'] . '] dari ' . $this->data['first_name'] . ' ' . $this->data['last_name'],
            replyTo: [$this->data['email']] // Memudahkan owner jika ingin membalas langsung di Gmail
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-inquiry', // Mengarah ke file resources/views/emails/contact-inquiry.blade.php
        );
    }

    public function attachments(): array
    {
        return [];
    }
}