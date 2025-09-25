<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Services\IonosApiService;

class CheckDomainsInIonos extends Command
{
    protected $signature = 'ionos:check-domains {--limit=20 : Límite de dominios a verificar} {--show-missing : Mostrar solo los que NO están en IONOS}';
    protected $description = 'Verifica qué dominios de la base local están realmente en IONOS.';

    public function handle()
    {
        $limit = $this->option('limit');
        $showMissing = $this->option('show-missing');

        $this->info("🔍 Verificando dominios en IONOS...");
        $this->line("  - Límite: {$limit}");
        $this->line("  - Mostrar solo faltantes: " . ($showMissing ? 'SÍ' : 'NO'));

        $query = Dominio::query();
        if ($limit) {
            $query->limit($limit);
        }

        $dominios = $query->get();
        $ionosService = new IonosApiService();
        
        $foundInIonos = 0;
        $notFoundInIonos = 0;
        $missingDomains = [];

        $progressBar = $this->output->createProgressBar($dominios->count());
        $progressBar->start();

        foreach ($dominios as $dominio) {
            $result = $ionosService->getDomainInfo($dominio->dominio);
            
            if ($result['success']) {
                $foundInIonos++;
                if (!$showMissing) {
                    $this->line("\n✅ {$dominio->dominio} - ENCONTRADO en IONOS");
                }
            } else {
                $notFoundInIonos++;
                $missingDomains[] = $dominio->dominio;
                if ($showMissing || !$showMissing) {
                    $this->line("\n❌ {$dominio->dominio} - NO encontrado en IONOS");
                    $this->line("   Razón: " . $result['message']);
                }
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        $this->info("📊 Resumen de la verificación:");
        $this->line("  - Dominios verificados: " . $dominios->count());
        $this->line("  - Encontrados en IONOS: {$foundInIonos}");
        $this->line("  - NO encontrados en IONOS: {$notFoundInIonos}");

        if ($notFoundInIonos > 0) {
            $this->warn("\n🚨 Dominios que NO están en IONOS:");
            foreach ($missingDomains as $domain) {
                $this->line("  - {$domain}");
            }
            
            $this->newLine();
            $this->info("💡 Posibles razones:");
            $this->line("  - Dominios registrados en otros proveedores");
            $this->line("  - Dominios expirados y no renovados");
            $this->line("  - Dominios transferidos a otros registradores");
            $this->line("  - Errores en los nombres de dominio");
        }
    }
}
