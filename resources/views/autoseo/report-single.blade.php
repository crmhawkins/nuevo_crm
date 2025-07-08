<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>Informe SEO - Reporte {{ $seo['dominio'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
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
            margin: 24px 0;
            background: #fff;
            border-radius: 12px;
            padding: 8px 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .chart-container canvas {
            display: block;
            margin: 0 auto;
            width: 800px !important;
            height: 380px !important;
        }

        h3 {
            font-size: 1.2em;
            margin-top: 36px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .section-title {
            margin-top: 40px;
            font-size: 1.3em;
            color: #34495e;
            border-left: 4px solid #667eea;
            padding-left: 12px;
        }
    </style>
</head>

<body>
    <h1>📄 Informe SEO - Reporte {{ $seo['dominio'] }}</h1>
    <div class="card">
        <h2>🏠 Dominio Analizado</h2>
        <ul>
            <li><strong>Dominio:</strong> {{ $seo['dominio'] ?? 'N/A' }}</li>
            <li><strong>Fecha del informe:</strong> {{ $seo['uploaded_at'] ?? '-' }}</li>
        </ul>
        <h3 class="section-title">Short Tail Keywords</h3>
        <div class="chart-container">
            <canvas id="shortTailBar" width="800" height="380"></canvas>
        </div>
        <ul>
            @foreach ($short_tail_labels as $kw)
                <li>{{ $kw }}</li>
            @endforeach
        </ul>
        <h3 class="section-title">Long Tail Keywords</h3>
        <div class="chart-container">
            <canvas id="longTailBar" width="800" height="380"></canvas>
        </div>
        <ul>
            @foreach ($long_tail_labels as $kw)
                <li>{{ $kw }}</li>
            @endforeach
        </ul>
        <h3 class="section-title">People Also Ask</h3>
        <div class="chart-container">
            <canvas id="paaBar" width="800" height="380"></canvas>
        </div>
        <ul>
            @foreach ($paa_labels as $q)
                <li>{{ $q }}</li>
            @endforeach
        </ul>
        <h3 class="section-title">Detalles de Keywords</h3>
        <div class="chart-container">
            <canvas id="detalleKeywordsBar" width="800" height="380"></canvas>
        </div>
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
                <li><strong>Clicks:</strong> {{ isset($sc_clicks[0]) ? $sc_clicks[0] : '-' }}</li>
                <li><strong>Impresiones:</strong> {{ isset($sc_impressions[0]) ? $sc_impressions[0] : '-' }}</li>
                <li><strong>CTR promedio:</strong> {{ isset($sc_avg_ctr[0]) ? $sc_avg_ctr[0] : '-' }}</li>
                <li><strong>Posición promedio:</strong> {{ isset($sc_avg_position[0]) ? $sc_avg_position[0] : '-' }}
                </li>
            </ul>
        @endif
    </div>
    <script>
        // Short Tail
        const shortTailLabels = @json($short_tail_labels);
        const shortTailData = @json(array_map(function ($kw) use ($seo) {
                $found = null;
                foreach ($seo['detalles_keywords'] ?? [] as $item) {
                    if (($item['keyword'] ?? '') === $kw) {
                        $found = $item['total_results'] ?? 0;
                        break;
                    }
                }
                return $found ?? 0;
            }, $short_tail_labels));
        new Chart(document.getElementById('shortTailBar'), {
            type: 'bar',
            data: {
                labels: shortTailLabels,
                datasets: [{
                    label: 'Resultados',
                    data: shortTailData,
                    backgroundColor: 'rgba(52,152,219,0.7)'
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

        // Long Tail
        const longTailLabels = @json($long_tail_labels);
        const longTailData = @json(array_map(function ($kw) use ($seo) {
                $found = null;
                foreach ($seo['detalles_keywords'] ?? [] as $item) {
                    if (($item['keyword'] ?? '') === $kw) {
                        $found = $item['total_results'] ?? 0;
                        break;
                    }
                }
                return $found ?? 0;
            }, $long_tail_labels));
        new Chart(document.getElementById('longTailBar'), {
            type: 'bar',
            data: {
                labels: longTailLabels,
                datasets: [{
                    label: 'Resultados',
                    data: longTailData,
                    backgroundColor: 'rgba(46,204,113,0.7)'
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

        // PAA
        const paaLabels = @json($paa_labels);
        const paaData = @json(array_map(function ($q) use ($seo) {
                $found = null;
                foreach ($seo['people_also_ask'] ?? [] as $item) {
                    if (($item['question'] ?? '') === $q) {
                        $found = $item['total_results'] ?? 0;
                        break;
                    }
                }
                return $found ?? 0;
            }, $paa_labels));
        new Chart(document.getElementById('paaBar'), {
            type: 'bar',
            data: {
                labels: paaLabels,
                datasets: [{
                    label: 'Resultados',
                    data: paaData,
                    backgroundColor: 'rgba(241,196,15,0.7)'
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

        // Detalles de Keywords
        const detalleKeywordsLabels = @json(array_map(function ($item) {
                return $item['keyword'] ?? '';
            }, $seo['detalles_keywords'] ?? []));
        const detalleKeywordsData = @json(array_map(function ($item) {
                return $item['total_results'] ?? 0;
            }, $seo['detalles_keywords'] ?? []));
        new Chart(document.getElementById('detalleKeywordsBar'), {
            type: 'bar',
            data: {
                labels: detalleKeywordsLabels,
                datasets: [{
                    label: 'Resultados',
                    data: detalleKeywordsData,
                    backgroundColor: 'rgba(155,89,182,0.7)'
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

</html>
