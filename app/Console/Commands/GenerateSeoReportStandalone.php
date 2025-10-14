<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SerpApiService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class GenerateSeoReportStandalone extends Command
{
    protected $signature = 'seo:generate-standalone {domain : Dominio a analizar} {--output= : Archivo de salida}';
    protected $description = 'Genera un informe SEO standalone sin base de datos';

    public function handle()
    {
        $domain = $this->argument('domain');
        $outputFile = $this->option('output') ?: "informe_seo_{$domain}_" . date('Y-m-d') . ".html";

        $this->info("🔍 Generando informe SEO standalone para: {$domain}");

        try {
            // Verificar configuración de SerpAPI
            if (!env('SERPAPI_KEY')) {
                $this->error("❌ SERPAPI_KEY no configurada en .env");
                return 1;
            }

            $this->info("✅ Configuración de SerpAPI verificada");

            // Crear objeto Autoseo simulado
            $mockAutoseo = new \stdClass();
            $mockAutoseo->id = 1;
            $mockAutoseo->url = $domain;
            $mockAutoseo->client_name = ucfirst($domain);
            $mockAutoseo->client_email = 'test@example.com';

            $this->info("📊 Obteniendo datos de SerpAPI...");

            // Obtener datos de SerpAPI
            $serpApiService = new SerpApiService();
            $currentData = $serpApiService->getCurrentData($mockAutoseo);

            $this->info("✅ Datos obtenidos:");
            $this->info("   - Keywords: " . count($currentData['detalles_keywords'] ?? []));
            $this->info("   - Short tail: " . count($currentData['short_tail'] ?? []));
            $this->info("   - Long tail: " . count($currentData['long_tail'] ?? []));
            $this->info("   - PAA: " . count($currentData['people_also_ask'] ?? []));

            // Generar HTML del informe
            $this->info("📝 Generando informe HTML...");
            
            $html = $this->generateReportHtml($currentData, $domain);

            // Guardar archivo
            $this->info("💾 Guardando informe...");
            Storage::disk('public')->put("reports/{$outputFile}", $html);

            $this->info("✅ Informe generado exitosamente!");
            $this->info("📁 Archivo: storage/app/public/reports/{$outputFile}");
            $this->info("🌐 URL: " . Storage::disk('public')->url("reports/{$outputFile}"));

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function generateReportHtml($data, $domain)
    {
        $keywords = $data['detalles_keywords'] ?? [];
        $shortTail = $data['short_tail'] ?? [];
        $longTail = $data['long_tail'] ?? [];
        $paa = $data['people_also_ask'] ?? [];

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO - ' . htmlspecialchars($domain) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        h2 { color: #34495e; margin-top: 30px; }
        .summary { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .keyword-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .keyword-table th, .keyword-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .keyword-table th { background: #3498db; color: white; }
        .keyword-table tr:nth-child(even) { background: #f9f9f9; }
        .position { font-weight: bold; }
        .position.good { color: #27ae60; }
        .position.medium { color: #f39c12; }
        .position.bad { color: #e74c3c; }
        .not-found { color: #95a5a6; font-style: italic; }
        .section { margin: 30px 0; }
        .paa-item { background: #f8f9fa; padding: 10px; margin: 10px 0; border-left: 4px solid #3498db; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Informe SEO - ' . htmlspecialchars($domain) . '</h1>
        
        <div class="summary">
            <h3>📈 Resumen Ejecutivo</h3>
            <p><strong>Dominio:</strong> ' . htmlspecialchars($domain) . '</p>
            <p><strong>Fecha de análisis:</strong> ' . date('d/m/Y H:i') . '</p>
            <p><strong>Keywords analizadas:</strong> ' . count($keywords) . '</p>
            <p><strong>Short tail keywords:</strong> ' . count($shortTail) . '</p>
            <p><strong>Long tail keywords:</strong> ' . count($longTail) . '</p>
            <p><strong>Preguntas relacionadas:</strong> ' . count($paa) . '</p>
        </div>

        <div class="section">
            <h2>🎯 Keywords Analizadas</h2>
            <table class="keyword-table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Posición</th>
                        <th>Total Resultados</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($keywords as $keyword) {
            $position = $keyword['position'] ?? null;
            $totalResults = $keyword['total_results'] ?? 'N/A';
            
            $positionClass = '';
            $positionText = '';
            
            if ($position === null) {
                $positionText = 'No encontrado';
                $positionClass = 'not-found';
            } else {
                $positionText = "Posición {$position}";
                if ($position <= 3) {
                    $positionClass = 'good';
                } elseif ($position <= 10) {
                    $positionClass = 'medium';
                } else {
                    $positionClass = 'bad';
                }
            }

            $html .= '<tr>
                <td>' . htmlspecialchars($keyword['keyword']) . '</td>
                <td class="position ' . $positionClass . '">' . $positionText . '</td>
                <td>' . number_format($totalResults) . '</td>
                <td>' . ($position ? ($position <= 10 ? '✅ En top 10' : '⚠️ Fuera del top 10') : '❌ No encontrado') . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
        </div>';

        if (!empty($shortTail)) {
            $html .= '<div class="section">
                <h2>🔤 Short Tail Keywords</h2>
                <ul>';
            foreach ($shortTail as $keyword) {
                $html .= '<li>' . htmlspecialchars($keyword) . '</li>';
            }
            $html .= '</ul>
            </div>';
        }

        if (!empty($longTail)) {
            $html .= '<div class="section">
                <h2>📝 Long Tail Keywords</h2>
                <ul>';
            foreach ($longTail as $keyword) {
                $html .= '<li>' . htmlspecialchars($keyword) . '</li>';
            }
            $html .= '</ul>
            </div>';
        }

        if (!empty($paa)) {
            $html .= '<div class="section">
                <h2>❓ People Also Ask</h2>';
            foreach ($paa as $question) {
                $html .= '<div class="paa-item">
                    <strong>' . htmlspecialchars($question['question']) . '</strong>';
                if (isset($question['position'])) {
                    $html .= '<br><small>Posición: ' . $question['position'] . '</small>';
                }
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        $html .= '<div class="section">
            <h2>📋 Recomendaciones</h2>
            <ul>
                <li>Optimizar contenido para keywords con posiciones bajas</li>
                <li>Crear contenido específico para long tail keywords</li>
                <li>Responder a las preguntas de People Also Ask en el sitio web</li>
                <li>Monitorear regularmente las posiciones de keywords</li>
            </ul>
        </div>

        <div class="section">
            <p><em>Informe generado automáticamente por el sistema SEO de Hawkins</em></p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
}
