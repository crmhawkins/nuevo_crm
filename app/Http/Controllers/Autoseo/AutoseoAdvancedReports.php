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
     * Carga todos los JSONs históricos del cliente desde múltiples fuentes
     */
    private function loadAllHistoricalJsons($autoseo)
    {
        $historicalData = [];

        // 1. Cargar desde json_storage (archivos guardados)
        $jsonStorage = $autoseo->json_storage ? json_decode($autoseo->json_storage, true) : [];
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

        // 2. Cargar desde json_home (datos actuales)
        if ($autoseo->json_home) {
            $currentData = json_decode($autoseo->json_home, true);
            if ($currentData) {
                $currentData['uploaded_at'] = $autoseo->updated_at->format('Y-m-d H:i:s');
                $currentData['source'] = 'current';
                $historicalData[] = $currentData;
            }
        }

        // 3. Cargar desde json_mesanterior (datos del mes anterior)
        if ($autoseo->json_mesanterior) {
            $previousData = json_decode($autoseo->json_mesanterior, true);
            if ($previousData) {
                $previousData['uploaded_at'] = date('Y-m-d H:i:s', strtotime('-1 month'));
                $previousData['source'] = 'previous_month';
                $historicalData[] = $previousData;
            }
        }

        // 4. Buscar archivos JSON adicionales en el directorio del cliente
        $clientDir = storage_path('app/public/autoseo_json/' . $autoseo->id);
        if (File::exists($clientDir)) {
            $files = File::glob($clientDir . '/*.json');
            foreach ($files as $file) {
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);
                
                if ($data) {
                    // Intentar extraer fecha del nombre del archivo o usar fecha de modificación
                    $filename = basename($file);
                    $fileDate = File::lastModified($file);
                    $data['uploaded_at'] = date('Y-m-d H:i:s', $fileDate);
                    $data['source'] = 'file_' . $filename;
                    $historicalData[] = $data;
                }
            }
        }

        // 5. Buscar en directorio general de autoseo_json
        $generalDir = storage_path('app/public/autoseo_json');
        if (File::exists($generalDir)) {
            $files = File::glob($generalDir . '/*_' . $autoseo->id . '_*.json');
            foreach ($files as $file) {
                $jsonContent = File::get($file);
                $data = json_decode($jsonContent, true);
                
                if ($data) {
                    $fileDate = File::lastModified($file);
                    $data['uploaded_at'] = date('Y-m-d H:i:s', $fileDate);
                    $data['source'] = 'general_' . basename($file);
                    $historicalData[] = $data;
                }
            }
        }

        // Eliminar duplicados basándose en la fecha y contenido
        $uniqueData = [];
        foreach ($historicalData as $data) {
            $date = $data['uploaded_at'] ?? '1970-01-01';
            $month = date('Y-m', strtotime($date));
            $key = $month . '_' . md5(json_encode($data['detalles_keywords'] ?? []));
            
            if (!isset($uniqueData[$key])) {
                $uniqueData[$key] = $data;
            }
        }

        $historicalData = array_values($uniqueData);

        // Ordenar por fecha
        usort($historicalData, function($a, $b) {
            $dateA = $a['uploaded_at'] ?? '1970-01-01';
            $dateB = $b['uploaded_at'] ?? '1970-01-01';
            return strtotime($dateA) - strtotime($dateB);
        });

        Log::info("📊 Datos históricos encontrados: " . count($historicalData) . " períodos");
        foreach ($historicalData as $data) {
            $date = $data['uploaded_at'] ?? 'Unknown';
            $source = $data['source'] ?? 'unknown';
            $keywords = count($data['detalles_keywords'] ?? []);
            Log::info("  - {$date} ({$source}): {$keywords} keywords");
        }

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

        // Si no hay datos históricos, crear análisis básico con datos actuales
        if (empty($historicalData)) {
            return $this->getBasicAnalysis($autoseo);
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
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                    colors: {
                        primary: {
                            50: "#eef2ff",
                            100: "#e0e7ff",
                            200: "#c7d2fe",
                            300: "#a5b4fc",
                            400: "#818cf8",
                            500: "#6366f1",
                            600: "#4f46e5",
                            700: "#4338ca",
                            800: "#3730a3",
                            900: "#312e81",
                        }
                    }
                }
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: "Inter", sans-serif;
        }

        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .custom-gradient {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);
        }

        .pattern-bg {
            background-image: url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%236366f1\' fill-opacity=\'0.1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2V6h4V4H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
        }

        @media print {
            .no-print { display: none; }
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
<body class="min-h-screen pattern-bg">
    <!-- Navbar -->
    <nav class="glass-morphism border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <img src="https://hawkins.es/wp-content/uploads/2022/05/logo-hawkins.png" alt="Hawkins Logo" class="h-8 w-auto">
                </div>
                <div class="flex items-center space-x-4">
                    <a href="https://hawkins.es/contacto" class="text-gray-600 hover:text-primary-600 text-sm font-medium">
                        Contacto
                    </a>
                    <a href="https://hawkins.es" class="text-gray-600 hover:text-primary-600 text-sm font-medium">
                        Volver a Hawkins
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">';

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
    </main>

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
        }, { threshold: 0.1 });
        
        document.querySelectorAll(".animate-in").forEach(el => {
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
        <!-- Header -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <div class="grid md:grid-cols-3 gap-8 items-center">
                <div class="md:col-span-2">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2 flex items-center">
                        <i class="fas fa-chart-line text-primary-600 mr-3"></i>
                        Informe SEO Avanzado
                    </h1>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-4">' . htmlspecialchars($domain) . '</h3>
                    <div class="space-y-2 text-gray-600">
                        <p class="flex items-center">
                            <i class="fas fa-building mr-2 text-primary-500"></i>
                            <span class="font-medium">' . htmlspecialchars($autoseo->client_name) . '</span>
                        </p>
                        <p class="flex items-center">
                            <i class="fas fa-calendar mr-2 text-primary-500"></i>
                            <span>Generado: ' . $reportDate . '</span>
                        </p>
                        ' . ($summary['date_range']['start'] ? '<p class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock mr-2"></i>
                            <span>Período: ' . date('d/m/Y', strtotime($summary['date_range']['start'])) . ' - ' . date('d/m/Y', strtotime($summary['date_range']['end'])) . '</span>
                        </p>' : '') . '
                    </div>
                </div>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-36 h-36 rounded-full text-white text-5xl font-bold shadow-2xl" style="background: ' . $scoreColor . ';">
                        ' . $summary['performance_score'] . '
                    </div>
                    <p class="text-gray-900 font-bold mt-4 mb-1">Performance Score</p>
                    <p class="text-sm text-gray-500">Puntuación general de rendimiento SEO</p>
                </div>
            </div>
        </div>';
    }

    private function generateStatsSection($summary, $trends)
    {
        return '
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Keywords -->
            <div class="glass-morphism rounded-xl p-6 hover:shadow-xl transition-all duration-300 animate-in">
                <div class="flex items-center justify-center w-14 h-14 bg-primary-100 rounded-lg mb-4">
                    <i class="fas fa-hashtag text-primary-600 text-2xl"></i>
                </div>
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Keywords</div>
                <div class="text-4xl font-bold text-primary-600">' . $summary['total_keywords'] . '</div>
                <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-primary-600 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            <!-- Top 10 -->
            <div class="glass-morphism rounded-xl p-6 hover:shadow-xl transition-all duration-300 animate-in" style="animation-delay: 0.1s">
                <div class="flex items-center justify-center w-14 h-14 bg-green-100 rounded-lg mb-4">
                    <i class="fas fa-trophy text-green-600 text-2xl"></i>
                </div>
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Top 10 Posiciones</div>
                <div class="text-4xl font-bold text-green-600">' . $summary['keywords_in_top10'] . '</div>
                <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-green-600 rounded-full" style="width: ' . ($summary['total_keywords'] > 0 ? ($summary['keywords_in_top10'] / $summary['total_keywords'] * 100) : 0) . '%"></div>
                </div>
            </div>

            <!-- Posición Promedio -->
            <div class="glass-morphism rounded-xl p-6 hover:shadow-xl transition-all duration-300 animate-in" style="animation-delay: 0.2s">
                <div class="flex items-center justify-center w-14 h-14 bg-amber-100 rounded-lg mb-4">
                    <i class="fas fa-chart-bar text-amber-600 text-2xl"></i>
                </div>
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Posición Promedio</div>
                <div class="text-4xl font-bold text-amber-600">' . ($summary['average_position'] > 0 ? number_format($summary['average_position'], 1) : 'N/A') . '</div>
                <p class="text-xs text-gray-500 mt-2">Menor es mejor</p>
            </div>

            <!-- Visibilidad -->
            <div class="glass-morphism rounded-xl p-6 hover:shadow-xl transition-all duration-300 animate-in" style="animation-delay: 0.3s">
                <div class="flex items-center justify-center w-14 h-14 bg-blue-100 rounded-lg mb-4">
                    <i class="fas fa-eye text-blue-600 text-2xl"></i>
                </div>
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Score de Visibilidad</div>
                <div class="text-4xl font-bold text-blue-600">' . $summary['visibility_score'] . '%</div>
                <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" style="width: ' . $summary['visibility_score'] . '%"></div>
                </div>
            </div>
        </div>

        <!-- Tendencias -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-pie text-primary-600 mr-3"></i>
                Distribución de Tendencias
            </h2>
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <canvas id="trendsChart" height="250"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <i class="fas fa-arrow-up text-4xl text-green-600 mb-3"></i>
                        <h4 class="text-3xl font-bold text-green-600 mb-1">' . $trends['improved'] . '</h4>
                        <small class="text-gray-600 font-medium">Mejoradas</small>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-xl">
                        <i class="fas fa-arrow-down text-4xl text-red-600 mb-3"></i>
                        <h4 class="text-3xl font-bold text-red-600 mb-1">' . $trends['declined'] . '</h4>
                        <small class="text-gray-600 font-medium">Empeoradas</small>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <i class="fas fa-star text-4xl text-blue-600 mb-3"></i>
                        <h4 class="text-3xl font-bold text-blue-600 mb-1">' . $trends['new'] . '</h4>
                        <small class="text-gray-600 font-medium">Nuevas</small>
                    </div>
                    <div class="text-center p-4 bg-gray-50 rounded-xl">
                        <i class="fas fa-minus text-4xl text-gray-600 mb-3"></i>
                        <h4 class="text-3xl font-bold text-gray-600 mb-1">' . $trends['stable'] . '</h4>
                        <small class="text-gray-600 font-medium">Estables</small>
                    </div>
                </div>
            </div>
        </div>';
    }

    private function generateChartsSection($chartData, $monthlyData)
    {
        $hasHistoricalData = $chartData['hasHistoricalData'] ?? false;
        $chartTitle = $hasHistoricalData ? 
            'Evolución de Posiciones (Top Keywords)' : 
            'Posiciones Actuales (Top Keywords)';
        $chartSubtitle = $hasHistoricalData ? 
            'Muestra las 10 keywords con mejor rendimiento. Posiciones más bajas son mejores.' :
            'Posiciones actuales de las mejores keywords. Se mostrará la evolución cuando haya datos históricos.';

        return '
        <!-- Evolución de Posiciones -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-chart-line text-green-600 mr-3"></i>
                ' . $chartTitle . '
            </h2>
            <div class="chart-wrapper">
                <canvas id="positionsChart"></canvas>
            </div>
            <p class="text-sm text-gray-500 mt-4 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                ' . $chartSubtitle . '
            </p>
            ' . (!$hasHistoricalData ? '
            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                    <p class="text-blue-800 text-sm">
                        <strong>💡 Consejo:</strong> Para ver la evolución temporal, necesitas tener datos de meses anteriores. 
                        Los próximos informes mostrarán la progresión de tus keywords.
                    </p>
                </div>
            </div>' : '') . '
        </div>

        <!-- Distribución y Top Positions -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            <div class="glass-morphism rounded-2xl shadow-xl p-8 animate-in">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-layer-group text-amber-600 mr-3"></i>
                    Distribución de Posiciones
                </h2>
                <div class="chart-wrapper" style="height: 300px;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
            <div class="glass-morphism rounded-2xl shadow-xl p-8 animate-in">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-trophy text-amber-600 mr-3"></i>
                    Posiciones Top 1, Top 3, Top 10
                </h2>
                <div class="chart-wrapper" style="height: 300px;">
                    <canvas id="topPositionsChart"></canvas>
                </div>
            </div>
        </div>';
    }

    private function generateKeywordsTableSection($keywords)
    {
        $html = '
        <!-- Tabla de Keywords -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-table text-blue-600 mr-3"></i>
                Análisis Detallado de Keywords
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-key mr-1"></i>Keyword
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-map-marker-alt mr-1"></i>Posición Actual
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-trophy mr-1"></i>Mejor Posición
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-chart-line mr-1"></i>Tendencia
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-users mr-1"></i>Competencia
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-history mr-1"></i>Historial
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">';

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
                ($currentPos == 1 ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">#' . $currentPos . '</span>' :
                ($currentPos <= 3 ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">#' . $currentPos . '</span>' :
                ($currentPos <= 10 ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">#' . $currentPos . '</span>' :
                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">#' . $currentPos . '</span>'))) :
                '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">N/A</span>';

            // Tendencia
            $trendIcons = [
                'improved' => '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-green-50 text-green-700"><i class="fas fa-arrow-up mr-1"></i> Mejorada</span>',
                'declined' => '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-red-50 text-red-700"><i class="fas fa-arrow-down mr-1"></i> Empeorada</span>',
                'stable' => '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-700"><i class="fas fa-minus mr-1"></i> Estable</span>',
                'new' => '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700"><i class="fas fa-star mr-1"></i> Nueva</span>',
                'not_found' => '<span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-50 text-gray-600"><i class="fas fa-search-minus mr-1"></i> No encontrada</span>',
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
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">' . htmlspecialchars($kw['keyword']) . '</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">' . $positionBadge . '</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">' . ($bestPos !== 'N/A' ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">#' . $bestPos . '</span>' : '<span class="text-gray-400">N/A</span>') . '</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">' . $trendBadge . '</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center">
                                            <span class="w-3 h-3 rounded-full mr-2" style="background: ' . $compColor . ';"></span>
                                            <span class="text-sm text-gray-900">' . $compLevel . '</span>
                                        </div>
                                        <div class="text-xs text-gray-500">' . number_format($competition) . ' resultados</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-500">' . count($kw['positions']) . ' registros</span>
                                    </td>
                                </tr>';
        }

        $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>';

        return $html;
    }

    private function generateCompetitionSection($competition)
    {
        return '
        <!-- Análisis de Competencia -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-users-cog text-red-600 mr-3"></i>
                Análisis de Competencia
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-green-50 rounded-xl">
                    <h3 class="text-4xl font-bold text-green-600 mb-2">' . $competition['low'] . '</h3>
                    <p class="font-semibold text-gray-900 mb-1">Competencia Baja</p>
                    <small class="text-gray-600">< 10K resultados</small>
                </div>
                <div class="text-center p-6 bg-amber-50 rounded-xl">
                    <h3 class="text-4xl font-bold text-amber-600 mb-2">' . $competition['medium'] . '</h3>
                    <p class="font-semibold text-gray-900 mb-1">Competencia Media</p>
                    <small class="text-gray-600">10K - 100K</small>
                </div>
                <div class="text-center p-6 bg-orange-50 rounded-xl">
                    <h3 class="text-4xl font-bold text-orange-600 mb-2">' . $competition['high'] . '</h3>
                    <p class="font-semibold text-gray-900 mb-1">Competencia Alta</p>
                    <small class="text-gray-600">100K - 1M</small>
                </div>
                <div class="text-center p-6 bg-red-50 rounded-xl">
                    <h3 class="text-4xl font-bold text-red-700 mb-2">' . $competition['very_high'] . '</h3>
                    <p class="font-semibold text-gray-900 mb-1">Competencia Muy Alta</p>
                    <small class="text-gray-600">> 1M resultados</small>
                </div>
            </div>
        </div>';
    }

    private function generatePeopleAlsoAskSection($peopleAlsoAsk)
    {
        $html = '
        <!-- Preguntas Frecuentes -->
        <div class="glass-morphism rounded-2xl shadow-xl p-8 mb-8 animate-in">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-question-circle text-blue-600 mr-3"></i>
                Preguntas Frecuentes (People Also Ask)
            </h2>
            <div class="grid md:grid-cols-2 gap-4">';

        foreach (array_slice($peopleAlsoAsk, 0, 12) as $index => $paa) {
            $html .= '
                <div class="bg-white border-l-4 border-blue-500 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mr-3">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-sm font-medium">
                                ' . ($index + 1) . '
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 mb-1">' . htmlspecialchars($paa['question']) . '</p>
                            <p class="text-sm text-gray-500 flex items-center">
                                <i class="fas fa-search mr-1"></i>
                                ' . (isset($paa['total_results']) ? number_format($paa['total_results']) : '0') . ' resultados
                            </p>
                        </div>
                    </div>
                </div>';
        }

        $html .= '
            </div>
        </div>';

        return $html;
    }

    private function generateFooterSection($autoseo)
    {
        return '
        <!-- Botón Imprimir -->
        <div class="no-print text-center mb-8">
            <button onclick="window.print()" class="inline-flex items-center px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200">
                <i class="fas fa-print mr-2"></i>
                Imprimir / Guardar PDF
            </button>
        </div>';
    }

    private function prepareChartData($keywords, $monthlyData)
    {
        // Preparar datos para gráficos de Chart.js
        $top10Keywords = array_slice(array_filter($keywords, function($kw) {
            return $kw['current_position'] !== null && $kw['current_position'] <= 20;
        }), 0, 10);

        // Si no hay keywords con posición, usar las mejores disponibles
        if (empty($top10Keywords)) {
            $top10Keywords = array_slice(array_filter($keywords, function($kw) {
                return $kw['current_position'] !== null;
            }), 0, 10);
        }

        // Si aún no hay datos, usar todas las keywords disponibles
        if (empty($top10Keywords)) {
            $top10Keywords = array_slice($keywords, 0, 10);
        }

        // Extraer fechas y posiciones
        $dates = [];
        $datasets = [];
        $colors = [
            '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16',
            '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
            '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef'
        ];

        // Obtener todas las fechas únicas de los datos históricos
        if (!empty($monthlyData)) {
            foreach ($monthlyData as $month => $data) {
                $dates[] = date('M Y', strtotime($data['date']));
            }
        } else {
            // Si no hay datos históricos, crear una fecha actual
            $dates[] = date('M Y');
        }

        // Crear datasets para cada keyword
        $colorIndex = 0;
        foreach ($top10Keywords as $kw) {
            $keywordData = [];
            
            if (!empty($monthlyData)) {
                // Si hay datos históricos, buscar posición de esta keyword en cada mes
                foreach (array_keys($monthlyData) as $month) {
                    $position = null;
                    foreach ($kw['positions'] as $pos) {
                        if (date('Y-m', strtotime($pos['date'])) == $month) {
                            $position = $pos['position'];
                            break;
                        }
                    }
                    $keywordData[] = $position;
                }
            } else {
                // Si no hay datos históricos, usar solo la posición actual
                $keywordData[] = $kw['current_position'];
            }

            $datasets[] = [
                'label' => $kw['keyword'],
                'data' => $keywordData,
                'borderColor' => $colors[$colorIndex % count($colors)],
                'backgroundColor' => $colors[$colorIndex % count($colors)] . '33',
                'tension' => 0.4,
                'fill' => false,
                'pointRadius' => 6,
                'pointHoverRadius' => 8,
                'pointBackgroundColor' => $colors[$colorIndex % count($colors)],
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 2,
            ];
            $colorIndex++;
        }

        return [
            'dates' => $dates,
            'datasets' => $datasets,
            'keywords' => $top10Keywords,
            'hasHistoricalData' => !empty($monthlyData)
        ];
    }

    private function generateChartScripts($chartData, $trends = [], $summary = [])
    {
        $datesJson = json_encode($chartData['dates']);
        $datasetsJson = json_encode($chartData['datasets']);
        $hasHistoricalData = $chartData['hasHistoricalData'] ?? false;

        return "
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando gráficos...');
            console.log('Datos históricos disponibles:', " . ($hasHistoricalData ? 'true' : 'false') . ");
            
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
                                padding: 15,
                                font: { size: 12 }
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
                            min: 1,
                            max: " . ($hasHistoricalData ? '100' : '50') . ",
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
                                text: " . ($hasHistoricalData ? "'Período'" : "'Estado Actual'") . ",
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

