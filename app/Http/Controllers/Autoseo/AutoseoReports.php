<?php

namespace App\Http\Controllers\Autoseo;

use App\Http\Controllers\Controller;
use App\Models\Autoseo\Autoseo;
use App\Models\Autoseo\AutoseoReportsModel; // 👈 Importar el modelo correcto
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AutoseoReports extends Controller
{
    public function show() {
        return view('autoseo.reports');
    }

    public function login(Request $request) {
        $pin = $request->input('pin');

        $cliente = Autoseo::where('pin', $pin)->first();
        if ($cliente) {
            $reports = AutoseoReportsModel::where('autoseo_id', $cliente->id)
            ->get(['id', 'path', 'created_at', 'autoseo_id']);

            return response()->json([
            'success' => true,
            'reports' => $reports->map(function ($report) {
                return [
                'id' => $report->id,
                'autoseo_id' => $report->autoseo_id,
                'created_at' => $report->created_at,
                ];
            }),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }
    }

    public function upload(Request $request) {
        $id = $request->input('id');
        if (!$id) {
            $id = $request->id;
        }
        $file = $request->file('file');

        $cliente = Autoseo::where('id', $id)->first();
        if ($cliente) {
            if ($file) {
                $path = $file->store('autoseo_reports', 'public');
                AutoseoReportsModel::create([
                    'autoseo_id' => $cliente->id,
                    'path' => $path
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Reporte almacenado correctamente',
                    'path' => $path
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se ha proporcionado un archivo'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Cliente no encontrado'
            ]);
        }
    }

    public function showReport($userid, $id, Request $request) {
        $report = AutoseoReportsModel::where('id', $id)->where('autoseo_id', $userid)->first();
        if ($report) {
            $filePath = storage_path('app/public/' . $report->path);
            if (file_exists($filePath)) {
                return response()->file($filePath);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo no encontrado'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Reporte no encontrado'
            ]);
        }
    }

    /**
     * Genera un informe SEO basado únicamente en JSONs históricos
     * Reemplaza el sistema anterior que usaba SerpAPI
     */
    public function generateJsonOnlyReport(Request $request) {
        // Aumentar tiempo de ejecución a 5 minutos
        set_time_limit(300);
        
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json([
                'success' => false,
                'message' => 'ID del cliente es requerido'
            ], 400);
        }

        try {
            Log::info("🔍 Generando informe SEO basado en JSONs históricos para cliente ID: {$id}");

            $autoseo = Autoseo::find($id);
            if (!$autoseo) {
                return response()->json([
                    'success' => false,
                    'message' => "Cliente Autoseo con ID {$id} no encontrado."
                ], 404);
            }

            Log::info("📊 Cliente: {$autoseo->client_name} ({$autoseo->url})");

            // Descargar datos históricos reales
            $historicalData = $this->downloadRealHistoricalData($id);
            
            if (empty($historicalData)) {
                Log::info("ℹ️ No hay datos históricos previos, se generará informe solo con datos actuales.");
            }

            Log::info("✅ Datos históricos obtenidos: " . count($historicalData) . " períodos (máximo 12)");

            // Analizar datos históricos
            Log::info("🔍 Analizando datos históricos...");
            $analysis = $this->analyzeHistoricalData($historicalData);
            Log::info("✅ Análisis completado");

            // Generar informe HTML
            Log::info("📝 Generando informe HTML...");
            $html = $this->generateReportHtml($autoseo, $analysis);

            // Guardar archivo
            $filename = "informe_seo_json_only_{$id}_" . date('Y-m-d') . ".html";
            $path = "autoseo_reports/{$filename}";
            Storage::disk('public')->put($path, $html);

            // Guardar en base de datos
            $report = AutoseoReportsModel::create([
                'autoseo_id' => $autoseo->id,
                'path' => $path
            ]);

            Log::info("✅ Informe generado exitosamente!");
            Log::info("📁 Archivo: storage/app/public/{$path}");
            Log::info("🌐 URL: " . url(Storage::disk('public')->url($path)));

            return response()->json([
                'success' => true,
                'message' => 'Informe generado exitosamente',
                'data' => [
                    'report_id' => $report->id,
                    'filename' => $filename,
                    'path' => $path,
                    'url' => url(Storage::disk('public')->url($path)),
                    'summary' => $analysis['summary'],
                    'trends' => $analysis['trends']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Error generando informe: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generando informe: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descarga datos históricos reales desde el servidor
     */
    private function downloadRealHistoricalData($id)
    {
        try {
            Log::info("   Descargando desde: https://crm.hawkins.es/api/autoseo/json/storage?id={$id}");

            // Aumentar timeout a 5 minutos para la descarga del ZIP
            $response = Http::timeout(300)
                ->withoutVerifying()
                ->get("https://crm.hawkins.es/api/autoseo/json/storage", ['id' => $id]);

            if (!$response->successful()) {
                Log::warning("   Error descargando datos históricos: " . $response->status());
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
                    Log::info("   📄 Archivo: " . basename($file));
                    Log::info("   📊 Estructura: " . json_encode(array_keys($data)));

                    // Normalizar estructura de datos
                    $normalizedData = $this->normalizeHistoricalData($data, $file);
                    if ($normalizedData) {
                        $historicalData[] = $normalizedData;
                    }
                } else {
                    Log::warning("   ⚠️ Error decodificando JSON: " . basename($file));
                }
            }

            // Ordenar por fecha y limitar a 12 períodos
            usort($historicalData, function($a, $b) {
                $dateA = $a['uploaded_at'] ?? '1970-01-01';
                $dateB = $b['uploaded_at'] ?? '1970-01-01';
                return strtotime($dateA) - strtotime($dateB);
            });

            $historicalData = array_slice($historicalData, -12); // Últimos 12 meses

            File::deleteDirectory($tempDir);

            return $historicalData;

        } catch (\Exception $e) {
            Log::warning("   ⚠️ Error procesando datos históricos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Normaliza la estructura de datos históricos
     */
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
            Log::warning("   ⚠️ No se encontraron keywords en: " . basename($filename));
            return null;
        }

        return [
            'uploaded_at' => $uploadedAt,
            'detalles_keywords' => $keywords,
            'source_file' => basename($filename),
            'total_keywords' => count($keywords)
        ];
    }

    /**
     * Analiza los datos históricos y calcula métricas
     */
    private function analyzeHistoricalData($historicalData)
    {
        $periodCount = count($historicalData);
        Log::info("   🔍 Analizando " . $periodCount . " períodos históricos...");
        
        $allKeywords = [];
        $keywordHistory = [];
        $historicalDates = [];

        // Si no hay datos históricos, retornar estructura vacía pero válida
        if ($periodCount === 0) {
            Log::info("   ℹ️ No hay períodos para analizar, retornando estructura vacía");
            return [
                'summary' => [
                    'total_keywords' => 0,
                    'keywords_in_top3' => 0,
                    'keywords_in_top10' => 0,
                    'average_position' => 0,
                    'visibility_score' => 0,
                    'periods_analyzed' => 0
                ],
                'keyword_history' => [],
                'historical_dates' => [],
                'trends' => [
                    'improved' => 0,
                    'declined' => 0,
                    'stable' => 0,
                    'new' => 0,
                    'not_found' => 0
                ]
            ];
        }

        foreach ($historicalData as $periodIndex => $data) {
            $date = date('Y-m-d', strtotime($data['uploaded_at']));
            $historicalDates[] = $date;

            if (isset($data['detalles_keywords']) && is_array($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $keywordInfo) {
                    $keyword = $keywordInfo['keyword'] ?? 'N/A';
                    $position = $keywordInfo['position'] ?? null;

                    if (!isset($keywordHistory[$keyword])) {
                        $keywordHistory[$keyword] = [
                            'keyword' => $keyword,
                            'positions' => array_fill(0, count($historicalData), null),
                            'volumes' => array_fill(0, count($historicalData), null),
                            'current_position' => null,
                            'last_month_position' => null,
                            'trend' => 'new'
                        ];
                    }
                    $keywordHistory[$keyword]['positions'][$periodIndex] = $position;
                    $keywordHistory[$keyword]['volumes'][$periodIndex] = $keywordInfo['volume'] ?? null;
                }
            }
        }

        // Calcular posición actual, posición del mes pasado y tendencia
        $lastPeriodIndex = count($historicalData) - 1;
        $secondLastPeriodIndex = count($historicalData) - 2;

        foreach ($keywordHistory as $keyword => &$info) {
            $info['current_position'] = $info['positions'][$lastPeriodIndex];
            $info['last_month_position'] = $info['positions'][$secondLastPeriodIndex] ?? null;
            $info['trend'] = $this->calculateKeywordTrend($info['current_position'], $info['last_month_position']);
            $allKeywords[] = $keyword;
        }
        unset($info); // Romper la referencia

        $uniqueKeywords = array_unique($allKeywords);
        Log::info("   🔑 Keywords únicas encontradas: " . count($uniqueKeywords));

        // Calcular métricas de resumen
        $totalKeywords = count($uniqueKeywords);
        $keywordsInTop3 = 0;
        $keywordsInTop10 = 0;
        $totalPositions = 0;
        $validPositionsCount = 0;

        foreach ($keywordHistory as $keyword => $info) {
            if ($info['current_position'] !== null) {
                if ($info['current_position'] <= 3) {
                    $keywordsInTop3++;
                }
                if ($info['current_position'] <= 10) {
                    $keywordsInTop10++;
                }
                $totalPositions += $info['current_position'];
                $validPositionsCount++;
            }
        }

        $averagePosition = $validPositionsCount > 0 ? $totalPositions / $validPositionsCount : 0;
        $visibilityScore = $totalKeywords > 0 ? ($keywordsInTop10 / $totalKeywords) * 100 : 0;

        // Calcular tendencias generales
        $trends = [
            'improved' => 0,
            'declined' => 0,
            'stable' => 0,
            'new' => 0,
            'not_found' => 0
        ];
        foreach ($keywordHistory as $keyword => $info) {
            $trend = $info['trend'];
            if (isset($trends[$trend])) {
                $trends[$trend]++;
            } else {
                $trends['stable']++; // Fallback para tendencias no reconocidas
            }
        }

        return [
            'summary' => [
                'total_keywords' => $totalKeywords,
                'keywords_in_top3' => $keywordsInTop3,
                'keywords_in_top10' => $keywordsInTop10,
                'average_position' => $averagePosition,
                'visibility_score' => $visibilityScore,
                'periods_analyzed' => count($historicalData)
            ],
            'keyword_history' => $keywordHistory,
            'historical_dates' => $historicalDates,
            'trends' => $trends
        ];
    }

    /**
     * Calcula la tendencia de una keyword
     */
    private function calculateKeywordTrend($currentPosition, $lastMonthPosition)
    {
        if ($currentPosition === null) {
            return 'not_found';
        }
        if ($lastMonthPosition === null) {
            return 'new';
        }
        if ($currentPosition < $lastMonthPosition) {
            return 'improved';
        }
        if ($currentPosition > $lastMonthPosition) {
            return 'declined';
        }
        return 'stable';
    }

    /**
     * Genera el HTML del informe
     */
    private function generateReportHtml($autoseo, $analysis)
    {
        $domain = parse_url($autoseo->url, PHP_URL_HOST) ?: $autoseo->url;
        $reportDate = date('d/m/Y H:i', strtotime('+2 hours'));

        $summary = $analysis['summary'];
        $keywordHistory = $analysis['keyword_history'];
        $historicalDates = $analysis['historical_dates'];
        $trends = $analysis['trends'];

        // Preparar datos para Chart.js
        $chartLabels = empty($historicalDates) ? ['Sin datos'] : $historicalDates;
        $chartDatasets = [];
        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#4BC0C0', '#FF6384', '#36A2EB', '#FFCE56', '#9966FF'];

        $datasetIndex = 0;
        foreach ($keywordHistory as $keyword => $info) {
            $chartDatasets[] = [
                'label' => $keyword,
                'data' => $info['positions'],
                'borderColor' => $colors[$datasetIndex % count($colors)],
                'backgroundColor' => $colors[$datasetIndex % count($colors)] . '20',
                'fill' => false,
                'tension' => 0.1,
                'pointRadius' => 4,
                'pointHoverRadius' => 6,
                'pointBackgroundColor' => $colors[$datasetIndex % count($colors)],
                'pointBorderColor' => '#fff',
                'pointBorderWidth' => 2
            ];
            $datasetIndex++;
        }

        $html = '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Informe SEO Histórico - ' . $domain . '</title>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1a202c; }
                .card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); padding: 1.5rem; margin-bottom: 1.5rem; }
                .chart-container { height: 400px; margin: 1rem 0; }
                .metric { font-size: 1.125rem; font-weight: 600; }
                .trend-up { color: #059669; }
                .trend-down { color: #dc2626; }
                .trend-neutral { color: #6b7280; }
                .badge { padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; }
                .badge-green { background-color: #d1fae5; color: #065f46; }
                .badge-red { background-color: #fee2e2; color: #991b1b; }
                .badge-blue { background-color: #dbeafe; color: #1e40af; }
                .badge-gray { background-color: #e5e7eb; color: #374151; }
                .trend-icon-improved { color: #059669; }
                .trend-icon-declined { color: #dc2626; }
                .trend-icon-stable { color: #6b7280; }
                .trend-icon-new { color: #1e40af; }
            </style>
        </head>
        <body class="p-6">
            <div class="max-w-7xl mx-auto">
                <div class="card">
                    <h1 class="text-2xl font-bold mb-2">Informe SEO Histórico</h1>
                    <p class="text-gray-600">Dominio: ' . $domain . '</p>
                    <p class="text-sm text-gray-500">Fecha del informe: ' . $reportDate . '</p>
                </div>

                <div class="card">
                    <h2 class="text-xl font-semibold mb-4">Resumen General</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Keywords Totales</p>
                            <p class="metric text-blue-700">' . $summary['total_keywords'] . '</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Keywords en Top 10</p>
                            <p class="metric text-green-700">' . $summary['keywords_in_top10'] . '</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Score de Visibilidad</p>
                            <p class="metric text-yellow-700">' . round($summary['visibility_score'], 1) . '%</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Posición Promedio</p>
                            <p class="metric text-gray-700">' . round($summary['average_position'], 1) . '</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Mejoradas</p>
                            <p class="metric text-green-700">' . $trends['improved'] . '</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Empeoradas</p>
                            <p class="metric text-red-700">' . $trends['declined'] . '</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">Nuevas</p>
                            <p class="metric text-blue-700">' . $trends['new'] . '</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500">No encontradas</p>
                            <p class="metric text-gray-700">' . $trends['not_found'] . '</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h2 class="text-xl font-semibold mb-4">Evolución de Posiciones por Keyword</h2>';
        
        if (empty($keywordHistory)) {
            $html .= '<div class="bg-blue-50 p-6 rounded-lg text-center">
                        <p class="text-blue-700 text-lg font-semibold">ℹ️ Primer Informe</p>
                        <p class="text-blue-600 mt-2">Este es el primer informe mensual, por lo que no hay datos históricos para analizar.</p>
                    </div>';
        } else {
            $html .= '<div class="chart-container">
                        <canvas id="positionEvolutionChart"></canvas>
                    </div>';
        }
        
        $html .= '</div>

                <div class="card">
                    <h2 class="text-xl font-semibold mb-4">Análisis Detallado de Keywords</h2>';
        
        if (empty($keywordHistory)) {
            $html .= '<div class="bg-yellow-50 p-6 rounded-lg text-center">
                        <p class="text-yellow-700 text-lg font-semibold">📊 Sin Datos de Keywords</p>
                        <p class="text-yellow-600 mt-2">Este es el primer informe mensual, por lo que no hay datos históricos para analizar.</p>
                    </div>';
        } else {
            $html .= '<div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Keyword</th>
                                    <th class="py-2 px-4 border-b text-left">Posición Actual</th>
                                    <th class="py-2 px-4 border-b text-left">Posición Mes Anterior</th>
                                    <th class="py-2 px-4 border-b text-left">Tendencia</th>
                                    <th class="py-2 px-4 border-b text-left">Historial de Posiciones</th>
                                </tr>
                            </thead>
                            <tbody id="keywordTableBody">';
        }
        
        foreach ($keywordHistory as $keyword => $info) {
            $currentPositionDisplay = $info['current_position'] ?? 'N/A';
            $lastMonthPositionDisplay = $info['last_month_position'] ?? 'N/A';
            $trendClass = '';
            $trendIcon = '';
            switch ($info['trend']) {
                case 'improved':
                    $trendClass = 'trend-icon-improved';
                    $trendIcon = '↗';
                    break;
                case 'declined':
                    $trendClass = 'trend-icon-declined';
                    $trendIcon = '↘';
                    break;
                case 'stable':
                    $trendClass = 'trend-icon-stable';
                    $trendIcon = '→';
                    break;
                case 'new':
                    $trendClass = 'trend-icon-new';
                    $trendIcon = '★';
                    break;
                case 'not_found':
                    $trendClass = 'trend-icon-stable';
                    $trendIcon = '—';
                    break;
            }

            $positionHistoryHtml = '';
            foreach ($info['positions'] as $pos) {
                $positionHistoryHtml .= '<span class="inline-block w-6 text-center text-xs ' . ($pos === null ? 'text-gray-400' : 'text-gray-700') . '">' . ($pos ?? '-') . '</span>';
            }

            $html .= '<tr>
                        <td class="py-2 px-4 border-b">' . $keyword . '</td>
                        <td class="py-2 px-4 border-b">' . $currentPositionDisplay . '</td>
                        <td class="py-2 px-4 border-b">' . $lastMonthPositionDisplay . '</td>
                        <td class="py-2 px-4 border-b ' . $trendClass . '">' . $trendIcon . ' ' . $this->getTrendTextInSpanish($info['trend']) . '</td>
                        <td class="py-2 px-4 border-b flex items-center gap-1">' . $positionHistoryHtml . '</td>
                    </tr>';
        }
        
        if (!empty($keywordHistory)) {
            $html .= '</tbody>
                        </table>
                    </div>';
        }
        
        $html .= '</div>
            </div>

            <script>
                const chartLabels = ' . json_encode($chartLabels) . ';
                const chartDatasets = ' . json_encode($chartDatasets) . ';

                const chartElement = document.getElementById("positionEvolutionChart");
                if (chartElement && chartDatasets.length > 0) {
                    const ctx = chartElement.getContext("2d");
                    new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: chartLabels,
                        datasets: chartDatasets,
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: "bottom",
                            },
                            tooltip: {
                                mode: "index",
                                intersect: false,
                                callbacks: {
                                    label: function (context) {
                                        const value = context.parsed.y;
                                        return context.dataset.label + ": " + (value !== null ? "Posición " + value : "No encontrado");
                                    },
                                },
                            },
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: "Fecha",
                                },
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: "Posición",
                                },
                                reverse: true, // Posición 1 en la parte superior
                                min: 1,
                                max: 100, // Ajustar según la necesidad
                            },
                        },
                    },
                });
                }
            </script>
        </body>
        </html>';

        return $html;
    }

    /**
     * Convierte las tendencias a texto en español
     */
    private function getTrendTextInSpanish($trend)
    {
        $trends = [
            'improved' => 'Mejorada',
            'declined' => 'Empeorada',
            'stable' => 'Estable',
            'new' => 'Nueva',
            'not_found' => 'No encontrada'
        ];
        
        return $trends[$trend] ?? 'Desconocida';
    }
}
