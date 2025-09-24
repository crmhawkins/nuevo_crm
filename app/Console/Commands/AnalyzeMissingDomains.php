<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DomainSyncService;
use App\Models\Dominios\Dominio;

class AnalyzeMissingDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:analyze-missing 
                            {--limit=20 : Límite de dominios a analizar}
                            {--show-details : Mostrar detalles de cada dominio}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analizar dominios faltantes en la base local';

    protected $syncService;

    public function __construct(DomainSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 ANÁLISIS DE DOMINIOS FALTANTES');
        $this->line(str_repeat('=', 50));

        $limit = (int) $this->option('limit');
        $showDetails = $this->option('show-details');

        try {
            // Obtener dominios externos
            $externalDomains = $this->syncService->getExternalDomains();
            $this->info("📊 Total de dominios en base externa: " . count($externalDomains));

            // Obtener dominios locales
            $localDomains = Dominio::pluck('dominio')->toArray();
            $this->info("📊 Total de dominios en base local: " . count($localDomains));

            $this->newLine();

            // Analizar dominios faltantes
            $this->analyzeMissingDomains($externalDomains, $localDomains, $limit, $showDetails);

        } catch (\Exception $e) {
            $this->error("❌ Error en análisis: " . $e->getMessage());
        }
    }

    private function analyzeMissingDomains($externalDomains, $localDomains, $limit, $showDetails)
    {
        $this->info('🔍 ANÁLISIS DE DOMINIOS FALTANTES');
        $this->line(str_repeat('-', 40));

        $missingDomains = [];
        $count = 0;

        foreach ($externalDomains as $externalDomain) {
            if ($count >= $limit) break;
            $count++;

            $domainName = $externalDomain['nombre'];
            $normalizedExternal = $this->normalizeDomain($domainName);
            
            $found = false;
            foreach ($localDomains as $localDomain) {
                $normalizedLocal = $this->normalizeDomain($localDomain);
                if ($normalizedExternal === $normalizedLocal) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $missingDomains[] = [
                    'dominio' => $domainName,
                    'normalizado' => $normalizedExternal,
                    'precio_compra' => $externalDomain['precio_compra'] ?? 'N/A',
                    'precio_venta' => $externalDomain['precio_venta'] ?? 'N/A',
                    'iban' => $externalDomain['IBAN'] ?? 'N/A',
                    'fecha_expiracion' => $externalDomain['fecha_expiracion'] ?? 'N/A'
                ];
            }
        }

        if (count($missingDomains) > 0) {
            $this->warn("⚠️ DOMINIOS FALTANTES EN BASE LOCAL:");
            $this->line(str_repeat('-', 40));
            
            if ($showDetails) {
                $this->table(
                    ['Dominio', 'Normalizado', 'Precio Compra', 'Precio Venta', 'IBAN', 'Fecha Exp.'],
                    array_slice($missingDomains, 0, 20)
                );
            } else {
                $this->table(
                    ['Dominio', 'Precio Compra', 'Precio Venta', 'IBAN'],
                    array_slice($missingDomains, 0, 20)
                );
            }

            if (count($missingDomains) > 20) {
                $this->warn("... y " . (count($missingDomains) - 20) . " dominios más faltantes");
            }

            $this->newLine();
            $this->info("📊 ESTADÍSTICAS:");
            $this->line("• Total faltantes: " . count($missingDomains));
            $this->line("• Con precio de compra: " . count(array_filter($missingDomains, fn($d) => $d['precio_compra'] !== 'N/A' && $d['precio_compra'] > 0)));
            $this->line("• Con precio de venta: " . count(array_filter($missingDomains, fn($d) => $d['precio_venta'] !== 'N/A' && $d['precio_venta'] > 0)));
            $this->line("• Con IBAN válido: " . count(array_filter($missingDomains, fn($d) => $d['iban'] !== 'N/A' && !empty($d['iban']))));

            // Analizar patrones
            $this->analyzePatterns($missingDomains);

        } else {
            $this->info("✅ Todos los dominios externos tienen coincidencia en la base local");
        }
    }

    private function analyzePatterns($missingDomains)
    {
        $this->newLine();
        $this->info('🔍 ANÁLISIS DE PATRONES:');
        $this->line(str_repeat('-', 30));

        // Dominios con precios
        $withPrices = array_filter($missingDomains, fn($d) => 
            ($d['precio_compra'] !== 'N/A' && $d['precio_compra'] > 0) || 
            ($d['precio_venta'] !== 'N/A' && $d['precio_venta'] > 0)
        );

        if (count($withPrices) > 0) {
            $this->warn("💰 DOMINIOS CON PRECIOS (posiblemente importantes):");
            foreach (array_slice($withPrices, 0, 10) as $domain) {
                $this->line("• {$domain['dominio']} - Compra: €{$domain['precio_compra']}, Venta: €{$domain['precio_venta']}");
            }
            if (count($withPrices) > 10) {
                $this->warn("... y " . (count($withPrices) - 10) . " dominios más con precios");
            }
        }

        // Dominios con IBAN
        $withIban = array_filter($missingDomains, fn($d) => 
            $d['iban'] !== 'N/A' && !empty($d['iban']) && 
            !in_array(strtoupper($d['iban']), ['CANCELADO', 'NO LE INTERESA', 'PAGADO POR TRANSFERENCIA AUTORIZADO'])
        );

        if (count($withIban) > 0) {
            $this->warn("🏦 DOMINIOS CON IBAN VÁLIDO:");
            foreach (array_slice($withIban, 0, 10) as $domain) {
                $this->line("• {$domain['dominio']} - IBAN: {$domain['iban']}");
            }
            if (count($withIban) > 10) {
                $this->warn("... y " . (count($withIban) - 10) . " dominios más con IBAN");
            }
        }
    }

    private function normalizeDomain($domain)
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/'); // Eliminar barra final
        return $domain;
    }
}