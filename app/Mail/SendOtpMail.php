<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // Variabel penampung kode OTP untuk dilempar ke Blade

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi VESTA Anda',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // Mengarah ke file resources/views/emails/otp.blade.php
        );
    }

    public function attachments(): array
    {
        return [];
    }
}