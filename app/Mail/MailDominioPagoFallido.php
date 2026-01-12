<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Dominios\Dominio;
use App\Models\Clients\Client;

class MailDominioPagoFallido extends Mailable
{
    use Queueable, SerializesModels;

    public $dominio;
    public $cliente;
    public $razonFallo;

    /**
     * Create a new message instance.
     */
    public function __construct(Dominio $dominio, Client $cliente, $razonFallo = 'Razón desconocida')
    {
        $this->dominio = $dominio;
        $this->cliente = $cliente;
        $this->razonFallo = $razonFallo;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acción requerida: Pago fallido para renovación de dominio ' . $this->dominio->dominio,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.dominio-pago-fallido',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
