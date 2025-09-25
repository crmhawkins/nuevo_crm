<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Dominios\Dominio;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncAllMissingIonosDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ionos:sync-all-missing 
                            {--client-id= : ID del cliente por defecto para nuevos dominios}
                            {--batch-size=50 : Tamaño del lote para procesar}
                            {--dry-run : Solo mostrar qué dominios se añadirían sin crear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza TODOS los dominios de IONOS que no existen en la tabla local';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defaultClientId = $this->option('client-id');
        $batchSize = $this->option('batch-size');
        $dryRun = $this->option('dry-run');

        $this->info('🔄 Sincronizando TODOS los dominios faltantes de IONOS...');
        $this->line("  - Cliente por defecto: " . ($defaultClientId ?: 'ID 1'));
        $this->line("  - Tamaño del lote: {$batchSize}");
        $this->line("  - Modo: " . ($dryRun ? 'DRY RUN (solo mostrar)' : 'CREAR dominios'));

        if ($defaultClientId) {
            $client = Client::find($defaultClientId);
            if ($client) {
                $this->line("  - Cliente: {$client->name} (ID: {$defaultClientId})");
            } else {
                $this->error("❌ Cliente con ID {$defaultClientId} no encontrado");
                return;
            }
        }

        try {
            $totalCreated = 0;
            $totalErrors = 0;
            $offset = 0;
            $hasMore = true;

            while ($hasMore) {
                $this->info("📡 Procesando lote desde offset {$offset}...");
                
                // Obtener dominios de IONOS
                $response = Http::withHeaders([
                    'X-API-Key' => config('services.ionos.api_key'),
                    'X-Tenant-Id' => config('services.ionos.tenant_id'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->get('https://api.hosting.ionos.com/domains/v1/domainitems', [
                    'includeDomainStatus' => 'true',
                    'sortBy' => 'DOMAIN_NAME',
                    'direction' => 'ASC',
                    'limit' => $batchSize,
                    'offset' => $offset
                ]);

                if (!$response->successful()) {
                    $this->error('❌ Error al obtener dominios de IONOS: ' . $response->body());
                    break;
                }

                $data = $response->json();
                $ionosDomains = $data['domains'] ?? [];
                $totalIonos = $data['count'] ?? 0;
                
                if (empty($ionosDomains)) {
                    $hasMore = false;
                    break;
                }

                $this->info("🔄 Procesando " . count($ionosDomains) . " dominios del lote...");
                
                $batchCreated = 0;
                $batchErrors = 0;
                $batchFound = 0;
                
                $progressBar = $this->output->createProgressBar(count($ionosDomains));
                $progressBar->start();
                
                foreach ($ionosDomains as $ionosDomain) {
                    try {
                        $domainName = $ionosDomain['name'];
                        
                        // Buscar el dominio en nuestra base de datos
                        $localDomain = Dominio::where('dominio', $domainName)->first();
                        
                        if ($localDomain) {
                            $batchFound++;
                            $progressBar->advance();
                            continue;
                        }
                        
                        // Dominio no encontrado - es un dominio faltante
                        if ($dryRun) {
                            $this->line("\n🔍 Dominio faltante encontrado: {$domainName}");
                            $progressBar->advance();
                            continue;
                        }
                        
                        // Obtener información detallada del dominio
                        $detailUrl = "https://api.hosting.ionos.com/domains/v1/domainitems/{$ionosDomain['id']}?includeDomainStatus=true";
                        $detailResponse = Http::withHeaders([
                            'X-API-Key' => config('services.ionos.api_key'),
                            'X-Tenant-Id' => config('services.ionos.tenant_id'),
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ])->get($detailUrl);
                        
                        if ($detailResponse->successful()) {
                            $detailData = $detailResponse->json();
                            $parsedData = $this->parseDomainData($detailData);
                            
                            // Crear el nuevo dominio
                            $newDomain = Dominio::create([
                                'dominio' => $domainName,
                                'client_id' => $defaultClientId ?? 1, // Cliente por defecto o ID 1
                                'date_start' => $parsedData['fecha_activacion_ionos'] ?? now(),
                                'date_end' => $parsedData['fecha_renovacion_ionos'] ?? now()->addYear(),
                                'estado_id' => 1, // Estado por defecto (activo)
                                'comentario' => 'Dominio sincronizado desde IONOS',
                                'fecha_activacion_ionos' => $parsedData['fecha_activacion_ionos'],
                                'fecha_renovacion_ionos' => $parsedData['fecha_renovacion_ionos'],
                                'sincronizado_ionos' => true,
                                'ultima_sincronizacion_ionos' => now()
                            ]);
                            
                            $batchCreated++;
                            $this->line("\n✅ Dominio creado: {$domainName} (ID: {$newDomain->id})");
                        } else {
                            $batchErrors++;
                            $this->line("\n❌ Error obteniendo detalles de {$domainName}: " . $detailResponse->body());
                        }
                        
                        $progressBar->advance();
                        
                    } catch (\Exception $e) {
                        $batchErrors++;
                        $this->line("\n❌ Error procesando {$domainName}: " . $e->getMessage());
                        $progressBar->advance();
                    }
                }
                
                $progressBar->finish();
                $this->newLine();
                
                // Mostrar resumen del lote
                $this->info("📊 Resumen del lote (offset {$offset}):");
                $this->line("  - Dominios encontrados en local: {$batchFound}");
                $this->line("  - Dominios creados: {$batchCreated}");
                $this->line("  - Errores: {$batchErrors}");
                
                $totalCreated += $batchCreated;
                $totalErrors += $batchErrors;
                
                // Verificar si hay más dominios
                if (count($ionosDomains) < $batchSize) {
                    $hasMore = false;
                } else {
                    $offset += $batchSize;
                }
                
                // Pausa entre lotes para no sobrecargar la API
                if ($hasMore) {
                    $this->info("⏳ Esperando 2 segundos antes del siguiente lote...");
                    sleep(2);
                }
            }
            
            // Mostrar resumen final
            $this->newLine();
            $this->info('🎉 Sincronización completada:');
            $this->line("  - Total de dominios creados: {$totalCreated}");
            $this->line("  - Total de errores: {$totalErrors}");
            
            if ($dryRun) {
                $this->info('🔍 Modo DRY RUN: No se crearon dominios reales.');
                $this->info('💡 Para crear los dominios, ejecuta sin --dry-run');
            } else {
                $this->info('✅ Sincronización completada exitosamente.');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
            Log::error('Error en SyncAllMissingIonosDomains: ' . $e->getMessage());
        }
    }
    
    /**
     * Parsear datos del dominio de IONOS
     */
    private function parseDomainData($domainData)
    {
        $result = [
            'fecha_activacion_ionos' => null,
            'fecha_renovacion_ionos' => null,
        ];

        // Procesar fecha de expiración
        if (isset($domainData['expirationDate'])) {
            $result['fecha_renovacion_ionos'] = $this->parseDate($domainData['expirationDate']);
        }

        // Procesar fecha de renovación desde el estado
        if (isset($domainData['status']['provisioningStatus']['setToRenewOn'])) {
            $result['fecha_renovacion_ionos'] = $this->parseDate($domainData['status']['provisioningStatus']['setToRenewOn']);
        }

        // Calcular fecha de activación aproximada
        $result['fecha_activacion_ionos'] = $this->calculateApproximateRegistrationDate($domainData);

        return $result;
    }
    
    /**
     * Parsear fecha de IONOS
     */
    private function parseDate($dateString)
    {
        if (!$dateString) return null;
        
        try {
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Calcular fecha de registro aproximada
     */
    private function calculateApproximateRegistrationDate($domainData)
    {
        try {
            if (!isset($domainData['expirationDate'])) {
                return null;
            }
            
            $expirationDate = $this->parseDate($domainData['expirationDate']);
            if (!$expirationDate) {
                return null;
            }
            
            // Calcular fecha de registro restando el período típico de registro
            $expiration = new \DateTime($expirationDate);
            
            // Determinar período de registro basado en el TLD
            $tld = $domainData['tld'] ?? 'com';
            $registrationPeriod = $this->getTypicalRegistrationPeriod($tld);
            
            $expiration->modify("-{$registrationPeriod} year");
            
            return $expiration->format('Y-m-d H:i:s');
            
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Obtener período típico de registro por TLD
     */
    private function getTypicalRegistrationPeriod($tld)
    {
        $periods = [
            'com' => 1, 'net' => 1, 'org' => 1, 'info' => 1, 'biz' => 1,
            'es' => 1, 'eu' => 1, 'uk' => 1, 'de' => 1, 'fr' => 1,
            'it' => 1, 'nl' => 1, 'be' => 1, 'pt' => 1, 'at' => 1,
            'ch' => 1, 'se' => 1, 'no' => 1, 'dk' => 1, 'fi' => 1,
            'pl' => 1, 'cz' => 1, 'hu' => 1, 'sk' => 1, 'si' => 1,
            'hr' => 1, 'bg' => 1, 'ro' => 1, 'lt' => 1, 'lv' => 1,
            'ee' => 1, 'ie' => 1, 'mt' => 1, 'cy' => 1, 'lu' => 1,
            'li' => 1, 'is' => 1, 'gr' => 1, 'tr' => 1, 'ru' => 1,
            'ua' => 1, 'by' => 1, 'md' => 1, 'ge' => 1, 'am' => 1,
            'az' => 1, 'kz' => 1, 'kg' => 1, 'tj' => 1, 'tm' => 1,
            'uz' => 1, 'mn' => 1, 'cn' => 1, 'jp' => 1, 'kr' => 1,
            'tw' => 1, 'hk' => 1, 'sg' => 1, 'my' => 1, 'th' => 1,
            'ph' => 1, 'id' => 1, 'vn' => 1, 'la' => 1, 'kh' => 1,
            'mm' => 1, 'bd' => 1, 'lk' => 1, 'mv' => 1, 'bt' => 1,
            'np' => 1, 'pk' => 1, 'af' => 1, 'ir' => 1, 'iq' => 1,
            'sy' => 1, 'lb' => 1, 'jo' => 1, 'il' => 1, 'ps' => 1,
            'sa' => 1, 'ae' => 1, 'qa' => 1, 'bh' => 1, 'kw' => 1,
            'om' => 1, 'ye' => 1, 'so' => 1, 'dj' => 1, 'et' => 1,
            'er' => 1, 'sd' => 1, 'ss' => 1, 'ke' => 1, 'ug' => 1,
            'tz' => 1, 'rw' => 1, 'bi' => 1, 'mw' => 1, 'zm' => 1,
            'zw' => 1, 'bw' => 1, 'na' => 1, 'sz' => 1, 'ls' => 1,
            'mg' => 1, 'mu' => 1, 'sc' => 1, 'km' => 1, 'yt' => 1,
            're' => 1, 'mz' => 1, 'ao' => 1, 'cd' => 1, 'cg' => 1,
            'cf' => 1, 'td' => 1, 'cm' => 1, 'gq' => 1, 'ga' => 1,
            'st' => 1, 'cv' => 1, 'gw' => 1, 'gn' => 1, 'sl' => 1,
            'lr' => 1, 'ci' => 1, 'gh' => 1, 'tg' => 1, 'bj' => 1,
            'ne' => 1, 'bf' => 1, 'ml' => 1, 'sn' => 1, 'gm' => 1,
            'us' => 1, 'ca' => 1, 'mx' => 1, 'br' => 1, 'ar' => 1,
            'cl' => 1, 'co' => 1, 'pe' => 1, 've' => 1, 'ec' => 1,
            'bo' => 1, 'py' => 1, 'uy' => 1, 'gy' => 1, 'sr' => 1,
            'gf' => 1, 'fk' => 1, 'au' => 1, 'nz' => 1, 'fj' => 1,
            'pg' => 1, 'sb' => 1, 'vu' => 1, 'nc' => 1, 'pf' => 1,
            'ws' => 1, 'to' => 1, 'tv' => 1, 'ki' => 1, 'nr' => 1,
            'pw' => 1, 'fm' => 1, 'mh' => 1, 'mp' => 1, 'gu' => 1,
            'as' => 1, 'vi' => 1, 'pr' => 1, 'do' => 1, 'ht' => 1,
            'cu' => 1, 'jm' => 1, 'bb' => 1, 'ag' => 1, 'dm' => 1,
            'gd' => 1, 'kn' => 1, 'lc' => 1, 'vc' => 1, 'tt' => 1,
            'bz' => 1, 'gt' => 1, 'sv' => 1, 'hn' => 1, 'ni' => 1,
            'cr' => 1, 'pa' => 1, 'aw' => 1, 'an' => 1, 'cw' => 1,
            'sx' => 1, 'bq' => 1, 'ai' => 1, 'vg' => 1, 'ky' => 1,
            'tc' => 1, 'bs' => 1, 'bm' => 1, 'gl' => 1, 'fo' => 1,
            'sj' => 1, 'svalbard' => 1, 'jan' => 1, 'may' => 1,
            'jun' => 1, 'jul' => 1, 'aug' => 1, 'sep' => 1,
            'oct' => 1, 'nov' => 1, 'dec' => 1, 'january' => 1,
            'february' => 1, 'march' => 1, 'april' => 1, 'may' => 1,
            'june' => 1, 'july' => 1, 'august' => 1, 'september' => 1,
            'october' => 1, 'november' => 1, 'december' => 1
        ];
        
        return $periods[strtolower($tld)] ?? 1; // Por defecto 1 año
    }
}
