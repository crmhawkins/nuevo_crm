<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoseoReportGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $autoseoId;
    public $filename;
    public $domain;
    public $pin;

    public function __construct($autoseoId, $filename, $domain, $pin)
    {
        $this->autoseoId = $autoseoId;
        $this->filename = $filename;
        $this->domain = $domain;
        $this->pin = $pin;
    }

    public function build()
    {
        return $this->from('autoseo@hawkins.es', 'Autoseo Hawkins')
            ->subject("Nuevo informe SEO generado - {$this->domain}")
            ->view('mails.autoseo-report-generated')
            ->with([
                'autoseoId' => $this->autoseoId,
                'filename' => $this->filename,
                'domain' => $this->domain,
                'pin' => $this->pin
            ]);
    }
}
