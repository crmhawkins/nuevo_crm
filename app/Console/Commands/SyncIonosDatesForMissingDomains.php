<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Services\IonosApiService;
use Illuminate\Support\Facades\Log;

class SyncIonosDatesForMissingDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ionos:sync-missing-dates 
                            {--limit=50 : Límite de dominios a procesar}
                            {--dry-run : Solo mostrar qué dominios se sincronizarían sin actualizar}
                            {--force : Forzar sincronización de todos los dominios sin fechas IONOS}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza las fechas de IONOS para dominios que no las tienen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Sincronizando fechas de IONOS para dominios sin datos...');
        $this->line("  - Límite: {$limit}");
        $this->line("  - Modo: " . ($dryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR fechas'));
        $this->line("  - Forzar: " . ($force ? 'SÍ' : 'NO'));

        try {
            // Buscar dominios que no tienen fechas de IONOS
            $query = Dominio::where(function($query) {
                $query->whereNull('fecha_activacion_ionos')
                      ->orWhereNull('fecha_renovacion_ionos')
                      ->orWhere('sincronizado_ionos', false);
            });

            if (!$force) {
                // Solo dominios que fueron sincronizados desde IONOS pero no tienen fechas
                $query->whereNotNull('fecha_activacion_ionos')
                      ->orWhereNotNull('fecha_renovacion_ionos');
            }

            $dominios = $query->limit($limit)->get();
            
            $this->info("📊 Procesando " . $dominios->count() . " dominios...");
            
            if ($dominios->count() == 0) {
                $this->info('✅ No hay dominios que necesiten sincronización de fechas IONOS.');
                return;
            }
            
            $ionosService = new IonosApiService();
            $sincronizadosCount = 0;
            $errorCount = 0;
            $noEncontradosCount = 0;
            
            $progressBar = $this->output->createProgressBar($dominios->count());
            $progressBar->start();
            
            foreach ($dominios as $dominio) {
                try {
                    $this->line("\n🔍 Procesando: {$dominio->dominio}");
                    
                    if ($dryRun) {
                        $this->line("  - Fecha activación IONOS: " . ($dominio->fecha_activacion_ionos ?: 'N/A'));
                        $this->line("  - Fecha renovación IONOS: " . ($dominio->fecha_renovacion_ionos ?: 'N/A'));
                        $this->line("  - Sincronizado IONOS: " . ($dominio->sincronizado_ionos ? 'SÍ' : 'NO'));
                        $progressBar->advance();
                        continue;
                    }
                    
                    // Obtener información de IONOS
                    $result = $ionosService->getDomainInfo($dominio->dominio);
                    
                    if ($result['success']) {
                        $updates = [];
                        
                        if (isset($result['fecha_activacion_ionos']) && $result['fecha_activacion_ionos']) {
                            $updates['fecha_activacion_ionos'] = $result['fecha_activacion_ionos'];
                        }
                        
                        if (isset($result['fecha_renovacion_ionos']) && $result['fecha_renovacion_ionos']) {
                            $updates['fecha_renovacion_ionos'] = $result['fecha_renovacion_ionos'];
                        }
                        
                        if (!empty($updates)) {
                            $updates['sincronizado_ionos'] = true;
                            $updates['ultima_sincronizacion_ionos'] = now();
                            
                            $dominio->update($updates);
                            $sincronizadosCount++;
                            
                            $this->line("  ✅ Sincronizado exitosamente");
                            if (isset($updates['fecha_activacion_ionos'])) {
                                $this->line("    - Fecha activación: " . $updates['fecha_activacion_ionos']);
                            }
                            if (isset($updates['fecha_renovacion_ionos'])) {
                                $this->line("    - Fecha renovación: " . $updates['fecha_renovacion_ionos']);
                            }
                        } else {
                            $this->line("  ⚠️ No se encontraron fechas en IONOS");
                        }
                    } else {
                        if (strpos($result['message'], 'no encontrado') !== false) {
                            $noEncontradosCount++;
                            $this->line("  ❌ Dominio no encontrado en IONOS");
                        } else {
                            $errorCount++;
                            $this->line("  ❌ Error: " . $result['message']);
                        }
                    }
                    
                    $progressBar->advance();
                    
                    // Pequeña pausa para no sobrecargar la API
                    usleep(500000); // 0.5 segundos
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $this->line("\n❌ Error procesando {$dominio->dominio}: " . $e->getMessage());
                    $progressBar->advance();
                }
            }
            
            $progressBar->finish();
            $this->newLine();
            
            // Mostrar resumen
            $this->info('📊 Resumen de la sincronización:');
            $this->line("  - Dominios procesados: " . $dominios->count());
            
            if ($dryRun) {
                $this->line("  - Modo DRY RUN: No se actualizaron fechas");
            } else {
                $this->line("  - Dominios sincronizados exitosamente: {$sincronizadosCount}");
                $this->line("  - Dominios no encontrados en IONOS: {$noEncontradosCount}");
                $this->line("  - Errores: {$errorCount}");
            }
            
            if ($dryRun) {
                $this->info('🔍 Modo DRY RUN: No se realizaron cambios.');
                $this->info('💡 Para aplicar los cambios, ejecuta sin --dry-run');
            } else {
                $this->info('✅ Sincronización de fechas IONOS completada exitosamente.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
        }
    }
}
