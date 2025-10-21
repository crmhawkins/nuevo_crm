<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ElevenlabsConversation;
use Carbon\Carbon;
use Exception;

class ElevenlabsService
{
    protected $apiKey;
    protected $apiUrl;
    protected $apiVersion;
    protected $timeout;

    public function __construct()
    {
        $this->apiKey = config('elevenlabs.api_key');
        $this->apiUrl = config('elevenlabs.api_url');
        $this->apiVersion = config('elevenlabs.api_version', 'v1');
        $this->timeout = config('elevenlabs.timeout', 30);
    }

    /**
     * Obtener lista de conversaciones
     */
    public function getConversations(int $page = 1, int $limit = 100, ?string $fromDate = null): array
    {
        try {
            $params = [
                'page' => $page,
                'page_size' => $limit,
            ];

            if ($fromDate) {
                $params['from_date'] = $fromDate;
            }

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false]) // Deshabilitar verificación SSL
                ->timeout($this->timeout)
                ->get($this->buildUrl('/convai/conversations'), $params);

            if (!$response->successful()) {
                Log::error('Error al obtener conversaciones de Eleven Labs', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Error al obtener conversaciones: ' . $response->status());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Excepción al obtener conversaciones', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener detalles de una conversación específica
     */
    public function getConversation(string $conversationId): array
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false]) // Deshabilitar verificación SSL
                ->timeout($this->timeout)
                ->get($this->buildUrl("/convai/conversations/{$conversationId}"));

            if (!$response->successful()) {
                Log::error('Error al obtener conversación de Eleven Labs', [
                    'conversation_id' => $conversationId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Error al obtener conversación: ' . $response->status());
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('Excepción al obtener conversación', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener transcripción de una conversación
     */
    public function getTranscript(string $conversationId): ?string
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false]) // Deshabilitar verificación SSL
                ->timeout($this->timeout)
                ->get($this->buildUrl("/convai/conversations/{$conversationId}/transcript"));

            if (!$response->successful()) {
                Log::error('Error al obtener transcripción de Eleven Labs', [
                    'conversation_id' => $conversationId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            
            // La transcripción puede venir en diferentes formatos según la API
            if (isset($data['transcript'])) {
                return $data['transcript'];
            }
            
            if (isset($data['text'])) {
                return $data['text'];
            }

            // Si es un array de mensajes, concatenarlos
            if (isset($data['messages']) && is_array($data['messages'])) {
                return $this->formatMessagesAsTranscript($data['messages']);
            }

            return json_encode($data);
        } catch (Exception $e) {
            Log::error('Excepción al obtener transcripción', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Sincronizar conversaciones desde Eleven Labs
     */
    public function syncConversations(?Carbon $fromDate = null): array
    {
        $page = 1;
        $limit = config('elevenlabs.batch_size', 100);
        $allConversations = [];
        $stats = [
            'total' => 0,
            'new' => 0,
            'updated' => 0,
        ];

        try {
            do {
                $response = $this->getConversations(
                    $page,
                    $limit,
                    $fromDate ? $fromDate->toIso8601String() : null
                );

                $conversations = $response['conversations'] ?? $response['data'] ?? [];
                
                foreach ($conversations as $convData) {
                    $result = $this->saveConversation($convData);
                    $stats['total']++;
                    
                    if ($result['created']) {
                        $stats['new']++;
                    } else {
                        $stats['updated']++;
                    }
                }

                $allConversations = array_merge($allConversations, $conversations);
                
                // Verificar si hay más páginas
                $hasMore = $response['has_more'] ?? false;
                $page++;

            } while ($hasMore && count($conversations) > 0);

            Log::info('Sincronización completada', $stats);
            return $stats;

        } catch (Exception $e) {
            Log::error('Error en sincronización de conversaciones', [
                'error' => $e->getMessage(),
                'stats' => $stats,
            ]);
            throw $e;
        }
    }

    /**
     * Guardar o actualizar una conversación en la base de datos
     */
    protected function saveConversation(array $conversationData): array
    {
        $conversationId = $conversationData['conversation_id'] ?? $conversationData['id'] ?? null;
        
        if (!$conversationId) {
            throw new Exception('conversation_id no encontrado en los datos');
        }

        $conversation = ElevenlabsConversation::where('conversation_id', $conversationId)->first();
        $created = false;

        if (!$conversation) {
            $conversation = new ElevenlabsConversation();
            $created = true;
        }

        // Mapear datos de la API a nuestro modelo
        $conversation->conversation_id = $conversationId;
        $conversation->conversation_date = $this->parseDate($conversationData['created_at'] ?? $conversationData['date'] ?? now());
        $conversation->duration_seconds = $conversationData['duration'] ?? $conversationData['duration_seconds'] ?? 0;
        $conversation->metadata = $conversationData;

        // Obtener transcripción si no existe
        if (empty($conversation->transcript)) {
            $transcript = $this->getTranscript($conversationId);
            $conversation->transcript = $transcript;
        }

        $conversation->save();

        return [
            'created' => $created,
            'conversation' => $conversation,
        ];
    }

    /**
     * Formatear mensajes como transcripción
     */
    protected function formatMessagesAsTranscript(array $messages): string
    {
        $transcript = '';
        
        foreach ($messages as $message) {
            $speaker = $message['speaker'] ?? $message['role'] ?? 'Unknown';
            $text = $message['text'] ?? $message['content'] ?? '';
            $transcript .= "{$speaker}: {$text}\n";
        }

        return trim($transcript);
    }

    /**
     * Parsear fecha de diferentes formatos
     */
    protected function parseDate($date): Carbon
    {
        if ($date instanceof Carbon) {
            return $date;
        }

        try {
            return Carbon::parse($date);
        } catch (Exception $e) {
            return now();
        }
    }

    /**
     * Construir URL completa
     */
    protected function buildUrl(string $endpoint): string
    {
        $baseUrl = rtrim($this->apiUrl, '/');
        $version = $this->apiVersion;
        $endpoint = ltrim($endpoint, '/');

        return "{$baseUrl}/{$version}/{$endpoint}";
    }

    /**
     * Obtener headers para las peticiones
     */
    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'xi-api-key' => $this->apiKey,
        ];
    }

    /**
     * Verificar conectividad con la API
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false]) // Deshabilitar verificación SSL
                ->timeout(10)
                ->get($this->buildUrl('/user'));

            return $response->successful();
        } catch (Exception $e) {
            Log::error('Error al probar conexión con Eleven Labs', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

