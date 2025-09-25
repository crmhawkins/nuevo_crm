<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;

class NormalizeDomainNames extends Command
{
    protected $signature = 'domains:normalize {--dry-run : Solo mostrar los cambios sin aplicarlos}';
    protected $description = 'Normaliza todos los nombres de dominio a minúsculas y limpia caracteres especiales.';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info("🔄 Normalizando nombres de dominios...");
        $this->line("  - Modo: " . ($isDryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR nombres'));

        $dominios = Dominio::all();
        $normalizedCount = 0;
        $noChangeCount = 0;

        $this->line("📊 Dominios encontrados: " . $dominios->count());

        foreach ($dominios as $dominio) {
            $originalName = $dominio->dominio;
            $normalizedName = $this->normalizeDomainName($originalName);

            if ($originalName !== $normalizedName) {
                if ($isDryRun) {
                    $this->line("🔍 {$originalName} → {$normalizedName}");
                } else {
                    try {
                        $dominio->update(['dominio' => $normalizedName]);
                        $this->info("✅ {$originalName} → {$normalizedName}");
                        $normalizedCount++;
                    } catch (\Exception $e) {
                        $this->error("❌ Error al normalizar {$originalName}: " . $e->getMessage());
                    }
                }
            } else {
                $noChangeCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Resumen de la normalización:");
        $this->line("  - Dominios procesados: " . $dominios->count());
        if ($isDryRun) {
            $this->line("  - Modo DRY RUN: No se actualizaron nombres");
        } else {
            $this->line("  - Nombres normalizados: {$normalizedCount}");
            $this->line("  - Nombres sin cambios: {$noChangeCount}");
        }

        if ($isDryRun) {
            $this->warn("🔍 Modo DRY RUN: No se realizaron cambios.");
            $this->info("💡 Para aplicar los cambios, ejecuta sin --dry-run");
        } else {
            $this->info("✅ Normalización de nombres completada exitosamente.");
        }
    }

    private function normalizeDomainName($domainName)
    {
        // Convertir a minúsculas
        $domainName = strtolower(trim($domainName));
        
        // Eliminar http://, https://, y barras finales
        $domainName = preg_replace('/^https?:\/\//', '', $domainName);
        $domainName = rtrim($domainName, '/');
        
        // Eliminar espacios extra
        $domainName = preg_replace('/\s+/', '', $domainName);
        
        // Eliminar caracteres especiales que no deberían estar en dominios
        $domainName = preg_replace('/[^a-z0-9.-]/', '', $domainName);
        
        return $domainName;
    }
}
