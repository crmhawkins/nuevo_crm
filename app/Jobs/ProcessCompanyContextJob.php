<?php

namespace App\Jobs;

use App\Models\Autoseo\Autoseo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessCompanyContextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $autoseoId;
    public $originalContext;

    /**
     * Número de intentos
     */
    public $tries = 3;

    /**
     * Timeout del job (2 minutos)
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct($autoseoId, $originalContext)
    {
        $this->autoseoId = $autoseoId;
        $this->originalContext = $originalContext;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("========================================");
            Log::info("🚀 [Job] INICIO - ProcessCompanyContextJob");
            Log::info("========================================");
            Log::info("🤖 [Job] Procesando contexto empresarial para Autoseo ID: {$this->autoseoId}");
            Log::info("📝 [Job] Texto original (" . strlen($this->originalContext) . " caracteres)");
            Log::info("📝 [Job] Preview texto: " . substr($this->originalContext, 0, 100) . "...");
            
            $url = 'https://aiapi.hawkins.es/chat';
            $modelo = 'gpt-oss:120b-cloud';
            $apiKey = 'OllamaAPI_2024_K8mN9pQ2rS5tU7vW3xY6zA1bC4eF8hJ0lM';
            
            Log::info("🌐 [Job] Preparando petición HTTP");
            Log::info("🌐 [Job] URL: {$url}");
            Log::info("🌐 [Job] Modelo: {$modelo}");
            Log::info("🌐 [Job] API Key (primeros 20 chars): " . substr($apiKey, 0, 20) . "...");
            Log::info("🌐 [Job] Timeout: 100 segundos");
            
            $prompt = "Contexto de empresa a procesar:\n\n" . $this->originalContext . "\n\nINSTRUCCIONES:\n- Si el texto es demasiado largo (>1200 caracteres): Resúmelo manteniendo la información clave. Es vital que no inventes informacion, solo expande la existente, no te inventes la capacidad de la empresa, ni servicios, ni ubicaciones. Basate en el contexto existente.\n- Si el texto es muy corto (<800 caracteres): Amplíalo con detalles profesionales relevantes.\n- Objetivo: Aproximadamente 1000 caracteres.\n- IMPORTANTE: Devuelve ÚNICAMENTE el texto procesado, sin introducciones, sin explicaciones, sin frases como 'Aquí está el resumen' ni nada similar. Solo el texto final.\n\nTexto procesado:";
            
            Log::info("📤 [Job] Enviando petición a la API de IA...");
            
            // Llamar a la IA (timeout de 100 segundos)
            $response = Http::timeout(100)
                ->withHeaders([
                    'X-Api-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'modelo' => $modelo,
                    'prompt' => $prompt,
                    'stream' => false,
                    'temperature' => 0.7
                ]);
            
            Log::info("📥 [Job] Respuesta recibida");
            Log::info("📊 [Job] Status Code: " . $response->status());
            Log::info("📊 [Job] Successful: " . ($response->successful() ? 'SÍ' : 'NO'));

            if ($response->successful()) {
                Log::info("✅ [Job] Respuesta HTTP exitosa (200)");
                
                $responseBody = $response->body();
                Log::info("📄 [Job] Cuerpo de respuesta (primeros 500 chars): " . substr($responseBody, 0, 500) . "...");
                
                $data = $response->json();
                Log::info("📋 [Job] JSON parseado correctamente");
                Log::info("📋 [Job] Campos disponibles en respuesta: " . implode(', ', array_keys($data)));
                
                // Buscar la respuesta en diferentes campos posibles (Ollama usa "respuesta")
                $processedContext = $data['respuesta'] ?? $data['response'] ?? $data['text'] ?? $data['message'] ?? null;
                
                Log::info("🔍 [Job] Campo 'respuesta' encontrado: " . (isset($data['respuesta']) ? 'SÍ' : 'NO'));
                Log::info("🔍 [Job] Campo 'response' encontrado: " . (isset($data['response']) ? 'SÍ' : 'NO'));
                Log::info("🔍 [Job] Campo 'text' encontrado: " . (isset($data['text']) ? 'SÍ' : 'NO'));
                Log::info("🔍 [Job] Campo 'message' encontrado: " . (isset($data['message']) ? 'SÍ' : 'NO'));

                if ($processedContext && !empty(trim($processedContext))) {
                    $processedContext = trim($processedContext);
                    Log::info("✅ [Job] Contexto procesado válido (" . strlen($processedContext) . " caracteres)");
                    
                    // Actualizar el registro en la base de datos
                    Log::info("💾 [Job] Buscando cliente Autoseo ID: {$this->autoseoId}");
                    $autoseo = Autoseo::find($this->autoseoId);
                    
                    if ($autoseo) {
                        Log::info("✅ [Job] Cliente encontrado: {$autoseo->client_name}");
                        
                        $autoseo->company_context = $processedContext;
                        $autoseo->save();

                        Log::info("✅ [Job] Contexto actualizado en base de datos");
                        Log::info("📝 [Job] Resultado final: " . substr($processedContext, 0, 300) . "...");
                        Log::info("========================================");
                        Log::info("🎉 [Job] FIN - Procesamiento exitoso");
                        Log::info("========================================");
                    } else {
                        Log::error("❌ [Job] No se encontró el cliente Autoseo ID: {$this->autoseoId}");
                        Log::error("========================================");
                        
                        // Lanzar excepción para que el job se marque como fallido
                        throw new \Exception("No se encontró el cliente Autoseo ID: {$this->autoseoId}");
                    }
                } else {
                    Log::error("❌ [Job] La IA devolvió un contexto vacío o inválido");
                    Log::error("📝 [Job] Valor de processedContext: " . var_export($processedContext, true));
                    Log::error("📝 [Job] Respuesta completa de IA: " . json_encode($data, JSON_PRETTY_PRINT));
                    Log::error("========================================");
                    
                    // Lanzar excepción para que el job se marque como fallido
                    throw new \Exception("La IA devolvió un contexto vacío o inválido. Respuesta: " . json_encode($data));
                }
            } else {
                Log::error("========================================");
                Log::error("❌ [Job] ERROR HTTP - Respuesta no exitosa");
                Log::error("========================================");
                Log::error("❌ [Job] Status Code: " . $response->status());
                Log::error("❌ [Job] Reason: " . $response->reason());
                Log::error("❌ [Job] URL: https://aiapi.hawkins.es/chat");
                Log::error("❌ [Job] Modelo: gpt-oss:120b-cloud");
                Log::error("❌ [Job] Cuerpo de respuesta completo: " . $response->body());
                Log::error("❌ [Job] Headers de respuesta: " . json_encode($response->headers(), JSON_PRETTY_PRINT));
                Log::error("========================================");
                
                // Lanzar excepción para que el job se marque como fallido
                throw new \Exception("Error al comunicar con IA: HTTP " . $response->status() . " - " . $response->body());
            }

        } catch (\Exception $e) {
            Log::error("========================================");
            Log::error("💥 [Job] EXCEPCIÓN CAPTURADA");
            Log::error("========================================");
            Log::error("❌ [Job] Tipo de excepción: " . get_class($e));
            Log::error("❌ [Job] Mensaje: " . $e->getMessage());
            Log::error("❌ [Job] Archivo: " . $e->getFile());
            Log::error("❌ [Job] Línea: " . $e->getLine());
            Log::error("❌ [Job] Intento actual: " . $this->attempts());
            Log::error("❌ [Job] Máximo de intentos: " . $this->tries);
            Log::error("📜 [Job] Stack trace:");
            Log::error($e->getTraceAsString());
            Log::error("========================================");
            
            // Relanzar la excepción para que Laravel marque el job como fallido
            // y lo reintente según la configuración
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("========================================");
        Log::error("☠️ [Job] JOB FALLIDO DEFINITIVAMENTE");
        Log::error("========================================");
        Log::error("❌ [Job] Autoseo ID: {$this->autoseoId}");
        Log::error("❌ [Job] Se agotaron los " . $this->tries . " intentos");
        Log::error("❌ [Job] Último error: " . $exception->getMessage());
        Log::error("❌ [Job] Tipo de excepción: " . get_class($exception));
        Log::error("📝 [Job] Texto original guardado (no se pudo procesar con IA)");
        Log::error("========================================");
        // El contexto original ya está guardado, no hacer nada más
    }
}
