<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateAgentNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elevenlabs:update-agent-names 
                            {--force : Actualizar todos los registros, incluso los que ya tienen agent_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza el campo agent_name de las conversaciones consultando la API de Eleven Labs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Actualizando nombres de agentes...');
        $this->newLine();

        $force = $this->option('force');

        // Obtener conversaciones que necesitan actualización
        $query = ElevenlabsConversation::whereNotNull('agent_id');
        
        if (!$force) {
            $query->whereNull('agent_name');
        }

        $conversations = $query->get();
        $total = $conversations->count();

        if ($total === 0) {
            $this->info('✅ No hay conversaciones que actualizar');
            return 0;
        }

        $this->info("📊 Total de conversaciones a actualizar: {$total}");
        $this->newLine();

        // Agrupar por agent_id para minimizar llamadas a la API
        $agentIds = $conversations->pluck('agent_id')->unique();
        $agentNames = [];

        $this->info("🤖 Consultando información de {$agentIds->count()} agentes...");
        $progressBar = $this->output->createProgressBar($agentIds->count());
        $progressBar->start();

        foreach ($agentIds as $agentId) {
            $agentName = $this->getAgentName($agentId);
            $agentNames[$agentId] = $agentName;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Actualizar conversaciones
        $this->info("💾 Actualizando conversaciones...");
        $progressBar = $this->output->createProgressBar($total);
        $progressBar->start();

        $updated = 0;
        $failed = 0;

        foreach ($conversations as $conversation) {
            try {
                $agentName = $agentNames[$conversation->agent_id] ?? null;
                
                if ($agentName) {
                    $conversation->agent_name = $agentName;
                    $conversation->save();
                    $updated++;
                } else {
                    $failed++;
                }
                
                $progressBar->advance();
            } catch (\Exception $e) {
                $failed++;
                Log::error('Error actualizando agent_name', [
                    'conversation_id' => $conversation->conversation_id,
                    'agent_id' => $conversation->agent_id,
                    'error' => $e->getMessage(),
                ]);
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Resumen
        $this->info('✅ Actualización completada');
        $this->newLine();
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Total procesadas', $total],
                ['Actualizadas exitosamente', $updated],
                ['Fallos', $failed],
            ]
        );

        return 0;
    }

    /**
     * Obtener el nombre de un agente desde la API de Eleven Labs (con caché)
     */
    protected function getAgentName(string $agentId): ?string
    {
        // Intentar obtener desde caché primero
        $cacheKey = "elevenlabs_agent_name_{$agentId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $apiKey = config('elevenlabs.api_key');
            
            $response = Http::withOptions(['verify' => false])
                ->withHeaders(['xi-api-key' => $apiKey])
                ->timeout((int) config('elevenlabs.timeout', 30))
                ->get("https://api.elevenlabs.io/v1/convai/agents/{$agentId}");

            if ($response->successful()) {
                $data = $response->json();
                $agentName = $data['name'] ?? null;
                
                if ($agentName) {
                    // Guardar en caché por 24 horas
                    Cache::put($cacheKey, $agentName, now()->addHours(24));
                    return $agentName;
                }
            } else {
                Log::warning('No se pudo obtener información del agente', [
                    'agent_id' => $agentId,
                    'status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error consultando API de Eleven Labs para agente', [
                'agent_id' => $agentId,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}

