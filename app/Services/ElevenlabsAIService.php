<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ElevenlabsConversation;
use Exception;

class ElevenlabsAIService
{
    protected $aiServiceUrl;
    protected $aiApiKey;
    protected $aiModel;
    protected $timeout;
    protected $retryAttempts;
    protected $retryDelay;

    public function __construct()
    {
        $this->aiServiceUrl = config('elevenlabs.ai_service_url');
        $this->aiApiKey = config('elevenlabs.ai_api_key');
        $this->aiModel = config('elevenlabs.ai_model');
        $this->timeout = config('elevenlabs.timeout', 30);
        $this->retryAttempts = config('elevenlabs.retry_attempts', 3);
        $this->retryDelay = config('elevenlabs.retry_delay', 5);
    }

    /**
     * Procesar una conversación completa (categorizar + resumir)
     */
    public function processConversation(ElevenlabsConversation $conversation): bool
    {
        try {
            $conversation->markAsProcessing();

            // Primera pasada: Categorización
            $categorizationResult = $this->categorizeConversation($conversation->transcript);
            
            if (!$categorizationResult) {
                Log::error('No se pudo categorizar la conversación', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                $conversation->markAsFailed();
                return false;
            }

            // Actualizar categoría y confianza
            $conversation->category = $categorizationResult['category'] ?? null;
            $conversation->confidence_score = $categorizationResult['confidence'] ?? null;
            $conversation->save();

            Log::info('Conversación categorizada', [
                'conversation_id' => $conversation->conversation_id,
                'category' => $conversation->category,
                'confidence' => $conversation->confidence_score,
            ]);

            // Segunda pasada: Resumen
            $summary = $this->summarizeConversation($conversation->transcript);
            
            if (!$summary) {
                Log::warning('No se pudo generar resumen de la conversación', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                // No fallamos la conversación completa si solo falla el resumen
            } else {
                $conversation->summary_es = $summary;
                $conversation->save();

                Log::info('Resumen generado', [
                    'conversation_id' => $conversation->conversation_id,
                    'summary_length' => strlen($summary),
                ]);
            }

            $conversation->markAsCompleted();
            return true;

        } catch (Exception $e) {
            Log::error('Error al procesar conversación', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $conversation->markAsFailed();
            return false;
        }
    }

    /**
     * Categorizar una conversación usando IA
     */
    public function categorizeConversation(string $transcript): ?array
    {
        if (empty($transcript)) {
            Log::warning('Transcripción vacía para categorización');
            return null;
        }

        $prompt = str_replace('{transcript}', $transcript, config('elevenlabs.prompts.categorization'));

        $response = $this->sendChatRequest($prompt);

        if (!$response) {
            return null;
        }

        return $this->parseCategorizationResponse($response);
    }

    /**
     * Generar resumen de una conversación usando IA
     */
    public function summarizeConversation(string $transcript): ?string
    {
        if (empty($transcript)) {
            Log::warning('Transcripción vacía para resumen');
            return null;
        }

        $prompt = str_replace('{transcript}', $transcript, config('elevenlabs.prompts.summarization'));

        $response = $this->sendChatRequest($prompt);

        if (!$response) {
            return null;
        }

        return $this->parseSummarizationResponse($response);
    }

    /**
     * Enviar petición a la IA local
     */
    protected function sendChatRequest(string $prompt): ?string
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->retryAttempts) {
            try {
                $attempt++;

                Log::debug('Enviando petición a IA local', [
                    'attempt' => $attempt,
                    'url' => $this->aiServiceUrl,
                    'model' => $this->aiModel,
                    'prompt_length' => strlen($prompt),
                ]);

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->aiApiKey,
                ])
                ->withOptions(['verify' => false]) // Deshabilitar verificación SSL
                ->timeout($this->timeout)
                ->post($this->aiServiceUrl, [
                    'modelo' => $this->aiModel,
                    'prompt' => $prompt,
                    // Sin temperature ni max_tokens según lo solicitado
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    Log::debug('Respuesta recibida de IA local', [
                        'status' => $response->status(),
                        'data' => $data,
                    ]);

                    // La respuesta puede venir en diferentes formatos
                    if (isset($data['response'])) {
                        return $data['response'];
                    }
                    
                    if (isset($data['text'])) {
                        return $data['text'];
                    }

                    if (isset($data['message'])) {
                        return $data['message'];
                    }

                    if (is_string($data)) {
                        return $data;
                    }

                    // Si es un objeto/array, intentar extraer el contenido
                    if (isset($data['content'])) {
                        return $data['content'];
                    }

                    // Si nada de lo anterior, devolver como JSON
                    return json_encode($data);
                }

                Log::warning('Respuesta no exitosa de IA local', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'attempt' => $attempt,
                ]);

            } catch (Exception $e) {
                $lastException = $e;
                
                Log::warning('Error en petición a IA local', [
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);
            }

            // Esperar antes de reintentar
            if ($attempt < $this->retryAttempts) {
                sleep($this->retryDelay);
            }
        }

        Log::error('No se pudo obtener respuesta de IA local después de varios intentos', [
            'attempts' => $this->retryAttempts,
            'last_error' => $lastException ? $lastException->getMessage() : 'Unknown',
        ]);

        return null;
    }

