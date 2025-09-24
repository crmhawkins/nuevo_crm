<?php

namespace App\Services;

use App\Models\Dominios\Dominio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class DomainSyncService
{
    private $externalHost = "82.223.118.182";
    private $externalUser = "dominios_hawkins";
    private $externalPassword = "5z452iA#e";
    private $externalDatabase = "dominios_hawkins";

    /**
     * Obtener conexión a la base de datos externa
     */
    private function getExternalConnection()
    {
        try {
            $conn = new \mysqli($this->externalHost, $this->externalUser, $this->externalPassword, $this->externalDatabase);
            
            if ($conn->connect_error) {
                throw new Exception("Error de conexión a la base externa: " . $conn->connect_error);
            }
            
            return $conn;
        } catch (Exception $e) {
            Log::error("Error conectando a la base externa: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtener todos los dominios de la base externa
     */
    public function getExternalDomains()
    {
        $conn = $this->getExternalConnection();
        
        try {
            $query = "SELECT id, nombre, fecha_expiracion, precio_compra, precio_venta, IBAN FROM dominios";
            $result = $conn->query($query);
            
            $domains = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $domains[] = $row;
                }
            }
            
            return $domains;
        } catch (Exception $e) {
            Log::error("Error obteniendo dominios externos: " . $e->getMessage());
            throw $e;
        } finally {
            $conn->close();
        }
    }

    /**
     * Sincronizar un dominio específico
     */
    public function syncDomain($domainName, $externalData)
    {
        try {
            // Normalizar el dominio para la búsqueda
            $normalizedDomain = $this->normalizeDomain($domainName);
            
            // Buscar el dominio usando normalización
            $domain = Dominio::all()->filter(function($d) use ($normalizedDomain) {
                return $this->normalizeDomain($d->dominio) === $normalizedDomain;
            })->first();
            
            if (!$domain) {
                Log::warning("Dominio no encontrado en la base local: {$domainName} (normalizado: {$normalizedDomain})");
                return false;
            }

            $domain->update([
                'precio_compra' => $externalData['precio_compra'] ?? null,
                'precio_venta' => $externalData['precio_venta'] ?? null,
                'iban' => $externalData['IBAN'] ?? null,
                'sincronizado' => true,
                'ultima_sincronizacion' => now()
            ]);

            Log::info("Dominio sincronizado: {$domainName}");
            return true;
            
        } catch (Exception $e) {
            Log::error("Error sincronizando dominio {$domainName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sincronizar todos los dominios
     */
    public function syncAllDomains()
    {
        try {
            $externalDomains = $this->getExternalDomains();
            $syncedCount = 0;
            $errorCount = 0;
            $errors = [];

            Log::info("Iniciando sincronización de " . count($externalDomains) . " dominios");

            foreach ($externalDomains as $externalDomain) {
                $domainName = $externalDomain['nombre'];
                
                try {
                    if ($this->syncDomain($domainName, $externalDomain)) {
                        $syncedCount++;
                    } else {
                        $errorCount++;
                        $errors[] = "Error sincronizando {$domainName}: Dominio no encontrado en base local";
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    $errors[] = "Error sincronizando {$domainName}: " . $e->getMessage();
                }
            }

            Log::info("Sincronización completada. Exitosos: {$syncedCount}, Errores: {$errorCount}");
            
            return [
                'total' => count($externalDomains),
                'synced' => $syncedCount,
                'errors' => $errorCount,
                'error_details' => $errors
            ];

        } catch (Exception $e) {
            Log::error("Error en sincronización masiva: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Normalizar dominio para comparación
     */
    private function normalizeDomain($domain)
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/'); // Eliminar barra final
        return $domain;
    }

    /**
     * Buscar dominios que necesitan sincronización
     */
    public function getDomainsNeedingSync()
    {
        return Dominio::where(function($query) {
            $query->where('sincronizado', false)
                  ->orWhereNull('sincronizado')
                  ->orWhere('ultima_sincronizacion', '<', now()->subDays(7));
        })->get();
    }

    /**
     * Obtener estadísticas de sincronización
     */
    public function getSyncStats()
    {
        $total = Dominio::count();
        $synced = Dominio::where('sincronizado', true)->count();
        $notSynced = $total - $synced;
        $lastSync = Dominio::where('sincronizado', true)
                          ->max('ultima_sincronizacion');

        return [
            'total_domains' => $total,
            'synced_domains' => $synced,
            'not_synced_domains' => $notSynced,
            'sync_percentage' => $total > 0 ? round(($synced / $total) * 100, 2) : 0,
            'last_sync' => $lastSync
        ];
    }

    /**
     * Buscar dominios por nombre (búsqueda flexible)
     */
    public function searchDomainsByName($searchTerm)
    {
        return Dominio::where('dominio', 'like', "%{$searchTerm}%")
                      ->orWhere('dominio', 'like', "%{$searchTerm}%")
                      ->get();
    }

    /**
     * Obtener dominios con información de precios
     */
    public function getDomainsWithPricing()
    {
        return Dominio::whereNotNull('precio_compra')
                      ->orWhereNotNull('precio_venta')
                      ->get();
    }

    /**
     * Calcular estadísticas de precios
     */
    public function getPricingStats()
    {
        $domains = Dominio::whereNotNull('precio_compra')
                          ->whereNotNull('precio_venta')
                          ->get();

        if ($domains->isEmpty()) {
            return [
                'total_investment' => 0,
                'total_revenue' => 0,
                'total_profit' => 0,
                'average_margin' => 0,
                'count' => 0
            ];
        }

        $totalInvestment = $domains->sum('precio_compra');
        $totalRevenue = $domains->sum('precio_venta');
        $totalProfit = $totalRevenue - $totalInvestment;
        $averageMargin = $totalInvestment > 0 ? ($totalProfit / $totalInvestment) * 100 : 0;

        return [
            'total_investment' => $totalInvestment,
            'total_revenue' => $totalRevenue,
            'total_profit' => $totalProfit,
            'average_margin' => round($averageMargin, 2),
            'count' => $domains->count()
        ];
    }
}
