<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestIonosCurl extends Command
{
    protected $signature = 'ionos:test-curl {domain : Dominio a probar}';
    protected $description = 'Prueba la conexión con IONOS usando cURL directo.';

    public function handle()
    {
        $domain = $this->argument('domain');
        
        $this->info("🔍 Probando conexión con IONOS para: {$domain}");
        
        // Obtener configuración
        $apiKey = config('services.ionos.api_key');
        $tenantId = config('services.ionos.tenant_id');
        $baseUrl = config('services.ionos.base_url', 'https://api.hosting.ionos.com/domains/v1/domainitems');
        
        $this->line("📡 Configuración:");
        $this->line("  - API Key: " . substr($apiKey, 0, 10) . "...");
        $this->line("  - Tenant ID: {$tenantId}");
        $this->line("  - Base URL: {$baseUrl}");
        
        // Comando cURL para obtener lista de dominios
        $curlCommand = "curl -X GET '{$baseUrl}?includeDomainStatus=true&sortBy=DOMAIN_NAME&direction=ASC&limit=100' " .
                      "-H 'X-API-Key: {$apiKey}' " .
                      "-H 'X-Tenant-Id: {$tenantId}' " .
                      "-H 'Content-Type: application/json' " .
                      "-H 'Accept: application/json' " .
                      "--connect-timeout 30 " .
                      "--max-time 60";
        
        $this->line("\n🔧 Comando cURL:");
        $this->line($curlCommand);
        
        $this->line("\n⏳ Ejecutando cURL...");
        
        // Ejecutar cURL
        $output = [];
        $returnCode = 0;
        exec($curlCommand . ' 2>&1', $output, $returnCode);
        
        // Filtrar solo las líneas que contienen JSON
        $jsonLines = array_filter($output, function($line) {
            return strpos($line, '{') === 0 || strpos($line, '[') === 0;
        });
        
        $response = implode("\n", $jsonLines);
        
        if ($returnCode === 0) {
            $this->info("✅ cURL ejecutado exitosamente");
            
            // Intentar decodificar JSON
            $data = json_decode($response, true);
            if ($data) {
                $this->line("\n📊 Respuesta JSON:");
                $this->line("  - Status: " . ($data['status'] ?? 'N/A'));
                $this->line("  - Total dominios: " . (isset($data['domains']) ? count($data['domains']) : 'N/A'));
                
                if (isset($data['domains'])) {
                    $found = false;
                    foreach ($data['domains'] as $domainData) {
                        if (isset($domainData['name']) && $domainData['name'] === $domain) {
                            $found = true;
                            $this->info("\n🎯 Dominio encontrado: {$domain}");
                            $this->line("  - ID: " . ($domainData['id'] ?? 'N/A'));
                            $this->line("  - Status: " . ($domainData['status'] ?? 'N/A'));
                            $this->line("  - TLD: " . ($domainData['tld'] ?? 'N/A'));
                            $this->line("  - Auto Renew: " . ($domainData['autoRenew'] ? 'SÍ' : 'NO'));
                            if (isset($domainData['expirationDate'])) {
                                $this->line("  - Fecha expiración: " . $domainData['expirationDate']);
                            }
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $this->warn("\n❌ Dominio NO encontrado en la lista");
                        $this->line("Primeros 10 dominios encontrados:");
                        $count = 0;
                        foreach ($data['domains'] as $domainData) {
                            if ($count >= 10) break;
                            $this->line("  - " . ($domainData['name'] ?? 'N/A'));
                            $count++;
                        }
                    }
                }
            } else {
                $this->error("❌ Error al decodificar JSON");
                $this->line("Respuesta cruda:");
                $this->line(substr($response, 0, 500) . "...");
            }
        } else {
            $this->error("❌ Error en cURL (código: {$returnCode})");
            $this->line("Respuesta:");
            $this->line($response);
        }
        
        $this->line("\n💡 Para probar manualmente, copia y pega este comando:");
        $this->line($curlCommand);
    }
}
