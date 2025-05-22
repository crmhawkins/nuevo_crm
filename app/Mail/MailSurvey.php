<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailSurvey extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $budget;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($budget)
    {
        $this->budget = $budget;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        $mail = $this->from('info@crmhawkins.com')
        ->subject("Encuesta de satisfacción - Los Creativos de Hawkins")
        ->view('mails.mailSurvey');

        return $mail;
    }
}