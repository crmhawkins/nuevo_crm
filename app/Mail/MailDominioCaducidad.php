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
use Carbon\Carbon;

class MailDominioCaducidad extends Mailable
{
    use Queueable, SerializesModels;

    public $dominio;
    public $cliente;
    public $fechaCaducidad;
    public $urlPago;

    /**
     * Create a new message instance.
     */
    public function __construct(Dominio $dominio, Client $cliente, $fechaCaducidad, $urlPago)
    {
        $this->dominio = $dominio;
        $this->cliente = $cliente;
        $this->fechaCaducidad = $fechaCaducidad;
        $this->urlPago = $urlPago;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Acción requerida: Su dominio ' . $this->dominio->dominio . ' está próximo a caducar',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.dominio-caducidad',
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
