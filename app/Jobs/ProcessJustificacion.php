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

class ProcessJustificacion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $justificacionId;
    
    /**
     * Número de intentos en caso de fallo
     */
    public $tries = 3;
    
    /**
     * Timeout en segundos (10 minutos)
     */
    public $timeout = 600;

    /**
     * Mapa de URLs por tipo de justificación
     */
    private const API_URLS = [
        'segunda_justificacion_presencia_basica' => 'https://aiapi.hawkins.es/sgbasc',
        'puesto_trabajo_seguro' => 'https://aiapi.hawkins.es/sgpseg',
        'presencia_avanzada_2' => 'https://aiapi.hawkins.es/sgpavz2',
        'crm_erp_factura' => 'https://aiapi.hawkins.es/sgcrmerpfac',
        // Agregar más tipos aquí según sea necesario
    ];

    /**
     * Create a new job instance.
     */
    public function __construct($justificacionId)
    {
        $this->justificacionId = $justificacionId;
        
        // Usar cola específica 'justificaciones' para serializar (uno a la vez)
        $this->onQueue('justificaciones');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('🚀 Iniciando procesamiento de Justificación', [
            'justificacion_id' => $this->justificacionId
        ]);

        try {
            $justificacion = Justificacion::findOrFail($this->justificacionId);
            $tipo = $justificacion->tipo_justificacion;
            $metadata = $justificacion->metadata ?? [];
            
            Log::info('📋 Tipo de justificación: ' . $tipo);

            // Actualizar estado a procesando
            $metadata['estado'] = 'procesando';
            $metadata['procesamiento_iniciado'] = now()->toDateTimeString();
            $justificacion->update(['metadata' => $metadata]);

            // Obtener URL según el tipo
            $apiUrl = $this->getApiUrl($tipo);
            if (!$apiUrl) {
                throw new \Exception("No se encontró URL para el tipo de justificación: {$tipo}");
            }

            Log::info('📤 Enviando a: ' . $apiUrl);

            // Construir payload según el tipo
            $payload = $this->buildPayload($justificacion);
            
            Log::info('📦 Payload construido', ['payload' => $payload]);

            // Enviar petición a la API externa
            $response = Http::timeout(120)->post($apiUrl, $payload);

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
                $metadata['api_enviado'] = now()->toDateTimeString();
                $justificacion->update(['metadata' => $metadata]);

            } else {
                Log::error('❌ Error en respuesta de API externa', [
                    'justificacion_id' => $this->justificacionId,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                $metadata['estado'] = 'error';
                $metadata['mensaje'] = 'Error al comunicarse con el servidor: ' . $response->status();
                $metadata['error_body'] = $response->body();
                $justificacion->update(['metadata' => $metadata]);

                throw new \Exception('API respondió con error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('❌ Error procesando Justificación', [
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
     * Obtener URL de API según tipo de justificación
     */
    private function getApiUrl($tipo): ?string
    {
        return self::API_URLS[$tipo] ?? null;
    }

    /**
     * Construir payload según tipo de justificación
     */
    private function buildPayload($justificacion): array
    {
        $metadata = $justificacion->metadata ?? [];
        $tipo = $justificacion->tipo_justificacion;
        $user = $justificacion->user;
        
        // Callback URL común para todos
        $callbackUrl = route('justificaciones.receive.public', $justificacion->id);

        $basePayload = [
            'justificacion_id' => $justificacion->id,
            'user_id' => $justificacion->admin_user_id,
            'user_name' => $user ? $user->name : 'Usuario',
            'nombre_justificacion' => $justificacion->nombre_justificacion,
            'tipo_justificacion' => $tipo,
            'callback_url' => $callbackUrl,
            'timestamp' => now()->toDateTimeString()
        ];

        // Payload específico según el tipo
        switch ($tipo) {
            case 'puesto_trabajo_seguro':
                return array_merge($basePayload, [
                    'nombre' => $metadata['nombre'] ?? '',
                    'email' => $metadata['email'] ?? '',
                    'nombre_empresa' => $metadata['empresa'] ?? ''
                ]);

            case 'segunda_justificacion_presencia_basica':
                return array_merge($basePayload, [
                    'url' => $metadata['url'] ?? '',
                    'tipo_analisis' => $metadata['tipo_analisis'] ?? 'web'
                ]);

            case 'presencia_avanzada_2':
                return array_merge($basePayload, [
                    'kd' => $metadata['kd'] ?? '',
                    'nombre' => $metadata['nombre'] ?? '',
                    'url' => $metadata['url'] ?? '',
                    'keyword_principal' => $metadata['keyword_principal'] ?? '',
                    'phone' => $metadata['phone'] ?? '',
                    'email' => $metadata['email'] ?? '',
                    'address' => $metadata['address'] ?? '',
                    'descripcion' => $metadata['descripcion'] ?? ''
                ]);

            case 'crm_erp_factura':
                return array_merge($basePayload, [
                    'tipo_sistema' => $metadata['tipo_sistema'] ?? '',
                    'url' => $metadata['url'] ?? '',
                    'username' => $metadata['username'] ?? 'admin',
                    'password' => $metadata['password'] ?? '12345678'
                ]);

            default:
                // Payload genérico con toda la metadata
                return array_merge($basePayload, $metadata);
        }
    }

    /**
     * Manejar fallo del job
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('💥 Job de Justificación falló definitivamente', [
            'justificacion_id' => $this->justificacionId,
            'error' => $exception->getMessage()
        ]);

        try {
            $justificacion = Justificacion::find($this->justificacionId);
            if ($justificacion) {
                $metadata = $justificacion->metadata ?? [];
                $metadata['estado'] = 'error';
                $metadata['mensaje'] = 'Falló después de ' . $this->tries . ' intentos';
                $metadata['ultimo_error'] = $exception->getMessage();
                $metadata['fallo_definitivo'] = now()->toDateTimeString();
                $justificacion->update(['metadata' => $metadata]);
            }
        } catch (\Exception $e) {
            Log::error('No se pudo actualizar el estado final de error');
        }
    }
}
