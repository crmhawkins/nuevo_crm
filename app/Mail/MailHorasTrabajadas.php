<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailHorasTrabajadas extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    
     public $arrayHorasTotal;

 
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($arrayHorasTotal){
        $this->arrayHorasTotal = $arrayHorasTotal;
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
        ->view('mails.mailHorasTrabajadas');

        return $mail;
    }
}