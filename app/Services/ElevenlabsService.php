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

            Log::info('🔍 Obteniendo conversaciones de Eleven Labs', [
                'url' => $url,
                'params' => $params,
                'from_timestamp' => $fromTimestamp,
                'cursor' => $cursor ? substr($cursor, 0, 20) . '...' : null,
            ]);

            $response = Http::withHeaders($this->getHeaders())
                ->withOptions(['verify' => false])
                ->timeout((int) $this->timeout)
                ->get($url, $params);

            $data = $response->json();

            Log::info('📥 Respuesta recibida', [
                'status' => $response->status(),
                'has_conversations' => isset($data['conversations']),
                'count' => count($data['conversations'] ?? []),
                'has_more' => $data['has_more'] ?? false,
            ]);

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

            Log::info('🔍 Obteniendo detalles de conversación', [
                'conversation_id' => $conversationId,
                'url' => $url,
            ]);

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

            Log::info('✅ Conversación obtenida', [
                'conversation_id' => $conversationId,
                'has_transcript' => isset($data['transcript']),
                'message_count' => isset($data['transcript']) ? count($data['transcript']) : 0,
            ]);

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
            Log::info('👥 SINCRONIZANDO AGENTES...');

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

            Log::info("📋 Agentes recibidos: " . count($agents));

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
                Log::debug("  ✅ Agente sincronizado: {$name} ({$agentId})");
            }

            Log::info("✅ {$syncedCount} agentes sincronizados en BD local");
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

        $fromDateStr = $fromTimestamp ? Carbon::createFromTimestamp($fromTimestamp)->format('Y-m-d H:i:s') : 'todas';

        Log::info('🚀 INICIANDO SINCRONIZACIÓN', [
            'from_timestamp' => $fromTimestamp,
            'from_date' => $fromDateStr,
            'max_pages' => $maxPages,
            'batch_size' => $limit,
        ]);

        // PRIMERO: Sincronizar agentes para tener el caché local actualizado
        $this->syncAgents();

        try {
            do {
                Log::info("📄 === PÁGINA {$page}/{$maxPages} ===");

                $response = $this->getConversations($page, $limit, $fromTimestamp);

                $conversations = $response['conversations'] ?? [];

                Log::info("📋 Conversaciones en página {$page}: " . count($conversations));

                foreach ($conversations as $index => $convData) {
                    $convId = $convData['conversation_id'] ?? 'unknown';
                    Log::info("  📞 [" . ($index + 1) . "/" . count($conversations) . "] Procesando: {$convId}");

                    try {
                        $result = $this->saveConversation($convData);
                        $stats['total']++;

                        if ($result['created']) {
                            $stats['new']++;
                            Log::info("    ✅ NUEVA conversación guardada");
                        } else {
                            $stats['updated']++;
                            Log::info("    🔄 Conversación ACTUALIZADA");
                        }
                    } catch (Exception $e) {
                        Log::error("    ❌ Error guardando conversación {$convId}", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

                $hasMore = $response['has_more'] ?? false;
                Log::info("📊 Página {$page} completada. ¿Hay más? " . ($hasMore ? 'SÍ' : 'NO'));
                Log::info("📈 Stats actuales: Total={$stats['total']}, Nuevas={$stats['new']}, Actualizadas={$stats['updated']}");

                $page++;

            } while ($hasMore && count($conversations) > 0 && $page <= $maxPages);

            Log::info('🎉 SINCRONIZACIÓN COMPLETADA', $stats);
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

        Log::debug("      🔍 Buscando si existe en BD: {$conversationId}");
        $conversation = ElevenlabsConversation::where('conversation_id', $conversationId)->first();
        $created = false;

        if (!$conversation) {
            $conversation = new ElevenlabsConversation();
            $created = true;
            Log::debug("      ➕ Nueva conversación - será creada");
        } else {
            Log::debug("      🔄 Conversación existente - será actualizada");
        }

        // Obtener detalles completos si no tenemos la transcripción
        $fullData = $conversationData;
        if (!isset($conversationData['transcript'])) {
            Log::info("      📥 Obteniendo detalles completos con transcripción...");
            try {
                $fullData = $this->getConversation($conversationId);
            } catch (Exception $e) {
                Log::warning('      ⚠️ No se pudo obtener detalles completos', [
                    'conversation_id' => $conversationId,
                    'error' => $e->getMessage(),
                ]);
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
                Log::debug("      👤 Agente (desde BD local): {$agentName}");
            } else {
                Log::warning("      ⚠️ Agente {$agentId} no encontrado en BD local");
            }
        }

        // Fecha
        $timestamp = $fullData['metadata']['start_time_unix_secs'] ?? $conversationData['start_time_unix_secs'] ?? time();
        $conversation->conversation_date = Carbon::createFromTimestamp($timestamp);
        Log::debug("      📅 Fecha: {$conversation->conversation_date->format('Y-m-d H:i:s')}");

        // Duración
        $conversation->duration_seconds = $fullData['metadata']['call_duration_secs'] ?? $conversationData['call_duration_secs'] ?? 0;
        Log::debug("      ⏱️ Duración: {$conversation->duration_seconds} segundos");

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
            $messageCount = count($fullData['transcript']);
            Log::info("      💬 Formateando transcripción con {$messageCount} mensajes");
            $conversation->transcript = $this->formatTranscript($fullData['transcript']);
            $transcriptProcessed = true;
        } elseif (isset($fullData['analysis']['transcript_summary']) && !empty($fullData['analysis']['transcript_summary'])) {
            Log::info("      📝 Usando transcript_summary del análisis");
            $conversation->transcript = $fullData['analysis']['transcript_summary'];
            $transcriptProcessed = true;
        } elseif (isset($fullData['analysis']['call_summary_title']) && !empty($fullData['analysis']['call_summary_title'])) {
            Log::warning("      ⚠️ Usando call_summary_title (no hay transcripción completa)");
            $conversation->transcript = "Resumen: " . $fullData['analysis']['call_summary_title'];
            $transcriptProcessed = true;
        }

        if (!$transcriptProcessed) {
            Log::warning("      ⚠️ NO SE ENCONTRÓ TRANSCRIPCIÓN");
        }

        $conversation->save();

        $this->linkConversationToCampaign($conversation, $fullData);
        Log::info("      💾 Conversación guardada en BD (ID: {$conversation->id})");

        return [
            'created' => $created,
            'conversation' => $conversation,
        ];
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
            [$campaignUid, $callUid] = $this->extractCampaignIdentifiers($fullData);

            if (!$campaignUid && !$callUid) {
                Log::debug('🔎 Conversación sin metadatos de campaña', [
                    'conversation_id' => $conversation->conversation_id,
                ]);
                return;
            }

            $campaign = $campaignUid
                ? ElevenlabsCampaign::where('uid', $campaignUid)->first()
                : null;

            if (!$campaign && $callUid) {
                $campaignCall = ElevenlabsCampaignCall::where('uid', $callUid)->first();
                $campaign = $campaignCall ? $campaignCall->campaign : null;
            }

            if (!$campaign) {
                Log::warning('⚠️ No se encontró la campaña vinculada', [
                    'conversation_id' => $conversation->conversation_id,
                    'campaign_uid' => $campaignUid,
                    'call_uid' => $callUid,
                ]);
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
                Log::warning('⚠️ No se encontró llamada asociada en la campaña', [
                    'conversation_id' => $conversation->conversation_id,
                    'campaign_id' => $campaign->id,
                ]);
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

            Log::info('🔗 Conversación vinculada a campaña', [
                'conversation_id' => $conversation->conversation_id,
                'campaign_id' => $campaign->id,
                'campaign_call_id' => $campaignCall->id,
            ]);
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

        if (!$campaignUid && isset($data['metadata']) && is_array($data['metadata'])) {
            $campaignUid = data_get($data['metadata'], 'conversation_initiation_client_data.metadata.crm_campaign_uid', $campaignUid);
            $callUid = data_get($data['metadata'], 'conversation_initiation_client_data.metadata.crm_call_uid', $callUid);
        }

        return [$campaignUid, $callUid];
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

