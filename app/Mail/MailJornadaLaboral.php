<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailJornadaLaboral extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $empleado;
    public $hora;
    public $fecha;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($empleado, $hora, $fecha)
    {
        $this->empleado = $empleado;
        $this->hora = $hora;
        $this->fecha = $fecha;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from('journey@crmhawkins.com')
        ->subject("Jornada - Los Creativos de Hawkins")
        ->view('mails.mailJornadaLaboral');

        return $mail;
    }
}