<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailInvoice extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $filename;
    public $mailInvoice;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailInvoice, $filename)
    {
        $this->mailInvoice = $mailInvoice;
        $this->filename = $filename;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from("info@crmhawkins.com")
        ->subject("Factura - Los Creativos de Hawkins")
        ->view('mails.mailInvoice')
        ->attach($this->filename);

        return $mail;
    }
}