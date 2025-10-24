<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ElevenlabsService;
use Illuminate\Support\Facades\Log;

class SyncElevenlabsAgents extends Command
{
    protected $signature = 'elevenlabs:sync-agents';
    protected $description = 'Sincroniza los agentes de Eleven Labs a la base de datos local';

    private $elevenlabsService;

    public function __construct(ElevenlabsService $elevenlabsService)
    {
        parent::__construct();
        $this->elevenlabsService = $elevenlabsService;
    }

    public function handle()
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('👥 SINCRONIZACIÓN DE AGENTES DE ELEVENLABS');
        $this->info('═══════════════════════════════════════════');
        $this->newLine();

        try {
            $this->info('📥 Obteniendo agentes desde Eleven Labs API...');
            
            $agentsCount = $this->elevenlabsService->syncAgents();
            
            if ($agentsCount > 0) {
                $this->newLine();
                $this->info("✅ {$agentsCount} agentes sincronizados correctamente");
                
                // Mostrar lista de agentes sincronizados
                $agents = \App\Models\ElevenlabsAgent::all();
                
                if ($agents->isNotEmpty()) {
                    $this->newLine();
                    $this->info('📋 Agentes en base de datos:');
                    $this->table(
                        ['Agent ID', 'Nombre', 'Archivado'],
                        $agents->map(function($agent) {
                            return [
                                substr($agent->agent_id, 0, 20) . '...',
                                $agent->name,
                                $agent->archived ? '❌ Sí' : '✅ No'
                            ];
                        })->toArray()
                    );
                }
                
                $this->newLine();
                $this->info('═══════════════════════════════════════════');
                
                Log::info('SyncAgents: Sincronización de agentes completada', [
                    'count' => $agentsCount
                ]);
                
                return 0;
            } else {
                $this->warn('⚠️  No se encontraron agentes o hubo un error');
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error al sincronizar agentes: ' . $e->getMessage());
            Log::error('SyncAgents: Error en sincronización', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}

