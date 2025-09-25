<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use Illuminate\Support\Facades\Http;

class AnalyzeMissingIonosDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ionos:analyze-missing 
                            {--limit=100 : Límite de dominios a analizar}
                            {--offset=0 : Offset inicial para la paginación}
                            {--show-details : Mostrar detalles de cada dominio faltante}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analiza qué dominios de IONOS no existen en la tabla local';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $offset = $this->option('offset');
        $showDetails = $this->option('show-details');

        $this->info('🔍 Analizando dominios faltantes de IONOS...');
        $this->line("  - Límite: {$limit}");
        $this->line("  - Offset: {$offset}");
        $this->line("  - Mostrar detalles: " . ($showDetails ? 'Sí' : 'No'));

        try {
            // Obtener todos los dominios de IONOS
            $this->info('📡 Obteniendo lista de dominios de IONOS...');
            $response = Http::withHeaders([
                'X-API-Key' => config('services.ionos.api_key'),
                'X-Tenant-Id' => config('services.ionos.tenant_id'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->get('https://api.hosting.ionos.com/domains/v1/domainitems', [
                'includeDomainStatus' => 'true',
                'sortBy' => 'DOMAIN_NAME',
                'direction' => 'ASC',
                'limit' => $limit,
                'offset' => $offset
            ]);

            if (!$response->successful()) {
                $this->error('❌ Error al obtener dominios de IONOS: ' . $response->body());
                return;
            }

            $data = $response->json();
            $ionosDomains = $data['domains'] ?? [];
            $totalIonos = $data['count'] ?? 0;
            
            $this->info("📊 Encontrados {$totalIonos} dominios en IONOS");
            $this->info("🔄 Analizando " . count($ionosDomains) . " dominios...");
            
            $missingDomains = [];
            $foundCount = 0;
            
            $progressBar = $this->output->createProgressBar(count($ionosDomains));
            $progressBar->start();
            
            foreach ($ionosDomains as $ionosDomain) {
                $domainName = $ionosDomain['name'];
                
                // Buscar el dominio en nuestra base de datos
                $localDomain = Dominio::where('dominio', $domainName)->first();
                
                if ($localDomain) {
                    $foundCount++;
                } else {
                    // Dominio no encontrado - es un dominio faltante
                    $missingDomains[] = [
                        'name' => $domainName,
                        'id' => $ionosDomain['id'],
                        'tld' => $ionosDomain['tld'] ?? 'unknown',
                        'status' => $ionosDomain['status']['provisioningStatus']['status'] ?? 'unknown',
                        'expiration_date' => $ionosDomain['expirationDate'] ?? null,
                        'auto_renew' => $ionosDomain['autoRenew'] ?? false,
                        'privacy_enabled' => $ionosDomain['privacyEnabled'] ?? false,
                        'transfer_lock' => $ionosDomain['transferLock'] ?? false,
                        'dns_sec_enabled' => $ionosDomain['dnsSecEnabled'] ?? false,
                        'domain_type' => $ionosDomain['domainType'] ?? 'unknown',
                        'domain_guard_enabled' => $ionosDomain['domainGuardEnabled'] ?? false
                    ];
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine();
            
            // Mostrar resumen
            $this->info('📊 Resumen del análisis:');
            $this->line("  - Dominios encontrados en local: {$foundCount}");
            $this->line("  - Dominios faltantes: " . count($missingDomains));
            $this->line("  - Porcentaje faltante: " . round((count($missingDomains) / count($ionosDomains)) * 100, 2) . '%');
            
            if (count($missingDomains) > 0) {
                $this->newLine();
                $this->info('🔍 Dominios faltantes encontrados:');
                
                // Agrupar por TLD
                $tldGroups = [];
                foreach ($missingDomains as $domain) {
                    $tld = $domain['tld'];
                    if (!isset($tldGroups[$tld])) {
                        $tldGroups[$tld] = [];
                    }
                    $tldGroups[$tld][] = $domain;
                }
                
                // Mostrar por TLD
                foreach ($tldGroups as $tld => $domains) {
                    $this->line("  📁 TLD: .{$tld} ({$tld} dominios)");
                    
                    if ($showDetails) {
                        foreach ($domains as $domain) {
                            $this->line("    - {$domain['name']} (ID: {$domain['id']})");
                            $this->line("      Estado: {$domain['status']}");
                            $this->line("      Expiración: " . ($domain['expiration_date'] ?: 'N/A'));
                            $this->line("      Auto-renovar: " . ($domain['auto_renew'] ? 'Sí' : 'No'));
                            $this->line("      Privacidad: " . ($domain['privacy_enabled'] ? 'Sí' : 'No'));
                            $this->line("      Transfer Lock: " . ($domain['transfer_lock'] ? 'Sí' : 'No'));
                            $this->line("      DNS Sec: " . ($domain['dns_sec_enabled'] ? 'Sí' : 'No'));
                            $this->line("      Tipo: {$domain['domain_type']}");
                            $this->line("      Domain Guard: " . ($domain['domain_guard_enabled'] ? 'Sí' : 'No'));
                            $this->line("");
                        }
                    } else {
                        // Mostrar solo nombres
                        $domainNames = array_column($domains, 'name');
                        $chunks = array_chunk($domainNames, 5);
                        foreach ($chunks as $chunk) {
                            $this->line("    " . implode(', ', $chunk));
                        }
                    }
                    $this->line("");
                }
                
                $this->newLine();
                $this->info('💡 Para sincronizar estos dominios, usa:');
                $this->line('  php artisan ionos:sync-missing --dry-run');
                $this->line('  php artisan ionos:sync-missing --client-id=1');
            } else {
                $this->info('✅ Todos los dominios de IONOS ya existen en la base local.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
        }
    }
}
