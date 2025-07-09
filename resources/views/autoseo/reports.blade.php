<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe SEO - {{ $url ?? 'Análisis' }}</title>

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
        <header class="bg-white shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Informe SEO</h1>
                        <p class="mt-1 text-sm text-gray-500">{{ $url ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative">
                            <input type="text"
                                   id="searchInput"
                                   class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="Buscar...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <select id="sortSelect" class="border border-gray-300 rounded-lg py-2 px-4 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="keyword">Ordenar por Keyword</option>
                            <option value="results">Ordenar por Resultados</option>
                            <option value="position">Ordenar por Posición</option>
                        </select>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="space-y-8">
                <!-- Resumen General -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900">Resumen General</h2>
                        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div class="bg-primary-50 rounded-lg px-6 py-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <dt class="text-sm font-medium text-gray-500">Keywords Short Tail</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $shortTailCount ?? 0 }}</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary-50 rounded-lg px-6 py-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <dt class="text-sm font-medium text-gray-500">Keywords Long Tail</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $longTailCount ?? 0 }}</dd>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-primary-50 rounded-lg px-6 py-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <dt class="text-sm font-medium text-gray-500">Preguntas Frecuentes</dt>
                                        <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $faqCount ?? 0 }}</dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Keywords Short Tail -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900">Keywords Short Tail</h2>
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="shortTailTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultados</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($shortTail ?? [] as $keyword)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $keyword['keyword'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($keyword['results'], 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $keyword['position'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Keywords Long Tail -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900">Keywords Long Tail</h2>
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="longTailTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keyword</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultados</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($longTail ?? [] as $keyword)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $keyword['keyword'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($keyword['results'], 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $keyword['position'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Preguntas Frecuentes -->
                <section class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h2 class="text-lg font-medium text-gray-900">Preguntas Frecuentes</h2>
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="faqTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pregunta</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resultados</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posición</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($faq ?? [] as $question)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $question['keyword'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($question['results'], 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $question['position'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const sortSelect = document.getElementById('sortSelect');
            const tables = ['shortTailTable', 'longTailTable', 'faqTable'];

            // Función para filtrar las tablas
            function filterTables(searchTerm) {
                tables.forEach(tableId => {
                    const table = document.getElementById(tableId);
                    if (!table) return;

                    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

                    for (let row of rows) {
                        const text = row.textContent.toLowerCase();
                        const match = text.includes(searchTerm.toLowerCase());
                        row.style.display = match ? '' : 'none';
                    }
                });
            }

            // Función para ordenar las tablas
            function sortTables(column) {
                tables.forEach(tableId => {
                    const table = document.getElementById(tableId);
                    if (!table) return;

                    const tbody = table.getElementsByTagName('tbody')[0];
                    const rows = Array.from(tbody.getElementsByTagName('tr'));

                    rows.sort((a, b) => {
                        let aValue = a.cells[column === 'keyword' ? 0 : column === 'results' ? 1 : 2].textContent;
                        let bValue = b.cells[column === 'keyword' ? 0 : column === 'results' ? 1 : 2].textContent;

                        if (column === 'results') {
                            aValue = parseInt(aValue.replace(/\./g, '')) || 0;
                            bValue = parseInt(bValue.replace(/\./g, '')) || 0;
                        }

                        if (column === 'position') {
                            aValue = aValue === 'N/A' ? Infinity : parseInt(aValue) || Infinity;
                            bValue = bValue === 'N/A' ? Infinity : parseInt(bValue) || Infinity;
                        }

                        if (aValue < bValue) return -1;
                        if (aValue > bValue) return 1;
                        return 0;
                    });

                    // Limpiar y reordenar tbody
                    while (tbody.firstChild) {
                        tbody.removeChild(tbody.firstChild);
                    }
                    rows.forEach(row => tbody.appendChild(row));
                });
            }

            // Event listeners
            searchInput.addEventListener('input', (e) => {
                filterTables(e.target.value);
            });

            sortSelect.addEventListener('change', (e) => {
                sortTables(e.target.value);
            });
        });
    </script>
</body>
</html>
