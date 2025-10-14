<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateJsonOnlySeoReport extends Command
{
    protected $signature = 'seo:generate-json-only {id : ID del cliente Autoseo}';
    protected $description = 'Genera informe SEO basado únicamente en JSONs históricos (sin SerpAPI)';

    public function handle()
    {
        $id = $this->argument('id');

        $this->info("🔍 Generando informe SEO basado en JSONs históricos para cliente ID: {$id}");

        try {
            // Obtener cliente de la base de datos
            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                $this->error("❌ Cliente Autoseo con ID {$id} no encontrado");
                return 1;
            }

            $this->info("📊 Cliente: {$autoseo->client_name} ({$autoseo->url})");

            // Descargar datos históricos reales
            $this->info("📥 Descargando datos históricos reales...");
            $historicalData = $this->downloadHistoricalData($id);

            if (empty($historicalData)) {
                $this->error("❌ No se encontraron datos históricos para el cliente");
                return 1;
            }

            // Limitar a máximo 12 períodos
            $historicalData = array_slice($historicalData, -12);
            $this->info("✅ Datos históricos obtenidos: " . count($historicalData) . " períodos (máximo 12)");

            // Analizar datos históricos
            $this->info("🔍 Analizando datos históricos...");
            $analysis = $this->analyzeHistoricalData($historicalData);

            // Generar informe HTML
            $this->info("📝 Generando informe HTML...");
            $html = $this->generateReportHtml($analysis, $autoseo, $historicalData);

            // Guardar informe
            $filename = "informe_seo_json_only_{$id}_" . date('Y-m-d') . ".html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            $this->info("✅ Informe generado exitosamente!");
            $this->info("📁 Archivo: storage/app/public/reports/{$filename}");
            $this->info("🌐 URL: " . Storage::disk('public')->url("reports/{$filename}"));

            // Mostrar resumen
            $this->displaySummary($analysis);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    private function downloadHistoricalData($id)
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
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);
                
                if ($data) {
                    $this->info("   📄 Archivo: " . basename($file));
                    $this->info("   📊 Estructura: " . json_encode(array_keys($data)));
                    
                    // Normalizar estructura de datos
                    $normalizedData = $this->normalizeHistoricalData($data, $file);
                    if ($normalizedData) {
                        $historicalData[] = $normalizedData;
                    }
                } else {
                    $this->warn("   ⚠️ Error decodificando JSON: " . basename($file));
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

    private function normalizeHistoricalData($data, $filename)
    {
        // Extraer fecha del nombre del archivo si no está en los datos
        $uploadedAt = $data['uploaded_at'] ?? null;
        if (!$uploadedAt) {
            // Intentar extraer fecha del nombre del archivo
            if (preg_match('/(\d{4}-\d{2}-\d{2})/', basename($filename), $matches)) {
                $uploadedAt = $matches[1] . ' 00:00:00';
            } else {
                $uploadedAt = date('Y-m-d H:i:s', filemtime($filename));
            }
        }

        // Normalizar estructura de keywords
        $keywords = [];
        
        // Buscar keywords en diferentes estructuras posibles
        if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
            $keywords = $data['detalles_keywords'];
        } elseif (isset($data['keywords']) && is_array($data['keywords'])) {
            $keywords = $data['keywords'];
        } elseif (isset($data['results']) && is_array($data['results'])) {
            $keywords = $data['results'];
        } elseif (isset($data['organic_results']) && is_array($data['organic_results'])) {
            // Convertir organic_results a formato keywords
            foreach ($data['organic_results'] as $result) {
                if (isset($result['title']) && isset($result['position'])) {
                    $keywords[] = [
                        'keyword' => $result['title'],
                        'position' => $result['position'],
                        'url' => $result['link'] ?? '',
                        'title' => $result['title'] ?? '',
                        'snippet' => $result['snippet'] ?? ''
                    ];
                }
            }
        }

        // Si no hay keywords, intentar extraer de otros campos
        if (empty($keywords)) {
            $this->warn("   ⚠️ No se encontraron keywords en: " . basename($filename));
            return null;
        }

        return [
            'uploaded_at' => $uploadedAt,
            'detalles_keywords' => $keywords,
            'source_file' => basename($filename),
            'total_keywords' => count($keywords),
            'domain' => $data['dominio'] ?? 'unknown'
        ];
    }

    private function analyzeHistoricalData($historicalData)
    {
        $this->info("   🔍 Analizando " . count($historicalData) . " períodos históricos...");

        $analysis = [
            'total_periods' => count($historicalData),
            'all_keywords' => [],
            'keyword_evolution' => [],
            'position_distribution' => [],
            'trends' => [],
            'summary' => []
        ];

        // Recopilar todas las keywords únicas
        $allKeywords = [];
        foreach ($historicalData as $data) {
            if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $keyword) {
                    if (isset($keyword['keyword'])) {
                        $allKeywords[] = $keyword['keyword'];
                    }
                }
            }
        }
        $uniqueKeywords = array_unique($allKeywords);
        $analysis['all_keywords'] = $uniqueKeywords;

        $this->info("   🔑 Keywords únicas encontradas: " . count($uniqueKeywords));

        // Crear evolución de keywords
        foreach ($uniqueKeywords as $keyword) {
            $evolution = [];
            foreach ($historicalData as $data) {
                $position = null;
                if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
                    foreach ($data['detalles_keywords'] as $kw) {
                        if (isset($kw['keyword']) && $kw['keyword'] === $keyword) {
                            $position = $kw['position'] ?? null;
                            break;
                        }
                    }
                }
                $evolution[] = $position;
            }
            $analysis['keyword_evolution'][$keyword] = $evolution;
        }

        // Calcular distribución de posiciones del período más reciente
        $latestData = end($historicalData);
        if ($latestData && isset($latestData['detalles_keywords'])) {
            $analysis['position_distribution'] = $this->calculatePositionDistribution($latestData['detalles_keywords']);
        }

        // Calcular tendencias
        $analysis['trends'] = $this->calculateTrends($analysis['keyword_evolution']);

        // Calcular resumen
        $analysis['summary'] = $this->calculateSummary($analysis, $historicalData);

        $this->info("   ✅ Análisis completado");
        return $analysis;
    }

    private function calculatePositionDistribution($keywords)
    {
        $distribution = [
            'Top 3' => 0,
            'Top 10' => 0,
            'Top 20' => 0,
            'Top 50' => 0,
            'Fuera Top 50' => 0,
            'No encontradas' => 0
        ];
        
        foreach ($keywords as $keyword) {
            $position = $keyword['position'] ?? null;
            
            if ($position === null) {
                $distribution['No encontradas']++;
            } elseif ($position <= 3) {
                $distribution['Top 3']++;
            } elseif ($position <= 10) {
                $distribution['Top 10']++;
            } elseif ($position <= 20) {
                $distribution['Top 20']++;
            } elseif ($position <= 50) {
                $distribution['Top 50']++;
            } else {
                $distribution['Fuera Top 50']++;
            }
        }
        
        return $distribution;
    }

    private function calculateTrends($keywordEvolution)
    {
        $trends = [
            'improved' => 0,
            'declined' => 0,
            'stable' => 0,
            'new' => 0
        ];

        foreach ($keywordEvolution as $keyword => $positions) {
            $trend = $this->calculateKeywordTrend($positions);
            $trends[$trend]++;
        }

        return $trends;
    }

    private function calculateKeywordTrend($positions)
    {
        $currentPosition = end($positions);
        $previousPosition = count($positions) > 1 ? $positions[count($positions) - 2] : null;

        if ($currentPosition === null && $previousPosition === null) {
            return 'stable';
        } elseif ($currentPosition === null) {
            return 'declined';
        } elseif ($previousPosition === null) {
            return 'new';
        } elseif ($currentPosition < $previousPosition) {
            return 'improved';
        } elseif ($currentPosition > $previousPosition) {
            return 'declined';
        } else {
            return 'stable';
        }
    }

    private function calculateSummary($analysis, $historicalData)
    {
        $latestData = end($historicalData);
        $keywords = $latestData['detalles_keywords'] ?? [];

        $summary = [
            'total_keywords' => count($analysis['all_keywords']),
            'keywords_in_top10' => 0,
            'keywords_in_top3' => 0,
            'average_position' => 0,
            'visibility_score' => 0,
            'periods_analyzed' => count($historicalData)
        ];

        if (!empty($keywords)) {
            $positions = array_filter(array_column($keywords, 'position'));
            $summary['keywords_in_top10'] = count(array_filter($positions, fn($p) => $p <= 10));
            $summary['keywords_in_top3'] = count(array_filter($positions, fn($p) => $p <= 3));
            $summary['average_position'] = !empty($positions) ? round(array_sum($positions) / count($positions), 1) : 0;
            
            $foundKeywords = count(array_filter($keywords, fn($k) => $k['position'] !== null));
            $summary['visibility_score'] = count($keywords) > 0 ? round(($foundKeywords / count($keywords)) * 100, 1) : 0;
        }

        return $summary;
    }

    private function generateReportHtml($analysis, $autoseo, $historicalData)
    {
        $domain = parse_url($autoseo->url, PHP_URL_HOST) ?: $autoseo->url;
        
        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informe SEO Histórico - ' . htmlspecialchars($autoseo->client_name) . '</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net/">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Figtree", sans-serif;
            background: #f8fafc;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #1f2937;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header .subtitle {
            color: #6b7280;
            font-size: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .stat-card .label {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-canvas {
            height: 400px;
            position: relative;
        }

        .keyword-table {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }

        .position-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .position-top3 { background: #dcfce7; color: #166534; }
        .position-top10 { background: #dbeafe; color: #1e40af; }
        .position-top20 { background: #fef3c7; color: #92400e; }
        .position-top50 { background: #fecaca; color: #991b1b; }
        .position-not-found { background: #f3f4f6; color: #6b7280; }

        .trend-icon {
            font-size: 1rem;
        }

        .trend-up { color: #059669; }
        .trend-down { color: #dc2626; }
        .trend-stable { color: #6b7280; }
        .trend-new { color: #2563eb; }

        .periods-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
        }

        .periods-info h3 {
            color: #0369a1;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .periods-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .period-tag {
            background: #0ea5e9;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .chart-canvas {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Informe SEO Histórico</h1>
            <div class="subtitle">
                <strong>Cliente:</strong> ' . htmlspecialchars($autoseo->client_name) . '<br>
                <strong>Dominio:</strong> ' . htmlspecialchars($autoseo->url) . '<br>
                <strong>Generado:</strong> ' . date('d/m/Y H:i', strtotime('+2 hours')) . '
            </div>
        </div>

        <!-- Períodos analizados -->
        <div class="periods-info">
            <h3><i class="fas fa-calendar-alt"></i> Períodos Analizados</h3>
            <div class="periods-list">';

        foreach ($historicalData as $data) {
            $date = date('M Y', strtotime($data['uploaded_at']));
            $html .= '<span class="period-tag">' . $date . '</span>';
        }

        $html .= '</div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon" style="background: #dbeafe; color: #1e40af;">
                    <i class="fas fa-key"></i>
                </div>
                <div class="value">' . $analysis['summary']['total_keywords'] . '</div>
                <div class="label">Keywords Totales</div>
            </div>

            <div class="stat-card">
                <div class="icon" style="background: #dcfce7; color: #166534;">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="value">' . $analysis['summary']['keywords_in_top3'] . '</div>
                <div class="label">Top 3</div>
            </div>

            <div class="stat-card">
                <div class="icon" style="background: #dbeafe; color: #1e40af;">
                    <i class="fas fa-medal"></i>
                </div>
                <div class="value">' . $analysis['summary']['keywords_in_top10'] . '</div>
                <div class="label">Top 10</div>
            </div>

            <div class="stat-card">
                <div class="icon" style="background: #fef3c7; color: #92400e;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="value">' . $analysis['summary']['average_position'] . '</div>
                <div class="label">Posición Promedio</div>
            </div>

            <div class="stat-card">
                <div class="icon" style="background: #e0e7ff; color: #3730a3;">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="value">' . $analysis['summary']['visibility_score'] . '%</div>
                <div class="label">Visibilidad</div>
            </div>

            <div class="stat-card">
                <div class="icon" style="background: #f3e8ff; color: #7c3aed;">
                    <i class="fas fa-history"></i>
                </div>
                <div class="value">' . $analysis['summary']['periods_analyzed'] . '</div>
                <div class="label">Períodos</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-pie"></i>
                Distribución de Posiciones (Período Más Reciente)
            </div>
            <div class="chart-canvas">
                <canvas id="positionChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-line"></i>
                Evolución de Keywords en el Tiempo
            </div>
            <div class="chart-canvas">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-title">
                <i class="fas fa-chart-bar"></i>
                Análisis de Tendencias
            </div>
            <div class="chart-canvas">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        <!-- Keyword Table -->
        <div class="keyword-table">
            <div class="chart-title">
                <i class="fas fa-table"></i>
                Evolución Detallada de Keywords
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Posición Actual</th>
                        <th>Tendencia</th>
                        <th>Evolución</th>
                    </tr>
                </thead>
                <tbody>';

        // Generar filas de la tabla
        $topKeywords = array_slice($analysis['all_keywords'], 0, 20); // Mostrar solo las primeras 20
        foreach ($topKeywords as $keyword) {
            $evolution = $analysis['keyword_evolution'][$keyword] ?? [];
            $currentPosition = end($evolution);
            $trend = $this->calculateKeywordTrend($evolution);
            
            $positionClass = $this->getPositionClass($currentPosition);
            $trendIcon = $this->getTrendIcon($trend);
            
            $html .= '<tr>
                <td>' . htmlspecialchars($keyword) . '</td>
                <td><span class="position-badge ' . $positionClass . '">' . 
                    ($currentPosition ? $currentPosition : 'N/A') . '</span></td>
                <td>' . $trendIcon . '</td>
                <td>' . $this->formatEvolution($evolution) . '</td>
            </tr>';
        }

        $html .= '</tbody>
            </table>
        </div>
    </div>

    <script>
        // Datos para las gráficas
        const positionData = ' . json_encode($analysis['position_distribution']) . ';
        const evolutionData = ' . json_encode($this->prepareEvolutionData($analysis, $historicalData)) . ';
        const trendsData = ' . json_encode($analysis['trends']) . ';

        // Gráfica de distribución de posiciones
        const positionCtx = document.getElementById("positionChart").getContext("2d");
        new Chart(positionCtx, {
            type: "doughnut",
            data: {
                labels: Object.keys(positionData),
                datasets: [{
                    data: Object.values(positionData),
                    backgroundColor: [
                        "#10b981",
                        "#3b82f6", 
                        "#f59e0b",
                        "#ef4444",
                        "#6b7280",
                        "#8b5cf6"
                    ],
                    borderWidth: 2,
                    borderColor: "#fff"
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: "bottom"
                    }
                }
            }
        });

        // Gráfica de evolución
        const evolutionCtx = document.getElementById("evolutionChart").getContext("2d");
        new Chart(evolutionCtx, {
            type: "line",
            data: evolutionData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: "index"
                },
                plugins: {
                    legend: {
                        display: true,
                        position: "top",
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return context.dataset.label + ": " + (value ? "Posición " + value : "No encontrado");
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: "Período"
                        }
                    },
                    y: {
                        reverse: true,
                        min: 1,
                        max: 50,
                        title: {
                            display: true,
                            text: "Posición"
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.1,
                        spanGaps: false
                    },
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });

        // Gráfica de tendencias
        const trendsCtx = document.getElementById("trendsChart").getContext("2d");
        new Chart(trendsCtx, {
            type: "bar",
            data: {
                labels: ["Mejoradas", "Empeoradas", "Estables", "Nuevas"],
                datasets: [{
                    label: "Keywords",
                    data: [
                        trendsData.improved,
                        trendsData.declined, 
                        trendsData.stable,
                        trendsData.new
                    ],
                    backgroundColor: [
                        "#10b981",
                        "#ef4444",
                        "#6b7280", 
                        "#3b82f6"
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>
</html>';

        return $html;
    }

    private function prepareEvolutionData($analysis, $historicalData)
    {
        $labels = [];
        foreach ($historicalData as $data) {
            $labels[] = date('M Y', strtotime($data['uploaded_at']));
        }

        $datasets = [];
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#C9CBCF', '#4BC0C0', '#FF6384', '#36A2EB'
        ];

        $topKeywords = array_slice($analysis['all_keywords'], 0, 10);
        foreach ($topKeywords as $index => $keyword) {
            $evolution = $analysis['keyword_evolution'][$keyword] ?? [];
            
            // Filtrar keywords que tengan al menos una posición válida
            $hasValidPosition = false;
            foreach ($evolution as $pos) {
                if ($pos !== null && $pos > 0) {
                    $hasValidPosition = true;
                    break;
                }
            }
            
            if ($hasValidPosition) {
                // Procesar datos para manejar mejor los valores nulos
                $processedData = [];
                $pointRadius = [];
                $pointHoverRadius = [];
                
                foreach ($evolution as $pos) {
                    if ($pos === null) {
                        $processedData[] = null;
                        $pointRadius[] = 0;
                        $pointHoverRadius[] = 0;
                    } else {
                        $processedData[] = $pos;
                        $pointRadius[] = 4;
                        $pointHoverRadius[] = 6;
                    }
                }
                
                $datasets[] = [
                    'label' => strlen($keyword) > 20 ? substr($keyword, 0, 17) . '...' : $keyword,
                    'data' => $processedData,
                    'borderColor' => $colors[$index % count($colors)],
                    'backgroundColor' => $colors[$index % count($colors)] . '20',
                    'fill' => false,
                    'tension' => 0.1,
                    'pointRadius' => $pointRadius,
                    'pointHoverRadius' => $pointHoverRadius,
                    'pointBackgroundColor' => $colors[$index % count($colors)],
                    'pointBorderColor' => '#fff',
                    'pointBorderWidth' => 2,
                    'spanGaps' => false, // No conectar gaps automáticamente
                    'showLine' => true
                ];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }

    private function getPositionClass($position)
    {
        if ($position === null) return 'position-not-found';
        if ($position <= 3) return 'position-top3';
        if ($position <= 10) return 'position-top10';
        if ($position <= 20) return 'position-top20';
        if ($position <= 50) return 'position-top50';
        return 'position-not-found';
    }

    private function getTrendIcon($trend)
    {
        switch ($trend) {
            case 'improved':
                return '<i class="fas fa-arrow-up trend-icon trend-up"></i>';
            case 'declined':
                return '<i class="fas fa-arrow-down trend-icon trend-down"></i>';
            case 'stable':
                return '<i class="fas fa-minus trend-icon trend-stable"></i>';
            case 'new':
                return '<i class="fas fa-plus trend-icon trend-new"></i>';
            default:
                return '<i class="fas fa-question trend-icon trend-stable"></i>';
        }
    }

    private function formatEvolution($evolution)
    {
        $formatted = [];
        foreach ($evolution as $pos) {
            $formatted[] = $pos ? $pos : 'N/A';
        }
        return implode(' → ', $formatted);
    }

    private function displaySummary($analysis)
    {
        $this->info("📊 Resumen del análisis:");
        $this->info("   - Keywords totales: " . $analysis['summary']['total_keywords']);
        $this->info("   - Keywords en top 3: " . $analysis['summary']['keywords_in_top3']);
        $this->info("   - Keywords en top 10: " . $analysis['summary']['keywords_in_top10']);
        $this->info("   - Posición promedio: " . $analysis['summary']['average_position']);
        $this->info("   - Score de visibilidad: " . $analysis['summary']['visibility_score'] . "%");
        $this->info("   - Períodos analizados: " . $analysis['summary']['periods_analyzed']);
        $this->info("   - Keywords mejoradas: " . $analysis['trends']['improved']);
        $this->info("   - Keywords empeoradas: " . $analysis['trends']['declined']);
        $this->info("   - Keywords estables: " . $analysis['trends']['stable']);
        $this->info("   - Keywords nuevas: " . $analysis['trends']['new']);
    }
}
