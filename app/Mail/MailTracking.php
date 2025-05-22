<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailTracking extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The mailTracking object instance.
     *
     * @var mailTracking
     */
    public $mailTracking;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailTracking)
    {
        $this->mailTracking = $mailTracking;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->mailTracking->gestorMail)
        ->subject("Código de seguimiento de tu pedido - Los Creativos de Hawkins")
        ->view('mails.mailTrackingCode');

        return $mail;
    }
}