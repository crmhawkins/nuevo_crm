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
            Log::info("🤖 [Job] Procesando contexto para Autoseo ID: {$this->autoseoId} (" . strlen($this->originalContext) . " caracteres)");
            
            // Llamar a la IA (timeout de 100 segundos)
            $response = Http::timeout(100)
                ->withHeaders([
                    'X-Api-Key' => 'OllamaAPI_2024_K8mN9pQ2rS5tU7vW3xY6zA1bC4eF8hJ0lM',
                    'Content-Type' => 'application/json',
                ])
                ->post('https://aiapi.hawkins.es/chat/chat', [
                    'modelo' => 'gpt-oss:120b-cloud',
                    'prompt' => "Contexto de empresa a procesar:\n\n" . $this->originalContext . "\n\nINSTRUCCIONES:\n- Si el texto es demasiado largo (>1200 caracteres): Resúmelo manteniendo la información clave. Es vital que no inventes informacion, solo expande la existente, no te inventes la capacidad de la empresa, ni servicios, ni ubicaciones. Basate en el contexto existente.\n- Si el texto es muy corto (<800 caracteres): Amplíalo con detalles profesionales relevantes.\n- Objetivo: Aproximadamente 1000 caracteres.\n- IMPORTANTE: Devuelve ÚNICAMENTE el texto procesado, sin introducciones, sin explicaciones, sin frases como 'Aquí está el resumen' ni nada similar. Solo el texto final.\n\nTexto procesado:",
                    'stream' => false,
                    'temperature' => 0.7
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Buscar la respuesta en diferentes campos posibles (Ollama usa "respuesta")
                $processedContext = $data['respuesta'] ?? $data['response'] ?? $data['text'] ?? $data['message'] ?? null;

                if ($processedContext && !empty(trim($processedContext))) {
                    $processedContext = trim($processedContext);
                    
                    // Actualizar el registro en la base de datos
                    $autoseo = Autoseo::find($this->autoseoId);
                    if ($autoseo) {
                        $autoseo->company_context = $processedContext;
                        $autoseo->save();

                        Log::info("✅ [Job] Contexto procesado y actualizado (" . strlen($processedContext) . " caracteres)");
                    } else {
                        Log::error("❌ [Job] No se encontró el cliente Autoseo ID: {$this->autoseoId}");
                        throw new \Exception("No se encontró el cliente Autoseo ID: {$this->autoseoId}");
                    }
                } else {
                    Log::error("❌ [Job] La IA devolvió un contexto vacío. Respuesta: " . json_encode($data));
                    throw new \Exception("La IA devolvió un contexto vacío o inválido");
                }
            } else {
                Log::error("❌ [Job] Error HTTP " . $response->status() . " - " . $response->body());
                throw new \Exception("Error al comunicar con IA: HTTP " . $response->status());
            }

        } catch (\Exception $e) {
            Log::error("❌ [Job] Error: " . $e->getMessage() . " (Intento " . $this->attempts() . "/" . $this->tries . ")");
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("☠️ [Job] Fallido definitivamente para Autoseo ID: {$this->autoseoId} - " . $exception->getMessage());
    }
}
