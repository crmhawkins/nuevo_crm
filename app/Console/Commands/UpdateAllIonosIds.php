<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Services\IonosApiService;
use Carbon\Carbon;

class UpdateAllIonosIds extends Command
{
    protected $signature = 'ionos:update-all-ids {--limit=50 : Límite de dominios a procesar} {--dry-run : Solo mostrar los cambios sin aplicarlos}';
    protected $description = 'Actualiza todos los dominios con el ID de IONOS y las fechas correspondientes.';

    public function handle()
    {
        $limit = $this->option('limit');
        $isDryRun = $this->option('dry-run');

        $this->info("🔄 Actualizando ID de IONOS para todos los dominios...");
        $this->line("  - Límite: {$limit}");
        $this->line("  - Modo: " . ($isDryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR dominios'));

        // Obtener dominios que no tienen ionos_id o fechas IONOS
        $query = Dominio::where(function ($q) {
            $q->whereNull('ionos_id')
              ->orWhereNull('fecha_activacion_ionos')
              ->orWhereNull('fecha_renovacion_ionos')
              ->orWhere('sincronizado_ionos', false);
        });

        $dominiosToSync = $query->limit($limit)->get();

        $this->line("📊 Dominios encontrados que necesitan actualización: " . $dominiosToSync->count());

        $ionosService = new IonosApiService();
        $syncedCount = 0;
        $notFoundCount = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($dominiosToSync->count());
        $progressBar->start();

        foreach ($dominiosToSync as $dominio) {
            $this->line("\n🔍 Procesando: {$dominio->dominio}");
            $result = $ionosService->getDomainInfo($dominio->dominio);

            if ($result['success']) {
                if ($isDryRun) {
                    $this->line("  - IONOS ID: " . ($result['ionos_id'] ?? 'N/A'));
                    $this->line("  - Fecha activación IONOS: " . ($result['fecha_activacion_ionos'] ?? 'N/A'));
                    $this->line("  - Fecha renovación IONOS: " . ($result['fecha_renovacion_ionos'] ?? 'N/A'));
                    $this->line("  - Auto renew: " . ($result['auto_renew'] ? 'SÍ' : 'NO'));
                    $this->line("  - Sincronizado IONOS: SÍ");
                } else {
                    try {
                        $dominio->update([
                            'fecha_activacion_ionos' => $result['fecha_activacion_ionos'],
                            'fecha_renovacion_ionos' => $result['fecha_renovacion_ionos'],
                            'ionos_id' => $result['ionos_id'],
                            'sincronizado_ionos' => true,
                            'ultima_sincronizacion_ionos' => Carbon::now()
                        ]);
                        $this->info("  ✅ Sincronizado exitosamente");
                        $this->line("    - IONOS ID: " . ($dominio->ionos_id ?? 'N/A'));
                        $this->line("    - Fecha activación: " . ($dominio->fecha_activacion_ionos_formateada ?? 'N/A'));
                        $this->line("    - Fecha renovación: " . ($dominio->fecha_renovacion_ionos_formateada ?? 'N/A'));
                        $syncedCount++;
                    } catch (\Exception $e) {
                        $this->error("  ❌ Error al actualizar dominio {$dominio->dominio}: " . $e->getMessage());
                        $errors++;
                    }
                }
            } else {
                $this->error("  ❌ " . $result['message']);
                $notFoundCount++;
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Resumen de la actualización:");
        $this->line("  - Dominios procesados: {$dominiosToSync->count()}");
        if ($isDryRun) {
            $this->line("  - Modo DRY RUN: No se actualizaron dominios");
        } else {
            $this->line("  - Dominios sincronizados exitosamente: {$syncedCount}");
            $this->line("  - Dominios no encontrados en IONOS: {$notFoundCount}");
        }
        $this->line("  - Errores: {$errors}");

        if ($isDryRun) {
            $this->warn("🔍 Modo DRY RUN: No se realizaron cambios.");
            $this->info("💡 Para aplicar los cambios, ejecuta sin --dry-run");
        } else {
            $this->info("✅ Actualización de ID de IONOS completada exitosamente.");
        }
    }
}
