@extends('layouts.app')

@section('content')
    <style>
        .css-96uzu9 {
            opacity: 0 !important;
        }

        .pattern-bg {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f46e5' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30 pattern-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Card Principal -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-500 via-primary-500 to-purple-500 p-8">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center space-x-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="p-3 bg-white/10 backdrop-blur-xl rounded-2xl shadow-inner transform hover:scale-105 transition-all duration-300">
                                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white tracking-tight">Clientes SEO</h2>
                                <p class="text-indigo-100 text-sm mt-1 font-medium">{{ $clients->count() }} clientes
                                    registrados en la plataforma</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <input type="text" id="searchInput"
                                    class="w-full sm:w-64 pl-10 pr-4 py-2 border border-white/20 rounded-xl bg-white/10 backdrop-blur-xl text-gray-900 placeholder-gray-500 focus:ring-2 focus:ring-white/30 focus:border-transparent"
                                    placeholder="Buscar cliente...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-white/70" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                            <select id="filterReports"
                                class="border border-white/20 rounded-xl bg-white/10 backdrop-blur-xl text-white py-2 px-4 focus:ring-2 focus:ring-white/30 focus:border-transparent">
                                <option value="all">Todos los clientes</option>
                                <option value="with-reports">Con reportes</option>
                                <option value="without-reports">Sin reportes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead>
                            <tr class="bg-gray-50/50 backdrop-blur-lg">
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 transition-colors duration-200"
                                    onclick="sortTable('name')">
                                    Cliente
                                    <span class="ml-1 sort-icon">↕</span>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 transition-colors duration-200"
                                    onclick="sortTable('email')">
                                    Información
                                    <span class="ml-1 sort-icon">↕</span>
                                </th>
                                <th class="px-8 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider cursor-pointer hover:text-gray-700 transition-colors duration-200"
                                    onclick="sortTable('reports')">
                                    Estado
                                    <span class="ml-1 sort-icon">↕</span>
                                </th>
                                <th
                                    class="px-8 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white/50 backdrop-blur-sm divide-y divide-gray-200/50" id="clientsTableBody">
                            @forelse($clients as $client)
                                <tr class="hover:bg-gray-50/80 transition-all duration-300"
                                    data-reports="{{ $client->reports()->count() }}">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                <div class="relative group">
                                                    <div
                                                        class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl blur opacity-0 group-hover:opacity-50 transition duration-300">
                                                    </div>
                                                    <img class="relative h-12 w-12 rounded-xl shadow-lg object-cover transform group-hover:scale-105 transition duration-300"
                                                        src="https://ui-avatars.com/api/?name={{ urlencode($client->client_name) }}&color=7F9CF5&background=EBF4FF"
                                                        alt="{{ $client->client_name }}">
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-base font-semibold text-gray-900">
                                                    {{ $client->client_name }}</div>
                                                <a href="{{ $client->url }}" target="_blank"
                                                    class="text-sm text-primary-600 hover:text-primary-900 transition-colors duration-200 hover:underline">
                                                    {{ $client->url }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="text-sm text-gray-900">{{ $client->client_email }}</div>
                                        @if ($client->pin)
                                            <div class="text-sm text-gray-500 mt-2">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-gray-100/80 text-gray-800 border border-gray-200/50 shadow-sm backdrop-blur-sm">
                                                    <svg class="h-3.5 w-3.5 text-gray-400 mr-1.5" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    {{ $client->pin }}
                                                </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex justify-center">
                                            @php
                                                $reports = $client->reports()->get();
                                                $reportsCount = $reports->count();
                                            @endphp
                                            @if ($reportsCount > 0)
                                                <span
                                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium bg-green-100/80 text-green-800 border border-green-200/50 shadow-sm backdrop-blur-sm">
                                                    <span class="flex h-2 w-2 relative mr-2">
                                                        <span
                                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                    </span>
                                                    {{ $reportsCount }} reportes
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-medium bg-yellow-100/80 text-yellow-800 border border-yellow-200/50 shadow-sm backdrop-blur-sm">
                                                    <span class="flex h-2 w-2 relative mr-2">
                                                        <span
                                                            class="animate-pulse absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                        <span
                                                            class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                                    </span>
                                                    Sin reportes
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div class="flex justify-end space-x-3">
                                            <button type="button" onclick="toggleReports('{{ $client->id }}')"
                                                class="inline-flex items-center px-4 py-2 border border-gray-200 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                                Ver
                                            </button>
                                            <a href="{{ route('autoseo.generate.report', $client->id) }}"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-gradient-to-r from-indigo-500 via-primary-500 to-purple-500 hover:from-indigo-600 hover:via-primary-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                                Generar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Panel de Reportes -->
                                <tr class="hidden" id="reports-{{ $client->id }}">
                                    <td colspan="4" class="px-8 py-6 bg-gray-50/30 backdrop-blur-sm">
                                        <div
                                            class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 overflow-hidden">
                                            @if ($reportsCount > 0)
                                                <div class="divide-y divide-gray-100/50">
                                                    @foreach ($reports as $report)
                                                        <div class="p-5 hover:bg-gray-50/50 transition-all duration-300">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex items-center min-w-0 space-x-4">
                                                                    <div class="flex-shrink-0">
                                                                        <div
                                                                            class="p-2.5 bg-primary-50/80 rounded-xl border border-primary-100/50">
                                                                            <svg class="h-5 w-5 text-primary-600"
                                                                                fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="1.5"
                                                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                            </svg>
                                                                        </div>
                                                                    </div>
                                                                    <div class="min-w-0">
                                                                        <p
                                                                            class="text-sm font-semibold text-gray-900 truncate">
                                                                            {{ basename($report->path) }}</p>
                                                                        <p class="text-sm text-gray-500 mt-0.5">
                                                                            {{ \Carbon\Carbon::parse($report->created_at)->format('d/m/Y H:i') }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <a href="https://crm.hawkins.es/autoseo/reports/{{ $client->id }}/{{ $report->id }}"
                                                                    target="_blank"
                                                                    class="inline-flex items-center px-4 py-2 border border-gray-200 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                                                    <svg class="h-4 w-4 mr-2" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                    Ver Reporte
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-12 text-center">
                                                    <div
                                                        class="mx-auto h-16 w-16 text-gray-400 bg-gray-100/50 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                        <svg class="h-8 w-8" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="mt-4 text-sm font-medium text-gray-900">Sin reportes
                                                        disponibles</h3>
                                                    <p class="mt-1 text-sm text-gray-500">No hay reportes generados para
                                                        este cliente</p>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="max-w-sm mx-auto">
                                            <div
                                                class="mx-auto h-24 w-24 text-gray-400 bg-gray-100/50 rounded-full flex items-center justify-center backdrop-blur-sm">
                                                <svg class="h-12 w-12" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="mt-4 text-lg font-medium text-gray-900">Sin clientes registrados
                                            </h3>
                                            <p class="mt-2 text-sm text-gray-500">No hay clientes SEO registrados en el
                                                sistema actualmente.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterReports = document.getElementById('filterReports');
            const clientsTableBody = document.getElementById('clientsTableBody');
            let sortDirection = {};

            // Función para filtrar clientes
            function filterClients() {
                const searchTerm = searchInput.value.toLowerCase();
                const filterValue = filterReports.value;
                const rows = clientsTableBody.getElementsByTagName('tr');

                Array.from(rows).forEach(row => {
                    if (row.id.startsWith('reports-')) return; // Skip report rows

                    const clientName = row.querySelector('.text-base').textContent.toLowerCase();
                    const clientEmail = row.querySelector('.text-sm').textContent.toLowerCase();
                    const clientUrl = row.querySelector('.text-primary-600').textContent.toLowerCase();
                    const clientPin = row.querySelector('.bg-gray-100\\/80')?.textContent.trim()
                        .toLowerCase() || '';
                    const reportsCount = parseInt(row.dataset.reports || 0);

                    const matchesSearch = clientName.includes(searchTerm) ||
                        clientEmail.includes(searchTerm) ||
                        clientUrl.includes(searchTerm) ||
                        clientPin.includes(searchTerm);

                    const matchesFilter = filterValue === 'all' ||
                        (filterValue === 'with-reports' && reportsCount > 0) ||
                        (filterValue === 'without-reports' && reportsCount === 0);

                    row.style.display = matchesSearch && matchesFilter ? '' : 'none';

                    // Hide associated reports row if main row is hidden
                    const reportsRow = document.getElementById(`reports-${row.id}`);
                    if (reportsRow) {
                        reportsRow.style.display = 'none';
                    }
                });
            }

            // Función para ordenar la tabla
            function sortTable(column) {
                const rows = Array.from(clientsTableBody.getElementsByTagName('tr'))
                    .filter(row => !row.id.startsWith('reports-')); // Exclude report rows

                sortDirection[column] = !sortDirection[column];
                const direction = sortDirection[column] ? 1 : -1;

                rows.sort((a, b) => {
                    let aValue, bValue;

                    switch (column) {
                        case 'name':
                            aValue = a.querySelector('.text-base').textContent.toLowerCase();
                            bValue = b.querySelector('.text-base').textContent.toLowerCase();
                            break;
                        case 'email':
                            aValue = a.querySelector('.text-sm').textContent.toLowerCase();
                            bValue = b.querySelector('.text-sm').textContent.toLowerCase();
                            break;
                        case 'reports':
                            aValue = parseInt(a.dataset.reports || 0);
                            bValue = parseInt(b.dataset.reports || 0);
                            break;
                        default:
                            return 0;
                    }

                    if (aValue < bValue) return -1 * direction;
                    if (aValue > bValue) return 1 * direction;
                    return 0;
                });

                // Reordenar las filas
                rows.forEach(row => {
                    clientsTableBody.appendChild(row);
                    const reportsRow = document.getElementById(`reports-${row.id}`);
                    if (reportsRow) {
                        clientsTableBody.appendChild(reportsRow);
                    }
                });

                // Actualizar iconos de ordenamiento
                document.querySelectorAll('.sort-icon').forEach(icon => {
                    icon.textContent = '↕';
                });
                const currentIcon = document.querySelector(`th[onclick="sortTable('${column}')"] .sort-icon`);
                currentIcon.textContent = direction === 1 ? '↓' : '↑';
            }

            // Event listeners
            searchInput.addEventListener('input', filterClients);
            filterReports.addEventListener('change', filterClients);

            // Exponer la función sortTable globalmente
            window.sortTable = sortTable;
        });

        function toggleReports(clientId) {
            const reportsRow = document.getElementById(`reports-${clientId}`);
            if (reportsRow) {
                reportsRow.classList.toggle('hidden');
            }
        }
    </script>

@endsection
