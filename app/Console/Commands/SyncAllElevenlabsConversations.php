<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElevenlabsService;
use App\Services\ElevenlabsAIService;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncAllElevenlabsConversations extends Command
{
    protected $signature = 'elevenlabs:sync-all {--from-date=2025-10-20}';
    protected $description = 'Sincroniza TODAS las conversaciones desde una fecha específica sin límites';

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
        $fromDate = $this->option('from-date');
        $fromTimestamp = Carbon::parse($fromDate)->startOfDay()->timestamp;

        $this->info("═══════════════════════════════════════════");
        $this->info("🔄 SINCRONIZACIÓN MASIVA DE CONVERSACIONES");
        $this->info("═══════════════════════════════════════════");
        $this->info("📅 Desde: {$fromDate} (" . Carbon::parse($fromDate)->format('d/m/Y') . ")");
        $this->info("⏰ Timestamp: {$fromTimestamp}");
        $this->newLine();

        if (!$this->confirm('¿Deseas continuar con la sincronización masiva?', true)) {
            $this->info('❌ Sincronización cancelada.');
            return 0;
        }


        try {
            // PRIMERO: Sincronizar agentes para tener el caché local actualizado
            $this->info('👥 Sincronizando agentes primero...');
            $agentsCount = $this->elevenlabsService->syncAgents();
            $this->info("✅ {$agentsCount} agentes sincronizados en BD local");
            $this->newLine();

            $this->info('📥 Descargando TODAS las conversaciones con paginación...');
            $this->info('ℹ️  La API usa cursor para paginar (100 conversaciones por página)');
            $this->newLine();

            $startDate = Carbon::parse($fromDate)->startOfDay();
            $this->line("📅 Desde: " . $startDate->format('d/m/Y H:i:s'));
            $this->line("⏰ Hasta: Ahora");
            $this->newLine();

            $allConversations = [];
            $cursor = null;
            $page = 1;
            $hasMore = true;

            $this->line("📡 Descargando páginas...");

            // Paginar usando cursor hasta que no haya más
            while ($hasMore) {
                $this->line("   📄 Página {$page}...");

                $response = $this->elevenlabsService->getConversations($fromTimestamp, $cursor, 100);
                $conversations = $response['conversations'] ?? [];
                $hasMore = $response['has_more'] ?? false;
                $cursor = $response['next_cursor'] ?? null;

                $this->line("      ✓ " . count($conversations) . " conversaciones en esta página");

                if (!empty($conversations)) {
                    $allConversations = array_merge($allConversations, $conversations);
                }

                if ($hasMore) {
                    $this->line("      → Hay más conversaciones, continuando...");
                } else {
                    $this->line("      ✓ No hay más páginas");
                }

                $page++;

                // Pequeña pausa entre requests para no saturar la API
                if ($hasMore) {
                    usleep(200000); // 0.2 segundos
                }
            }

            $this->newLine();
            $this->info("📊 Total de conversaciones a procesar: " . count($allConversations));
            $this->newLine();

            if (empty($allConversations)) {
                $this->info('✅ No hay conversaciones para sincronizar.');
                return 0;
            }

            $progressBar = $this->output->createProgressBar(count($allConversations));
            $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% - %message%');
            $progressBar->setMessage('Iniciando...');
            $progressBar->start();

            $processedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;
            $noAgentCount = 0;
            $failedCount = 0;
            $emptyTranscriptCount = 0;

            foreach ($allConversations as $conversationData) {
                try {
                    $conversationId = $conversationData['conversation_id'];
                    $agentId = $conversationData['agent_id'];
                    $status = $conversationData['status'] ?? 'unknown';

                    $progressBar->setMessage("Procesando {$conversationId}...");

                    // Omitir conversaciones con status "failed"
                    if ($status === 'failed') {
                        $failedCount++;
                        $progressBar->advance();
                        continue;
                    }

                    // Verificar si la conversación ya existe
                    $existingConversation = ElevenlabsConversation::where('conversation_id', $conversationId)->first();

                    if ($existingConversation) {
                        $skippedCount++;
                        $progressBar->advance();
                        continue;
                    }

                    // Verificar si el agente está configurado
                    $agent = ElevenlabsAgent::where('agent_id', $agentId)->first();

                    if (!$agent) {
                        $noAgentCount++;
                        $progressBar->advance();
                        continue;
                    }

                    // Obtener detalles completos de la conversación
                    $fullConversation = $this->elevenlabsService->getConversation($conversationId);

                    // Crear la conversación
                    $conversation = $this->createConversationFromData($fullConversation);

                    if ($conversation) {
                        // Procesar con IA
                        $this->processConversationWithAI($conversation, $agent);
                        $processedCount++;
                    } else {
                        // Si createConversationFromData devuelve null, es porque el transcript está vacío
                        $emptyTranscriptCount++;
                    }

                    $progressBar->advance();

                    // Pequeña pausa para no saturar la API
                    usleep(100000); // 0.1 segundos

                } catch (\Exception $e) {
                    Log::error('SyncAll: Error procesando conversación', [
                        'conversation_id' => $conversationData['conversation_id'] ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    $errorCount++;
                    $progressBar->advance();
                }
            }

            $progressBar->setMessage('Completado');
            $progressBar->finish();
            $this->newLine(2);

            $this->info("═══════════════════════════════════════════");
            $this->info("📊 RESUMEN DE SINCRONIZACIÓN MASIVA:");
            $this->info("═══════════════════════════════════════════");
            $this->info("   📥 Total encontradas: " . count($allConversations));
            $this->info("   ✅ Procesadas: {$processedCount}");
            $this->info("   ⏩ Omitidas (duplicadas): {$skippedCount}");
            $this->info("   ❌ Status 'failed': {$failedCount}");
            $this->info("   📝 Transcript vacío: {$emptyTranscriptCount}");
            $this->info("   ⚠️  Sin agente configurado: {$noAgentCount}");
            $this->info("   ❗ Errores: {$errorCount}");
            $this->info("═══════════════════════════════════════════");


            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error en sincronización masiva: ' . $e->getMessage());
            Log::error('SyncAll: Error fatal', [
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
            }

            $conversation = ElevenlabsConversation::create([
                'conversation_id' => $data['conversation_id'],
                'agent_id' => $agentId,
                'agent_name' => $agentName, // Guardar nombre del agente desde BD local
                'client_id' => $clientId,
                'conversation_date' => isset($data['metadata']['start_time_unix_secs'])
                    ? Carbon::createFromTimestamp($data['metadata']['start_time_unix_secs'])
                    : now(),
                'duration_seconds' => $data['metadata']['call_duration_secs'] ?? 0,
                'transcript' => $transcript,
                'metadata' => $data['metadata'] ?? [],
                'sentiment_category' => null,
                'specific_category' => null,
                'summary_es' => null,
                'confidence_score' => null,
                'processing_status' => 'pending',
                'processed_at' => null,
            ]);

            return $conversation;

        } catch (\Exception $e) {
            Log::error('SyncAll: Error creando conversación', [
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
            $this->aiService->processConversation($conversation);

        } catch (\Exception $e) {
            Log::error('SyncAll: Excepción en procesamiento IA', [
                'conversation_id' => $conversation->conversation_id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

