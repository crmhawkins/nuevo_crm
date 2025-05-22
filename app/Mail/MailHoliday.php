<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailHoliday extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $estado;
    public $empleado;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($estado, $empleado)
    {
        $this->estado = $estado;
        $this->empleado = $empleado;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from('holidays@crmhawkins.com')
        ->subject("Vacaciones - Los Creativos de Hawkins")
        ->view('mails.mailHoliday');

        return $mail;
    }
}
