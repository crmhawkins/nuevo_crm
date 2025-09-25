<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class IonosApiService
{
    private $apiKey;
    private $baseUrl;
    private $tenantId;

    public function __construct()
    {
        $this->apiKey = config('services.ionos.api_key');
        $this->baseUrl = config('services.ionos.base_url', 'https://api.hosting.ionos.com/domains/v1/domainitems');
        $this->tenantId = config('services.ionos.tenant_id');
    }

    /**
     * Obtener información de un dominio específico
     */
    public function getDomainInfo($domainName)
    {
        try {
            // Primero obtener la lista de dominios para encontrar el ID
            $listResponse = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-Tenant-Id' => $this->tenantId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(30)->get($this->baseUrl, [
                'includeDomainStatus' => 'true',
                'sortBy' => 'DOMAIN_NAME',
                'direction' => 'ASC',
                'limit' => 100
            ]);

            if (!$listResponse->successful()) {
                return [
                    'success' => false,
                    'message' => 'Error al obtener lista de dominios: ' . $listResponse->body()
                ];
            }

            $listData = $listResponse->json();
            $domainId = null;

            // Buscar el dominio en la lista
            if (isset($listData['domains'])) {
                foreach ($listData['domains'] as $domain) {
                    if ($domain['name'] === $domainName) {
                        $domainId = $domain['id'];
                        break;
                    }
                }
            }

            if (!$domainId) {
                return [
                    'success' => false,
                    'message' => 'Dominio no encontrado en IONOS'
                ];
            }

            // Obtener información detallada del dominio
            $detailUrl = "https://api.hosting.ionos.com/domains/v1/domainitems/{$domainId}?includeDomainStatus=true";
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-Tenant-Id' => $this->tenantId,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(30)->get($detailUrl);

            if ($response->successful()) {
                $data = $response->json();
                return $this->parseDomainData($data);
            }

            return [
                'success' => false,
                'message' => 'Error al obtener detalles del dominio: ' . $response->body()
            ];

        } catch (Exception $e) {
            Log::error('Error en IonosApiService: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener todos los dominios de la cuenta
     */
    public function getAllDomains()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-Ionos-Integration/1.0'
            ])->timeout(30)->get($this->baseUrl);

            if ($response->successful()) {
                $data = $response->json();
                $domains = [];
                
                // La API de IONOS devuelve los dominios en el campo 'domains'
                if (isset($data['domains'])) {
                    foreach ($data['domains'] as $domain) {
                        $domains[] = $this->parseDomainData($domain);
                    }
                }
                
                return [
                    'success' => true,
                    'domains' => $domains
                ];
            }

            return [
                'success' => false,
                'message' => 'Error en la API de IONOS: ' . $response->body()
            ];

        } catch (Exception $e) {
            Log::error('Error en IonosApiService getAllDomains: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Procesar los datos del dominio obtenidos de la API
     */
    private function parseDomainData($domainData)
    {
        $result = [
            'success' => true,
            'domain_name' => $domainData['name'] ?? null,
            'status' => $domainData['status'] ?? null,
            'fecha_activacion_ionos' => null,
            'fecha_renovacion_ionos' => null,
            'fecha_expiracion' => null,
            'auto_renew' => $domainData['autoRenew'] ?? false,
            'domain_id' => $domainData['id'] ?? null,
            'tld' => $domainData['tld'] ?? null,
            'privacy_enabled' => $domainData['privacyEnabled'] ?? false,
            'transfer_lock' => $domainData['transferLock'] ?? false,
            'dns_sec_enabled' => $domainData['dnsSecEnabled'] ?? false,
            'domain_type' => $domainData['domainType'] ?? null,
            'domain_guard_enabled' => $domainData['domainGuardEnabled'] ?? false,
            'raw_data' => $domainData
        ];

        // Procesar fecha de expiración
        if (isset($domainData['expirationDate'])) {
            $result['fecha_expiracion'] = $this->parseDate($domainData['expirationDate']);
            $result['fecha_renovacion_ionos'] = $this->parseDate($domainData['expirationDate']);
        }

        // Procesar fecha de renovación desde el estado
        if (isset($domainData['status']['provisioningStatus']['setToRenewOn'])) {
            $result['fecha_renovacion_ionos'] = $this->parseDate($domainData['status']['provisioningStatus']['setToRenewOn']);
        }

        // Procesar fecha de activación
        // Nota: La API de IONOS no proporciona createdDate para dominios
        // Usamos el cálculo aproximado basado en la fecha de expiración
        $result['fecha_activacion_ionos'] = $this->calculateApproximateRegistrationDate($domainData);

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
            // Intentar diferentes formatos de fecha
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

            // Si no funciona con formatos específicos, usar strtotime
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }

            return null;

        } catch (Exception $e) {
            Log::warning('Error parseando fecha de IONOS: ' . $dateString . ' - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verificar si las credenciales de la API son válidas
     */
    public function testConnection()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'Laravel-Ionos-Integration/1.0'
            ])->timeout(10)->get($this->baseUrl, [
                'limit' => 1
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Conexión exitosa con la API de IONOS'
                ];
            }

            return [
                'success' => false,
                'message' => 'Error de autenticación: ' . $response->status()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Obtener fecha de registro del dominio usando API WHOIS
     */
    public function getDomainRegistrationDate($domainName)
    {
        try {
            // Limpiar el dominio
            $cleanDomain = $this->cleanDomainForWhois($domainName);
            
            // Usar API gratuita de WHOIS
            $response = Http::timeout(10)->get("https://api.whoisjson.com/v1/{$cleanDomain}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Buscar fecha de creación en diferentes campos
                $creationFields = [
                    'creation_date',
                    'created_date',
                    'registered_date',
                    'registration_date',
                    'created',
                    'registration'
                ];
                
                foreach ($creationFields as $field) {
                    if (isset($data[$field]) && $data[$field]) {
                        $parsedDate = $this->parseWhoisDate($data[$field]);
                        if ($parsedDate) {
                            return $parsedDate;
                        }
                    }
                }
            }
            
            // Fallback: intentar con otra API
            return $this->getDomainRegistrationDateAlternative($cleanDomain);
            
        } catch (\Exception $e) {
            Log::warning('Error obteniendo fecha de registro WHOIS para ' . $domainName . ': ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Método alternativo para obtener fecha de registro
     */
    private function getDomainRegistrationDateAlternative($domainName)
    {
        try {
            // Usar API alternativa
            $response = Http::timeout(10)->get("https://whoisjson.com/api/v1/{$domainName}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['creation_date'])) {
                    return $this->parseWhoisDate($data['creation_date']);
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            Log::warning('Error en método alternativo WHOIS para ' . $domainName . ': ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Limpiar dominio para consulta WHOIS
     */
    private function cleanDomainForWhois($domainName)
    {
        // Remover protocolo si existe
        $domain = preg_replace('/^https?:\/\//', '', $domainName);
        // Remover www si existe
        $domain = preg_replace('/^www\./', '', $domain);
        // Remover barra final si existe
        $domain = rtrim($domain, '/');
        
        return $domain;
    }
    
    /**
     * Parsear fecha de WHOIS
     */
    private function parseWhoisDate($dateString)
    {
        try {
            // Formatos comunes de fecha en WHOIS
            $formats = [
                'Y-m-d\TH:i:s\Z',
                'Y-m-d\TH:i:s.u\Z',
                'Y-m-d H:i:s',
                'Y-m-d',
                'd-m-Y',
                'd/m/Y',
                'Y-m-d\TH:i:s',
                'Y-m-d\TH:i:s.u'
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d H:i:s');
                }
            }

            // Intentar con strtotime como último recurso
            $timestamp = strtotime($dateString);
            if ($timestamp !== false) {
                return date('Y-m-d H:i:s', $timestamp);
            }

            return null;

        } catch (\Exception $e) {
            Log::warning('Error parseando fecha WHOIS: ' . $dateString . ' - ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calcular fecha de registro aproximada basada en la expiración
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
            Log::warning('Error calculando fecha de registro aproximada: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtener período típico de registro basado en el TLD
     */
    private function getTypicalRegistrationPeriod($tld)
    {
        $periods = [
            'com' => 1,
            'net' => 1,
            'org' => 1,
            'info' => 1,
            'biz' => 1,
            'es' => 1,
            'eu' => 1,
            'uk' => 1,
            'de' => 1,
            'fr' => 1,
            'it' => 1,
            'nl' => 1,
            'be' => 1,
            'pt' => 1,
            'at' => 1,
            'ch' => 1,
            'se' => 1,
            'no' => 1,
            'dk' => 1,
            'fi' => 1,
            'pl' => 1,
            'cz' => 1,
            'hu' => 1,
            'sk' => 1,
            'si' => 1,
            'hr' => 1,
            'bg' => 1,
            'ro' => 1,
            'lt' => 1,
            'lv' => 1,
            'ee' => 1,
            'ie' => 1,
            'mt' => 1,
            'cy' => 1,
            'lu' => 1,
            'li' => 1,
            'is' => 1,
            'gr' => 1,
            'tr' => 1,
            'ru' => 1,
            'ua' => 1,
            'by' => 1,
            'md' => 1,
            'ge' => 1,
            'am' => 1,
            'az' => 1,
            'kz' => 1,
            'kg' => 1,
            'tj' => 1,
            'tm' => 1,
            'uz' => 1,
            'mn' => 1,
            'cn' => 1,
            'jp' => 1,
            'kr' => 1,
            'tw' => 1,
            'hk' => 1,
            'sg' => 1,
            'my' => 1,
            'th' => 1,
            'ph' => 1,
            'id' => 1,
            'vn' => 1,
            'la' => 1,
            'kh' => 1,
            'mm' => 1,
            'bd' => 1,
            'lk' => 1,
            'mv' => 1,
            'bt' => 1,
            'np' => 1,
            'pk' => 1,
            'af' => 1,
            'ir' => 1,
            'iq' => 1,
            'sy' => 1,
            'lb' => 1,
            'jo' => 1,
            'il' => 1,
            'ps' => 1,
            'sa' => 1,
            'ae' => 1,
            'qa' => 1,
            'bh' => 1,
            'kw' => 1,
            'om' => 1,
            'ye' => 1,
            'so' => 1,
            'dj' => 1,
            'et' => 1,
            'er' => 1,
            'sd' => 1,
            'ss' => 1,
            'ke' => 1,
            'ug' => 1,
            'tz' => 1,
            'rw' => 1,
            'bi' => 1,
            'mw' => 1,
            'zm' => 1,
            'zw' => 1,
            'bw' => 1,
            'na' => 1,
            'sz' => 1,
            'ls' => 1,
            'mg' => 1,
            'mu' => 1,
            'sc' => 1,
            'km' => 1,
            'yt' => 1,
            're' => 1,
            'mz' => 1,
            'ao' => 1,
            'cd' => 1,
            'cg' => 1,
            'cf' => 1,
            'td' => 1,
            'cm' => 1,
            'gq' => 1,
            'ga' => 1,
            'st' => 1,
            'cv' => 1,
            'gw' => 1,
            'gn' => 1,
            'sl' => 1,
            'lr' => 1,
            'ci' => 1,
            'gh' => 1,
            'tg' => 1,
            'bj' => 1,
            'ne' => 1,
            'bf' => 1,
            'ml' => 1,
            'sn' => 1,
            'gm' => 1,
            'gn' => 1,
            'gw' => 1,
            'cv' => 1,
            'st' => 1,
            'ao' => 1,
            'mz' => 1,
            'mg' => 1,
            'mu' => 1,
            'sc' => 1,
            'km' => 1,
            'yt' => 1,
            're' => 1,
            'zw' => 1,
            'zm' => 1,
            'bw' => 1,
            'na' => 1,
            'sz' => 1,
            'ls' => 1,
            'za' => 1,
            'us' => 1,
            'ca' => 1,
            'mx' => 1,
            'br' => 1,
            'ar' => 1,
            'cl' => 1,
            'co' => 1,
            'pe' => 1,
            've' => 1,
            'ec' => 1,
            'bo' => 1,
            'py' => 1,
            'uy' => 1,
            'gy' => 1,
            'sr' => 1,
            'gf' => 1,
            'fk' => 1,
            'au' => 1,
            'nz' => 1,
            'fj' => 1,
            'pg' => 1,
            'sb' => 1,
            'vu' => 1,
            'nc' => 1,
            'pf' => 1,
            'ws' => 1,
            'to' => 1,
            'tv' => 1,
            'ki' => 1,
            'nr' => 1,
            'pw' => 1,
            'fm' => 1,
            'mh' => 1,
            'mp' => 1,
            'gu' => 1,
            'as' => 1,
            'vi' => 1,
            'pr' => 1,
            'do' => 1,
            'ht' => 1,
            'cu' => 1,
            'jm' => 1,
            'bb' => 1,
            'ag' => 1,
            'dm' => 1,
            'gd' => 1,
            'kn' => 1,
            'lc' => 1,
            'vc' => 1,
            'tt' => 1,
            'bz' => 1,
            'gt' => 1,
            'sv' => 1,
            'hn' => 1,
            'ni' => 1,
            'cr' => 1,
            'pa' => 1,
            'aw' => 1,
            'an' => 1,
            'cw' => 1,
            'sx' => 1,
            'bq' => 1,
            'ai' => 1,
            'vg' => 1,
            'ky' => 1,
            'tc' => 1,
            'bs' => 1,
            'bm' => 1,
            'gl' => 1,
            'fo' => 1,
            'sj' => 1,
            'svalbard' => 1,
            'jan' => 1,
            'may' => 1,
            'jun' => 1,
            'jul' => 1,
            'aug' => 1,
            'sep' => 1,
            'oct' => 1,
            'nov' => 1,
            'dec' => 1,
            'january' => 1,
            'february' => 1,
            'march' => 1,
            'april' => 1,
            'may' => 1,
            'june' => 1,
            'july' => 1,
            'august' => 1,
            'september' => 1,
            'october' => 1,
            'november' => 1,
            'december' => 1
        ];
        
        return $periods[strtolower($tld)] ?? 1; // Por defecto 1 año
    }
}
