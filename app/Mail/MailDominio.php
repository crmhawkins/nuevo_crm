<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailDominio extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    
    public $dominio;
    public $fecha;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($dominio, $fecha){
        $this->dominio = $dominio;
        $this->fecha = $fecha;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from('dominios@crmhawkins.com')
        ->subject("Dominio - Los Creativos de Hawkins")
        ->view('mails.mailDominio');

        return $mail;
    }
}