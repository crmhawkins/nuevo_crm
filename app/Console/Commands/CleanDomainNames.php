<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;

class CleanDomainNames extends Command
{
    protected $signature = 'domains:clean-names {--dry-run : Solo mostrar los cambios sin aplicarlos}';
    protected $description = 'Limpia los nombres de dominios eliminando http://, https:// y barras finales.';

    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info("🧹 Limpiando nombres de dominios...");
        $this->line("  - Modo: " . ($isDryRun ? 'DRY RUN (solo mostrar)' : 'ACTUALIZAR nombres'));

        // Buscar dominios que necesitan limpieza
        $dominios = Dominio::where(function ($query) {
            $query->where('dominio', 'like', 'http%')
                  ->orWhere('dominio', 'like', '%/')
                  ->orWhere('dominio', 'like', 'www.%');
        })->get();

        if ($dominios->isEmpty()) {
            $this->info("✅ No hay dominios que necesiten limpieza.");
            return;
        }

        $this->info("📊 Dominios encontrados que necesitan limpieza: " . $dominios->count());

        $cleanedCount = 0;
        $skippedCount = 0;

        foreach ($dominios as $dominio) {
            $originalName = $dominio->dominio;
            $cleanedName = $this->cleanDomainName($originalName);
            
            if ($cleanedName !== $originalName) {
                if ($isDryRun) {
                    $this->line("🔍 {$originalName} → {$cleanedName}");
                } else {
                    try {
                        $dominio->update(['dominio' => $cleanedName]);
                        $this->line("✅ {$originalName} → {$cleanedName}");
                        $cleanedCount++;
                    } catch (\Exception $e) {
                        $this->error("❌ Error al actualizar {$originalName}: " . $e->getMessage());
                        $skippedCount++;
                    }
                }
            } else {
                $skippedCount++;
            }
        }

        $this->newLine();
        $this->info("📊 Resumen de la limpieza:");
        $this->line("  - Dominios procesados: " . $dominios->count());
        
        if ($isDryRun) {
            $this->line("  - Modo DRY RUN: No se actualizaron nombres");
        } else {
            $this->line("  - Nombres limpiados: {$cleanedCount}");
            $this->line("  - Nombres sin cambios: {$skippedCount}");
        }

        if ($isDryRun) {
            $this->warn("🔍 Modo DRY RUN: No se realizaron cambios.");
            $this->info("💡 Para aplicar los cambios, ejecuta sin --dry-run");
        } else {
            $this->info("✅ Limpieza de nombres completada exitosamente.");
        }
    }

    private function cleanDomainName($domainName)
    {
        // Eliminar http:// y https://
        $cleaned = preg_replace('/^https?:\/\//', '', $domainName);
        
        // Eliminar www.
        $cleaned = preg_replace('/^www\./', '', $cleaned);
        
        // Eliminar barras finales
        $cleaned = rtrim($cleaned, '/');
        
        // Eliminar espacios en blanco
        $cleaned = trim($cleaned);
        
        // Convertir a minúsculas
        $cleaned = strtolower($cleaned);
        
        return $cleaned;
    }
}
