<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailBudget extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $atta;
    public $mailBudget;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailBudget,$atta)
    {
        $this->mailBudget = $mailBudget;
        $this->atta = $atta;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from("info@crmhawkins.com")
        ->subject("Presupuesto - Los Creativos de Hawkins")
        ->view('mails.mailBudget');

        foreach($this->atta as $filePath){
            $mail->attach($filePath);
        }

        return $mail;
    }
}