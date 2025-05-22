<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailHorasTrabajadasUsuario extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    
     public $mensajeHorasTrabajadas;
     public $mensajeHorasProducidas;

 
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($mensajeHorasTrabajadas, $mensajeHorasProducidas){
        $this->mensajeHorasTrabajadas = $mensajeHorasTrabajadas;
        $this->mensajeHorasProducidas = $mensajeHorasProducidas;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from('info@crmhawkins.com')
        ->subject("Horas Trabajadas - Los Creativos de Hawkins")
        ->view('mails.mailHorasTrabajadasUsuario');

        return $mail;
    }
}