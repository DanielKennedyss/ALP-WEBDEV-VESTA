<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $replyMessage;
    public $customerName;
    public $originalSubject;

    public function __construct($customerName, $originalSubject, $replyMessage)
    {
        $this->customerName = $customerName;
        $this->originalSubject = $originalSubject;
        $this->replyMessage = $replyMessage;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->originalSubject . ' - VESTA Support',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.support_reply', // Buat file view blade sederhana untuk template email balasan
        );
    }
}