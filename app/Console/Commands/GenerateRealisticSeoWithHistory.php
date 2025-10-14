<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealisticSeoAnalysisService;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateRealisticSeoWithHistory extends Command
{
    protected $signature = 'seo:generate-with-real-history {id : ID del cliente Autoseo}';
    protected $description = 'Genera informe SEO realista usando datos históricos reales';

    public function handle()
    {
        $id = $this->argument('id');

        $this->info("🔍 Generando informe SEO con datos históricos reales para cliente ID: {$id}");

        try {
            // Verificar configuración
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                return 1;
            }

            // Obtener cliente de la base de datos
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                $this->error("❌ Cliente Autoseo con ID {$id} no encontrado");
                return 1;
            }

            $this->info("📊 Cliente: {$autoseo->client_name} ({$autoseo->url})");

            // Descargar datos históricos reales
            $this->info("📥 Descargando datos históricos reales...");
            $historicalData = $this->downloadRealHistoricalData($id);

            if (empty($historicalData)) {
                $this->warn("⚠️ No se encontraron datos históricos. Generando análisis inicial...");
            } else {
                $this->info("✅ Datos históricos obtenidos: " . count($historicalData) . " períodos");
            }

            // Generar análisis realista
            $this->info("🔍 Generando análisis SEO realista...");
            $analysisService = new RealisticSeoAnalysisService();
            $currentData = $analysisService->generateRealisticAnalysis($autoseo, $historicalData);

            // Generar informe HTML mejorado
            $this->info("📝 Generando informe HTML...");
            $html = $this->generateRealisticReportHtml($currentData, $autoseo, $historicalData);

            // Guardar informe
            $filename = "informe_seo_realista_{$id}_" . date('Y-m-d') . ".html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            $this->info("✅ Informe generado exitosamente!");
            $this->info("📁 Archivo: storage/app/public/reports/{$filename}");
            $this->info("🌐 URL: " . Storage::disk('public')->url("reports/{$filename}"));

            // Mostrar resumen
            $this->displaySummary($currentData);

            // Guardar datos actuales para próximo mes
            $this->info("💾 Guardando datos actuales para próximo mes...");
            $this->storeCurrentDataForNextMonth($autoseo, $currentData);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function downloadRealHistoricalData($id)
    {
        try {
            $this->info("   Descargando desde: https://crm.hawkins.es/api/autoseo/json/storage?id={$id}");
            
            $response = Http::timeout(120)
                ->withoutVerifying()
                ->get("https://crm.hawkins.es/api/autoseo/json/storage", ['id' => $id]);

            if (!$response->successful()) {
                $this->warn("   Error descargando datos históricos: " . $response->status());
                return [];
            }

            // Procesar ZIP
            $tempDir = storage_path("app/temp/historical_{$id}");
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            $zipPath = $tempDir . '/historical.zip';
            File::put($zipPath, $response->body());

            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                throw new \Exception('Error al extraer el ZIP');
            }

            // Leer archivos JSON
            $jsonFiles = File::glob($tempDir . '/*.json');
            $historicalData = [];

            foreach ($jsonFiles as $file) {
                $data = json_decode(File::get($file), true);
                if ($data) {
                    $historicalData[] = $data;
                }
            }

            // Ordenar por fecha
            usort($historicalData, function($a, $b) {
                $dateA = $a['uploaded_at'] ?? '1970-01-01';
                $dateB = $b['uploaded_at'] ?? '1970-01-01';
                return strtotime($dateA) - strtotime($dateB);
            });

            File::deleteDirectory($tempDir);
            
            $this->info("   ✅ Procesados " . count($historicalData) . " archivos históricos");
            return $historicalData;

        } catch (\Exception $e) {
            $this->warn("   ⚠️ Error procesando datos históricos: " . $e->getMessage());
            return [];
        }
    }

    private function generateRealisticReportHtml($currentData, $autoseo, $historicalData)
    {
        $summary = $currentData['summary'] ?? [];
        $insights = $currentData['insights'] ?? [];
        $historicalComparison = $currentData['historical_comparison'] ?? [];

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO Realista - ' . htmlspecialchars($autoseo->client_name) . '</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1a202c; line-height: 1.6; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; border-radius: 12px; margin-bottom: 30px; }
        .header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .header p { opacity: 0.9; font-size: 1.1rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .metric-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); text-align: center; }
        .metric-value { font-size: 2.5rem; font-weight: bold; margin-bottom: 10px; }
        .metric-label { color: #6b7280; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .trend-up { color: #10b981; }
        .trend-down { color: #ef4444; }
        .trend-neutral { color: #6b7280; }
        .keyword-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .keyword-table th, .keyword-table td { padding: 15px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .keyword-table th { background: #f9fafb; font-weight: 600; color: #374151; }
        .keyword-table tr:hover { background: #f9fafb; }
        .position-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .position-excellent { background: #d1fae5; color: #065f46; }
        .position-good { background: #dbeafe; color: #1e40af; }
        .position-fair { background: #fef3c7; color: #92400e; }
        .position-poor { background: #fee2e2; color: #991b1b; }
        .position-not-found { background: #f3f4f6; color: #6b7280; }
        .trend-badge { padding: 4px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
        .trend-improved { background: #d1fae5; color: #065f46; }
        .trend-declined { background: #fee2e2; color: #991b1b; }
        .trend-stable { background: #f3f4f6; color: #6b7280; }
        .trend-new { background: #dbeafe; color: #1e40af; }
        .chart-container { height: 400px; margin: 20px 0; }
        .insight-card { background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #667eea; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Informe SEO Realista</h1>
            <p>' . htmlspecialchars($autoseo->client_name) . ' - ' . htmlspecialchars($autoseo->url) . '</p>
            <p>Análisis basado en datos históricos reales • Fecha: ' . date('d/m/Y H:i') . '</p>
        </div>';

        // Resumen ejecutivo
        $html .= '<div class="card">
            <h2>📈 Resumen Ejecutivo</h2>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">' . ($summary['total_keywords'] ?? 0) . '</div>
                    <div class="metric-label">Keywords Analizadas</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value trend-up">' . ($summary['found_keywords'] ?? 0) . '</div>
                    <div class="metric-label">Keywords Encontradas</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">' . ($summary['top_10_keywords'] ?? 0) . '</div>
                    <div class="metric-label">Top 10 Posiciones</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">' . ($summary['visibility_score'] ?? 0) . '%</div>
                    <div class="metric-label">Score de Visibilidad</div>
                </div>
            </div>
        </div>';

        // Análisis de tendencias
        if (!empty($historicalComparison)) {
            $html .= '<div class="card">
                <h2>📊 Análisis de Tendencias</h2>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-value trend-up">' . ($summary['improved_keywords'] ?? 0) . '</div>
                        <div class="metric-label">Keywords Mejoradas</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value trend-down">' . ($summary['declined_keywords'] ?? 0) . '</div>
                        <div class="metric-label">Keywords Empeoradas</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-value">' . count($historicalData) . '</div>
                        <div class="metric-label">Períodos Históricos</div>
                    </div>
                </div>
            </div>';
        }

        // Tabla de keywords con tendencias
        $keywordAnalysis = $currentData['detalles_keywords'] ?? [];
        if (!empty($keywordAnalysis)) {
            $html .= '<div class="card">
                <h2>🎯 Análisis de Keywords con Tendencias</h2>
                <table class="keyword-table">
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            <th>Posición Actual</th>
                            <th>Total Resultados</th>
                            <th>Tendencia</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($keywordAnalysis as $index => $keyword) {
                $positionText = $keyword['position'] ? "Posición {$keyword['position']}" : "No encontrado";
                $positionClass = $this->getPositionClass($keyword['position']);
                
                $trend = $historicalComparison[$index]['trend'] ?? 'new';
                $trendClass = 'trend-' . str_replace('_', '-', $trend);
                $trendText = $this->getTrendText($trend);

                $html .= '<tr>
                    <td><strong>' . htmlspecialchars($keyword['keyword']) . '</strong></td>
                    <td><span class="position-badge ' . $positionClass . '">' . $positionText . '</span></td>
                    <td>' . ($keyword['total_results'] ? number_format($keyword['total_results']) : 'N/A') . '</td>
                    <td><span class="trend-badge ' . $trendClass . '">' . $trendText . '</span></td>
                </tr>';
            }

            $html .= '</tbody>
                </table>
            </div>';
        }

        // People Also Ask
        $paa = $currentData['people_also_ask'] ?? [];
        if (!empty($paa)) {
            $html .= '<div class="card">
                <h2>❓ People Also Ask</h2>
                <div style="display: grid; gap: 15px;">';

            foreach ($paa as $question) {
                $html .= '<div class="insight-card">
                    <strong>' . htmlspecialchars($question['question']) . '</strong>
                </div>';
            }

            $html .= '</div>
            </div>';
        }

        // Insights y recomendaciones
        if (!empty($insights)) {
            $html .= '<div class="card">
                <h2>💡 Insights y Recomendaciones</h2>';
            
            $posInsights = $insights['positions'] ?? [];
            $trendInsights = $insights['trends'] ?? [];
            
            $html .= '<div class="insight-card">
                <h3>📊 Análisis de Posiciones</h3>
                <p>De ' . ($posInsights['total_analyzed'] ?? 0) . ' keywords analizadas, ' . ($posInsights['found'] ?? 0) . ' fueron encontradas en los resultados de búsqueda.</p>
                <p><strong>Score de visibilidad:</strong> ' . ($posInsights['visibility_percentage'] ?? 0) . '%</p>
            </div>';
            
            if (!empty($trendInsights)) {
                $html .= '<div class="insight-card">
                    <h3>📈 Análisis de Tendencias</h3>
                    <p><strong>Keywords mejoradas:</strong> ' . ($trendInsights['improved'] ?? 0) . '</p>
                    <p><strong>Keywords empeoradas:</strong> ' . ($trendInsights['declined'] ?? 0) . '</p>
                    <p><strong>Keywords estables:</strong> ' . ($trendInsights['stable'] ?? 0) . '</p>
                    <p><strong>Keywords nuevas:</strong> ' . ($trendInsights['new'] ?? 0) . '</p>
                </div>';
            }
            
            $html .= '</div>';
        }

        $html .= '<div class="card">
            <p style="text-align: center; color: #6b7280; font-size: 0.9rem;">
                Informe generado automáticamente por el sistema SEO realista de Hawkins<br>
                Basado en datos históricos reales y análisis actual de SerpAPI
            </p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    private function getPositionClass($position)
    {
        if ($position === null) return 'position-not-found';
        if ($position <= 3) return 'position-excellent';
        if ($position <= 10) return 'position-good';
        if ($position <= 20) return 'position-fair';
        return 'position-poor';
    }

    private function getTrendText($trend)
    {
        $trends = [
            'improved' => '📈 Mejoró',
            'declined' => '📉 Empeoró',
            'stable' => '➡️ Estable',
            'new' => '🆕 Nueva',
            'stable_not_found' => '❌ No encontrada'
        ];
        
        return $trends[$trend] ?? '❓ Desconocido';
    }

    private function displaySummary($currentData)
    {
        $summary = $currentData['summary'] ?? [];
        $insights = $currentData['insights'] ?? [];

        $this->info("📊 Resumen del análisis:");
        $this->info("   - Keywords analizadas: " . ($summary['total_keywords'] ?? 0));
        $this->info("   - Keywords encontradas: " . ($summary['found_keywords'] ?? 0));
        $this->info("   - Keywords en top 10: " . ($summary['top_10_keywords'] ?? 0));
        $this->info("   - Score de visibilidad: " . ($summary['visibility_score'] ?? 0) . "%");
        $this->info("   - Keywords mejoradas: " . ($summary['improved_keywords'] ?? 0));
        $this->info("   - Keywords empeoradas: " . ($summary['declined_keywords'] ?? 0));
    }

    private function storeCurrentDataForNextMonth($autoseo, $currentData)
    {
        try {
            $filename = uniqid() . '_' . $autoseo->id . '.json';
            $relativePath = "autoseo/json/$filename";
            
            Storage::disk('public')->makeDirectory('autoseo/json');
            $saved = Storage::disk('public')->put('autoseo/json/' . $filename, json_encode($currentData, JSON_PRETTY_PRINT));

            if (!$saved) {
                $this->error("Error al guardar datos actuales");
                return;
            }

            // Actualizar json_storage en la base de datos
            $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
            $jsonStorage[] = [
                'id' => $autoseo->id,
                'path' => $relativePath,
                'uploaded_at' => now()->toDateTimeString(),
                'source' => 'realistic_analysis'
            ];

            $autoseo->json_mesanterior = $relativePath;
            $autoseo->json_storage = json_encode($jsonStorage);
            $autoseo->save();

            $this->info("✅ Datos actuales almacenados para próximo mes");

        } catch (\Exception $e) {
            $this->error("❌ Error almacenando datos: " . $e->getMessage());
        }
    }
}
