<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justificación SEO - {{ $seo['dominio'] ?? 'Análisis' }}</title>

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
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Justificación SEO</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ $seo['dominio'] ?? '' }}</p>
                    <p class="text-xs text-gray-400">Fecha del análisis: {{ $version_dates[0] ?? 'No disponible' }}</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-8">
                <!-- Resumen de Acciones -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Resumen de Acciones SEO</h2>
                        <div class="prose max-w-none">
                            <p class="text-gray-600">Durante el último periodo de análisis, se han realizado las siguientes acciones de optimización SEO:</p>
                            <ul class="mt-4 space-y-2">
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Monitorización y seguimiento de {{ count($short_tail_table) + count($long_tail_table) }} palabras clave relevantes</span>
                                </li>
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Análisis de {{ count($paa_table) }} preguntas frecuentes para optimización de contenido</span>
                                </li>
                                @if($sc_has_data)
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Seguimiento de métricas en Search Console con {{ number_format(end($sc_impressions)) }} impresiones</span>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Análisis de Keywords -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Análisis de Keywords</h2>

                        <!-- Short Tail Keywords -->
                        <div class="mb-8">
                            <h3 class="text-md font-medium text-gray-700 mb-3">Keywords Short Tail Principales</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tendencia</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach(array_slice($short_tail_table, 0, 5) as $keyword)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $keyword['keyword'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $keyword['metrics']['last_position'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(isset($keyword['metrics']['position_change']))
                                                    @if($keyword['metrics']['position_change'] > 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            +{{ $keyword['metrics']['position_change'] }}
                                                        </span>
                                                    @elseif($keyword['metrics']['position_change'] < 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ $keyword['metrics']['position_change'] }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            =
                                                        </span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Long Tail Keywords -->
                        <div>
                            <h3 class="text-md font-medium text-gray-700 mb-3">Keywords Long Tail Destacadas</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tendencia</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach(array_slice($long_tail_table, 0, 5) as $keyword)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $keyword['keyword'] }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $keyword['metrics']['last_position'] ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if(isset($keyword['metrics']['position_change']))
                                                    @if($keyword['metrics']['position_change'] > 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            +{{ $keyword['metrics']['position_change'] }}
                                                        </span>
                                                    @elseif($keyword['metrics']['position_change'] < 0)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {{ $keyword['metrics']['position_change'] }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            =
                                                        </span>
                                                    @endif
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>

                @if($sc_has_data)
                <!-- Métricas de Search Console -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Rendimiento en Search Console</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Impresiones</h3>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format(end($sc_impressions)) }}</p>
                                @php
                                    $impressionChange = end($sc_impressions) - reset($sc_impressions);
                                    $impressionChangePercent = reset($sc_impressions) ? round(($impressionChange / reset($sc_impressions)) * 100, 1) : 0;
                                @endphp
                                <p class="text-sm mt-2">
                                    <span class="{{ $impressionChangePercent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $impressionChangePercent >= 0 ? '+' : '' }}{{ $impressionChangePercent }}%
                                    </span>
                                    <span class="text-gray-500">vs periodo anterior</span>
                                </p>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-2">Posición Media</h3>
                                <p class="text-2xl font-semibold text-gray-900">{{ round(end($sc_avg_position), 1) }}</p>
                                @php
                                    $positionChange = reset($sc_avg_position) - end($sc_avg_position);
                                    $positionChangePercent = reset($sc_avg_position) ? round(($positionChange / reset($sc_avg_position)) * 100, 1) : 0;
                                @endphp
                                <p class="text-sm mt-2">
                                    <span class="{{ $positionChangePercent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $positionChangePercent >= 0 ? '+' : '' }}{{ $positionChangePercent }}%
                                    </span>
                                    <span class="text-gray-500">vs periodo anterior</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                @endif

                <!-- Conclusiones -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Conclusiones y Recomendaciones</h2>
                        <div class="prose max-w-none text-gray-600">
                            <p>Basado en el análisis realizado, se observa:</p>
                            <ul class="mt-4 space-y-2">
                                @if(isset($short_tail_table[0]['metrics']['position_change']))
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Las keywords principales muestran una tendencia
                                        @if($short_tail_table[0]['metrics']['position_change'] > 0)
                                            positiva
                                        @elseif($short_tail_table[0]['metrics']['position_change'] < 0)
                                            que requiere atención
                                        @else
                                            estable
                                        @endif
                                        en el posicionamiento.</span>
                                </li>
                                @endif
                                @if($sc_has_data && isset($impressionChangePercent))
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>La visibilidad en búsquedas ha
                                        @if($impressionChangePercent > 0)
                                            aumentado un {{ $impressionChangePercent }}%
                                        @elseif($impressionChangePercent < 0)
                                            disminuido un {{ abs($impressionChangePercent) }}%
                                        @else
                                            mantenido estable
                                        @endif
                                        respecto al periodo anterior.</span>
                                </li>
                                @endif
                                <li class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Se han identificado {{ count($paa_table) }} preguntas frecuentes que representan oportunidades para crear contenido relevante.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
