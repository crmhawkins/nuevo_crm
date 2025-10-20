<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\AutoseoReportsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AutoseoAdvancedReports extends Controller
{
    /**
     * Genera un informe SEO avanzado con análisis profundo y visualizaciones interactivas
     */
    public function generateAdvancedReport(Request $request)
    {
        set_time_limit(300);
        
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'ID del cliente es requerido'
            ], 400);
        }

        try {
            Log::info("🎨 Generando informe SEO AVANZADO para cliente ID: {$id}");

            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                return response()->json([
                    'success' => false,
                    'message' => "Cliente Autoseo con ID {$id} no encontrado."
                ], 404);
            }

            // Cargar todos los JSONs históricos
            $historicalData = $this->loadAllHistoricalJsons($autoseo);
            
            if (empty($historicalData)) {
                Log::info("ℹ️ No hay datos históricos, generando informe básico");
            }

            // Análisis profundo de datos
            $analysis = $this->performAdvancedAnalysis($historicalData, $autoseo);
            
            // Generar HTML con todas las visualizaciones
            $html = $this->generateAdvancedHtml($autoseo, $analysis);

            // Guardar archivo
            $filename = "informe_seo_avanzado_{$id}_" . date('Y-m-d_His') . ".html";
            $path = "autoseo_reports/{$filename}";
            Storage::disk('public')->put($path, $html);

            // Guardar en base de datos
            $report = AutoseoReportsModel::create([
                'autoseo_id' => $autoseo->id,
                'path' => $path
            ]);

            Log::info("✅ Informe avanzado generado exitosamente!");

            return response()->json([
                'success' => true,
                'message' => 'Informe avanzado generado exitosamente',
                'data' => [
                    'report_id' => $report->id,
                    'filename' => $filename,
                    'path' => $path,
                    'url' => url(Storage::disk('public')->url($path)),
                    'summary' => $analysis['summary'],
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error generando informe avanzado: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generando informe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Carga todos los JSONs históricos del cliente desde el storage local
     */
    private function loadAllHistoricalJsons($autoseo)
    {
        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
        $historicalData = [];

        foreach ($jsonStorage as $jsonInfo) {
            $path = $jsonInfo['path'] ?? null;
            
            if (!$path) continue;

            $fullPath = storage_path('app/public/' . $path);
            
            if (!File::exists($fullPath)) continue;

            $jsonContent = File::get($fullPath);
            $data = json_decode($jsonContent, true);

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

        return $historicalData;
    }

    /**
     * Realiza un análisis profundo de los datos SEO
     */
    private function performAdvancedAnalysis($historicalData, $autoseo)
    {
        $analysis = [
            'summary' => [],
            'keywords_analysis' => [],
            'trends' => [],
            'competitive_analysis' => [],
            'people_also_ask' => [],
            'timeline' => [],
            'performance_score' => 0,
        ];

        // Si no hay datos históricos, retornar estructura vacía
        if (empty($historicalData)) {
            return $this->getEmptyAnalysis();
        }

        // Análisis de keywords a lo largo del tiempo
        $allKeywords = [];
        $monthlyData = [];
        $peopleAlsoAsk = [];

        foreach ($historicalData as $index => $period) {
            $date = $period['uploaded_at'] ?? 'Unknown';
            $month = date('Y-m', strtotime($date));
            
            $monthlyData[$month] = [
                'date' => $date,
                'keywords' => $period['detalles_keywords'] ?? [],
                'short_tail' => $period['short_tail'] ?? [],
                'long_tail' => $period['long_tail'] ?? [],
            ];

            // Recopilar People Also Ask
            if (isset($period['people_also_ask'])) {
                foreach ($period['people_also_ask'] as $paa) {
                    $question = $paa['question'] ?? null;
                    if ($question && !isset($peopleAlsoAsk[$question])) {
                        $peopleAlsoAsk[$question] = $paa;
                    }
                }
            }

            // Procesar keywords
            foreach ($period['detalles_keywords'] ?? [] as $kw) {
                $keyword = $kw['keyword'];
                
                if (!isset($allKeywords[$keyword])) {
                    $allKeywords[$keyword] = [
                        'keyword' => $keyword,
                        'positions' => [],
                        'volumes' => [],
                        'first_seen' => $date,
                        'last_seen' => $date,
                        'best_position' => null,
                        'worst_position' => null,
                        'current_position' => null,
                        'trend' => 'stable',
                    ];
                }

                $position = $kw['position'];
                $allKeywords[$keyword]['positions'][] = [
                    'date' => $date,
                    'position' => $position,
                ];
                $allKeywords[$keyword]['last_seen'] = $date;
                $allKeywords[$keyword]['current_position'] = $position;

                if ($position !== null) {
                    if ($allKeywords[$keyword]['best_position'] === null || $position < $allKeywords[$keyword]['best_position']) {
                        $allKeywords[$keyword]['best_position'] = $position;
                    }
                    if ($allKeywords[$keyword]['worst_position'] === null || $position > $allKeywords[$keyword]['worst_position']) {
                        $allKeywords[$keyword]['worst_position'] = $position;
                    }
                }

                // Guardar total_results si existe
                if (isset($kw['total_results'])) {
                    $allKeywords[$keyword]['competition'] = $kw['total_results'];
                }
            }
        }

        // Calcular tendencias y métricas
        $totalKeywords = count($allKeywords);
        $keywordsInTop1 = 0;
        $keywordsInTop3 = 0;
        $keywordsInTop10 = 0;
        $keywordsWithPosition = 0;
        $totalPositions = 0;
        $trends = ['improved' => 0, 'declined' => 0, 'stable' => 0, 'new' => 0, 'not_found' => 0];

        foreach ($allKeywords as &$kw) {
            $positions = array_column($kw['positions'], 'position');
            $validPositions = array_filter($positions, function($p) { return $p !== null; });

            // Calcular tendencia
            if (count($validPositions) >= 2) {
                $recent = array_slice($validPositions, -2);
                if ($recent[1] < $recent[0]) {
                    $kw['trend'] = 'improved';
                    $trends['improved']++;
                } elseif ($recent[1] > $recent[0]) {
                    $kw['trend'] = 'declined';
                    $trends['declined']++;
                } else {
                    $kw['trend'] = 'stable';
                    $trends['stable']++;
                }
            } elseif (count($validPositions) == 1) {
                $kw['trend'] = 'new';
                $trends['new']++;
            } else {
                $kw['trend'] = 'not_found';
                $trends['not_found']++;
            }

            // Métricas de posición actual
            if ($kw['current_position'] !== null) {
                $keywordsWithPosition++;
                $totalPositions += $kw['current_position'];
                
                if ($kw['current_position'] == 1) $keywordsInTop1++;
                if ($kw['current_position'] <= 3) $keywordsInTop3++;
                if ($kw['current_position'] <= 10) $keywordsInTop10++;
            }
        }

        $averagePosition = $keywordsWithPosition > 0 ? round($totalPositions / $keywordsWithPosition, 2) : 0;
        $visibilityScore = $totalKeywords > 0 ? round(($keywordsInTop10 / $totalKeywords) * 100, 2) : 0;

        // Calcular score de rendimiento (0-100)
        $performanceScore = $this->calculatePerformanceScore([
            'top1' => $keywordsInTop1,
            'top3' => $keywordsInTop3,
            'top10' => $keywordsInTop10,
            'total' => $totalKeywords,
            'average' => $averagePosition,
            'improved' => $trends['improved'],
            'declined' => $trends['declined'],
        ]);

        $analysis['summary'] = [
            'total_keywords' => $totalKeywords,
            'keywords_in_top1' => $keywordsInTop1,
            'keywords_in_top3' => $keywordsInTop3,
            'keywords_in_top10' => $keywordsInTop10,
            'average_position' => $averagePosition,
            'visibility_score' => $visibilityScore,
            'performance_score' => $performanceScore,
            'periods_analyzed' => count($historicalData),
            'date_range' => [
                'start' => $historicalData[0]['uploaded_at'] ?? null,
                'end' => end($historicalData)['uploaded_at'] ?? null,
            ]
        ];

        $analysis['keywords_analysis'] = $allKeywords;
        $analysis['trends'] = $trends;
        $analysis['monthly_data'] = $monthlyData;
        $analysis['people_also_ask'] = array_values($peopleAlsoAsk);
        $analysis['competitive_analysis'] = $this->analyzeCompetition($allKeywords);

        return $analysis;
    }

    /**
     * Calcula un score de rendimiento general (0-100)
     */
    private function calculatePerformanceScore($metrics)
    {
        $score = 0;

        // Peso por posiciones top (40 puntos máximo)
        if ($metrics['total'] > 0) {
            $topRatio = ($metrics['top1'] * 3 + $metrics['top3'] * 2 + $metrics['top10']) / $metrics['total'];
            $score += min(40, $topRatio * 10);
        }

        // Peso por posición promedio (30 puntos máximo)
        if ($metrics['average'] > 0 && $metrics['average'] <= 100) {
            $score += max(0, 30 - ($metrics['average'] * 0.3));
        }

        // Peso por tendencias (30 puntos máximo)
        $totalTrends = $metrics['improved'] + $metrics['declined'];
        if ($totalTrends > 0) {
            $trendRatio = $metrics['improved'] / $totalTrends;
            $score += $trendRatio * 30;
        } else {
            $score += 15; // Neutral si no hay cambios
        }

        return round(min(100, max(0, $score)), 1);
    }

    /**
     * Analiza la competencia basándose en total_results
     */
    private function analyzeCompetition($keywords)
    {
        $competition = [
            'low' => 0,      // < 10,000
            'medium' => 0,   // 10,000 - 100,000
            'high' => 0,     // 100,000 - 1,000,000
            'very_high' => 0 // > 1,000,000
        ];

        foreach ($keywords as $kw) {
            if (!isset($kw['competition'])) continue;

            $comp = $kw['competition'];
            if ($comp < 10000) $competition['low']++;
            elseif ($comp < 100000) $competition['medium']++;
            elseif ($comp < 1000000) $competition['high']++;
            else $competition['very_high']++;
        }

        return $competition;
    }

    /**
     * Retorna estructura de análisis vacía
     */
    private function getEmptyAnalysis()
    {
        return [
            'summary' => [
                'total_keywords' => 0,
                'keywords_in_top1' => 0,
                'keywords_in_top3' => 0,
                'keywords_in_top10' => 0,
                'average_position' => 0,
                'visibility_score' => 0,
                'performance_score' => 0,
                'periods_analyzed' => 0,
                'date_range' => ['start' => null, 'end' => null]
            ],
            'keywords_analysis' => [],
            'trends' => ['improved' => 0, 'declined' => 0, 'stable' => 0, 'new' => 0, 'not_found' => 0],
            'monthly_data' => [],
            'people_also_ask' => [],
            'competitive_analysis' => ['low' => 0, 'medium' => 0, 'high' => 0, 'very_high' => 0],
        ];
    }

    /**
     * Genera el HTML avanzado del informe
     */
    private function generateAdvancedHtml($autoseo, $analysis)
    {
        $domain = parse_url($autoseo->url, PHP_URL_HOST) ?: $autoseo->url;
        $reportDate = date('d/m/Y H:i');
        $summary = $analysis['summary'];
        $keywords = $analysis['keywords_analysis'];
        $trends = $analysis['trends'];
        $monthlyData = $analysis['monthly_data'];
        $peopleAlsoAsk = $analysis['people_also_ask'];
        $competition = $analysis['competitive_analysis'];

        // Preparar datos para gráficos
        $chartData = $this->prepareChartData($keywords, $monthlyData);

        // Determinar color del performance score
        $scoreColor = $summary['performance_score'] >= 75 ? '#10b981' : 
                      ($summary['performance_score'] >= 50 ? '#f59e0b' : '#ef4444');

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO Avanzado - ' . htmlspecialchars($domain) . '</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
        }

        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 2rem 0;
        }

        .report-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-container {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        .performance-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        .keyword-table {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }

        .keyword-row:hover {
            background: #f9fafb;
        }

        .badge-custom {
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .trend-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .icon-stat {
            width: 60px;
            height: 60px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .progress-modern {
            height: 8px;
            border-radius: 10px;
            background: #e5e7eb;
        }

        .progress-bar-modern {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .paa-card {
            background: white;
            border-left: 4px solid var(--info-color);
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .competition-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
        }

        .printable {
            display: none;
        }

        @media print {
            .no-print { display: none; }
            .printable { display: block; }
            body { background: white; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container report-container">';

        // Continúa en la siguiente parte...
        $html .= $this->generateHeaderSection($autoseo, $reportDate, $summary, $scoreColor);
        $html .= $this->generateStatsSection($summary, $trends);
        $html .= $this->generateChartsSection($chartData, $monthlyData);
        $html .= $this->generateKeywordsTableSection($keywords);
        $html .= $this->generateCompetitionSection($competition);
        
        if (!empty($peopleAlsoAsk)) {
            $html .= $this->generatePeopleAlsoAskSection($peopleAlsoAsk);
        }

        $html .= $this->generateFooterSection($autoseo);

        $html .= '
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        ' . $this->generateChartScripts($chartData, $trends, $summary) . '
    </script>
    <script>
        // Animaciones al scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate-in");
                }
            });
        });
        
        document.querySelectorAll(".stat-card, .chart-container").forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>';

        return $html;
    }

    // Continuará con los métodos de generación de secciones...
    
    private function generateHeaderSection($autoseo, $reportDate, $summary, $scoreColor)
    {
        $domain = parse_url($autoseo->url, PHP_URL_HOST) ?: $autoseo->url;
        
        return '
        <div class="header-card">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-4 mb-2">
                        <i class="fas fa-chart-line me-3"></i>Informe SEO Avanzado
                    </h1>
                    <h3 class="mb-3">' . htmlspecialchars($domain) . '</h3>
                    <p class="mb-2"><i class="fas fa-building me-2"></i>' . htmlspecialchars($autoseo->client_name) . '</p>
                    <p class="mb-0"><i class="fas fa-calendar me-2"></i>Generado: ' . $reportDate . '</p>
                    ' . ($summary['date_range']['start'] ? '<p class="mb-0 mt-2 text-white-50"><i class="fas fa-clock me-2"></i>Período analizado: ' . date('d/m/Y', strtotime($summary['date_range']['start'])) . ' - ' . date('d/m/Y', strtotime($summary['date_range']['end'])) . '</p>' : '') . '
                </div>
                <div class="col-md-4 text-center">
                    <div class="performance-circle mx-auto" style="background: ' . $scoreColor . ';">
                        ' . $summary['performance_score'] . '
                    </div>
                    <p class="text-white mt-3 mb-0 fw-bold">Performance Score</p>
                    <small class="text-white-50">Puntuación general de rendimiento SEO</small>
                </div>
            </div>
        </div>';
    }

    private function generateStatsSection($summary, $trends)
    {
        return '
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="icon-stat bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-hashtag"></i>
                    </div>
                    <div class="stat-label">Total Keywords</div>
                    <div class="stat-value text-primary">' . $summary['total_keywords'] . '</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern bg-primary" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="icon-stat bg-success bg-opacity-10 text-success">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="stat-label">Top 10 Posiciones</div>
                    <div class="stat-value text-success">' . $summary['keywords_in_top10'] . '</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern bg-success" style="width: ' . ($summary['total_keywords'] > 0 ? ($summary['keywords_in_top10'] / $summary['total_keywords'] * 100) : 0) . '%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="icon-stat bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="stat-label">Posición Promedio</div>
                    <div class="stat-value text-warning">' . ($summary['average_position'] > 0 ? number_format($summary['average_position'], 1) : 'N/A') . '</div>
                    <small class="text-muted">Menor es mejor</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="icon-stat bg-info bg-opacity-10 text-info">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-label">Score de Visibilidad</div>
                    <div class="stat-value text-info">' . $summary['visibility_score'] . '%</div>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern bg-info" style="width: ' . $summary['visibility_score'] . '%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="chart-container">
                    <h5 class="section-title">
                        <i class="fas fa-chart-pie text-primary"></i>
                        Distribución de Tendencias
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="trendsChart" height="250"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="text-center p-3 rounded" style="background: #d1fae5;">
                                        <i class="fas fa-arrow-up fa-2x text-success mb-2"></i>
                                        <h4 class="mb-0 text-success">' . $trends['improved'] . '</h4>
                                        <small class="text-muted">Mejoradas</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center p-3 rounded" style="background: #fee2e2;">
                                        <i class="fas fa-arrow-down fa-2x text-danger mb-2"></i>
                                        <h4 class="mb-0 text-danger">' . $trends['declined'] . '</h4>
                                        <small class="text-muted">Empeoradas</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center p-3 rounded" style="background: #dbeafe;">
                                        <i class="fas fa-star fa-2x text-primary mb-2"></i>
                                        <h4 class="mb-0 text-primary">' . $trends['new'] . '</h4>
                                        <small class="text-muted">Nuevas</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="text-center p-3 rounded" style="background: #e5e7eb;">
                                        <i class="fas fa-minus fa-2x text-secondary mb-2"></i>
                                        <h4 class="mb-0 text-secondary">' . $trends['stable'] . '</h4>
                                        <small class="text-muted">Estables</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    private function generateChartsSection($chartData, $monthlyData)
    {
        $html = '
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="chart-container">
                    <h5 class="section-title">
                        <i class="fas fa-chart-line text-success"></i>
                        Evolución de Posiciones (Top Keywords)
                    </h5>
                    <div class="chart-wrapper">
                        <canvas id="positionsChart"></canvas>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle me-1"></i>
                        Muestra las 10 keywords con mejor rendimiento. Posiciones más bajas son mejores.
                    </small>
                </div>
            </div>
        </div>';

        // Gráfico de distribución de posiciones
        $html .= '
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="section-title">
                        <i class="fas fa-layer-group text-warning"></i>
                        Distribución de Posiciones
                    </h5>
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="distributionChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="section-title">
                        <i class="fas fa-trophy text-warning"></i>
                        Posiciones Top 1, Top 3, Top 10
                    </h5>
                    <div class="chart-wrapper" style="height: 300px;">
                        <canvas id="topPositionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    private function generateKeywordsTableSection($keywords)
    {
        $html = '
        <div class="mb-4">
            <div class="keyword-table">
                <div class="p-4">
                    <h5 class="section-title">
                        <i class="fas fa-table text-info"></i>
                        Análisis Detallado de Keywords
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th><i class="fas fa-key me-1"></i>Keyword</th>
                                    <th class="text-center"><i class="fas fa-map-marker-alt me-1"></i>Posición Actual</th>
                                    <th class="text-center"><i class="fas fa-trophy me-1"></i>Mejor Posición</th>
                                    <th class="text-center"><i class="fas fa-chart-line me-1"></i>Tendencia</th>
                                    <th class="text-center"><i class="fas fa-users me-1"></i>Competencia</th>
                                    <th class="text-center"><i class="fas fa-history me-1"></i>Historial</th>
                                </tr>
                            </thead>
                            <tbody>';

        // Ordenar keywords por posición actual
        $sortedKeywords = $keywords;
        usort($sortedKeywords, function($a, $b) {
            $posA = $a['current_position'] ?? 999;
            $posB = $b['current_position'] ?? 999;
            return $posA - $posB;
        });

        foreach (array_slice($sortedKeywords, 0, 50) as $kw) {
            $currentPos = $kw['current_position'] ?? 'N/A';
            $bestPos = $kw['best_position'] ?? 'N/A';
            $competition = $kw['competition'] ?? 0;
            
            // Badge de posición
            $positionBadge = $currentPos !== 'N/A' ? 
                ($currentPos == 1 ? '<span class="badge bg-warning text-dark">#' . $currentPos . '</span>' :
                ($currentPos <= 3 ? '<span class="badge bg-success">#' . $currentPos . '</span>' :
                ($currentPos <= 10 ? '<span class="badge bg-info">#' . $currentPos . '</span>' :
                '<span class="badge bg-secondary">#' . $currentPos . '</span>'))) :
                '<span class="badge bg-light text-dark">N/A</span>';

            // Tendencia
            $trendIcons = [
                'improved' => '<span class="trend-badge badge-custom" style="background: #d1fae5; color: #065f46;"><i class="fas fa-arrow-up"></i> Mejorada</span>',
                'declined' => '<span class="trend-badge badge-custom" style="background: #fee2e2; color: #991b1b;"><i class="fas fa-arrow-down"></i> Empeorada</span>',
                'stable' => '<span class="trend-badge badge-custom" style="background: #e5e7eb; color: #374151;"><i class="fas fa-minus"></i> Estable</span>',
                'new' => '<span class="trend-badge badge-custom" style="background: #dbeafe; color: #1e40af;"><i class="fas fa-star"></i> Nueva</span>',
                'not_found' => '<span class="trend-badge badge-custom" style="background: #fee; color: #666;"><i class="fas fa-search-minus"></i> No encontrada</span>',
            ];
            $trendBadge = $trendIcons[$kw['trend']] ?? '';

            // Competencia
            $compLevel = $competition < 10000 ? 'Baja' : 
                        ($competition < 100000 ? 'Media' : 
                        ($competition < 1000000 ? 'Alta' : 'Muy Alta'));
            $compColor = $competition < 10000 ? '#10b981' : 
                        ($competition < 100000 ? '#f59e0b' : 
                        ($competition < 1000000 ? '#ef4444' : '#7f1d1d'));

            $html .= '
                                <tr class="keyword-row">
                                    <td><strong>' . htmlspecialchars($kw['keyword']) . '</strong></td>
                                    <td class="text-center">' . $positionBadge . '</td>
                                    <td class="text-center">' . ($bestPos !== 'N/A' ? '<span class="badge bg-light text-success">#' . $bestPos . '</span>' : 'N/A') . '</td>
                                    <td class="text-center">' . $trendBadge . '</td>
                                    <td class="text-center">
                                        <span class="competition-indicator" style="background: ' . $compColor . ';"></span>
                                        ' . $compLevel . '
                                        <br><small class="text-muted">' . number_format($competition) . ' resultados</small>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">' . count($kw['positions']) . ' registros</small>
                                    </td>
                                </tr>';
        }

        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    private function generateCompetitionSection($competition)
    {
        $total = array_sum($competition);
        
        return '
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="chart-container">
                    <h5 class="section-title">
                        <i class="fas fa-users-cog text-danger"></i>
                        Análisis de Competencia
                    </h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded" style="background: #d1fae5;">
                                <h3 class="text-success">' . $competition['low'] . '</h3>
                                <p class="mb-0">Competencia Baja</p>
                                <small class="text-muted">< 10K resultados</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded" style="background: #fef3c7;">
                                <h3 class="text-warning">' . $competition['medium'] . '</h3>
                                <p class="mb-0">Competencia Media</p>
                                <small class="text-muted">10K - 100K</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded" style="background: #fed7aa;">
                                <h3 class="text-danger">' . $competition['high'] . '</h3>
                                <p class="mb-0">Competencia Alta</p>
                                <small class="text-muted">100K - 1M</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 rounded" style="background: #fee2e2;">
                                <h3 style="color: #7f1d1d;">' . $competition['very_high'] . '</h3>
                                <p class="mb-0">Competencia Muy Alta</p>
                                <small class="text-muted">> 1M resultados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function generatePeopleAlsoAskSection($peopleAlsoAsk)
    {
        $html = '
        <div class="mb-4">
            <div class="chart-container">
                <h5 class="section-title">
                    <i class="fas fa-question-circle text-info"></i>
                    Preguntas Frecuentes (People Also Ask)
                </h5>
                <div class="row">';

        foreach (array_slice($peopleAlsoAsk, 0, 12) as $index => $paa) {
            $html .= '
                    <div class="col-md-6 mb-3">
                        <div class="paa-card">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <span class="badge bg-info rounded-circle" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                        ' . ($index + 1) . '
                                    </span>
                                </div>
                                <div>
                                    <strong>' . htmlspecialchars($paa['question']) . '</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-search me-1"></i>' . number_format($paa['total_results']) . ' resultados
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '
                </div>
            </div>
        </div>';

        return $html;
    }

    private function generateFooterSection($autoseo)
    {
        return '
        <div class="text-center mt-5 mb-4">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white py-4">
                    <h5 class="mb-3"><i class="fas fa-chart-line me-2"></i>Informe Generado Automáticamente</h5>
                    <p class="mb-2">Cliente: ' . htmlspecialchars($autoseo->client_name) . '</p>
                    <p class="mb-0"><small>Sistema AutoSEO - ' . config('app.name') . '</small></p>
                </div>
            </div>
        </div>
        
        <div class="no-print text-center">
            <button onclick="window.print()" class="btn btn-lg btn-primary">
                <i class="fas fa-print me-2"></i>Imprimir / Guardar PDF
            </button>
        </div>';
    }

    private function prepareChartData($keywords, $monthlyData)
    {
        // Preparar datos para gráficos de Chart.js
        $top10Keywords = array_slice(array_filter($keywords, function($kw) {
            return $kw['current_position'] !== null && $kw['current_position'] <= 20;
        }), 0, 10);

        // Extraer fechas y posiciones
        $dates = [];
        $datasets = [];
        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
            '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
            '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef'
        ];

        // Obtener todas las fechas únicas
        foreach ($monthlyData as $month => $data) {
            $dates[] = date('M Y', strtotime($data['date']));
        }

        // Crear datasets para cada keyword
        $colorIndex = 0;
        foreach ($top10Keywords as $kw) {
            $keywordData = [];
            
            foreach (array_keys($monthlyData) as $month) {
                // Buscar posición de esta keyword en este mes
                $position = null;
                foreach ($kw['positions'] as $pos) {
                    if (date('Y-m', strtotime($pos['date'])) == $month) {
                        $position = $pos['position'];
                        break;
                    }
                }
                $keywordData[] = $position;
            }

            $datasets[] = [
                'label' => $kw['keyword'],
                'data' => $keywordData,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)] . '33',
                'tension' => 0.4,
                'fill' => false,
            ];
            $colorIndex++;
        }

        return [
            'dates' => $dates,
            'datasets' => $datasets,
            'keywords' => $top10Keywords,
        ];
    }

    private function generateChartScripts($chartData, $trends = [], $summary = [])
    {
        $datesJson = json_encode($chartData['dates']);
        $datasetsJson = json_encode($chartData['datasets']);

        return "
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando gráficos...');
            
            // Gráfico de evolución de posiciones
            const positionsCanvas = document.getElementById('positionsChart');
            if (!positionsCanvas) {
                console.error('Canvas positionsChart no encontrado');
                return;
            }
            const positionsCtx = positionsCanvas.getContext('2d');
        new Chart(positionsCtx, {
            type: 'line',
            data: {
                labels: {$datesJson},
                datasets: {$datasetsJson}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (context.parsed.y !== null) {
                                    label += ': Posición ' + context.parsed.y;
                                } else {
                                    label += ': Sin posición';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        reverse: true,
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Posición en Google',
                            font: { size: 14, weight: 'bold' }
                        },
                        ticks: {
                            callback: function(value) {
                                return '#' + value;
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Período',
                            font: { size: 14, weight: 'bold' }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // Gráfico de tendencias (pie chart)
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        new Chart(trendsCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mejoradas', 'Empeoradas', 'Nuevas', 'Estables', 'No encontradas'],
                datasets: [{
                    data: [" . ($trends['improved'] ?? 0) . ", " . ($trends['declined'] ?? 0) . ", " . ($trends['new'] ?? 0) . ", " . ($trends['stable'] ?? 0) . ", " . ($trends['not_found'] ?? 0) . "],
                    backgroundColor: ['#10b981', '#ef4444', '#3b82f6', '#6b7280', '#d1d5db'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 13 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' keywords';
                            }
                        }
                    }
                }
            }
        });

        // Gráfico de distribución de posiciones
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'bar',
            data: {
                labels: ['Top 1', 'Top 2-3', 'Top 4-10', 'Top 11-20', 'Pos 21+', 'Sin posición'],
                datasets: [{
                    label: 'Keywords',
                    data: [" . ($summary['keywords_in_top1'] ?? 0) . ", 
                           " . (($summary['keywords_in_top3'] ?? 0) - ($summary['keywords_in_top1'] ?? 0)) . ", 
                           " . (($summary['keywords_in_top10'] ?? 0) - ($summary['keywords_in_top3'] ?? 0)) . ", 
                           0, 0, 
                           " . ($trends['not_found'] ?? 0) . "],
                    backgroundColor: ['#fbbf24', '#10b981', '#3b82f6', '#8b5cf6', '#6b7280', '#d1d5db'],
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Keywords'
                        }
                    }
                }
            }
        });

        // Gráfico de top positions
        const topPositionsCtx = document.getElementById('topPositionsChart').getContext('2d');
        new Chart(topPositionsCtx, {
            type: 'bar',
            data: {
                labels: ['Top 1', 'Top 3', 'Top 10'],
                datasets: [{
                    label: 'Keywords',
                    data: [" . ($summary['keywords_in_top1'] ?? 0) . ", " . ($summary['keywords_in_top3'] ?? 0) . ", " . ($summary['keywords_in_top10'] ?? 0) . "],
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)'
                    ],
                    borderColor: [
                        '#fbbf24',
                        '#10b981',
                        '#3b82f6'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Keywords'
                        }
                    }
                }
            }
        });
        
        console.log('✅ Gráficos inicializados correctamente');
    }); // Fin DOMContentLoaded
        ";
    }
}

