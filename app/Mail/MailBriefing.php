<?php
 
namespace App\Mail;
 
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
 
class MailBriefing extends Mailable
{
    use Queueable, SerializesModels;
     
    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $datos;
    public $mailBriefing;
    public $tipoBriefing;
 
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($datos, $tipoBriefing)
    {
        $this->datos = $datos;
        $this->tipoBriefing = $tipoBriefing;
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->tipoBriefing == "Web"){
            $mail = $this->from("info@crmhawkins.com")
        ->subject("Briefing Diseño Web - " . $this->datos['nombre_empresa'])
        ->view('mails.mailBriefing')->with('datos', $this->datos);
        
        if (isset($this->datos['logotipo'])) {
            $mail->attach(storage_path('app/' . $this->datos['logotipo']), ['as' => basename($this->datos['logotipo'])]);
        }
    
        if (isset($this->datos['contenido_multimedia'])) {
            foreach ($this->datos['contenido_multimedia'] as $path) {
                $mail->attach(storage_path('app/' . $path), ['as' => basename($path)]);
            }
        }
    
        return $mail;
        }else{
            $mail = $this->from("info@crmhawkins.com")
        ->subject("Briefing Diseño Gráfico - " . $this->datos['nombre_empresa'])
        ->view('mails.mailBriefingGrafico')->with('datos', $this->datos);

    
        return $mail;
        }
        
    }
}