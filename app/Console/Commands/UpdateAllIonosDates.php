<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Services\IonosApiService;
use Carbon\Carbon;

class UpdateAllIonosDates extends Command
{
    protected $signature = 'ionos:update-all-dates {--limit=50 : Límite de dominios a procesar} {--dry-run : Solo mostrar los cambios sin aplicarlos}';
    protected $description = 'Actualiza las fechas de IONOS para todos los dominios que no las tienen.';

    public function handle()
    {
        $limit = $this->option('limit');
        $isDryRun = $this->option('dry-run');

        $this->info("🔄 Actualizando fechas de IONOS para todos los dominios...");
        $this->line("  - Límite: {$limit}");
        $this->line("  - Modo: " . ($isDryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR fechas'));

        // Buscar dominios que no tienen fechas de IONOS
        $dominios = Dominio::where(function ($query) {
            $query->whereNull('fecha_activacion_ionos')
                  ->orWhereNull('fecha_renovacion_ionos')
                  ->orWhere('sincronizado_ionos', false);
        })->limit($limit)->get();

        if ($dominios->isEmpty()) {
            $this->info("✅ No hay dominios que necesiten actualización de fechas IONOS.");
            return;
        }

        $this->info("📊 Dominios encontrados que necesitan actualización: " . $dominios->count());

        $ionosService = new IonosApiService();
        $updatedCount = 0;
        $errorCount = 0;
        $notFoundCount = 0;

        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            $this->line("\n🔍 Procesando: {$dominio->dominio}");
            
            try {
                $result = $ionosService->getDomainInfo($dominio->dominio);

                if ($result['success']) {
                    if ($isDryRun) {
                        $this->line("  - Fecha activación IONOS: " . ($result['fecha_activacion_ionos'] ?? 'N/A'));
                        $this->line("  - Fecha renovación IONOS: " . ($result['fecha_renovacion_ionos'] ?? 'N/A'));
                        $this->line("  - Sincronizado IONOS: SÍ");
                    } else {
                        $dominio->update([
                            'fecha_activacion_ionos' => $result['fecha_activacion_ionos'],
                            'fecha_renovacion_ionos' => $result['fecha_renovacion_ionos'],
                            'sincronizado_ionos' => true,
                            'ultima_sincronizacion_ionos' => Carbon::now()
                        ]);
                        
                        $this->info("  ✅ Sincronizado exitosamente");
                        $this->line("    - Fecha activación: " . ($dominio->fecha_activacion_ionos_formateada ?? 'N/A'));
                        $this->line("    - Fecha renovación: " . ($dominio->fecha_renovacion_ionos_formateada ?? 'N/A'));
                        $updatedCount++;
                    }
                } else {
                    $this->error("  ❌ " . $result['message']);
                    $notFoundCount++;
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Error al procesar {$dominio->dominio}: " . $e->getMessage());
                $errorCount++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Resumen de la actualización:");
        $this->line("  - Dominios procesados: " . $dominios->count());
        
        if ($isDryRun) {
            $this->line("  - Modo DRY RUN: No se actualizaron fechas");
        } else {
            $this->line("  - Dominios actualizados exitosamente: {$updatedCount}");
            $this->line("  - Dominios no encontrados en IONOS: {$notFoundCount}");
        }
        
        $this->line("  - Errores: {$errorCount}");

        if ($isDryRun) {
            $this->warn("🔍 Modo DRY RUN: No se realizaron cambios.");
            $this->info("💡 Para aplicar los cambios, ejecuta sin --dry-run");
        } else {
            $this->info("✅ Actualización de fechas IONOS completada exitosamente.");
        }
    }
}
