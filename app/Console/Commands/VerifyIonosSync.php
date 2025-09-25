<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Services\IonosApiService;

class VerifyIonosSync extends Command
{
    protected $signature = 'ionos:verify-sync {--limit=20 : Límite de dominios a verificar}';
    protected $description = 'Verifica que los dominios marcados como sincronizados realmente estén en IONOS.';

    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("🔍 Verificando sincronización de dominios con IONOS...");
        $this->line("  - Límite: {$limit}");

        // Obtener dominios que dicen estar sincronizados
        $dominios = Dominio::whereNotNull('ionos_id')
                          ->where('sincronizado_ionos', true)
                          ->limit($limit)
                          ->get();

        $this->line("📊 Dominios marcados como sincronizados: " . $dominios->count());

        $ionosService = new IonosApiService();
        $verifiedCount = 0;
        $notFoundCount = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            $result = $ionosService->getDomainInfo($dominio->dominio);
            
            if ($result['success']) {
                $verifiedCount++;
                if ($result['ionos_id'] !== $dominio->ionos_id) {
                    $this->line("\n⚠️  ID diferente para {$dominio->dominio}:");
                    $this->line("  - Base de datos: {$dominio->ionos_id}");
                    $this->line("  - IONOS API: {$result['ionos_id']}");
                }
            } else {
                $notFoundCount++;
                $this->line("\n❌ {$dominio->dominio} - NO encontrado en IONOS");
                $this->line("  - Error: " . $result['message']);
                $this->line("  - ID en BD: {$dominio->ionos_id}");
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Resumen de la verificación:");
        $this->line("  - Dominios verificados: " . $dominios->count());
        $this->line("  - Confirmados en IONOS: {$verifiedCount}");
        $this->line("  - NO encontrados en IONOS: {$notFoundCount}");
        $this->line("  - Errores: {$errors}");

        if ($notFoundCount > 0) {
            $this->warn("\n⚠️  Se encontraron {$notFoundCount} dominios marcados como sincronizados pero que NO están en IONOS.");
            $this->info("💡 Considera ejecutar una sincronización completa para corregir estos datos.");
        } else {
            $this->info("✅ Todos los dominios verificados están correctamente sincronizados con IONOS.");
        }
    }
}
