<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealisticSeoAnalysisService;
use App\Models\Autoseo\Autoseo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class GenerateInteractiveSeoReport extends Command
{
    protected $signature = 'seo:generate-interactive {id : ID del cliente Autoseo}';
    protected $description = 'Genera informe SEO interactivo con gráficas dinámicas y filtros';

    public function handle()
    {
        $id = $this->argument('id');

        $this->info("🔍 Generando informe SEO interactivo para cliente ID: {$id}");

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

            // Preparar datos para gráficas interactivas
            $this->info("📊 Preparando datos para gráficas interactivas...");
            $chartData = $this->prepareChartData($historicalData, $currentData);

            // Generar informe HTML interactivo
            $this->info("📝 Generando informe HTML interactivo...");
            $html = $this->generateInteractiveReportHtml($currentData, $autoseo, $historicalData, $chartData);

            // Guardar informe
            $filename = "informe_seo_interactivo_{$id}_" . date('Y-m-d') . ".html";
            Storage::disk('public')->put("reports/{$filename}", $html);

            $this->info("✅ Informe interactivo generado exitosamente!");
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

    private function prepareChartData($historicalData, $currentData)
    {
        $chartData = [
            'labels' => [],
            'datasets' => [],
            'keywords' => [],
            'months' => []
        ];

        // Preparar labels de meses
        $months = [];
        foreach ($historicalData as $data) {
            $date = $data['uploaded_at'] ?? 'unknown';
            $month = date('M Y', strtotime($date));
            $months[] = $month;
        }
        $months[] = 'Actual (' . date('M Y') . ')';
        $chartData['labels'] = $months;
        $chartData['months'] = $months;

        // Recopilar todas las keywords únicas
        $allKeywords = [];
        foreach ($historicalData as $data) {
            if (isset($data['detalles_keywords'])) {
                foreach ($data['detalles_keywords'] as $keyword) {
                    $allKeywords[] = $keyword['keyword'];
                }
            }
        }
        
        // Agregar keywords actuales
        if (isset($currentData['detalles_keywords'])) {
            foreach ($currentData['detalles_keywords'] as $keyword) {
                $allKeywords[] = $keyword['keyword'];
            }
        }

        $uniqueKeywords = array_unique($allKeywords);
        $chartData['keywords'] = array_values($uniqueKeywords);

        // Crear datasets para cada keyword
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
            '#36A2EB', '#FFCE56', '#9966FF', '#FF9F40', '#C9CBCF'
        ];

        foreach ($uniqueKeywords as $index => $keyword) {
            $positions = [];
            
            // Obtener posiciones históricas
            foreach ($historicalData as $data) {
                $position = null;
                if (isset($data['detalles_keywords'])) {
                    foreach ($data['detalles_keywords'] as $kw) {
                        if ($kw['keyword'] === $keyword) {
                            $position = $kw['position'];
                            break;
                        }
                    }
                }
                $positions[] = $position;
            }
            
            // Obtener posición actual
            $currentPosition = null;
            if (isset($currentData['detalles_keywords'])) {
                foreach ($currentData['detalles_keywords'] as $kw) {
                    if ($kw['keyword'] === $keyword) {
                        $currentPosition = $kw['position'];
                        break;
                    }
                }
            }
            $positions[] = $currentPosition;

            $chartData['datasets'][] = [
                'label' => $keyword,
                'data' => $positions,
                'borderColor' => $colors[$index % count($colors)],
                'backgroundColor' => $colors[$index % count($colors)] . '20',
                'fill' => false,
                'tension' => 0.1,
                'pointRadius' => 6,
                'pointHoverRadius' => 8
            ];
        }

        return $chartData;
    }

    private function generateInteractiveReportHtml($currentData, $autoseo, $historicalData, $chartData)
    {
        $summary = $currentData['summary'] ?? [];
        $insights = $currentData['insights'] ?? [];
        $historicalComparison = $currentData['historical_comparison'] ?? [];

        $html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO Interactivo - ' . htmlspecialchars($autoseo->client_name) . '</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #1a202c; line-height: 1.6; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
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
        
        /* Controles de filtros */
        .filters { background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 0.9rem; font-weight: 600; color: #374151; }
        .filter-group select, .filter-group input { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 0.9rem; }
        .filter-group select:focus, .filter-group input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        
        /* Tabla interactiva */
        .table-container { overflow-x: auto; margin-top: 20px; }
        .interactive-table { width: 100%; border-collapse: collapse; min-width: 800px; }
        .interactive-table th, .interactive-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .interactive-table th { background: #f9fafb; font-weight: 600; color: #374151; cursor: pointer; user-select: none; }
        .interactive-table th:hover { background: #f3f4f6; }
        .interactive-table tr:hover { background: #f9fafb; }
        .interactive-table th.sortable::after { content: " ↕"; opacity: 0.5; }
        .interactive-table th.sort-asc::after { content: " ↑"; opacity: 1; color: #667eea; }
        .interactive-table th.sort-desc::after { content: " ↓"; opacity: 1; color: #667eea; }
        
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
        
        /* Gráficas */
        .chart-container { height: 500px; margin: 20px 0; position: relative; }
        .chart-controls { display: flex; gap: 10px; margin-bottom: 15px; }
        .chart-controls button { padding: 8px 16px; border: 1px solid #d1d5db; background: white; border-radius: 6px; cursor: pointer; font-size: 0.9rem; }
        .chart-controls button:hover { background: #f9fafb; }
        .chart-controls button.active { background: #667eea; color: white; border-color: #667eea; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .filters { flex-direction: column; align-items: stretch; }
            .chart-container { height: 400px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Informe SEO Interactivo</h1>
            <p>' . htmlspecialchars($autoseo->client_name) . ' - ' . htmlspecialchars($autoseo->url) . '</p>
            <p>Análisis interactivo con datos históricos reales • Fecha: ' . date('d/m/Y H:i') . '</p>
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

        // Gráfica interactiva de evolución
        $html .= '<div class="card">
            <h2>📊 Evolución de Posiciones por Keyword</h2>
            <div class="chart-controls">
                <button onclick="toggleKeywordVisibility(\'all\')" class="active" id="btn-all">Mostrar Todas</button>
                <button onclick="toggleKeywordVisibility(\'top10\')" id="btn-top10">Solo Top 10</button>
                <button onclick="toggleKeywordVisibility(\'improved\')" id="btn-improved">Solo Mejoradas</button>
                <button onclick="resetZoom()" id="btn-reset">Reset Zoom</button>
            </div>
            <div class="chart-container">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>';

        // Tabla interactiva de comparación
        $html .= '<div class="card">
            <h2>🎯 Comparación Detallada de Keywords</h2>
            <div class="filters">
                <div class="filter-group">
                    <label for="keywordFilter">Filtrar por Keyword:</label>
                    <input type="text" id="keywordFilter" placeholder="Buscar keyword..." onkeyup="filterTable()">
                </div>
                <div class="filter-group">
                    <label for="positionFilter">Filtrar por Posición:</label>
                    <select id="positionFilter" onchange="filterTable()">
                        <option value="">Todas las posiciones</option>
                        <option value="top3">Top 3</option>
                        <option value="top10">Top 10</option>
                        <option value="top20">Top 20</option>
                        <option value="notfound">No encontradas</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="trendFilter">Filtrar por Tendencia:</label>
                    <select id="trendFilter" onchange="filterTable()">
                        <option value="">Todas las tendencias</option>
                        <option value="improved">Mejoradas</option>
                        <option value="declined">Empeoradas</option>
                        <option value="stable">Estables</option>
                        <option value="new">Nuevas</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="sortBy">Ordenar por:</label>
                    <select id="sortBy" onchange="sortTable()">
                        <option value="keyword">Keyword</option>
                        <option value="position">Posición</option>
                        <option value="trend">Tendencia</option>
                        <option value="total_results">Total Resultados</option>
                    </select>
                </div>
            </div>
            <div class="table-container">
                <table class="interactive-table" id="keywordsTable">
                    <thead>
                        <tr>
                            <th class="sortable" onclick="sortTable(\'keyword\')">Keyword</th>
                            <th class="sortable" onclick="sortTable(\'position\')">Posición Actual</th>
                            <th class="sortable" onclick="sortTable(\'total_results\')">Total Resultados</th>
                            <th class="sortable" onclick="sortTable(\'trend\')">Tendencia</th>
                            <th>Evolución Histórica</th>
                        </tr>
                    </thead>
                    <tbody id="keywordsTableBody">
                    </tbody>
                </table>
            </div>
        </div>';

        // Gráfica de distribución de posiciones
        $html .= '<div class="card">
            <h2>📊 Distribución de Posiciones</h2>
            <div class="chart-container">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>';

        $html .= '<div class="card">
            <p style="text-align: center; color: #6b7280; font-size: 0.9rem;">
                Informe interactivo generado automáticamente por el sistema SEO de Hawkins<br>
                Datos históricos reales • Análisis dinámico • Filtros interactivos
            </p>
        </div>
    </div>

    <script>
        // Datos para las gráficas
        const chartData = ' . json_encode($chartData) . ';
        const historicalComparison = ' . json_encode($historicalComparison) . ';
        const currentData = ' . json_encode($currentData) . ';
        
        let evolutionChart;
        let distributionChart;
        let currentSortColumn = "";
        let currentSortDirection = "asc";
        
        // Inicializar gráficas
        document.addEventListener("DOMContentLoaded", function() {
            initEvolutionChart();
            initDistributionChart();
            populateTable();
        });
        
        function initEvolutionChart() {
            const ctx = document.getElementById("evolutionChart").getContext("2d");
            
            evolutionChart = new Chart(ctx, {
                type: "line",
                data: {
                    labels: chartData.labels,
                    datasets: chartData.datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: "index"
                    },
                    plugins: {
                        zoom: {
                            zoom: {
                                wheel: { enabled: true },
                                pinch: { enabled: true },
                                mode: "xy"
                            },
                            pan: {
                                enabled: true,
                                mode: "xy"
                            }
                        },
                        legend: {
                            display: true,
                            position: "top",
                            labels: {
                                usePointStyle: true,
                                padding: 20
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
                            title: {
                                display: true,
                                text: "Posición"
                            },
                            reverse: true,
                            min: 1,
                            max: 50
                        }
                    }
                },
                plugins: [ChartZoom]
            });
        }
        
        function initDistributionChart() {
            const ctx = document.getElementById("distributionChart").getContext("2d");
            
            // Calcular distribución de posiciones
            const distribution = {
                "Top 3": 0,
                "Top 10": 0,
                "Top 20": 0,
                "Fuera Top 20": 0,
                "No encontradas": 0
            };
            
            currentData.detalles_keywords.forEach(keyword => {
                if (keyword.position === null) {
                    distribution["No encontradas"]++;
                } else if (keyword.position <= 3) {
                    distribution["Top 3"]++;
                } else if (keyword.position <= 10) {
                    distribution["Top 10"]++;
                } else if (keyword.position <= 20) {
                    distribution["Top 20"]++;
                } else {
                    distribution["Fuera Top 20"]++;
                }
            });
            
            distributionChart = new Chart(ctx, {
                type: "doughnut",
                data: {
                    labels: Object.keys(distribution),
                    datasets: [{
                        data: Object.values(distribution),
                        backgroundColor: [
                            "#10b981",
                            "#3b82f6", 
                            "#f59e0b",
                            "#ef4444",
                            "#6b7280"
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ": " + context.parsed + " (" + percentage + "%)";
                                }
                            }
                        }
                    }
                }
            });
        }
        
        function populateTable() {
            const tbody = document.getElementById("keywordsTableBody");
            tbody.innerHTML = "";
            
            currentData.detalles_keywords.forEach((keyword, index) => {
                const trend = historicalComparison[index] ? historicalComparison[index].trend : "new";
                const row = createTableRow(keyword, trend, index);
                tbody.appendChild(row);
            });
        }
        
        function createTableRow(keyword, trend, index) {
            const row = document.createElement("tr");
            row.setAttribute("data-keyword", keyword.keyword.toLowerCase());
            row.setAttribute("data-position", keyword.position || 999);
            row.setAttribute("data-trend", trend);
            row.setAttribute("data-total-results", keyword.total_results || 0);
            
            const positionText = keyword.position ? "Posición " + keyword.position : "No encontrado";
            const positionClass = getPositionClass(keyword.position);
            const trendClass = "trend-" + trend.replace("_", "-");
            const trendText = getTrendText(trend);
            
            // Crear evolución histórica
            const evolutionHtml = createEvolutionHtml(keyword.keyword, index);
            
            row.innerHTML = `
                <td><strong>${keyword.keyword}</strong></td>
                <td><span class="position-badge ${positionClass}">${positionText}</span></td>
                <td>${keyword.total_results ? keyword.total_results.toLocaleString() : "N/A"}</td>
                <td><span class="trend-badge ${trendClass}">${trendText}</span></td>
                <td>${evolutionHtml}</td>
            `;
            
            return row;
        }
        
        function createEvolutionHtml(keyword, index) {
            const historicalPositions = historicalComparison[index] ? historicalComparison[index].historical_positions : [];
            let html = "<div style=\"display: flex; gap: 5px; flex-wrap: wrap;\">";
            
            // Mostrar posiciones históricas
            chartData.labels.slice(0, -1).forEach((month, monthIndex) => {
                const position = historicalPositions[monthIndex] ? historicalPositions[monthIndex].position : null;
                const positionText = position ? position : "N/A";
                const positionClass = getPositionClass(position);
                html += `<span class="position-badge ${positionClass}" style="font-size: 0.7rem;">${month}: ${positionText}</span>`;
            });
            
            html += "</div>";
            return html;
        }
        
        function getPositionClass(position) {
            if (position === null) return "position-not-found";
            if (position <= 3) return "position-excellent";
            if (position <= 10) return "position-good";
            if (position <= 20) return "position-fair";
            return "position-poor";
        }
        
        function getTrendText(trend) {
            const trends = {
                "improved": "📈 Mejoró",
                "declined": "📉 Empeoró", 
                "stable": "➡️ Estable",
                "new": "🆕 Nueva",
                "stable_not_found": "❌ No encontrada"
            };
            return trends[trend] || "❓ Desconocido";
        }
        
        // Funciones de filtrado
        function filterTable() {
            const keywordFilter = document.getElementById("keywordFilter").value.toLowerCase();
            const positionFilter = document.getElementById("positionFilter").value;
            const trendFilter = document.getElementById("trendFilter").value;
            
            const rows = document.querySelectorAll("#keywordsTableBody tr");
            
            rows.forEach(row => {
                const keyword = row.getAttribute("data-keyword");
                const position = parseInt(row.getAttribute("data-position"));
                const trend = row.getAttribute("data-trend");
                
                let show = true;
                
                // Filtro por keyword
                if (keywordFilter && !keyword.includes(keywordFilter)) {
                    show = false;
                }
                
                // Filtro por posición
                if (positionFilter) {
                    switch(positionFilter) {
                        case "top3":
                            show = show && position <= 3;
                            break;
                        case "top10":
                            show = show && position <= 10;
                            break;
                        case "top20":
                            show = show && position <= 20;
                            break;
                        case "notfound":
                            show = show && position === 999;
                            break;
                    }
                }
                
                // Filtro por tendencia
                if (trendFilter && trend !== trendFilter) {
                    show = false;
                }
                
                row.style.display = show ? "" : "none";
            });
        }
        
        // Funciones de ordenación
        function sortTable(column) {
            if (currentSortColumn === column) {
                currentSortDirection = currentSortDirection === "asc" ? "desc" : "asc";
            } else {
                currentSortColumn = column;
                currentSortDirection = "asc";
            }
            
            const tbody = document.getElementById("keywordsTableBody");
            const rows = Array.from(tbody.querySelectorAll("tr"));
            
            rows.sort((a, b) => {
                let aVal, bVal;
                
                switch(column) {
                    case "keyword":
                        aVal = a.getAttribute("data-keyword");
                        bVal = b.getAttribute("data-keyword");
                        break;
                    case "position":
                        aVal = parseInt(a.getAttribute("data-position"));
                        bVal = parseInt(b.getAttribute("data-position"));
                        break;
                    case "trend":
                        aVal = a.getAttribute("data-trend");
                        bVal = b.getAttribute("data-trend");
                        break;
                    case "total_results":
                        aVal = parseInt(a.getAttribute("data-total-results"));
                        bVal = parseInt(b.getAttribute("data-total-results"));
                        break;
                }
                
                if (currentSortDirection === "asc") {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });
            
            // Actualizar clases de ordenación
            document.querySelectorAll(".sortable").forEach(th => {
                th.classList.remove("sort-asc", "sort-desc");
            });
            
            const header = document.querySelector(`th[onclick*="${column}"]`);
            if (header) {
                header.classList.add(currentSortDirection === "asc" ? "sort-asc" : "sort-desc");
            }
            
            // Reordenar filas
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // Funciones de gráfica
        function toggleKeywordVisibility(type) {
            const datasets = evolutionChart.data.datasets;
            
            datasets.forEach((dataset, index) => {
                let show = true;
                
                switch(type) {
                    case "top10":
                        const currentPosition = currentData.detalles_keywords[index] ? currentData.detalles_keywords[index].position : null;
                        show = currentPosition && currentPosition <= 10;
                        break;
                    case "improved":
                        const trend = historicalComparison[index] ? historicalComparison[index].trend : "new";
                        show = trend === "improved";
                        break;
                }
                
                dataset.hidden = !show;
            });
            
            evolutionChart.update();
            
            // Actualizar botones
            document.querySelectorAll(".chart-controls button").forEach(btn => {
                btn.classList.remove("active");
            });
            document.getElementById("btn-" + type).classList.add("active");
        }
        
        function resetZoom() {
            evolutionChart.resetZoom();
        }
    </script>
</body>
</html>';

        return $html;
    }

    private function displaySummary($currentData)
    {
        $summary = $currentData['summary'] ?? [];
        $insights = $currentData['insights'] ?? [];

        $this->info("📊 Resumen del análisis interactivo:");
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
                'source' => 'interactive_analysis'
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
