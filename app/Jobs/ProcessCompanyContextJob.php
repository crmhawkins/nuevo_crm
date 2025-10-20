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
                ->post('https://aiapi.hawkins.es/chat/chat', [
                    'modelo' => 'gpt-oss:120b-cloud',
                    'prompt' => "CONTEXTO DEL SISTEMA:\nEres un procesador de información empresarial. El texto que vas a procesar es una descripción de una empresa que será usada como CONTEXTO DE REFERENCIA por otra IA de generación de contenido SEO.\n\nCuando la IA de contenido reciba este contexto, lo usará para:\n- Escribir artículos de blog relacionados con los servicios de la empresa\n- Crear descripciones de productos/servicios\n- Generar contenido optimizado para SEO\n- Responder preguntas sobre la empresa\n\nPor lo tanto, este texto debe ser FACTUAL, COMPLETO y ESTRUCTURADO para que la IA de contenido NO tenga que inventar información cuando genere textos.\n\nTEXTO ORIGINAL A PROCESAR:\n\n" . $this->originalContext . "\n\nTU TAREA:\nOptimiza este texto para que sirva como contexto de referencia claro y útil. La IA de contenido necesita saber exactamente:\n- Qué servicios ofrece la empresa (específicos, no genéricos)\n- Dónde opera (ciudades, regiones, países)\n- Qué especialidades o sectores cubre\n- Cualquier dato factual relevante (años de experiencia, certificaciones, etc.)\n\nREGLAS ESTRICTAS:\n❌ NO inventes servicios, ubicaciones, capacidades o datos que no estén en el texto original\n❌ NO añadas información que no esté explícita o claramente implícita\n❌ NO uses lenguaje promocional o de ventas\n❌ NO exageres ni embellezas la información\n\n✅ SÍ reformula para mayor claridad\n✅ SÍ estructura la información de forma lógica\n✅ SÍ corrige errores gramaticales\n✅ SÍ mantén TODOS los datos factuales del original\n\nLONGITUD:\n- Si >1200 caracteres: Resume pero mantén TODOS los servicios, ubicaciones y datos clave\n- Si <800 caracteres: Amplía SOLO clarificando o contextualizando lo ya mencionado\n- Objetivo: ~1000 caracteres, texto neutro, factual y estructurado\n\nFORMATO DE SALIDA:\nDevuelve ÚNICAMENTE el texto optimizado, sin introducciones, sin 'Aquí está...', sin explicaciones. Solo el contexto procesado.\n\nTexto optimizado:",
                    'stream' => false,
                    'temperature' => 0.3
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
