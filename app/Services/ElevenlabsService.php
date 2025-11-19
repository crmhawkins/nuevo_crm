<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\ElevenlabsCampaign;
use App\Models\ElevenlabsCampaignCall;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
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
     * Obtener lista de conversaciones con paginación por cursor
     * Endpoint: GET /v1/convai/conversations
     */
    public function getConversations(?int $fromTimestamp = null, ?string $cursor = null, int $pageSize = 100): array
    {
        try {
            $params = [
                'page_size' => min($pageSize, 100), // Máximo 100 según la API
            ];

            // Usar call_start_after_unix según documentación oficial
            if ($fromTimestamp) {
                $params['call_start_after_unix'] = $fromTimestamp;
            }

            // Cursor para paginación
            if ($cursor) {
                $params['cursor'] = $cursor;
            }

            $url = $this->buildUrl('/convai/conversations');

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout((int) $this->timeout)
                ->get($url, $params);

            $data = $response->json();

            if (!$response->successful()) {
                Log::error('❌ Error al obtener conversaciones', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Error al obtener conversaciones: ' . $response->status());
            }

            return $data;
        } catch (Exception $e) {
            Log::error('❌ Excepción al obtener conversaciones', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtener detalles completos de una conversación (incluye transcript)
     * Endpoint: GET /v1/convai/conversations/{conversation_id}
     */
    public function getConversation(string $conversationId): array
    {
        try {
            $url = $this->buildUrl("/convai/conversations/{$conversationId}");

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout((int) $this->timeout)
                ->get($url);

            if (!$response->successful()) {
                Log::error('❌ Error al obtener conversación', [
                    'conversation_id' => $conversationId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Error al obtener conversación: ' . $response->status());
            }

            $data = $response->json();

            return $data;
        } catch (Exception $e) {
            Log::error('❌ Excepción al obtener conversación', [
                'conversation_id' => $conversationId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Sincronizar agentes primero (para caché local)
     * Endpoint: GET /v1/convai/agents
     */
    public function syncAgents(): int
    {
        try {
            $url = $this->buildUrl('/convai/agents');
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout((int) $this->timeout)
                ->get($url);

            if (!$response->successful()) {
                Log::error('❌ Error al obtener agentes', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return 0;
            }

            $data = $response->json();
            $agents = $data['agents'] ?? [];

            $syncedCount = 0;
            foreach ($agents as $agentData) {
                $agentId = $agentData['agent_id'] ?? null;
                $name = $agentData['name'] ?? null;

                if (!$agentId || !$name) {
                    continue;
                }

                $agent = ElevenlabsAgent::updateOrCreate(
                    ['agent_id' => $agentId],
                    [
                        'name' => $name,
                        'archived' => $agentData['archived'] ?? false,
                        'last_call_time_unix_secs' => $agentData['last_call_time_unix_secs'] ?? null,
                        'metadata' => $agentData,
                    ]
                );

                $syncedCount++;
            }

            return $syncedCount;

        } catch (Exception $e) {
            Log::error('❌ Error sincronizando agentes', [
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Sincronizar conversaciones desde Eleven Labs (MANUAL)
     */
    public function syncConversations(?int $fromTimestamp = null, int $maxPages = 10): array
    {
        $page = 1;
        $limit = config('elevenlabs.batch_size', 100);
        $stats = [
            'total' => 0,
            'new' => 0,
            'updated' => 0,
        ];

        // PRIMERO: Sincronizar agentes para tener el caché local actualizado
        $this->syncAgents();

        try {
            do {
                $response = $this->getConversations($page, $limit, $fromTimestamp);

                $conversations = $response['conversations'] ?? [];

                foreach ($conversations as $index => $convData) {
                    try {
                        $result = $this->saveConversation($convData);
                        $stats['total']++;

                        if ($result['created']) {
                            $stats['new']++;
                        } else {
                            $stats['updated']++;
                        }
                    } catch (Exception $e) {
                        Log::error("    ❌ Error guardando conversación", [
                            'conversation_id' => $convData['conversation_id'] ?? 'unknown',
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $hasMore = $response['has_more'] ?? false;

                $page++;

            } while ($hasMore && count($conversations) > 0 && $page <= $maxPages);

            return $stats;

        } catch (Exception $e) {
            Log::error('❌ ERROR FATAL EN SINCRONIZACIÓN', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'stats' => $stats,
            ]);
            throw $e;
        }
    }

    /**
     * Guardar conversación en BD
     */
    protected function saveConversation(array $conversationData): array
    {
        $conversationId = $conversationData['conversation_id'] ?? null;

        if (!$conversationId) {
            throw new Exception('conversation_id no encontrado');
        }

        $conversation = ElevenlabsConversation::where('conversation_id', $conversationId)->first();
        $created = false;

        if (!$conversation) {
            $conversation = new ElevenlabsConversation();
            $created = true;
        }

        // Obtener detalles completos si no tenemos la transcripción
        $fullData = $conversationData;
        if (!isset($conversationData['transcript'])) {
            try {
                $fullData = $this->getConversation($conversationId);
            } catch (Exception $e) {
                // Log eliminado para evitar saturar los logs
            }
        }

        // Mapear campos
        $conversation->conversation_id = $conversationId;

        // Obtener información del agente desde caché local (BD)
        $agentId = $fullData['agent_id'] ?? $conversationData['agent_id'] ?? null;
        if ($agentId) {
            $conversation->agent_id = $agentId;

            // Buscar nombre del agente en la tabla local (sin hacer petición a API)
            $agentName = ElevenlabsAgent::getNameByAgentId($agentId);
            if ($agentName) {
                $conversation->agent_name = $agentName;
            }
        }

        // Fecha
        $timestamp = $fullData['metadata']['start_time_unix_secs'] ?? $conversationData['start_time_unix_secs'] ?? time();
        $conversation->conversation_date = Carbon::createFromTimestamp($timestamp);

        // Duración
        $conversation->duration_seconds = $fullData['metadata']['call_duration_secs'] ?? $conversationData['call_duration_secs'] ?? 0;

        // Número de teléfono asociado (si está disponible)
        $phoneNumber = $this->extractPhoneNumber($fullData);
        if ($phoneNumber && empty($conversation->numero)) {
            $conversation->numero = $phoneNumber;
        }

        // Metadata completa
        $conversation->metadata = $fullData;

        // Procesar transcripción
        $transcriptProcessed = false;
        if (isset($fullData['transcript']) && is_array($fullData['transcript'])) {
            $conversation->transcript = $this->formatTranscript($fullData['transcript']);
            $transcriptProcessed = true;
        } elseif (isset($fullData['analysis']['transcript_summary']) && !empty($fullData['analysis']['transcript_summary'])) {
            $conversation->transcript = $fullData['analysis']['transcript_summary'];
            $transcriptProcessed = true;
        } elseif (isset($fullData['analysis']['call_summary_title']) && !empty($fullData['analysis']['call_summary_title'])) {
            $conversation->transcript = "Resumen: " . $fullData['analysis']['call_summary_title'];
            $transcriptProcessed = true;
        }

        $conversation->save();

        $this->linkConversationToCampaign($conversation, $fullData);

        return [
            'created' => $created,
            'conversation' => $conversation,
        ];
    }

    public function linkExistingConversation(ElevenlabsConversation $conversation): bool
    {
        $beforeCampaign = $conversation->campaign_id;
        $beforeCall = $conversation->campaign_call_id;

        $metadataPayload = $conversation->metadata;

        if ($metadataPayload && !is_array($metadataPayload)) {
            $metadataPayload = (array) $metadataPayload;
        }

        if (!empty($metadataPayload)) {
            $this->linkConversationToCampaign($conversation, $metadataPayload);
        }

        /** @var ElevenlabsConversation|null $conversationAfter */
        $conversationAfter = $conversation->fresh();

        if (!$conversationAfter || !$conversationAfter->campaign_call_id) {
            try {
                $fullData = $this->getConversation($conversation->conversation_id);
                $result = $this->saveConversation($fullData);
                $conversationAfter = $result['conversation']->fresh();
            } catch (Exception $e) {
                // Log eliminado para evitar saturar los logs
                return false;
            }
        }

        if ($conversationAfter instanceof ElevenlabsConversation && $conversationAfter->campaignCall) {
            $campaignCall = $conversationAfter->campaignCall;
            $campaignCall->sentiment_category = $conversationAfter->sentiment_category;
            $campaignCall->specific_category = $conversationAfter->specific_category;
            $campaignCall->confidence_score = $conversationAfter->confidence_score;
            $campaignCall->summary = $conversationAfter->summary_es;
            $campaignCall->save();

            if ($campaignCall->campaign) {
                $campaignCall->campaign->refreshCounters();
            }
        }

        return $conversationAfter instanceof ElevenlabsConversation
            && $conversationAfter->campaign_call_id
            && (
                $conversationAfter->campaign_call_id !== $beforeCall
                || $conversationAfter->campaign_id !== $beforeCampaign
            );
    }

    protected function extractPhoneNumber(array $data): ?string
    {
        $paths = [
            'customer_phone_number',
            'phone_number',
            'metadata.customer_phone_number',
            'metadata.phone_number',
            'metadata.customer.phone_number',
            'metadata.recipient.phone_number',
            'metadata.call_details.customer_phone_number',
            'metadata.call_details.phone_number',
            'metadata.conversation.customer_phone_number',
            'metadata.conversation.phone_number',
            'metadata.crm_phone_number',
            'metadata.conversation_initiation_client_data.metadata.crm_phone_number',
            'metadata.phone_call.external_number',
            'metadata.phone_call.customer_phone_number',
        ];

        foreach ($paths as $path) {
            $value = data_get($data, $path);
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    protected function linkConversationToCampaign(ElevenlabsConversation $conversation, array $fullData): void
    {
        try {
            $identifiers = $this->extractCampaignIdentifiers($fullData);
            $campaignUid = $identifiers['campaign_uid'] ?? null;
            $callUid = $identifiers['call_uid'] ?? null;
            $batchCallId = $identifiers['batch_call_id'] ?? null;
            $batchCallRecipientId = $identifiers['batch_call_recipient_id'] ?? null;
            $phoneFromData = $conversation->numero ?: $this->extractPhoneNumber($fullData);

            if ($phoneFromData && empty($conversation->numero)) {
                $conversation->numero = $phoneFromData;
                $conversation->save();
            }

            // Log eliminado para evitar saturar los logs

            $campaign = $campaignUid
                ? ElevenlabsCampaign::where('uid', $campaignUid)->first()
                : null;

            if (!$campaign && $batchCallId) {
                $campaign = ElevenlabsCampaign::where('external_batch_id', $batchCallId)->first();
            }

            if (!$campaign && $callUid) {
                $campaignCall = ElevenlabsCampaignCall::where('uid', $callUid)->first();
                $campaign = $campaignCall ? $campaignCall->campaign : null;
            }

            if (!$campaign && $batchCallId) {
                $campaignCall = ElevenlabsCampaignCall::where(function ($query) use ($batchCallId) {
                    $query->where('metadata->metadata->batch_call.batch_call_id', $batchCallId)
                        ->orWhere('metadata->batch_call.batch_call_id', $batchCallId)
                        ->orWhere('metadata->batch_call_id', $batchCallId);
                })
                    ->orderByDesc('id')
                    ->first();
                if ($campaignCall) {
                    $campaign = $campaignCall->campaign;
                }
            }

            if (!$campaign && $phoneFromData) {
                $campaignCall = ElevenlabsCampaignCall::where('phone_number', $phoneFromData)
                    ->whereNull('eleven_conversation_internal_id')
                    ->orderByDesc('id')
                    ->first();
                $campaign = $campaignCall ? $campaignCall->campaign : null;
            }

            if (!$campaign) {
                // Log eliminado para evitar saturar los logs
                return;
            }

            $campaignCall = null;

            if ($callUid) {
                $campaignCall = $campaign->calls()->where('uid', $callUid)->first();
            }

            if (!$campaignCall && $conversation->numero) {
                $campaignCall = $campaign->calls()
                    ->where('phone_number', $conversation->numero)
                    ->whereNull('eleven_conversation_internal_id')
                    ->orderBy('id')
                    ->first();
            }

            if (!$campaignCall) {
                $campaignCall = $campaign->calls()
                    ->whereNull('eleven_conversation_internal_id')
                    ->orderBy('id')
                    ->first();
            }

            if (!$campaignCall) {
                // Log eliminado para evitar saturar los logs
                return;
            }

            $conversation->campaign_id = $campaign->id;
            $conversation->campaign_call_id = $campaignCall->id;
            $conversation->campaign_initial_prompt = $campaignCall->custom_prompt ?? $campaign->initial_prompt;
            $conversation->save();

            $campaignCall->eleven_conversation_id = $conversation->conversation_id;
            $campaignCall->eleven_conversation_internal_id = $conversation->id;
            $campaignCall->save();

            $campaign->refreshCounters();

        } catch (Exception $e) {
            Log::error('❌ Error vinculando conversación con campaña', [
                'conversation_id' => $conversation->conversation_id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function extractCampaignIdentifiers(array $data): array
    {
        $campaignUid = data_get($data, 'conversation_initiation_client_data.metadata.crm_campaign_uid');
        $callUid = data_get($data, 'conversation_initiation_client_data.metadata.crm_call_uid');
        $batchCallId = data_get($data, 'metadata.batch_call.batch_call_id');
        $batchCallRecipientId = data_get($data, 'metadata.batch_call.batch_call_recipient_id');

        if (!$campaignUid && isset($data['metadata']) && is_array($data['metadata'])) {
            $campaignUid = data_get($data['metadata'], 'conversation_initiation_client_data.metadata.crm_campaign_uid', $campaignUid);
            $callUid = data_get($data['metadata'], 'conversation_initiation_client_data.metadata.crm_call_uid', $callUid);
        }

        return [
            'campaign_uid' => $campaignUid,
            'call_uid' => $callUid,
            'batch_call_id' => $batchCallId,
            'batch_call_recipient_id' => $batchCallRecipientId,
        ];
    }

    /**
     * Formatear transcripción según estructura real de Eleven Labs
     * Según: https://api.elevenlabs.io/v1/convai/conversations/{conv_id}
     */
    protected function formatTranscript(array $messages): string
    {
        $transcript = '';

        foreach ($messages as $msg) {
            $role = $msg['role'] ?? 'unknown';
            $message = $msg['message'] ?? '';
            $time = $msg['time_in_call_secs'] ?? null;

            // Si el mensaje está vacío, es probable que sea una llamada a herramienta
            if (empty($message)) {
                if (isset($msg['tool_calls']) && !empty($msg['tool_calls'])) {
                    $message = '[Llamó a herramienta: ' . ($msg['tool_calls'][0]['tool_name'] ?? 'unknown') . ']';
                } elseif (isset($msg['tool_results']) && !empty($msg['tool_results'])) {
                    $message = '[Resultado de herramienta recibido]';
                } else {
                    continue; // Omitir mensajes vacíos sin herramientas
                }
            }

            // Formatear con timestamp
            if ($time !== null) {
                $minutes = floor($time / 60);
                $seconds = $time % 60;
                $timeStr = sprintf('[%02d:%02d]', $minutes, $seconds);
                $transcript .= "{$timeStr} {$role}: {$message}\n";
            } else {
                $transcript .= "{$role}: {$message}\n";
            }
        }

        return trim($transcript);
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
     * Headers para peticiones
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
     * Verificar conectividad
     */
    public function testConnection(): bool
    {
        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout(10)
                ->get($this->buildUrl('/user'));

            return $response->successful();
        } catch (Exception $e) {
            Log::error('❌ Error al probar conexión', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

