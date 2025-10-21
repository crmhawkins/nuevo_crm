<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO - {{ $seo['dominio'] ?? 'Dominio no especificado' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f8fafc; color: #1a202c; }
        .card { background: white; border-radius: 0.5rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); padding: 1.5rem; margin-bottom: 1.5rem; }
        .chart-container { height: 300px; margin: 1rem 0; }
        .keyword-card { border-left: 4px solid #3b82f6; padding-left: 1rem; margin-bottom: 1rem; }
        .metric { font-size: 1.125rem; font-weight: 600; }
        .trend-up { color: #059669; }
        .trend-down { color: #dc2626; }
        .trend-neutral { color: #6b7280; }
    </style>
</head>
<body class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="card">
            <h1 class="text-2xl font-bold mb-2">Informe SEO</h1>
            <p class="text-gray-600">{{ $seo['dominio'] ?? 'Dominio no especificado' }}</p>
            <p class="text-sm text-gray-500">Fecha del informe: {{ end($version_dates) ?? 'No disponible' }}</p>
        </div>

        @if(!empty($short_tail_table))
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Keywords Short Tail</h2>
            <div class="grid gap-4">
                @foreach($short_tail_table as $row)
                    @include('autoseo._keyword_card', ['row' => $row])
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($long_tail_table))
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Keywords Long Tail</h2>
            <div class="grid gap-4">
                @foreach($long_tail_table as $row)
                    @include('autoseo._keyword_card', ['row' => $row])
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($paa_table))
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Preguntas Frecuentes</h2>
            <div class="grid gap-4">
                @foreach($paa_table as $row)
                    @include('autoseo._keyword_card', ['row' => $row])
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <script>
        const colors = {
            blue: '#3b82f6',
            red: '#ef4444'
        };

        function createChart(elementId, dates, totalResults, positions) {
            const ctx = document.getElementById(elementId)?.getContext('2d');
            if (!ctx) return;
            
            console.log('Creando gráfica:', elementId, {
                dates: dates,
                totalResults: totalResults,
                positions: positions
            });

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Resultados',
                            data: totalResults,
                            borderColor: colors.blue,
                            backgroundColor: `${colors.blue}20`,
                            fill: true,
                            yAxisID: 'y',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBackgroundColor: colors.blue,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            spanGaps: true
                        },
                        {
                            label: 'Posición',
                            data: positions,
                            borderColor: colors.red,
                            backgroundColor: 'transparent',
                            borderDash: [5, 5],
                            yAxisID: 'y1',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 5,
                            pointHoverRadius: 8,
                            pointBackgroundColor: colors.red,
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            spanGaps: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
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
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Resultados de Búsqueda',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            ticks: {
                                callback: value => new Intl.NumberFormat().format(value)
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            reverse: true,
                            title: {
                                display: true,
                                text: 'Posición en Google',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: false,
                            min: 1,
                            max: 100,
                            grid: {
                                drawOnChartArea: false,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    }
                }
            });
        }

        @foreach(array_merge($short_tail_table ?? [], $long_tail_table ?? [], $paa_table ?? []) as $row)
            @if(isset($row['keyword']) && isset($row['metrics']))
                createChart(
                    '{{ $row['metrics']['chart_id'] }}',
                    @json($version_dates),
                    @json($row['total_results']),
                    @json($row['position'])
                );
            @endif
        @endforeach
    </script>
</body>
</html>
