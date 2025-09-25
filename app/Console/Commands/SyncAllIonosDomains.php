<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IonosApiService;
use App\Models\Dominios\Dominio;
use Illuminate\Support\Facades\Log;

class SyncAllIonosDomains extends Command
{
    protected $signature = 'ionos:sync-all {--limit=50 : Límite de dominios a procesar} {--offset=0 : Offset inicial}';
    protected $description = 'Sincronizar todos los dominios con IONOS';

    public function handle()
    {
        $this->info('🚀 Iniciando sincronización masiva con IONOS...');
        
        $ionosService = new IonosApiService();
        $limit = $this->option('limit');
        $offset = $this->option('offset');
        
        $this->line("📋 Configuración:");
        $this->line("  - Límite: {$limit}");
        $this->line("  - Offset: {$offset}");
        
        try {
            // Obtener todos los dominios de IONOS
            $this->info('📡 Obteniendo lista de dominios de IONOS...');
            $response = \Illuminate\Support\Facades\Http::withHeaders([
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
            $this->info("🔄 Procesando " . count($ionosDomains) . " dominios...");
            
            $syncedCount = 0;
            $errorCount = 0;
            $notFoundCount = 0;
            
            $progressBar = $this->output->createProgressBar(count($ionosDomains));
            $progressBar->start();
            
            foreach ($ionosDomains as $ionosDomain) {
                try {
                    $domainName = $ionosDomain['name'];
                    
                    // Buscar el dominio en nuestra base de datos
                    $localDomain = Dominio::where('dominio', $domainName)->first();
                    
                    if (!$localDomain) {
                        $notFoundCount++;
                        $progressBar->advance();
                        continue;
                    }
                    
                    // Obtener información detallada del dominio
                    $detailUrl = "https://api.hosting.ionos.com/domains/v1/domainitems/{$ionosDomain['id']}?includeDomainStatus=true";
                    $detailResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'X-API-Key' => config('services.ionos.api_key'),
                        'X-Tenant-Id' => config('services.ionos.tenant_id'),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->get($detailUrl);
                    
                    if ($detailResponse->successful()) {
                        $detailData = $detailResponse->json();
                        $parsedData = $this->parseDomainData($detailData);
                        
                        // Actualizar el dominio local
                        $localDomain->update([
                            'fecha_activacion_ionos' => $parsedData['fecha_activacion_ionos'],
                            'fecha_renovacion_ionos' => $parsedData['fecha_renovacion_ionos'],
                            'sincronizado_ionos' => true,
                            'ultima_sincronizacion_ionos' => now()
                        ]);
                        
                        $syncedCount++;
                    } else {
                        $errorCount++;
                        Log::warning("Error obteniendo detalles de {$domainName}: " . $detailResponse->body());
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("Error procesando {$domainName}: " . $e->getMessage());
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->newLine(2);
            
            // Mostrar resumen
            $this->info('📈 Resumen de sincronización:');
            $this->line("  ✅ Sincronizados: {$syncedCount}");
            $this->line("  ❌ Errores: {$errorCount}");
            $this->line("  🔍 No encontrados en BD local: {$notFoundCount}");
            $this->line("  📊 Total procesados: " . count($ionosDomains));
            
            if ($syncedCount > 0) {
                $this->info('🎉 Sincronización completada exitosamente');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error general: ' . $e->getMessage());
            Log::error('Error en sincronización masiva IONOS: ' . $e->getMessage());
        }
    }
    
    /**
     * Procesar los datos del dominio obtenidos de la API
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

        // Procesar fecha de activación (si está disponible)
        if (isset($domainData['created'])) {
            $result['fecha_activacion_ionos'] = $this->parseDate($domainData['created']);
        }

        return $result;
    }
    
    /**
     * Convertir fecha de la API a formato estándar
     */
    private function parseDate($dateString)
    {
        if (!$dateString) {
            return null;
        }

        try {
            $formats = [
                'Y-m-d\TH:i:s\Z',
                'Y-m-d\TH:i:s.u\Z',
                'Y-m-d H:i:s',
                'Y-m-d'
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d H:i:s');
                }
            }

            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Error parseando fecha de IONOS: ' . $dateString . ' - ' . $e->getMessage());
            return null;
        }
    }
}