    /**
     * Parsear respuesta de categorización
     */
    protected function parseCategorizationResponse(string $response): ?array
    {
        try {
            // Intentar parsear como JSON
            $data = json_decode($response, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($data['category'])) {
                // Normalizar el nombre de la categoría
                $category = $this->normalizeCategory($data['category']);
                
                return [
                    'category' => $category,
                    'confidence' => $data['confidence'] ?? 0.5,
                    'reason' => $data['reason'] ?? null,
                ];
            }

            // Si no es JSON válido, intentar extraer la categoría del texto
            return $this->extractCategoryFromText($response);

        } catch (Exception $e) {
            Log::error('Error al parsear respuesta de categorización', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            return null;
        }
    }

    /**
     * Parsear respuesta de resumen
     */
    protected function parseSummarizationResponse(string $response): ?string
    {
        // El resumen debería venir como texto plano
        $summary = trim($response);

        // Limpiar posibles markers JSON si la IA los incluye
        $summary = preg_replace('/^```json\s*/', '', $summary);
        $summary = preg_replace('/\s*```$/', '', $summary);
        $summary = preg_replace('/^```\s*/', '', $summary);

        // Si está vacío o es muy corto, no es válido
        if (strlen($summary) < 20) {
            Log::warning('Resumen demasiado corto o vacío', [
                'summary' => $summary,
            ]);
            return null;
        }

        return $summary;
    }

    /**
     * Normalizar nombre de categoría
     */
    protected function normalizeCategory(string $category): string
    {
        $category = strtolower(trim($category));
        
        // Mapeo de posibles variaciones a categorías válidas
        $mappings = [
            'contento' => 'contento',
            'satisfecho' => 'contento',
            'feliz' => 'contento',
            'descontento' => 'descontento',
            'insatisfecho' => 'descontento',
            'molesto' => 'descontento',
            'pregunta' => 'pregunta',
            'consulta' => 'pregunta',
            'duda' => 'pregunta',
            'necesita_asistencia' => 'necesita_asistencia',
            'necesita asistencia' => 'necesita_asistencia',
            'asistencia_extra' => 'necesita_asistencia',
            'escalado' => 'necesita_asistencia',
            'queja' => 'queja',
            'reclamo' => 'queja',
            'baja' => 'baja',
            'cancelacion' => 'baja',
            'cancelación' => 'baja',
        ];

        return $mappings[$category] ?? 'pregunta'; // Default a 'pregunta'
    }

    /**
     * Extraer categoría de texto cuando no viene en JSON
     */
    protected function extractCategoryFromText(string $text): ?array
    {
        $text = strtolower($text);
        
        $categories = [
            'contento',
            'descontento',
            'pregunta',
            'necesita_asistencia',
            'queja',
            'baja',
        ];

        foreach ($categories as $category) {
            if (strpos($text, $category) !== false) {
                return [
                    'category' => $category,
                    'confidence' => 0.5, // Confianza baja porque no es formato JSON
                    'reason' => 'Extraído del texto',
                ];
            }
        }

        Log::warning('No se pudo extraer categoría del texto', [
            'text' => $text,
        ]);

        return null;
    }

    /**
     * Verificar conectividad con la IA
     */
    public function testConnection(): bool
    {
        try {
            $response = $this->sendChatRequest('Test de conexión');
            return !empty($response);
        } catch (Exception $e) {
            Log::error('Error al probar conexión con IA local', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

