<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DomainSyncService;

class SyncDomainsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:sync 
                            {--domain= : Sincronizar un dominio específico}
                            {--stats : Mostrar estadísticas de sincronización}
                            {--force : Forzar sincronización de todos los dominios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincronizar dominios con la base de datos externa de precios e IBAN';

    protected $syncService;

    public function __construct(DomainSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 SINCRONIZACIÓN DE DOMINIOS');
        $this->line(str_repeat('=', 50));

        if ($this->option('stats')) {
            $this->showStats();
            return;
        }

        if ($domain = $this->option('domain')) {
            $this->syncSpecificDomain($domain);
        } else {
            $this->syncAllDomains();
        }
    }

    private function showStats()
    {
        $this->info('📊 ESTADÍSTICAS DE SINCRONIZACIÓN');
        $this->line(str_repeat('-', 40));

        $stats = $this->syncService->getSyncStats();
        $pricingStats = $this->syncService->getPricingStats();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Total Dominios', $stats['total_domains']],
                ['Dominios Sincronizados', $stats['synced_domains']],
                ['Dominios No Sincronizados', $stats['not_synced_domains']],
                ['Porcentaje de Sincronización', $stats['sync_percentage'] . '%'],
                ['Última Sincronización', $stats['last_sync'] ?? 'Nunca'],
            ]
        );

        $this->newLine();
        $this->info('💰 ESTADÍSTICAS DE PRECIOS');
        $this->line(str_repeat('-', 40));

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Inversión Total', '€' . number_format($pricingStats['total_investment'], 2)],
                ['Ingresos Totales', '€' . number_format($pricingStats['total_revenue'], 2)],
                ['Beneficio Total', '€' . number_format($pricingStats['total_profit'], 2)],
                ['Margen Promedio', $pricingStats['average_margin'] . '%'],
                ['Dominios con Precios', $pricingStats['count']],
            ]
        );
    }

    private function syncSpecificDomain($domainName)
    {
        $this->info("🔍 Sincronizando dominio: {$domainName}");
        
        try {
            $externalDomains = $this->syncService->getExternalDomains();
            $found = false;

            foreach ($externalDomains as $externalDomain) {
                if ($externalDomain['nombre'] === $domainName) {
                    $found = true;
                    if ($this->syncService->syncDomain($domainName, $externalDomain)) {
                        $this->info("✅ Dominio sincronizado exitosamente");
                    } else {
                        $this->error("❌ Error al sincronizar el dominio");
                    }
                    break;
                }
            }

            if (!$found) {
                $this->warn("⚠️ Dominio no encontrado en la base externa");
            }

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
        }
    }

    private function syncAllDomains()
    {
        $this->info('🔄 Iniciando sincronización masiva...');
        
        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres sincronizar todos los dominios?')) {
                $this->info('Sincronización cancelada.');
                return;
            }
        }

        try {
            $result = $this->syncService->syncAllDomains();
            
            $this->newLine();
            $this->info('✅ SINCRONIZACIÓN COMPLETADA');
            $this->line(str_repeat('-', 40));
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total Procesados', $result['total']],
                    ['Sincronizados Exitosamente', $result['synced']],
                    ['Errores', $result['errors']],
                ]
            );

        } catch (\Exception $e) {
            $this->error("❌ Error en sincronización: " . $e->getMessage());
        }
    }
}
