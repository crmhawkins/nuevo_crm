<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Informe SEO Comparativo</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: #eef2f7;
            color: #333;
            padding: 40px;
            margin: 0;
        }

        h1,
        h2,
        h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
            padding: 32px;
            margin-bottom: 50px;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }

        ul {
            padding-left: 20px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-top: 20px;
        }

        th {
            background-color: #dfe6ec;
            color: #2c3e50;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }

        td {
            background: #fff;
            padding: 12px;
            border-top: 1px solid #e0e6ed;
            border-bottom: 1px solid #e0e6ed;
        }

        tr:hover td {
            background: #f4f9ff;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 32px;
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .comparison-table {
            margin-top: 30px;
        }

        .comparison-table th {
            background-color: #34495e;
            color: white;
        }

        .version-header {
            background-color: #3498db !important;
            color: white !important;
        }

        .evolution-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-top: 20px;
        }

        .evolution-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .evolution-item:last-child {
            border-bottom: none;
        }

        .trend-up {
            color: #28a745;
        }

        .trend-down {
            color: #dc3545;
        }

        .trend-stable {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <h1>📊 Informe SEO del Dominio</h1>

    @if ($is_single)
        <div class="card">
            <h2>🏠 Dominio Analizado</h2>
            <ul>
                <li><strong>Dominio:</strong> {{ $seo['dominio'] ?? 'N/A' }}</li>
                <li><strong>Fecha del informe:</strong> {{ $seo['uploaded_at'] ?? '-' }}</li>
            </ul>
            <h3>Short Tail Keywords</h3>
            <ul>
                @foreach ($short_tail_labels as $kw)
                    <li>{{ $kw }}</li>
                @endforeach
            </ul>
            <h3>Long Tail Keywords</h3>
            <ul>
                @foreach ($long_tail_labels as $kw)
                    <li>{{ $kw }}</li>
                @endforeach
            </ul>
            <h3>People Also Ask</h3>
            <ul>
                @foreach ($paa_labels as $q)
                    <li>{{ $q }}</li>
                @endforeach
            </ul>
            <h3>Detalles de Keywords</h3>
            <table>
                <thead>
                    <tr>
                        <th>Keyword</th>
                        <th>Total Results</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($seo['detalles_keywords'] ?? [] as $item)
                        <tr>
                            <td>{{ $item['keyword'] ?? '' }}</td>
                            <td>{{ $item['total_results'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($sc_has_data)
                <h3>Search Console (último mes)</h3>
                <ul>
                    <li><strong>Clicks:</strong> {{ $sc_clicks[0] ?? '-' }}</li>
                    <li><strong>Impresiones:</strong> {{ $sc_impressions[0] ?? '-' }}</li>
                    <li><strong>CTR promedio:</strong> {{ $sc_avg_ctr[0] ?? '-' }}</li>
                    <li><strong>Posición promedio:</strong> {{ $sc_avg_position[0] ?? '-' }}</li>
                </ul>
            @endif
        </div>
    @else
        <div class="card">
            <h2>🏠 Dominio Analizado</h2>
            <p><strong>{{ $seo['dominio'] ?? 'N/A' }}</strong></p>

            <div class="summary-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ count($short_tail_labels) }}</div>
                    <div class="stat-label">Keywords Short Tail</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ count($long_tail_labels) }}</div>
                    <div class="stat-label">Keywords Long Tail</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ count($paa_labels) }}</div>
                    <div class="stat-label">Preguntas PAA</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📈 Evolución de Palabras Clave Short Tail</h2>
            <div class="chart-container">
                <canvas id="shortTailChart"></canvas>
            </div>

            <div class="evolution-summary">
                <h3>Resumen de Evolución Short Tail</h3>
                @foreach ($short_tail_labels as $keyword)
                    <div class="evolution-item">
                        <span><strong>{{ $keyword }}</strong></span>
                        <span>
                            @php
                                $dataset = collect($short_tail_chartjs_datasets)->firstWhere('label', $keyword);
                                $values = $dataset
                                    ? array_filter($dataset['data'], function ($v) {
                                        return $v !== null;
                                    })
                                    : [];
                            @endphp
                            @if (count($values) > 1)
                                @php
                                    $firstVal = $values[0];
                                    $lastVal = end($values);
                                    $change = $lastVal - $firstVal;
                                    $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                                @endphp
                                @if ($change > 0)
                                    <span class="trend-up">↗ +{{ number_format($change) }}
                                        (+{{ number_format($changePercent, 1) }}%)</span>
                                @elseif($change < 0)
                                    <span class="trend-down">↘ {{ number_format($change) }}
                                        ({{ number_format($changePercent, 1) }}%)</span>
                                @else
                                    <span class="trend-stable">→ Sin cambios</span>
                                @endif
                            @elseif(count($values) == 1)
                                <span>{{ number_format($values[0]) }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="comparison-table">
                <h3>Tabla Comparativa Short Tail</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            @foreach ($version_dates as $date)
                                <th class="version-header">{{ $date }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($short_tail_labels as $index => $keyword)
                            <tr>
                                <td><strong>{{ $keyword }}</strong></td>
                                @foreach ($short_tail_chartjs_datasets as $dataset)
                                    <td>
                                        @if (isset($dataset['data'][$index]) && $dataset['data'][$index] !== null)
                                            {{ number_format($dataset['data'][$index]) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>📊 Evolución de Palabras Clave Long Tail</h2>
            <div class="chart-container">
                <canvas id="longTailChart"></canvas>
            </div>

            <div class="evolution-summary">
                <h3>Resumen de Evolución Long Tail</h3>
                @foreach ($long_tail_labels as $keyword)
                    <div class="evolution-item">
                        <span><strong>{{ $keyword }}</strong></span>
                        <span>
                            @php
                                $dataset = collect($long_tail_chartjs_datasets)->firstWhere('label', $keyword);
                                $values = $dataset
                                    ? array_filter($dataset['data'], function ($v) {
                                        return $v !== null;
                                    })
                                    : [];
                            @endphp
                            @if (count($values) > 1)
                                @php
                                    $firstVal = $values[0];
                                    $lastVal = end($values);
                                    $change = $lastVal - $firstVal;
                                    $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                                @endphp
                                @if ($change > 0)
                                    <span class="trend-up">↗ +{{ number_format($change) }}
                                        (+{{ number_format($changePercent, 1) }}%)</span>
                                @elseif($change < 0)
                                    <span class="trend-down">↘ {{ number_format($change) }}
                                        ({{ number_format($changePercent, 1) }}%)</span>
                                @else
                                    <span class="trend-stable">→ Sin cambios</span>
                                @endif
                            @elseif(count($values) == 1)
                                <span>{{ number_format($values[0]) }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="comparison-table">
                <h3>Tabla Comparativa Long Tail</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            @foreach ($version_dates as $date)
                                <th class="version-header">{{ $date }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($long_tail_labels as $index => $keyword)
                            <tr>
                                <td><strong>{{ $keyword }}</strong></td>
                                @foreach ($long_tail_chartjs_datasets as $dataset)
                                    <td>
                                        @if (isset($dataset['data'][$index]) && $dataset['data'][$index] !== null)
                                            {{ number_format($dataset['data'][$index]) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>❓ Evolución de People Also Ask</h2>
            <div class="chart-container">
                <canvas id="paaChart"></canvas>
            </div>

            <div class="evolution-summary">
                <h3>Resumen de Evolución PAA</h3>
                @foreach ($paa_labels as $question)
                    <div class="evolution-item">
                        <span><strong>{{ $question }}</strong></span>
                        <span>
                            @php
                                $dataset = collect($paa_chartjs_datasets)->firstWhere('label', $question);
                                $values = $dataset
                                    ? array_filter($dataset['data'], function ($v) {
                                        return $v !== null;
                                    })
                                    : [];
                            @endphp
                            @if (count($values) > 1)
                                @php
                                    $firstVal = $values[0];
                                    $lastVal = end($values);
                                    $change = $lastVal - $firstVal;
                                    $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                                @endphp
                                @if ($change > 0)
                                    <span class="trend-up">↗ +{{ number_format($change) }}
                                        (+{{ number_format($changePercent, 1) }}%)</span>
                                @elseif($change < 0)
                                    <span class="trend-down">↘ {{ number_format($change) }}
                                        ({{ number_format($changePercent, 1) }}%)</span>
                                @else
                                    <span class="trend-stable">→ Sin cambios</span>
                                @endif
                            @elseif(count($values) == 1)
                                <span>{{ number_format($values[0]) }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="comparison-table">
                <h3>Tabla Comparativa PAA</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Pregunta</th>
                            @foreach ($version_dates as $date)
                                <th class="version-header">{{ $date }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paa_labels as $index => $question)
                            <tr>
                                <td><strong>{{ $question }}</strong></td>
                                @foreach ($paa_chartjs_datasets as $dataset)
                                    <td>
                                        @if (isset($dataset['data'][$index]) && $dataset['data'][$index] !== null)
                                            {{ number_format($dataset['data'][$index]) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>🔍 Evolución de Detalles de Keywords</h2>
            <div class="chart-container">
                <canvas id="keywordChart"></canvas>
            </div>

            <div class="evolution-summary">
                <h3>Resumen de Evolución Keywords</h3>
                @foreach ($detalle_keywords_labels as $keyword)
                    <div class="evolution-item">
                        <span><strong>{{ $keyword }}</strong></span>
                        <span>
                            @php
                                $dataset = collect($detalle_keywords_chartjs_datasets)->firstWhere('label', $keyword);
                                $values = $dataset
                                    ? array_filter($dataset['data'], function ($v) {
                                        return $v !== null;
                                    })
                                    : [];
                            @endphp
                            @if (count($values) > 1)
                                @php
                                    $firstVal = $values[0];
                                    $lastVal = end($values);
                                    $change = $lastVal - $firstVal;
                                    $changePercent = $firstVal ? ($change / $firstVal) * 100 : 0;
                                @endphp
                                @if ($change > 0)
                                    <span class="trend-up">↗ +{{ number_format($change) }}
                                        (+{{ number_format($changePercent, 1) }}%)</span>
                                @elseif($change < 0)
                                    <span class="trend-down">↘ {{ number_format($change) }}
                                        ({{ number_format($changePercent, 1) }}%)</span>
                                @else
                                    <span class="trend-stable">→ Sin cambios</span>
                                @endif
                            @elseif(count($values) == 1)
                                <span>{{ number_format($values[0]) }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="comparison-table">
                <h3>Tabla Comparativa Keywords</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Keyword</th>
                            @foreach ($version_dates as $date)
                                <th class="version-header">{{ $date }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detalle_keywords_labels as $index => $keyword)
                            <tr>
                                <td><strong>{{ $keyword }}</strong></td>
                                @foreach ($detalle_keywords_chartjs_datasets as $dataset)
                                    <td>
                                        @if (isset($dataset['data'][$index]) && $dataset['data'][$index] !== null)
                                            {{ number_format($dataset['data'][$index]) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>📊 Evolución Mensual Search Console</h2>
            @if ($sc_has_data)
                <div class="chart-row"
                    style="display: flex; flex-wrap: wrap; gap: 32px; justify-content: center; margin-bottom: 32px;">
                    <div class="chart-container"
                        style="flex: 1 1 400px; min-width: 350px; max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <canvas id="scClicksLine"></canvas>
                    </div>
                    <div class="chart-container"
                        style="flex: 1 1 400px; min-width: 350px; max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <canvas id="scImpressionsBar"></canvas>
                    </div>
                </div>
                <div class="chart-row"
                    style="display: flex; flex-wrap: wrap; gap: 32px; justify-content: center; margin-bottom: 32px;">
                    <div class="chart-container"
                        style="flex: 1 1 400px; min-width: 350px; max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <canvas id="scCtrLine"></canvas>
                    </div>
                    <div class="chart-container"
                        style="flex: 1 1 400px; min-width: 350px; max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 16px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                        <canvas id="scPositionLine"></canvas>
                    </div>
                </div>
            @else
                <div style="padding: 32px; text-align: center; color: #888; font-size: 1.2em">
                    No hay datos mensuales de Search Console disponibles para mostrar.
                </div>
            @endif
        </div>
    @endif

    <script>
        // Usar las fechas de los reportes como labels
        const versionLabels = @json($version_dates);

        // --- Short Tail: una línea por keyword ---
        const shortTailDatasets = @json($short_tail_chartjs_datasets);
        new Chart(document.getElementById('shortTailChart'), {
            type: 'line',
            data: {
                labels: versionLabels,
                datasets: shortTailDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Evolución de Keywords Short Tail'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString()
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // --- Long Tail: una línea por keyword ---
        const longTailDatasets = @json($long_tail_chartjs_datasets);
        new Chart(document.getElementById('longTailChart'), {
            type: 'line',
            data: {
                labels: versionLabels,
                datasets: longTailDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Evolución de Keywords Long Tail'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString()
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // --- PAA: una línea por pregunta ---
        const paaDatasets = @json($paa_chartjs_datasets);
        new Chart(document.getElementById('paaChart'), {
            type: 'line',
            data: {
                labels: versionLabels,
                datasets: paaDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Evolución de People Also Ask'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString()
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // --- Detalles Keywords: una línea por keyword ---
        const detalleKeywordsDatasets = @json($detalle_keywords_chartjs_datasets);
        new Chart(document.getElementById('keywordChart'), {
            type: 'line',
            data: {
                labels: versionLabels,
                datasets: detalleKeywordsDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Evolución de Detalles de Keywords'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString()
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // --- Search Console ---
        const scMonths = @json($sc_months);
        const scClicks = @json($sc_clicks);
        const scImpressions = @json($sc_impressions);
        const scAvgCtr = @json($sc_avg_ctr);
        const scAvgPosition = @json($sc_avg_position);

        if (scMonths.length > 0) {
            new Chart(document.getElementById('scClicksLine'), {
                type: 'line',
                data: {
                    labels: scMonths,
                    datasets: [{
                        label: 'Clics',
                        data: scClicks,
                        borderColor: 'rgba(52, 152, 219, 1)',
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Clics mensuales (último año)'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.y.toLocaleString() + ' clics'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v.toLocaleString()
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('scImpressionsBar'), {
                type: 'bar',
                data: {
                    labels: scMonths,
                    datasets: [{
                        label: 'Impresiones',
                        data: scImpressions,
                        backgroundColor: 'rgba(241, 196, 15, 0.7)',
                        borderColor: 'rgba(243, 156, 18, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Impresiones mensuales (último año)'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.y.toLocaleString() + ' impresiones'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v.toLocaleString()
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('scCtrLine'), {
                type: 'line',
                data: {
                    labels: scMonths,
                    datasets: [{
                        label: 'CTR promedio (%)',
                        data: scAvgCtr,
                        borderColor: 'rgba(46, 204, 113, 1)',
                        backgroundColor: 'rgba(46, 204, 113, 0.2)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'CTR promedio mensual (último año)'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.y.toFixed(2) + ' %'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: v => v.toFixed(2) + ' %'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('scPositionLine'), {
                type: 'line',
                data: {
                    labels: scMonths,
                    datasets: [{
                        label: 'Posición promedio',
                        data: scAvgPosition,
                        borderColor: 'rgba(155, 89, 182, 1)',
                        backgroundColor: 'rgba(155, 89, 182, 0.2)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Posición promedio mensual (último año)'
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.parsed.y.toFixed(2)
                            }
                        }
                    },
                    scales: {
                        y: {
                            reverse: true,
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'Posición (1 = top)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Mes'
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>

</html>
