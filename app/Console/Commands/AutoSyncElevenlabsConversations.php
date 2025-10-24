<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElevenlabsService;
use App\Services\ElevenlabsAIService;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Log;

class AutoSyncElevenlabsConversations extends Command
{
    protected $signature = 'elevenlabs:auto-sync';
    protected $description = 'Sincroniza automáticamente conversaciones nuevas de Eleven Labs cada 10 minutos';

    private $elevenlabsService;
    private $aiService;

    public function __construct(ElevenlabsService $elevenlabsService, ElevenlabsAIService $aiService)
    {
        parent::__construct();
        $this->elevenlabsService = $elevenlabsService;
        $this->aiService = $aiService;
    }

    public function handle()
    {
        $this->info('🔄 Iniciando sincronización automática de conversaciones...');
        Log::info('AutoSync: Iniciando sincronización automática');

        try {
            // PRIMERO: Sincronizar agentes para tener el caché local actualizado
            $this->info('👥 Sincronizando agentes primero...');
            $agentsCount = $this->elevenlabsService->syncAgents();
            $this->info("✅ {$agentsCount} agentes sincronizados en BD local");
            $this->newLine();
            
            // Obtener conversaciones de los últimos 15 minutos (margen de seguridad)
            $fifteenMinutesAgo = now()->subMinutes(15)->timestamp;
            
            $this->info("📥 Descargando conversaciones desde " . now()->subMinutes(15)->format('Y-m-d H:i:s'));
            
            $response = $this->elevenlabsService->getConversations($fifteenMinutesAgo, null, 100);
            $conversations = $response['conversations'] ?? [];
            
            $this->info("📊 Conversaciones totales devueltas por API: " . count($conversations));
            
            // Filtrar solo conversaciones dentro del rango de tiempo
            $filteredConversations = array_filter($conversations, function($conv) use ($fifteenMinutesAgo) {
                $startTime = $conv['metadata']['start_time_unix_secs'] ?? 0;
                return $startTime >= $fifteenMinutesAgo;
            });
            
            $this->info("📊 Conversaciones dentro de los últimos 15 minutos: " . count($filteredConversations));
            Log::info('AutoSync: Conversaciones obtenidas', [
                'total' => count($conversations),
                'filtered' => count($filteredConversations),
                'from_timestamp' => $fifteenMinutesAgo,
                'from_date' => date('Y-m-d H:i:s', $fifteenMinutesAgo)
            ]);

            if (empty($filteredConversations)) {
                $this->info('✅ No hay conversaciones nuevas en los últimos 15 minutos.');
                return 0;
            }

            $processedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $failedCount = 0;

            foreach ($filteredConversations as $conversationData) {
                try {
                    $conversationId = $conversationData['conversation_id'];
                    $agentId = $conversationData['agent_id'];
                    $status = $conversationData['status'] ?? 'unknown';
                    
                    // Verificar fecha de la conversación
                    $conversationStartTime = $conversationData['metadata']['start_time_unix_secs'] ?? 0;
                    $conversationDate = date('Y-m-d H:i:s', $conversationStartTime);

                    // Omitir conversaciones con status "failed"
                    if ($status === 'failed') {
                        $this->line("❌ Conversación {$conversationId} tiene status 'failed', saltando...");
                        $failedCount++;
                        continue;
                    }

                    // Verificar si la conversación ya existe
                    $existingConversation = ElevenlabsConversation::where('conversation_id', $conversationId)->first();
                    
                    if ($existingConversation) {
                        $this->line("⏩ Conversación {$conversationId} ya existe, saltando...");
                        $skippedCount++;
                        continue;
                    }

                    // Verificar si el agente está configurado
                    $agent = ElevenlabsAgent::where('agent_id', $agentId)->first();
                    
                    if (!$agent) {
                        $this->warn("⚠️  Agente {$agentId} no configurado, saltando conversación {$conversationId}");
                        Log::warning('AutoSync: Agente no configurado', [
                            'agent_id' => $agentId,
                            'conversation_id' => $conversationId
                        ]);
                        $skippedCount++;
                        continue;
                    }

                    // Obtener detalles completos de la conversación (incluye transcript)
                    $this->line("📥 Descargando conversación {$conversationId}...");
                    $fullConversation = $this->elevenlabsService->getConversation($conversationId);
                    
                    // Crear la conversación
                    $conversation = $this->createConversationFromData($fullConversation);
                    
                    if ($conversation) {
                        $this->info("🤖 Procesando con IA: {$conversationId}");
                        
                        // Procesar con IA
                        $this->processConversationWithAI($conversation, $agent);
                        $processedCount++;
                        
                        $this->info("✅ Conversación procesada: {$conversationId}");
                    } else {
                        $this->error("❌ Error creando conversación: {$conversationId}");
                        $errorCount++;
                    }

                } catch (\Exception $e) {
                    $this->error("❌ Error procesando conversación: " . $e->getMessage());
                    Log::error('AutoSync: Error procesando conversación individual', [
                        'conversation_id' => $conversationData['conversation_id'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $errorCount++;
                }
            }

            $this->newLine();
            $this->info("═══════════════════════════════════════════");
            $this->info("📊 RESUMEN DE SINCRONIZACIÓN:");
            $this->info("   📥 Devueltas por API: " . count($conversations));
            $this->info("   🕒 Dentro del rango (15 min): " . count($filteredConversations));
            $this->info("   ✅ Procesadas: {$processedCount}");
            $this->info("   ⏩ Omitidas (duplicadas): {$skippedCount}");
            $this->info("   ❌ Status 'failed': {$failedCount}");
            $this->info("   ❗ Errores: {$errorCount}");
            $this->info("═══════════════════════════════════════════");
            
            Log::info('AutoSync: Sincronización completada', [
                'api_total' => count($conversations),
                'filtered' => count($filteredConversations),
                'processed' => $processedCount,
                'skipped' => $skippedCount,
                'failed_status' => $failedCount,
                'errors' => $errorCount
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error en sincronización automática: ' . $e->getMessage());
            Log::error('AutoSync: Error fatal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Crear conversación desde datos de Eleven Labs
     */
    private function createConversationFromData(array $data): ?ElevenlabsConversation
    {
        try {
            // Formatear transcripción
            $transcript = '';
            if (isset($data['transcript']) && is_array($data['transcript'])) {
                foreach ($data['transcript'] as $message) {
                    $role = $message['role'] === 'agent' ? 'Agente' : 'Cliente';
                    $messageText = trim($message['message'] ?? '');
                    if (!empty($messageText)) {
                        $transcript .= "[{$role}] {$messageText}\n";
                    }
                }
            }

            // Validar que el transcript no esté vacío
            $transcript = trim($transcript);
            if (empty($transcript)) {
                Log::warning('AutoSync: Transcript vacío, omitiendo conversación', [
                    'conversation_id' => $data['conversation_id'] ?? 'unknown',
                ]);
                return null;
            }

            // Buscar cliente por teléfono si existe
            $clientId = null;
            if (isset($data['phone_call']['external_number'])) {
                $phoneNumber = $data['phone_call']['external_number'];
                $client = \App\Models\Clients\Client::where('phone', 'LIKE', "%{$phoneNumber}%")->first();
                if ($client) {
                    $clientId = $client->id;
                }
            }

            // Obtener nombre del agente desde la tabla local
            $agentId = $data['agent_id'] ?? null;
            $agentName = null;
            if ($agentId) {
                $agentName = ElevenlabsAgent::getNameByAgentId($agentId);
                if ($agentName) {
                    Log::debug('AutoSync: Agente encontrado en BD local', [
                        'agent_id' => $agentId,
                        'agent_name' => $agentName
                    ]);
                } else {
                    Log::warning('AutoSync: Agente no encontrado en BD local', [
                        'agent_id' => $agentId
                    ]);
                }
            }

            $conversation = ElevenlabsConversation::create([
                'conversation_id' => $data['conversation_id'],
                'agent_id' => $agentId,
                'agent_name' => $agentName, // Guardar nombre del agente desde BD local
                'client_id' => $clientId,
                'conversation_date' => isset($data['metadata']['start_time_unix_secs']) 
                    ? \Carbon\Carbon::createFromTimestamp($data['metadata']['start_time_unix_secs'])
                    : now(),
                'duration_seconds' => $data['metadata']['call_duration_secs'] ?? 0,
                'transcript' => $transcript,
                'metadata' => $data['metadata'] ?? [],
                'sentiment_category' => null, // Se asignará con IA
                'specific_category' => null,  // Se asignará con IA
                'summary_es' => null,         // Se generará con IA
                'confidence_score' => null,   // Se calculará con IA
                'processing_status' => 'pending', // Pendiente de procesar
                'processed_at' => null,      // Se marcará cuando se procese
            ]);

            return $conversation;

        } catch (\Exception $e) {
            Log::error('AutoSync: Error creando conversación', [
                'conversation_id' => $data['conversation_id'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Procesar conversación con IA
     */
    private function processConversationWithAI(ElevenlabsConversation $conversation, ElevenlabsAgent $agent)
    {
        try {
            // El método processConversation ya guarda todo directamente en el modelo
            $success = $this->aiService->processConversation($conversation);
            
            if ($success) {
                Log::info('AutoSync: Conversación procesada con IA exitosamente', [
                    'conversation_id' => $conversation->conversation_id,
                    'sentiment' => $conversation->sentiment_category,
                    'specific' => $conversation->specific_category
                ]);
            } else {
                Log::error('AutoSync: Error en procesamiento IA', [
                    'conversation_id' => $conversation->conversation_id
                ]);
            }

        } catch (\Exception $e) {
            Log::error('AutoSync: Excepción en procesamiento IA', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

