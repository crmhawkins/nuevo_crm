<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailNewsletter extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $newsletter;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($newsletter)
    {
        $this->newsletter = $newsletter;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        $mail = $this->from('newsletters@crmhawkins.com')
        ->subject('Newsletter - HAWKINS')
        ->view('mails.mailNewsletter');

        return $mail;
    }
}