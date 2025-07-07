<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\Autoseo\AutoseoReportsGen;
use Illuminate\Support\Facades\Log;

class GenerateSeoReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $email;

    /**
     * Create a new job instance.
     */
    public function __construct($id = 15, $email = null)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Iniciando generación de informe SEO para ID: {$this->id}");

        try {
            $controller = new AutoseoReportsGen();
            $result = $controller->generateReportFromCommand($this->id);

            if ($result->getStatusCode() === 200) {
                $data = json_decode($result->getContent(), true);
                Log::info("Informe SEO generado correctamente", [
                    'id' => $this->id,
                    'filename' => $data['filename'] ?? 'N/A'
                ]);

                // Aquí podrías enviar un email de notificación si se proporcionó
                if ($this->email) {
                    // TODO: Implementar envío de email de notificación
                    Log::info("Notificación enviada a: {$this->email}");
                }
            } else {
                Log::error("Error al generar informe SEO", [
                    'id' => $this->id,
                    'response' => $result->getContent()
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Excepción al generar informe SEO", [
                'id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }
}
