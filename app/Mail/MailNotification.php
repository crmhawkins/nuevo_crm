<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailNotification extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $notification;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        $mail = $this->from('info@crmhawkins.com')
        ->subject($this->notification->subject)
        ->view('mails.mailNotification');

        return $mail;
    }
}