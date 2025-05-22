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
        $mail = $this->from($this->MailConceptSupplier->gestorMail)
        ->subject("Orden de Compra - Los Creativos de Hawkins")
        ->view('mails.mailConceptSupplier');

        foreach($this->atta as $filePath){
            $mail->attach( $filePath);
        }

        return $mail;
    }
}
