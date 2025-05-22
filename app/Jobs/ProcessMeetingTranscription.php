<?php

namespace App\Jobs;

use App\Models\CrmActivities\CrmActivitiesMeetings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\CrmActivities\CrmActivityMeetingController;

class ProcessMeetingTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meeting;
    protected $audioUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(CrmActivitiesMeetings $meeting, $audioUrl)
    {
        $this->meeting = $meeting;
        $this->audioUrl = $audioUrl;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // El mismo código que ya tienes para dividir y transcribir
        $outputDirectory = storage_path('app/public/reuniones/segmentos');
        $crmActivityMeetingController = new CrmActivityMeetingController();
        $segmentos = $crmActivityMeetingController->dividirAudioPorTamaño($this->audioUrl, $outputDirectory, 24);
        $transcripciones = [];
        foreach ($segmentos as $segmento) {
            $transcripcion = $crmActivityMeetingController->transcripcion($segmento);  // Llamada a la transcripción para cada parte
            $transcripciones[] = $transcripcion['text'];  // Guardar el texto de cada transcripción
        }
        $textoCompleto = implode(" ", $transcripciones);

        $respuesta = $crmActivityMeetingController->chatgpt($textoCompleto);
        if (isset($respuesta['choices'][0]['message']['content'])) {
            $resumen = $respuesta['choices'][0]['message']['content'];
            $this->meeting->description = $resumen;
        } else {
            // Manejo del caso en que no se obtiene el resumen correctamente
            $this->meeting->description = 'No se pudo generar un resumen.';
        }

        // Guardar el resumen en la reunión
        $this->meeting->save();

        // Eliminar archivos temporales
        foreach ($segmentos as $segmento) {
            unlink($segmento);  // Eliminar cada archivo temporal
        }
    }

    // Implementar tus funciones de transcripción y chatgpt aquí, o llamarlas desde tus servicios
}
