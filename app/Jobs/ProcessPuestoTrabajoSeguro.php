<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Justificacion;

class ProcessPuestoTrabajoSeguro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $nombre;
    public $email;
    public $empresa;
    public $justificacionId;
    
    /**
     * Número de intentos en caso de fallo
     */
    public $tries = 3;
    
    /**
     * Timeout en segundos
     */
    public $timeout = 300;

    /**
     * Create a new job instance.
     */
    public function __construct($nombre, $email, $empresa, $justificacionId)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->empresa = $empresa;
        $this->justificacionId = $justificacionId;
        
        // Usar cola específica para serializar los trabajos (uno a la vez)
        $this->onQueue('puesto_trabajo_seguro');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🚀 Iniciando procesamiento de Puesto de Trabajo Seguro', [
            'justificacion_id' => $this->justificacionId,
            'nombre' => $this->nombre,
            'email' => $this->email
        ]);

        try {
            $justificacion = Justificacion::findOrFail($this->justificacionId);
            
            // Actualizar estado a procesando
            $metadata = $justificacion->metadata ?? [];
            $metadata['estado'] = 'procesando';
            $metadata['procesamiento_iniciado'] = now()->toDateTimeString();
            $justificacion->update(['metadata' => $metadata]);

            // Construir callback URL
            $callbackUrl = route('justificaciones.receive.public', $this->justificacionId);

            // Enviar petición a la API externa
            $response = Http::timeout(120)->post(config('app.sgpseg_api_url', 'https://aiapi.hawkins.es/sgpseg/generate-pdf'), [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'nombre_empresa' => $this->empresa,
                'justificacion_id' => $this->justificacionId,
                'callback_url' => $callbackUrl
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('✅ Petición exitosa a API externa', [
                    'justificacion_id' => $this->justificacionId,
                    'response' => $data
                ]);

                // Actualizar estado a en_cola (esperando archivos)
                $metadata['estado'] = 'en_cola';
                $metadata['mensaje'] = $data['message'] ?? 'Solicitud enviada correctamente';
                $metadata['api_response'] = $data;
                $justificacion->update(['metadata' => $metadata]);

            } else {
                Log::error('❌ Error en respuesta de API externa', [
                    'justificacion_id' => $this->justificacionId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $metadata['estado'] = 'error';
                $metadata['mensaje'] = 'Error al comunicarse con el servidor: ' . $response->status();
                $justificacion->update(['metadata' => $metadata]);

                throw new \Exception('API respondió con error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('❌ Error procesando Puesto de Trabajo Seguro', [
                'justificacion_id' => $this->justificacionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Actualizar justificación con error
            try {
                $justificacion = Justificacion::find($this->justificacionId);
                if ($justificacion) {
                    $metadata = $justificacion->metadata ?? [];
                    $metadata['estado'] = 'error';
                    $metadata['mensaje'] = 'Error: ' . $e->getMessage();
                    $metadata['error_fecha'] = now()->toDateTimeString();
                    $justificacion->update(['metadata' => $metadata]);
                }
            } catch (\Exception $updateError) {
                Log::error('No se pudo actualizar el estado de error', [
                    'error' => $updateError->getMessage()
                ]);
            }

            // Re-lanzar excepción para que Laravel Queue lo maneje
            throw $e;
        }
    }

    /**
     * Manejar fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💥 Job de Puesto de Trabajo Seguro falló definitivamente', [
            'justificacion_id' => $this->justificacionId,
            'nombre' => $this->nombre,
            'error' => $exception->getMessage()
        ]);

        try {
            $justificacion = Justificacion::find($this->justificacionId);
            if ($justificacion) {
                $metadata = $justificacion->metadata ?? [];
                $metadata['estado'] = 'error';
                $metadata['mensaje'] = 'Falló después de ' . $this->tries . ' intentos';
                $metadata['ultimo_error'] = $exception->getMessage();
                $justificacion->update(['metadata' => $metadata]);
            }
        } catch (\Exception $e) {
            Log::error('No se pudo actualizar el estado final de error');
        }
    }
}
