<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Informe SEO - {{ $seo['dominio'] ?? 'No especificado' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #3498db;
            --success: #2ecc71;
            --warning: #f1c40f;
            --danger: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --border: #dde1e3;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background: #f5f6fa;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .card-header {
            border-bottom: 2px solid var(--light);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-title {
            color: var(--primary);
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary);
            font-size: 0.9rem;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin: 2rem 0;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 1rem 0;
        }

        .data-table th {
            background: var(--light);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid var(--border);
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .data-table tr:hover td {
            background: #f8f9fa;
        }

        .trend {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .trend-up {
            color: var(--success);
            background: rgba(46, 204, 113, 0.1);
        }

        .trend-down {
            color: var(--danger);
            background: rgba(231, 76, 60, 0.1);
        }

        .trend-neutral {
            color: var(--warning);
            background: rgba(241, 196, 15, 0.1);
        }

        .metric-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border);
        }

        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .metric-title {
            font-weight: 600;
            color: var(--primary);
        }

        .metric-value {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--accent);
        }

        .keyword-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .keyword-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .keyword-item:last-child {
            border-bottom: none;
        }

        .keyword-name {
            font-weight: 500;
        }

        .keyword-value {
            color: var(--accent);
            font-weight: 600;
        }

        .tab-container {
            margin-bottom: 2rem;
        }

        .tab-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .tab-button {
            padding: 0.75rem 1.5rem;
            border: none;
            background: var(--light);
            color: var(--primary);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            background: var(--primary);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .chart-container {
                height: 300px;
            }

            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Informe SEO Detallado</h1>
            <p>{{ $seo['dominio'] ?? 'No especificado' }} - {{ $seo['uploaded_at'] ?? 'Fecha no especificada' }}</p>
        </div>

        <!-- Resumen General -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📊 Resumen General</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ count($short_tail_labels) }}</div>
                    <div class="stat-label">Keywords Short Tail</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ count($long_tail_labels) }}</div>
                    <div class="stat-label">Keywords Long Tail</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ count($paa_labels) }}</div>
                    <div class="stat-label">Preguntas PAA</div>
                </div>
                @if($sc_has_data)
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($sc_clicks[0] ?? 0) }}</div>
                    <div class="stat-label">Clicks (Último Mes)</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Keywords Short Tail -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🎯 Keywords Short Tail</h2>
            </div>
            <div class="chart-container">
                <canvas id="shortTailChart"></canvas>
            </div>
            <div class="metric-card">
                <div class="keyword-list">
                    @foreach($short_tail_labels as $index => $keyword)
                        <div class="keyword-item">
                            <span class="keyword-name">{{ $keyword }}</span>
                            @php
                                $dataset = collect($short_tail_chartjs_datasets)->firstWhere('label', $keyword);
                                $values = $dataset ? array_filter($dataset['data'], function($v) { return $v !== null; }) : [];
                                $firstVal = count($values) > 0 ? $values[array_key_first($values)] : 0;
                                $lastVal = count($values) > 0 ? end($values) : 0;
                                $change = $lastVal - $firstVal;
                                $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                            @endphp
                            <span class="trend {{ $change > 0 ? 'trend-up' : ($change < 0 ? 'trend-down' : 'trend-neutral') }}">
                                {{ $change > 0 ? '↑' : ($change < 0 ? '↓' : '→') }}
                                {{ number_format(abs($change)) }}
                                ({{ number_format($changePercent, 1) }}%)
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Keywords Long Tail -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📈 Keywords Long Tail</h2>
            </div>
            <div class="chart-container">
                <canvas id="longTailChart"></canvas>
            </div>
            <div class="metric-card">
                <div class="keyword-list">
                    @foreach($long_tail_labels as $index => $keyword)
                        <div class="keyword-item">
                            <span class="keyword-name">{{ $keyword }}</span>
                            @php
                                $dataset = collect($long_tail_chartjs_datasets)->firstWhere('label', $keyword);
                                $values = $dataset ? array_filter($dataset['data'], function($v) { return $v !== null; }) : [];
                                $firstVal = count($values) > 0 ? $values[array_key_first($values)] : 0;
                                $lastVal = count($values) > 0 ? end($values) : 0;
                                $change = $lastVal - $firstVal;
                                $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                            @endphp
                            <span class="trend {{ $change > 0 ? 'trend-up' : ($change < 0 ? 'trend-down' : 'trend-neutral') }}">
                                {{ $change > 0 ? '↑' : ($change < 0 ? '↓' : '→') }}
                                {{ number_format(abs($change)) }}
                                ({{ number_format($changePercent, 1) }}%)
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- People Also Ask -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">❓ People Also Ask</h2>
            </div>
            <div class="chart-container">
                <canvas id="paaChart"></canvas>
            </div>
            <div class="metric-card">
                <div class="keyword-list">
                    @foreach($paa_labels as $index => $question)
                        <div class="keyword-item">
                            <span class="keyword-name">{{ $question }}</span>
                            @php
                                $dataset = collect($paa_chartjs_datasets)->firstWhere('label', $question);
                                $values = $dataset ? array_filter($dataset['data'], function($v) { return $v !== null; }) : [];
                                $firstVal = count($values) > 0 ? $values[array_key_first($values)] : 0;
                                $lastVal = count($values) > 0 ? end($values) : 0;
                                $change = $lastVal - $firstVal;
                                $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                            @endphp
                            <span class="trend {{ $change > 0 ? 'trend-up' : ($change < 0 ? 'trend-down' : 'trend-neutral') }}">
                                {{ $change > 0 ? '↑' : ($change < 0 ? '↓' : '→') }}
                                {{ number_format(abs($change)) }}
                                ({{ number_format($changePercent, 1) }}%)
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Search Console Data -->
        @if($sc_has_data)
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">🔍 Datos de Search Console</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($sc_clicks[0] ?? 0) }}</div>
                    <div class="stat-label">Clicks</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($sc_impressions[0] ?? 0) }}</div>
                    <div class="stat-label">Impresiones</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format(($sc_avg_ctr[0] ?? 0), 2) }}%</div>
                    <div class="stat-label">CTR Promedio</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ number_format(($sc_avg_position[0] ?? 0), 1) }}</div>
                    <div class="stat-label">Posición Promedio</div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="scClicksLine"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="scImpressionsBar"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="scCtrLine"></canvas>
            </div>
            <div class="chart-container">
                <canvas id="scPositionLine"></canvas>
            </div>
        </div>
        @endif
    </div>

    <script>
        // Configuración común para los gráficos
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    padding: 10,
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#2c3e50',
                    bodyColor: '#2c3e50',
                    borderColor: '#dde1e3',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat().format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => new Intl.NumberFormat().format(value)
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        };

        // Función para generar colores
        function generateColors(count) {
            const colors = [
                '#3498db', '#2ecc71', '#e74c3c', '#f1c40f', '#9b59b6',
                '#1abc9c', '#e67e22', '#34495e', '#16a085', '#c0392b'
            ];
            return Array(count).fill().map((_, i) => colors[i % colors.length]);
        }

        // Short Tail Chart
        const shortTailData = @json($short_tail_chartjs_datasets);
        const shortTailColors = generateColors(shortTailData.length);
        shortTailData.forEach((dataset, i) => {
            dataset.borderColor = shortTailColors[i];
            dataset.backgroundColor = shortTailColors[i] + '20';
            dataset.borderWidth = 2;
            dataset.fill = true;
            dataset.tension = 0.4;
        });

        new Chart(document.getElementById('shortTailChart'), {
            type: 'line',
            data: {
                labels: @json($version_dates),
                datasets: shortTailData
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Evolución de Keywords Short Tail',
                        padding: 20
                    }
                }
            }
        });

        // Long Tail Chart
        const longTailData = @json($long_tail_chartjs_datasets);
        const longTailColors = generateColors(longTailData.length);
        longTailData.forEach((dataset, i) => {
            dataset.borderColor = longTailColors[i];
            dataset.backgroundColor = longTailColors[i] + '20';
            dataset.borderWidth = 2;
            dataset.fill = true;
            dataset.tension = 0.4;
        });

        new Chart(document.getElementById('longTailChart'), {
            type: 'line',
            data: {
                labels: @json($version_dates),
                datasets: longTailData
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Evolución de Keywords Long Tail',
                        padding: 20
                    }
                }
            }
        });

        // PAA Chart
        const paaData = @json($paa_chartjs_datasets);
        const paaColors = generateColors(paaData.length);
        paaData.forEach((dataset, i) => {
            dataset.borderColor = paaColors[i];
            dataset.backgroundColor = paaColors[i] + '20';
            dataset.borderWidth = 2;
            dataset.fill = true;
            dataset.tension = 0.4;
        });

        new Chart(document.getElementById('paaChart'), {
            type: 'line',
            data: {
                labels: @json($version_dates),
                datasets: paaData
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Evolución de People Also Ask',
                        padding: 20
                    }
                }
            }
        });

        // Search Console Charts
        @if($sc_has_data)
        // Clicks
        new Chart(document.getElementById('scClicksLine'), {
            type: 'line',
            data: {
                labels: @json($sc_months),
                datasets: [{
                    label: 'Clicks',
                    data: @json($sc_clicks),
                    borderColor: '#3498db',
                    backgroundColor: '#3498db20',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Clicks Mensuales',
                        padding: 20
                    }
                }
            }
        });

        // Impressions
        new Chart(document.getElementById('scImpressionsBar'), {
            type: 'bar',
            data: {
                labels: @json($sc_months),
                datasets: [{
                    label: 'Impresiones',
                    data: @json($sc_impressions),
                    backgroundColor: '#2ecc7180',
                    borderColor: '#2ecc71',
                    borderWidth: 1
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Impresiones Mensuales',
                        padding: 20
                    }
                }
            }
        });

        // CTR
        new Chart(document.getElementById('scCtrLine'), {
            type: 'line',
            data: {
                labels: @json($sc_months),
                datasets: [{
                    label: 'CTR Promedio (%)',
                    data: @json($sc_avg_ctr),
                    borderColor: '#e74c3c',
                    backgroundColor: '#e74c3c20',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'CTR Promedio Mensual',
                        padding: 20
                    }
                },
                scales: {
                    y: {
                        ticks: {
                            callback: value => value.toFixed(2) + '%'
                        }
                    }
                }
            }
        });

        // Position
        new Chart(document.getElementById('scPositionLine'), {
            type: 'line',
            data: {
                labels: @json($sc_months),
                datasets: [{
                    label: 'Posición Promedio',
                    data: @json($sc_avg_position),
                    borderColor: '#9b59b6',
                    backgroundColor: '#9b59b620',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: 'Posición Promedio Mensual',
                        padding: 20
                    }
                },
                scales: {
                    y: {
                        reverse: true,
                        beginAtZero: false
                    }
                }
            }
        });
        @endif
    </script>
</body>
</html>
