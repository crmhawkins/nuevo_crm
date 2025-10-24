<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElevenlabsConversation;
use App\Models\ElevenlabsAgent;
use Illuminate\Support\Facades\Log;

class UpdateElevenlabsAgentNames extends Command
{
    protected $signature = 'elevenlabs:update-agent-names';
    protected $description = 'Actualiza los nombres de agentes en conversaciones existentes que tengan agent_id pero no agent_name';

    public function handle()
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('🔄 ACTUALIZACIÓN DE NOMBRES DE AGENTES');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        try {
            // Buscar conversaciones que tengan agent_id pero no agent_name
            $conversations = ElevenlabsConversation::whereNotNull('agent_id')
                ->whereNull('agent_name')
                ->get();

            if ($conversations->isEmpty()) {
                $this->info('✅ No hay conversaciones que necesiten actualización');
                return 0;
            }

            $this->info("📊 Conversaciones a actualizar: {$conversations->count()}");
            $this->newLine();

            $progressBar = $this->output->createProgressBar($conversations->count());
            $progressBar->start();

            $updatedCount = 0;
            $notFoundCount = 0;

            foreach ($conversations as $conversation) {
                $agentName = ElevenlabsAgent::getNameByAgentId($conversation->agent_id);
                
                if ($agentName) {
                    $conversation->agent_name = $agentName;
                    $conversation->save();
                    $updatedCount++;
                } else {
                    $notFoundCount++;
                    Log::warning('UpdateAgentNames: Agente no encontrado en BD', [
                        'agent_id' => $conversation->agent_id,
                        'conversation_id' => $conversation->conversation_id
                    ]);
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info('═══════════════════════════════════════════');
            $this->info('📊 RESUMEN:');
            $this->info("   ✅ Actualizadas: {$updatedCount}");
            $this->info("   ⚠️  Agente no encontrado: {$notFoundCount}");
            $this->info('═══════════════════════════════════════════');

            Log::info('UpdateAgentNames: Actualización completada', [
                'total' => $conversations->count(),
                'updated' => $updatedCount,
                'not_found' => $notFoundCount
            ]);

            if ($notFoundCount > 0) {
                $this->newLine();
                $this->warn("💡 Ejecuta 'php artisan elevenlabs:sync-agents' para sincronizar los agentes faltantes");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error al actualizar nombres de agentes: ' . $e->getMessage());
            Log::error('UpdateAgentNames: Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}

