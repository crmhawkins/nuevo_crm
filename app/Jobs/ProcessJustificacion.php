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

            // Verificar si ya está completado (no sobrescribir)
            $estadoActual = $metadata['estado'] ?? 'pendiente';
            if ($estadoActual === 'completado') {
                Log::info('⚠️ Justificación ya completada, no se procesará de nuevo', [
                    'justificacion_id' => $this->justificacionId
                ]);
                return; // Salir sin procesar
            }

            Log::info('📋 Tipo de justificación: ' . $tipo);

            // Actualizar estado a procesando solo si no está completado
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
            Log::info('🔗 URL en payload (crm_erp_factura): ' . ($payload['url'] ?? 'N/A'));

            // Enviar petición a la API externa con timeout extendido
            $response = Http::timeout(300)->post($apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('✅ Petición exitosa a API externa', [
                    'justificacion_id' => $this->justificacionId,
                    'response' => $data
                ]);

                // Verificar si ya está completado antes de actualizar (el callback puede haber llegado antes)
                $justificacion->refresh();
                $metadataActualizada = $justificacion->metadata ?? [];
                $estadoActual = $metadataActualizada['estado'] ?? 'pendiente';

                // Si el callback ya llegó y el estado es "completado", solo actualizar campos adicionales sin cambiar el estado
                if ($estadoActual === 'completado') {
                    Log::info('⚠️ Justificación ya completada (callback recibido), solo actualizando campos adicionales', [
                        'justificacion_id' => $this->justificacionId
                    ]);
                    
                    // Solo actualizar campos adicionales sin cambiar el estado
                    $metadataActualizada['api_response'] = $data;
                    $metadataActualizada['api_enviado'] = now()->toDateTimeString();
                    
                    // Agregar información del PDF si está disponible
                    if (isset($data['pdf'])) {
                        $metadataActualizada['pdf_generado'] = $data['pdf'];
                    }
                    if (isset($data['pdf_path'])) {
                        $metadataActualizada['pdf_path'] = $data['pdf_path'];
                    }
                    
                    $justificacion->update(['metadata' => $metadataActualizada]);
                    return; // Salir sin cambiar el estado
                }

                // Si la respuesta incluye información de PDF generado pero no se envió al callback,
                // actualizar el estado a "procesando" para indicar que el servidor Python está trabajando
                if (isset($data['success']) && $data['success'] === true && isset($data['pdf'])) {
                    // Si el callback ya fue llamado según la respuesta del servidor Python
                    if (isset($data['callback_response']) && isset($data['callback_response']['success']) && $data['callback_response']['success'] === true) {
                        Log::info('✅ Callback ya procesado según respuesta del servidor Python');
                        // No cambiar el estado, debe estar en completado o lo estará pronto
                        // Solo actualizar campos adicionales
                        $metadataActualizada['api_response'] = $data;
                        $metadataActualizada['api_enviado'] = now()->toDateTimeString();
                        $metadataActualizada['pdf_generado'] = $data['pdf'];
                        if (isset($data['pdf_path'])) {
                            $metadataActualizada['pdf_path'] = $data['pdf_path'];
                        }
                        $justificacion->update(['metadata' => $metadataActualizada]);
                    } else {
                        // Verificar nuevamente el estado antes de actualizar (race condition)
                        $justificacion->refresh();
                        $metadataVerificada = $justificacion->metadata ?? [];
                        $estadoVerificado = $metadataVerificada['estado'] ?? 'pendiente';
                        
                        if ($estadoVerificado !== 'completado') {
                            $metadataVerificada['estado'] = 'procesando';
                            $metadataVerificada['mensaje'] = 'PDF generado correctamente. Esperando envío de archivos...';
                            $metadataVerificada['pdf_generado'] = $data['pdf'];
                            if (isset($data['pdf_path'])) {
                                $metadataVerificada['pdf_path'] = $data['pdf_path'];
                            }
                            $metadataVerificada['api_response'] = $data;
                            $metadataVerificada['api_enviado'] = now()->toDateTimeString();
                            $justificacion->update(['metadata' => $metadataVerificada]);
                        } else {
                            Log::info('⚠️ Estado ya es completado, solo actualizando campos adicionales');
                            $metadataVerificada['api_response'] = $data;
                            $metadataVerificada['api_enviado'] = now()->toDateTimeString();
                            $metadataVerificada['pdf_generado'] = $data['pdf'];
                            if (isset($data['pdf_path'])) {
                                $metadataVerificada['pdf_path'] = $data['pdf_path'];
                            }
                            $justificacion->update(['metadata' => $metadataVerificada]);
                        }
                    }
                } else {
                    // Verificar nuevamente el estado antes de actualizar (race condition)
                    $justificacion->refresh();
                    $metadataVerificada = $justificacion->metadata ?? [];
                    $estadoVerificado = $metadataVerificada['estado'] ?? 'pendiente';
                    
                    if ($estadoVerificado !== 'completado') {
                        // Actualizar estado a en_cola (esperando archivos)
                        $metadataVerificada['estado'] = 'en_cola';
                        $metadataVerificada['mensaje'] = $data['message'] ?? 'Solicitud enviada correctamente';
                        $metadataVerificada['api_response'] = $data;
                        $metadataVerificada['api_enviado'] = now()->toDateTimeString();
                        $justificacion->update(['metadata' => $metadataVerificada]);
                    } else {
                        Log::info('⚠️ Estado ya es completado, solo actualizando campos adicionales');
                        $metadataVerificada['api_response'] = $data;
                        $metadataVerificada['api_enviado'] = now()->toDateTimeString();
                        $justificacion->update(['metadata' => $metadataVerificada]);
                    }
                }

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
     * Normalizar URL eliminando protocolos duplicados
     */
    private function normalizarUrl($url): string
    {
        if (empty($url)) {
            return '';
        }

        $url = trim($url);

        // Eliminar TODOS los protocolos al inicio (puede haber múltiples duplicados)
        while (preg_match('/^https?:\/\//i', $url)) {
            $url = preg_replace('/^https?:\/\//i', '', $url);
        }

        // Eliminar cualquier otro protocolo duplicado que pueda quedar
        $url = preg_replace('/^https?:\/\/+/i', '', $url);

        // Si no tiene protocolo, agregar https://
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }

        // Eliminar barra final
        $url = rtrim($url, '/');

        return $url;
    }

    /**
     * Construir payload según tipo de justificación
     */
    private function buildPayload($justificacion): array
    {
        $metadata = $justificacion->metadata ?? [];
        $tipo = $justificacion->tipo_justificacion;
        $user = $justificacion->user;

        // Callback URL común para todos - forzar HTTPS
        $callbackUrl = route('justificaciones.receive.public', $justificacion->id);
        // Asegurar que siempre use HTTPS
        $callbackUrl = str_replace('http://', 'https://', $callbackUrl);

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
                    'url' => $this->normalizarUrl($metadata['url'] ?? ''),
                    'tipo_analisis' => $metadata['tipo_analisis'] ?? 'web',
                    'periodo' => $metadata['periodo'] ?? ($metadata['fecha_inicio_periodo_prestacion'] ?? '')
                ]);

            case 'presencia_avanzada_2':
                return array_merge($basePayload, [
                    'kd' => $metadata['kd'] ?? '',
                    'fecha' => $metadata['fecha'] ?? '',
                    'nombre' => $metadata['nombre'] ?? '',
                    'url' => $this->normalizarUrl($metadata['url'] ?? ''),
                    'keyword_principal' => $metadata['keyword_principal'] ?? '',
                    'phone' => $metadata['phone'] ?? '',
                    'email' => $metadata['email'] ?? '',
                    'address' => $metadata['address'] ?? '',
                    'descripcion' => $metadata['descripcion'] ?? ''
                ]);

            case 'crm_erp_factura':
                // Mapear tipo_sistema a tipo en mayúsculas como espera la API
                $tipoSistema = strtolower($metadata['tipo_sistema'] ?? '');
                $tipoMapeado = match($tipoSistema) {
                    'crm' => 'CRM',
                    'erp' => 'ERP',
                    'factura' => 'FACTURA',
                    default => strtoupper($tipoSistema)
                };

                // Normalizar URL: eliminar protocolo porque el servidor Python lo agregará
                // Esto evita el problema de https://https:// duplicado
                $urlRaw = $metadata['url'] ?? '';
                $urlRaw = trim($urlRaw);

                // Eliminar protocolos al inicio (el servidor Python los agregará)
                $urlSinProtocolo = preg_replace('/^https?:\/\//i', '', $urlRaw);

                // Eliminar barras finales
                $urlSinProtocolo = rtrim($urlSinProtocolo, '/');

                return array_merge($basePayload, [
                    'tipo' => $tipoMapeado,
                    'tipo_sistema' => $metadata['tipo_sistema'] ?? '',
                    'url' => $urlSinProtocolo, // Enviar sin protocolo para que el servidor Python lo agregue
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
