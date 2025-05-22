<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailMeeting extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $meeting;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($meeting)
    {
        $this->meeting = $meeting;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        $mail = $this->from('info@crmhawkins.com')
        ->subject('Acta de Reunion - HAWKINS')
        ->view('mails.mailMeeting');

        return $mail;
    }
}