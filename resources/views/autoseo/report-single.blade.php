<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO - {{ $seo['dominio'] ?? 'Dominio no especificado' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #1a202c;
        }

        .card {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .metric {
            font-size: 1.125rem;
            font-weight: 600;
        }

        .trend-up {
            color: #059669;
        }

        .trend-down {
            color: #dc2626;
        }

        .trend-neutral {
            color: #6b7280;
        }
    </style>
</head>

<body class="p-6 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <!-- Cabecera -->
        <div class="card">
            <h1 class="text-2xl font-bold mb-2">Informe SEO</h1>
            <p class="text-gray-600">{{ $seo['dominio'] ?? 'Dominio no especificado' }}</p>
            <p class="text-sm text-gray-500">Fecha del informe: {{ $version_dates[0] ?? 'No disponible' }}</p>
        </div>

        <!-- Resumen General -->
        <div class="card">
            <h2 class="text-xl font-semibold mb-4">Resumen General</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <span class="text-sm text-blue-600">Keywords Short Tail</span>
                    <div class="metric">{{ count($short_tail_table) }}</div>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <span class="text-sm text-green-600">Keywords Long Tail</span>
                    <div class="metric">{{ count($long_tail_table) }}</div>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg">
                    <span class="text-sm text-purple-600">Preguntas Frecuentes</span>
                    <div class="metric">{{ count($paa_table) }}</div>
                </div>
                @if ($sc_has_data)
                    <div class="p-4 bg-orange-50 rounded-lg">
                        <span class="text-sm text-orange-600">Impresiones (Search Console)</span>
                        <div class="metric">{{ number_format(end($sc_impressions)) }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Keywords Short Tail -->
        @if (!empty($short_tail_table))
            <div class="card">
                <h2 class="text-xl font-semibold mb-4">Keywords Short Tail</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keyword</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Resultados</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Posición</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($short_tail_table as $row)
                                @if (isset($row['keyword']) && isset($row['metrics']))
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $row['keyword'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <span
                                                class="font-medium">{{ number_format($row['metrics']['last_result']) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @if ($row['metrics']['last_position'])
                                                <span
                                                    class="font-medium">{{ number_format($row['metrics']['last_position'], 1) }}</span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Keywords Long Tail -->
        @if (!empty($long_tail_table))
            <div class="card">
                <h2 class="text-xl font-semibold mb-4">Keywords Long Tail</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Keyword</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Resultados</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Posición</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($long_tail_table as $row)
                                @if (isset($row['keyword']) && isset($row['metrics']))
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $row['keyword'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <span
                                                class="font-medium">{{ number_format($row['metrics']['last_result']) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @if ($row['metrics']['last_position'])
                                                <span
                                                    class="font-medium">{{ number_format($row['metrics']['last_position'], 1) }}</span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Preguntas Frecuentes -->
        @if (!empty($paa_table))
            <div class="card">
                <h2 class="text-xl font-semibold mb-4">Preguntas Frecuentes</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pregunta</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Resultados</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Posición</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($paa_table as $row)
                                @if (isset($row['keyword']) && isset($row['metrics']))
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            {{ $row['keyword'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            <span
                                                class="font-medium">{{ number_format($row['metrics']['last_result']) }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            @if ($row['metrics']['last_position'])
                                                <span
                                                    class="font-medium">{{ number_format($row['metrics']['last_position'], 1) }}</span>
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Search Console -->
        @if ($sc_has_data)
            <div class="card">
                <h2 class="text-xl font-semibold mb-4">Datos de Search Console</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Clicks</span>
                        <div class="metric">{{ number_format(end($sc_clicks)) }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Impresiones</span>
                        <div class="metric">{{ number_format(end($sc_impressions)) }}</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">CTR Promedio</span>
                        <div class="metric">{{ number_format(end($sc_avg_ctr), 2) }}%</div>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-600">Posición Promedio</span>
                        <div class="metric">{{ number_format(end($sc_avg_position), 1) }}</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
