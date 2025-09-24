<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DomainSyncService;
use App\Models\Dominios\Dominio;

class AnalyzeSyncErrors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:analyze-errors 
                            {--limit=50 : Límite de dominios a analizar}
                            {--show-missing : Mostrar dominios que faltan en la base local}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analizar errores de sincronización de dominios';

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
        $this->info('🔍 ANÁLISIS DE ERRORES DE SINCRONIZACIÓN');
        $this->line(str_repeat('=', 50));

        $limit = (int) $this->option('limit');
        $showMissing = $this->option('show-missing');

        try {
            // Obtener dominios externos
            $externalDomains = $this->syncService->getExternalDomains();
            $this->info("📊 Total de dominios en base externa: " . count($externalDomains));

            // Obtener dominios locales
            $localDomains = Dominio::pluck('dominio')->toArray();
            $this->info("📊 Total de dominios en base local: " . count($localDomains));

            $this->newLine();

            // Analizar coincidencias
            $this->analyzeMatches($externalDomains, $localDomains, $limit);

            if ($showMissing) {
                $this->newLine();
                $this->analyzeMissingDomains($externalDomains, $localDomains, $limit);
            }

        } catch (\Exception $e) {
            $this->error("❌ Error en análisis: " . $e->getMessage());
        }
    }

    private function analyzeMatches($externalDomains, $localDomains, $limit)
    {
        $this->info('🔍 ANÁLISIS DE COINCIDENCIAS');
        $this->line(str_repeat('-', 30));

        $matches = 0;
        $noMatches = 0;
        $errors = [];

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
                    $matches++;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $noMatches++;
                $errors[] = $domainName;
            }
        }

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Dominios Analizados', $count],
                ['Coincidencias Encontradas', $matches],
                ['Sin Coincidencia', $noMatches],
                ['Porcentaje de Éxito', $count > 0 ? round(($matches / $count) * 100, 2) . '%' : '0%']
            ]
        );

        if ($noMatches > 0) {
            $this->newLine();
            $this->warn("⚠️ DOMINIOS SIN COINCIDENCIA (primeros 10):");
            $errorsToShow = array_slice($errors, 0, 10);
            foreach ($errorsToShow as $error) {
                $this->line("• {$error}");
            }
            if (count($errors) > 10) {
                $this->warn("... y " . (count($errors) - 10) . " dominios más sin coincidencia");
            }
        }
    }

    private function analyzeMissingDomains($externalDomains, $localDomains, $limit)
    {
        $this->info('📋 DOMINIOS FALTANTES EN BASE LOCAL');
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
                    'precio_compra' => $externalDomain['precio_compra'] ?? 'N/A',
                    'precio_venta' => $externalDomain['precio_venta'] ?? 'N/A',
                    'iban' => $externalDomain['IBAN'] ?? 'N/A'
                ];
            }
        }

        if (count($missingDomains) > 0) {
            $this->table(
                ['Dominio', 'Precio Compra', 'Precio Venta', 'IBAN'],
                array_slice($missingDomains, 0, 20)
            );

            if (count($missingDomains) > 20) {
                $this->warn("... y " . (count($missingDomains) - 20) . " dominios más faltantes");
            }
        } else {
            $this->info("✅ Todos los dominios externos tienen coincidencia en la base local");
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