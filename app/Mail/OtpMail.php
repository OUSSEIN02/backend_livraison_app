<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $companyName;

    public function __construct($otp, $companyName = null)
    {
        $this->otp = $otp;
        $this->companyName = $companyName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre code de vérification - Gabon Livraison Express',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}