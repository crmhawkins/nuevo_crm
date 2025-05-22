<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailConcept extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $MailConcept;
    public $atta;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($MailConcept,$atta)
    {
        $this->MailConcept = $MailConcept;
        $this->atta = $atta;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from($this->MailConcept->gestorMail)
        ->subject("Solicitud de presupuesto - Los Creativos de Hawkins")
        ->view('mails.mailConcept');

        foreach($this->atta as $filePath){
            $mail->attach($filePath);
        }

        return $mail;
    }
}