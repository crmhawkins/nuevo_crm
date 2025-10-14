<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SerpApiService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class GenerateSeoReportWithHistory extends Command
{
    protected $signature = 'seo:generate-with-history {domain : Dominio a analizar} {--client-id=1 : ID del cliente}';
    protected $description = 'Genera un informe SEO con datos históricos simulados';

    public function handle()
    {
        $domain = $this->argument('domain');
        $clientId = $this->option('client-id');

        $this->info("🔍 Generando informe SEO con historial para: {$domain} (Cliente ID: {$clientId})");

        try {
            // Verificar configuración de SerpAPI
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                return 1;
            }

            $this->info("✅ Configuración de SerpAPI verificada");

            // Crear objeto Autoseo simulado
            $mockAutoseo = new \stdClass();
            $mockAutoseo->id = $clientId;
            $mockAutoseo->url = $domain;
            $mockAutoseo->client_name = ucfirst($domain);
            $mockAutoseo->client_email = 'test@example.com';

            $this->info("📊 Obteniendo datos actuales de SerpAPI...");

            // Obtener datos actuales de SerpAPI
            $serpApiService = new SerpApiService();
            $currentData = $serpApiService->getCurrentData($mockAutoseo);

            $this->info("✅ Datos actuales obtenidos:");
            $this->info("   - Keywords: " . count($currentData['detalles_keywords'] ?? []));
            $this->info("   - Short tail: " . count($currentData['short_tail'] ?? []));
            $this->info("   - Long tail: " . count($currentData['long_tail'] ?? []));
            $this->info("   - PAA: " . count($currentData['people_also_ask'] ?? []));

            // Simular descarga de datos históricos
            $this->info("📥 Simulando descarga de datos históricos...");
            $historicalData = $this->simulateHistoricalData($domain, $clientId);

            $this->info("✅ Datos históricos simulados:");
            $this->info("   - Períodos históricos: " . count($historicalData));

            // Combinar datos históricos con actuales
            $this->info("🔄 Combinando datos históricos con actuales...");
            $jsonDataList = array_merge($historicalData, [$currentData]);

            $this->info("✅ Datos combinados:");
            $this->info("   - Total períodos: " . count($jsonDataList));
            $this->info("   - Datos actuales incluidos: ✅");

            // Generar informe con comparaciones
            $this->info("📝 Generando informe con comparaciones históricas...");
            $html = $this->generateReportWithHistory($jsonDataList, $domain);

            // Guardar archivo
            $filename = "informe_seo_con_historial_{$domain}_" . date('Y-m-d') . ".html";
            $this->info("💾 Guardando informe...");
            Storage::disk('public')->put("reports/{$filename}", $html);

            $this->info("✅ Informe generado exitosamente!");
            $this->info("📁 Archivo: storage/app/public/reports/{$filename}");
            $this->info("🌐 URL: " . Storage::disk('public')->url("reports/{$filename}"));

            // Simular almacenamiento para próximo mes
            $this->info("💾 Simulando almacenamiento para próximo mes...");
            $this->simulateStoreForNextMonth($currentData, $clientId);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function simulateHistoricalData($domain, $clientId)
    {
        // Simular datos históricos de los últimos 3 meses
        $historicalData = [];
        
        for ($i = 3; $i >= 1; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('F Y');
            
            // Simular datos históricos con posiciones diferentes
            $historicalKeywords = [];
            $baseKeywords = ['hawkins', 'hawkins servicios', 'hawkins empresa', 'hawkins contacto'];
            
            foreach ($baseKeywords as $keyword) {
                // Simular posiciones históricas (peores en el pasado)
                $basePosition = rand(5, 20) + ($i * 2); // Peor posición cuanto más antiguo
                $historicalKeywords[] = [
                    'keyword' => $keyword,
                    'position' => $basePosition,
                    'total_results' => rand(100000, 1000000),
                    'url' => $domain
                ];
            }

            $historicalData[] = [
                'dominio' => $domain,
                'uploaded_at' => $date->toDateTimeString(),
                'detalles_keywords' => $historicalKeywords,
                'short_tail' => ['hawkins', 'hawkins servicios'],
                'long_tail' => ['hawkins empresa', 'hawkins contacto'],
                'people_also_ask' => [
                    ['question' => "¿Qué servicios ofrece {$domain}?"],
                    ['question' => "¿Cómo contactar con {$domain}?"]
                ],
                'monthly_performance' => [],
                'period' => $monthName
            ];
        }

        return $historicalData;
    }

    private function simulateStoreForNextMonth($currentData, $clientId)
    {
        $this->info("   - Datos actuales preparados para almacenamiento");
        $this->info("   - Se guardarían en: storage/app/public/autoseo/json/");
        $this->info("   - Cliente ID: {$clientId}");
        $this->info("   - Fecha: " . now()->toDateTimeString());
    }

    private function generateReportWithHistory($jsonDataList, $domain)
    {
        $currentData = end($jsonDataList); // Último elemento (datos actuales)
        $historicalData = array_slice($jsonDataList, 0, -1); // Todos excepto el último

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO con Historial - ' . htmlspecialchars($domain) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .summary { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .comparison-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .comparison-table th, .comparison-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .comparison-table th { background: #3498db; color: white; }
        .comparison-table tr:nth-child(even) { background: #f9f9f9; }
        .position { font-weight: bold; }
        .position.improved { color: #27ae60; }
        .position.declined { color: #e74c3c; }
        .position.stable { color: #f39c12; }
        .not-found { color: #95a5a6; font-style: italic; }
        .section { margin: 30px 0; }
        .trend-up { color: #27ae60; }
        .trend-down { color: #e74c3c; }
        .trend-stable { color: #f39c12; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Informe SEO con Historial - ' . htmlspecialchars($domain) . '</h1>
        
        <div class="summary">
            <h3>📈 Resumen Ejecutivo</h3>
            <p><strong>Dominio:</strong> ' . htmlspecialchars($domain) . '</p>
            <p><strong>Fecha de análisis:</strong> ' . date('d/m/Y H:i') . '</p>
            <p><strong>Períodos analizados:</strong> ' . count($jsonDataList) . '</p>
            <p><strong>Keywords actuales:</strong> ' . count($currentData['detalles_keywords'] ?? []) . '</p>
            <p><strong>Datos históricos:</strong> ' . count($historicalData) . ' períodos anteriores</p>
        </div>';

        // Comparación de posiciones
        if (!empty($currentData['detalles_keywords']) && !empty($historicalData)) {
            $html .= '<div class="section">
                <h2>📊 Comparación de Posiciones</h2>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Posición Actual</th>
                            <th>Posición Anterior</th>
                            <th>Cambio</th>
                            <th>Tendencia</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($currentData['detalles_keywords'] as $currentKeyword) {
                $keyword = $currentKeyword['keyword'];
                $currentPosition = $currentKeyword['position'];
                
                // Buscar posición histórica
                $historicalPosition = null;
                if (!empty($historicalData)) {
                    $lastHistorical = end($historicalData);
                    foreach ($lastHistorical['detalles_keywords'] ?? [] as $histKeyword) {
                        if ($histKeyword['keyword'] === $keyword) {
                            $historicalPosition = $histKeyword['position'];
                            break;
                        }
                    }
                }

                $change = '';
                $trendClass = '';
                
                if ($currentPosition && $historicalPosition) {
                    $diff = $historicalPosition - $currentPosition;
                    if ($diff > 0) {
                        $change = "+{$diff} posiciones";
                        $trendClass = 'trend-up';
                    } elseif ($diff < 0) {
                        $change = "{$diff} posiciones";
                        $trendClass = 'trend-down';
                    } else {
                        $change = "Sin cambio";
                        $trendClass = 'trend-stable';
                    }
                } elseif ($currentPosition) {
                    $change = "Nueva keyword";
                    $trendClass = 'trend-up';
                } else {
                    $change = "No encontrada";
                    $trendClass = 'trend-down';
                }

                $currentPosText = $currentPosition ? "Posición {$currentPosition}" : "No encontrado";
                $historicalPosText = $historicalPosition ? "Posición {$historicalPosition}" : "N/A";

                $html .= '<tr>
                    <td>' . htmlspecialchars($keyword) . '</td>
                    <td>' . $currentPosText . '</td>
                    <td>' . $historicalPosText . '</td>
                    <td>' . $change . '</td>
                    <td class="' . $trendClass . '">' . ($diff > 0 ? '📈 Mejoró' : ($diff < 0 ? '📉 Empeoró' : '➡️ Estable')) . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // Evolución temporal
        if (count($historicalData) > 0) {
            $html .= '<div class="section">
                <h2>📈 Evolución Temporal</h2>
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th>Período</th>
                            <th>Keywords Analizadas</th>
                            <th>Promedio Posición</th>
                            <th>Keywords Top 10</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($jsonDataList as $index => $data) {
                $period = isset($data['period']) ? $data['period'] : ($index === count($jsonDataList) - 1 ? 'Actual' : "Período " . ($index + 1));
                $keywords = $data['detalles_keywords'] ?? [];
                $keywordCount = count($keywords);
                
                $positions = array_filter(array_column($keywords, 'position'));
                $avgPosition = !empty($positions) ? round(array_sum($positions) / count($positions), 1) : 'N/A';
                
                $top10Count = count(array_filter($positions, fn($p) => $p <= 10));

                $html .= '<tr>
                    <td>' . htmlspecialchars($period) . '</td>
                    <td>' . $keywordCount . '</td>
                    <td>' . $avgPosition . '</td>
                    <td>' . $top10Count . '</td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        $html .= '<div class="section">
            <h2>📋 Recomendaciones</h2>
            <ul>
                <li>Monitorear keywords que han mejorado para mantener la tendencia</li>
                <li>Optimizar contenido para keywords que han empeorado</li>
                <li>Crear contenido específico para nuevas keywords encontradas</li>
                <li>Analizar factores que han contribuido a las mejoras</li>
                <li>Mantener estrategia SEO consistente para keywords estables</li>
            </ul>
        </div>

        <div class="section">
            <p><em>Informe generado automáticamente por el sistema SEO de Hawkins con datos históricos</em></p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
