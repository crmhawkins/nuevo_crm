<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailConceptSupplier extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $MailConceptSupplier;
    public $atta;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($MailConceptSupplier, $atta)
    {
        $this->MailConceptSupplier = $MailConceptSupplier;
        $this->atta = $atta;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Usar la dirección configurada en SMTP si está disponible, 
        // de lo contrario usar gestorMail
        $fromAddress = config('mail.from.address', $this->MailConceptSupplier->gestorMail);
        $fromName = config('mail.from.name', $this->MailConceptSupplier->gestor ?? 'Los Creativos de Hawkins');
        
        $mail = $this->from($fromAddress, $fromName)
        ->subject("Orden de Compra - Los Creativos de Hawkins")
        ->view('mails.mailConceptSupplier');

        foreach($this->atta as $filePath){
            $mail->attach( $filePath);
        }

        return $mail;
    }
}
