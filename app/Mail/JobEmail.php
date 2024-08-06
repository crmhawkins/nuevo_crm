<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\MailBudget;
use App\Mail\MailConcept;
use App\Mail\MailConceptSupplier;
use Illuminate\Queue\Queue as IlluminateQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Models\Jobs\Job;

class JobEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $email;
    public $to;
    public $bbc;
    public $cc;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mail,$to,$bbc,$cc,$tipo,$attac = null)
    {
        $lastJob = Job::orderBy('created_at', 'desc')->first();
        if($lastJob)
        {
            $dateLaste = unserialize(json_decode($lastJob->payload)->data->command)->delay->date;
            $this->delay = Carbon::parse($dateLaste)->addMinutes(3);
        }else
        { 
            $this->delay = Carbon::now()->addMinutes(3);
        }
        switch($tipo)
        {
            case "presupuesto":
                $this->email = new MailBudget($mail,$attac);
                $this->to = $to;
                $this->bbc = $bbc;
                $this->cc = $cc;
                break;
            case "ordenCompra":
                $this->email = new MailConcept($mail,$attac);
                $this->to = $to;
                $this->bbc = $bbc;
                $this->cc = $cc;
                break;   
            case "ordenCompraAprobada":
                $this->email = new MailConceptSupplier($mail,$attac);
                $this->to = $to;
                $this->bbc = $bbc;
                $this->cc = $cc;
                break;
        }
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    { 
        Mail::to($this->to)
        ->bcc($this->bbc)
        ->cc($this->cc)
        ->send($this->email);
    }
}

?>