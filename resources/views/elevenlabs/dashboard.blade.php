@extends('layouts.app')

@section('titulo', 'Dashboard - Monitoreo de Llamadas')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .stat-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .stat-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin: 10px 0;
    }
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    .category-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
    }
    .alert-box {
        border-left: 4px solid;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        background: #f8f9fa;
    }
    .alert-box.danger { border-color: #dc3545; }
    .alert-box.warning { border-color: #ffc107; }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 30px;
    }

    /* Estilos de paginación */
    .pagination {
        margin-bottom: 0;
    }
    .pagination .page-link {
        color: #667eea;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 5px;
        transition: all 0.3s;
    }
    .pagination .page-link:hover {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
    }
    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
        font-weight: bold;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3><i class="fas fa-phone-alt"></i> Monitoreo de Llamadas - Eleven Labs</h3>
                <p class="text-subtitle text-muted">Análisis IA de conversaciones</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="float-end">
                    <a href="{{ route('elevenlabs.agents') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-robot"></i> Agentes
                    </a>
                    <a href="{{ route('elevenlabs.conversations') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Todas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversaciones Recientes -->
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-list"></i> Conversaciones Recientes</h5>
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('elevenlabs.dashboard', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => 'conversation_date', 'sort_order' => $sortBy === 'conversation_date' && $sortOrder === 'desc' ? 'asc' : 'desc'])) }}"
                   class="btn btn-outline-primary {{ $sortBy === 'conversation_date' ? 'active' : '' }}">
                    <i class="fas fa-calendar"></i> Fecha
                    @if($sortBy === 'conversation_date')
                        <i class="fas fa-sort-{{ $sortOrder === 'desc' ? 'down' : 'up' }}"></i>
                    @endif
                </a>
                <a href="{{ route('elevenlabs.dashboard', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => 'sentiment_category', 'sort_order' => $sortBy === 'sentiment_category' && $sortOrder === 'desc' ? 'asc' : 'desc'])) }}"
                   class="btn btn-outline-primary {{ $sortBy === 'sentiment_category' ? 'active' : '' }}">
                    <i class="fas fa-smile"></i> Sentimiento
                    @if($sortBy === 'sentiment_category')
                        <i class="fas fa-sort-{{ $sortOrder === 'desc' ? 'down' : 'up' }}"></i>
                    @endif
                </a>
                <a href="{{ route('elevenlabs.dashboard', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => 'specific_category', 'sort_order' => $sortBy === 'specific_category' && $sortOrder === 'desc' ? 'asc' : 'desc'])) }}"
                   class="btn btn-outline-primary {{ $sortBy === 'specific_category' ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Categoría
                    @if($sortBy === 'specific_category')
                        <i class="fas fa-sort-{{ $sortOrder === 'desc' ? 'down' : 'up' }}"></i>
                    @endif
                </a>
                <a href="{{ route('elevenlabs.dashboard', array_merge(request()->except(['sort_by', 'sort_order', 'page']), ['sort_by' => 'duration_seconds', 'sort_order' => $sortBy === 'duration_seconds' && $sortOrder === 'desc' ? 'asc' : 'desc'])) }}"
                   class="btn btn-outline-primary {{ $sortBy === 'duration_seconds' ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Duración
                    @if($sortBy === 'duration_seconds')
                        <i class="fas fa-sort-{{ $sortOrder === 'desc' ? 'down' : 'up' }}"></i>
                    @endif
                </a>
            </div>
        </div>

        <!-- Filtros Avanzados -->
        <form method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Rango de Fechas</label>
                <input type="text" name="date_range" id="dateRangeFilter" class="form-control form-control-sm"
                       value="{{ request('date_range') }}" placeholder="Seleccionar rango">
            </div>
            <div class="col-md-2">
                <label class="form-label">Agente</label>
                <select name="agent_id" id="agentFilter" class="form-select form-select-sm" onchange="loadAgentCategories(this.value)">
                    <option value="">Todos los agentes</option>
                    @php
                        $agents = \App\Models\ElevenlabsAgent::orderBy('name')->get();
                    @endphp
                    @foreach($agents as $agent)
                        <option value="{{ $agent->agent_id }}" {{ request('agent_id') === $agent->agent_id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select name="category" id="categoryFilter" class="form-select form-select-sm">
                    <option value="">Todas las categorías</option>
                    <option value="contento" {{ request('category') === 'contento' ? 'selected' : '' }}>😊 Contento</option>
                    <option value="descontento" {{ request('category') === 'descontento' ? 'selected' : '' }}>😞 Descontento</option>
                    <option value="sin_respuesta" {{ request('category') === 'sin_respuesta' ? 'selected' : '' }}>📵 Sin Respuesta</option>
                    <option value="baja" {{ request('category') === 'baja' ? 'selected' : '' }}>🚫 Baja</option>
                    <option value="llamada_agendada" {{ request('category') === 'llamada_agendada' ? 'selected' : '' }}>📅 Llamada Agendada</option>
                    <option value="respuesta_ia" {{ request('category') === 'respuesta_ia' ? 'selected' : '' }}>🤖 Respuesta IA/Contestador</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Opciones</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="hide_no_response" id="hideNoResponse"
                           value="1" {{ $hideNoResponse ? 'checked' : '' }}>
                    <label class="form-check-label" for="hideNoResponse">
                        <small>Ocultar "Sin Respuesta"</small>
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="{{ route('elevenlabs.dashboard') }}" class="btn btn-secondary btn-sm w-100">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            </div>

            <!-- Mantener ordenamiento -->
            <input type="hidden" name="sort_by" value="{{ $sortBy }}">
            <input type="hidden" name="sort_order" value="{{ $sortOrder }}">
        </form>

        <!-- Selector de resultados por página y acciones -->
        <div class="row mb-3 align-items-center">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <label class="me-2 mb-0" style="white-space: nowrap;">Mostrar:</label>
                    <select id="perPageSelect" class="form-select form-select-sm" onchange="changePerPage(this.value)">
                        <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <span class="ms-2 text-muted" style="white-space: nowrap;">por página</span>
                </div>
            </div>
            <div class="col-md-9 text-end">
                <span id="selectedCount" class="badge bg-primary me-2" style="display: none;">0 seleccionadas</span>
                <button id="bulkAttendedBtn" class="btn btn-sm btn-success me-2" style="display: none;" onclick="markSelectedAsAttended()">
                    <i class="fas fa-check"></i> Marcar como atendidas
                </button>
                <button id="clearSelectionBtn" class="btn btn-sm btn-secondary" style="display: none;" onclick="clearSelection()">
                    <i class="fas fa-times"></i> Limpiar selección
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="conversationsTable">
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                        </th>
                        <th>Fecha</th>
                        <th>Agente</th>
                        <th>Cliente</th>
                        <th>Duración</th>
                        <th>Categoría</th>
                        <th>Confianza</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentConversations as $conversation)
                    <tr class="{{ $conversation->attended ? 'table-success' : '' }}" style="{{ $conversation->attended ? 'opacity: 0.7; font-weight: 500;' : '' }}" data-conversation-id="{{ $conversation->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input conversation-checkbox"
                                   value="{{ $conversation->id }}"
                                   onchange="updateSelectionCount()"
                                   {{ $conversation->attended ? 'disabled' : '' }}>
                        </td>
                        <td>
                            @if($conversation->attended)
                                <i class="fas fa-check-circle text-success" title="Atendida"></i>
                            @endif
                            {{ $conversation->conversation_date->copy()->addHours(2)->format('d/m/Y H:i') }}
                        </td>
                        <td><small class="text-muted">{{ $conversation->agent_name ?? 'N/A' }}</small></td>
                        <td>{{ $conversation->client->name ?? 'N/A' }}</td>
                        <td>{{ $conversation->duration_formatted }}</td>
                        <td>
                            @if($conversation->sentiment_category)
                                <span class="category-badge" style="background-color: {{ $conversation->sentiment_color }}; font-size: 0.75rem;">
                                    {{ $conversation->sentiment_label }}
                                </span>
                            @endif
                            @if($conversation->specific_category)
                                <span class="category-badge" style="background-color: {{ $conversation->specific_color }}; font-size: 0.75rem;">
                                    {{ $conversation->specific_label }}
                                </span>
                            @endif
                            @if(!$conversation->sentiment_category && !$conversation->specific_category)
                                <span class="badge bg-secondary">Sin categoría</span>
                            @endif
                        </td>
                        <td>
                            @if($conversation->confidence_score)
                                {{ number_format($conversation->confidence_score * 100, 1) }}%
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="verConversacion({{ $conversation->id }})">
                                <i class="fas fa-eye"></i> Ver
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">No hay conversaciones {{ $categoryFilter ? 'con esta categoría' : '' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div class="text-muted">
                <small>
                    Mostrando {{ $recentConversations->firstItem() ?? 0 }} a {{ $recentConversations->lastItem() ?? 0 }} de {{ $recentConversations->total() }} conversaciones
                </small>
            </div>
            <div>
                {{ $recentConversations->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    <!-- Información del período -->
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h4 class="mb-3"><i class="fas fa-chart-line"></i> Estadísticas y Análisis</h4>
                <span class="badge bg-secondary">Últimos 30 días</span>
                <small class="text-muted ms-2">
                    <i class="fas fa-sync-alt"></i> Sincronización automática cada 10 minutos
                </small>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Total Conversaciones</div>
                    <div class="stat-value">{{ $stats['total_conversations'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Últimos 30 días</div>
                    <div class="stat-value">{{ $stats['last_30_days'] }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Duración Media</div>
                    <div class="stat-value">{{ gmdate('i:s', $stats['average_duration']) }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Satisfacción</div>
                    <div class="stat-value">{{ number_format($stats['satisfaction_rate'], 1) }}%</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Sentimientos -->
    <div class="card-body">
        <h5><i class="fas fa-chart-pie"></i> Distribución de Sentimientos (Últimos 30 días)</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="alert-box" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white;">
                    <i class="fas fa-smile"></i> <strong>{{ $alerts['contentos'] }}</strong> Contentos
                    <small class="d-block mt-1">{{ $stats['total_conversations'] > 0 ? round(($alerts['contentos'] / $stats['last_30_days']) * 100, 1) : 0 }}%</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert-box" style="background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%); color: white;">
                    <i class="fas fa-frown"></i> <strong>{{ $alerts['descontentos'] }}</strong> Descontentos
                    <small class="d-block mt-1">{{ $stats['total_conversations'] > 0 ? round(($alerts['descontentos'] / $stats['last_30_days']) * 100, 1) : 0 }}%</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert-box" style="background: #e2e8f0; color: #64748b;">
                    <i class="fas fa-phone-slash"></i> <strong>{{ $alerts['sin_respuesta'] }}</strong> Sin respuesta
                    <small class="d-block mt-1">{{ $stats['total_conversations'] > 0 ? round(($alerts['sin_respuesta'] / $stats['last_30_days']) * 100, 1) : 0 }}%</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Distribución por Categorías</h5>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5>Tendencia de Conversaciones</h5>
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver conversación -->
<div class="modal fade" id="conversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-phone-alt"></i> Detalle de Conversación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conversationContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Cargando conversación...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnAtendido" onclick="toggleAttendedModal()">
                    <i class="fas fa-check"></i> <span id="btnAtendidoText">Marcar como Atendido</span>
                </button>
                <button type="button" class="btn btn-warning" id="btnReprocesar" onclick="reprocesarModal()">
                    <i class="fas fa-redo"></i> Reprocesar con IA
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Función para formatear fechas sin conversión de timezone
function formatDateWithoutTimezone(dateString) {
    if (!dateString) return '';

    // Parsear directamente desde el string sin crear objeto Date
    // Formato esperado: "YYYY-MM-DD HH:MM:SS" o "YYYY-MM-DD HH:MM"
    const parts = dateString.split(/[-: T]/);
    const year = parts[0];
    const month = parts[1];
    const day = parts[2];
    const hour = parts[3] || '00';
    const minute = parts[4] || '00';

    // Formatear directamente sin conversiones
    return `${day}/${month}/${year}, ${hour}:${minute}`;
}

const categoryData = @json($categoryStats);
const agentCategories = @json($agentCategories);
const agentCategoryColors = @json($agentCategoryColors);
const configCategories = @json(config('elevenlabs.categories'));

console.log('Category Data:', categoryData);
console.log('Agent Categories:', agentCategories);
console.log('Agent Colors:', agentCategoryColors);
console.log('Config Categories:', configCategories);

const categoryLabels = categoryData.map(item => {
    const categoryKey = item.category;

    // Buscar en categorías de agentes primero
    if (agentCategories[categoryKey]) {
        return agentCategories[categoryKey];
    }

    // Fallback a configuración general
    if (configCategories[categoryKey]) {
        return configCategories[categoryKey].label;
    }

    // Último fallback: mostrar la key tal cual
    return categoryKey;
});

const categoryCounts = categoryData.map(item => item.count);

const categoryColors = categoryData.map(item => {
    const categoryKey = item.category;

    // Buscar en categorías de agentes primero
    if (agentCategoryColors[categoryKey]) {
        return agentCategoryColors[categoryKey];
    }

    // Fallback a configuración general
    if (configCategories[categoryKey]) {
        return configCategories[categoryKey].color;
    }

    // Último fallback: color por defecto
    return '#6B7280';
});

console.log('Labels:', categoryLabels);
console.log('Colors:', categoryColors);

// Pie Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryCounts,
            backgroundColor: categoryColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Tendencia
loadTrendData();

function loadTrendData() {
    // Calcular últimos 30 días
    const endDate = new Date();
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 365);

    const startDateStr = startDate.toISOString().split('T')[0];
    const endDateStr = endDate.toISOString().split('T')[0];

    fetch(`{{ route("elevenlabs.stats") }}?start_date=${startDateStr}&end_date=${endDateStr}`)
        .then(response => response.json())
        .then(data => {
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: data.timeline.labels,
                    datasets: [{
                        label: 'Conversaciones por día',
                        data: data.timeline.data,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        });
}

let currentConversationId = null;
let currentConversationAttended = false;

function verConversacion(id) {
    currentConversationId = id;
    const modal = new bootstrap.Modal(document.getElementById('conversationModal'));
    modal.show();

    document.getElementById('conversationContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Cargando conversación...</p>
        </div>
    `;

    fetch(`/api/elevenlabs-monitoring/conversations/${id}`, {
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer {{ auth()->user()->createToken("temp")->plainTextToken ?? "" }}'
        }
    })
    .then(r => r.json())
    .then(conv => {
        currentConversationData = conv; // Guardar datos para edición
        currentConversationAttended = conv.attended || false;

        // Actualizar botón de atendido
        updateAttendedButton(currentConversationAttended);

        document.getElementById('conversationContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-info-circle"></i> Información</h6>
                            <p><strong>ID:</strong> ${conv.conversation_id}</p>
                            <p><strong>Fecha:</strong> ${new Date(conv.conversation_date).toLocaleString('es-ES')}</p>
                            ${conv.agent_name ? `<p><strong>Agente:</strong> <i class="fas fa-robot"></i> ${conv.agent_name}</p>` : ''}
                            <p><strong>Duración:</strong> ${conv.duration_formatted || '0:00'}</p>
                            ${conv.client ? `<p><strong>Cliente:</strong> ${conv.client.name || 'N/A'}</p>` : ''}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-brain"></i> Análisis IA</h6>

                            <!-- Categorías actuales con opción de editar -->
                            <div id="currentCategories">
                                ${['baja', 'llamada_agendada'].includes(conv.sentiment_category) ? `
                                    <p><strong>Acción:</strong>
                                        <span class="category-badge" style="background-color: ${conv.sentiment_color || '#6B7280'}">${conv.sentiment_label || conv.sentiment_category}</span>
                                    </p>
                                ` : `
                                    <p><strong>Sentimiento:</strong>
                                        ${conv.sentiment_category ? `<span class="category-badge" style="background-color: ${conv.sentiment_color || '#6B7280'}">${conv.sentiment_label || conv.sentiment_category}</span>` : '<span class="text-muted">-</span>'}
                                    </p>
                                    <p><strong>Categoría Específica:</strong>
                                        ${conv.specific_category ? `<span class="category-badge" style="background-color: ${conv.specific_color || '#6B7280'}">${conv.specific_label || conv.specific_category}</span>` : '<span class="text-muted">-</span>'}
                                    </p>
                                `}
                                ${conv.confidence_score ? `<p><strong>Confianza:</strong> ${(conv.confidence_score * 100).toFixed(1)}%</p>` : ''}
                                <button class="btn btn-sm btn-outline-secondary mt-2" onclick="toggleEditCategories()">
                                    <i class="fas fa-edit"></i> Cambiar Categorías
                                </button>
                            </div>

                            <!-- Editor de categorías (oculto por defecto) -->
                            <div id="editCategories" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Editando categorías manualmente
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Sentimiento/Acción:</strong></label>
                                    <select id="sentimentSelect" class="form-select form-select-sm">
                                        <option value="">Cargando...</option>
                                    </select>
                                    <small class="text-muted">Contento, Descontento, Sin Respuesta, Baja, Llamada Agendada</small>
                                </div>
                                <div class="mb-3" id="specificContainer">
                                    <label class="form-label"><strong>Categoría Específica:</strong></label>
                                    <select id="specificSelect" class="form-select form-select-sm">
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>
                                <button class="btn btn-sm btn-success" onclick="saveCategories()">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <button class="btn btn-sm btn-secondary" onclick="toggleEditCategories()">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>

                            ${conv.scheduled_call_datetime ? `
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-calendar-check"></i> <strong>Llamada Agendada:</strong><br>
                                    📅 ${formatDateWithoutTimezone(conv.scheduled_call_datetime)}<br>
                                    ${conv.scheduled_call_notes ? `📝 ${conv.scheduled_call_notes}` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
            ${conv.summary_es ? `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6><i class="fas fa-file-alt"></i> Resumen</h6>
                        <div class="p-3" style="background: #e7f3ff; border-left: 4px solid #0d6efd; border-radius: 5px;">
                            ${conv.summary_es}
                        </div>
                    </div>
                </div>
            ` : ''}
            ${conv.transcript ? `
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-comment-dots"></i> Transcripción Completa</h6>
                        <div class="p-3" style="background: #f8f9fa; border-left: 4px solid #667eea; border-radius: 5px; font-family: 'Courier New', monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;">
${conv.transcript}
                        </div>
                    </div>
                </div>
            ` : '<p class="text-muted">Sin transcripción disponible</p>'}
        `;
    })
    .catch(e => {
        document.getElementById('conversationContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Error al cargar: ${e.message}
            </div>
        `;
    });
}

// Ya no es necesaria - el color viene desde la BD

let availableCategories = null;
let currentConversationData = null;

function toggleEditCategories() {
    const currentDiv = document.getElementById('currentCategories');
    const editDiv = document.getElementById('editCategories');

    if (editDiv.style.display === 'none') {
        // Mostrar editor
        loadCategoriesForEdit();
        currentDiv.style.display = 'none';
        editDiv.style.display = 'block';
    } else {
        // Ocultar editor
        currentDiv.style.display = 'block';
        editDiv.style.display = 'none';
    }
}

function loadCategoriesForEdit() {
    if (!currentConversationData || !currentConversationData.agent_id) {
        console.error('No hay datos de conversación o agent_id');
        return;
    }

    console.log('Cargando categorías para agente:', currentConversationData.agent_id);

    fetch(`/api/elevenlabs-monitoring/agents/${currentConversationData.agent_id}/available-categories`)
        .then(r => r.json())
        .then(data => {
            console.log('Categorías recibidas:', data);
            if (data.success) {
                availableCategories = data;
                populateCategorySelects();
            } else {
                console.error('Error en respuesta:', data);
            }
        })
        .catch(e => console.error('Error cargando categorías:', e));
}

function populateCategorySelects() {
    const sentimentSelect = document.getElementById('sentimentSelect');
    const specificSelect = document.getElementById('specificSelect');

    if (!sentimentSelect || !specificSelect) {
        console.error('Selectores no encontrados');
        return;
    }

    console.log('Poblando selectores con:', availableCategories);

    // Poblar sentimientos
    sentimentSelect.innerHTML = '<option value="">Seleccionar...</option>';
    if (availableCategories.sentiment_categories) {
        availableCategories.sentiment_categories.forEach(cat => {
            const selected = currentConversationData.sentiment_category === cat.category_key ? 'selected' : '';
            sentimentSelect.innerHTML += `<option value="${cat.category_key}" ${selected}>${cat.category_label}</option>`;
        });
    }

    // Poblar específicas
    specificSelect.innerHTML = '<option value="">Sin categoría específica</option>';
    if (availableCategories.specific_categories) {
        availableCategories.specific_categories.forEach(cat => {
            const selected = currentConversationData.specific_category === cat.category_key ? 'selected' : '';
            specificSelect.innerHTML += `<option value="${cat.category_key}" ${selected}>${cat.category_label}</option>`;
        });
    }

    // Listener para deshabilitar específica si es baja o sin_respuesta
    sentimentSelect.addEventListener('change', function() {
        const specificContainer = document.getElementById('specificContainer');
        if (['baja', 'sin_respuesta', 'llamada_agendada'].includes(this.value)) {
            specificSelect.value = '';
            specificContainer.style.display = 'none';
        } else {
            specificContainer.style.display = 'block';
        }
    });

    // Verificar estado inicial
    if (['baja', 'sin_respuesta', 'llamada_agendada'].includes(currentConversationData.sentiment_category)) {
        document.getElementById('specificContainer').style.display = 'none';
    }
}

function saveCategories() {
    const sentiment = document.getElementById('sentimentSelect').value;
    const specific = document.getElementById('specificSelect').value;

    const btn = event.target;
    const html = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    btn.disabled = true;

    fetch(`/api/elevenlabs-monitoring/conversations/${currentConversationId}/update-categories`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sentiment_category: sentiment || null,
            specific_category: specific || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            // Recargar conversación
            verConversacion(currentConversationId);
            toggleEditCategories();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .finally(() => {
        btn.innerHTML = html;
        btn.disabled = false;
    });
}

function reprocesarModal() {
    if (!currentConversationId || !confirm('¿Reprocesar esta conversación con la IA?')) return;

    const btn = document.getElementById('btnReprocesar');
    const html = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reprocesando...';
    btn.disabled = true;

    fetch(`/elevenlabs/conversations/${currentConversationId}/reprocess`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('conversationModal')).hide();
            setTimeout(() => location.reload(), 1000);
        }
    })
    .finally(() => {
        btn.innerHTML = html;
        btn.disabled = false;
    });
}

// Cargar categorías del agente seleccionado
function loadAgentCategories(agentId) {
    const categorySelect = document.getElementById('categoryFilter');

    if (!agentId) {
        // Resetear a categorías generales
        categorySelect.innerHTML = `
            <option value="">Todas las categorías</option>
            <option value="contento">😊 Contento</option>
            <option value="descontento">😞 Descontento</option>
            <option value="sin_respuesta">📵 Sin Respuesta</option>
            <option value="baja">🚫 Baja</option>
            <option value="llamada_agendada">📅 Llamada Agendada</option>
            <option value="respuesta_ia">🤖 Respuesta IA/Contestador</option>
        `;
        return;
    }

    // Cargar categorías del agente
    fetch(`/api/elevenlabs-monitoring/agents/${agentId}/available-categories`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                categorySelect.innerHTML = '<option value="">Todas las categorías</option>';

                // Agregar sentimientos/acciones
                data.sentiment_categories.forEach(cat => {
                    categorySelect.innerHTML += `<option value="${cat.category_key}">${cat.category_label}</option>`;
                });

                // Agregar categorías específicas del agente
                if (data.specific_categories.length > 0) {
                    categorySelect.innerHTML += '<option disabled>───────────</option>';
                    data.specific_categories.forEach(cat => {
                        categorySelect.innerHTML += `<option value="${cat.category_key}">${cat.category_label}</option>`;
                    });
                }
            }
        });
}

// Cargar categorías del agente al inicio si hay agente seleccionado
document.addEventListener('DOMContentLoaded', function() {
    const agentId = document.getElementById('agentFilter')?.value;
    if (agentId) {
        loadAgentCategories(agentId);
    }
});

// Funciones para marcar como atendido
function updateAttendedButton(attended) {
    const btn = document.getElementById('btnAtendido');
    const text = document.getElementById('btnAtendidoText');

    if (attended) {
        btn.className = 'btn btn-outline-success';
        text.textContent = 'Atendida ✓';
    } else {
        btn.className = 'btn btn-success';
        text.textContent = 'Marcar como Atendido';
    }
}

function toggleAttendedModal() {
    if (!currentConversationId) return;

    const btn = document.getElementById('btnAtendido');
    const text = document.getElementById('btnAtendidoText');
    const wasAttended = currentConversationAttended;

    btn.disabled = true;
    text.textContent = wasAttended ? 'Desmarcando...' : 'Marcando...';

    const url = wasAttended
        ? `/elevenlabs/conversations/${currentConversationId}/unmark-attended`
        : `/elevenlabs/conversations/${currentConversationId}/mark-attended`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentConversationAttended = data.attended;
            updateAttendedButton(data.attended);

            // Recargar la página para actualizar la tabla
            setTimeout(() => location.reload(), 500);
        }
    })
    .catch(e => {
        alert('Error: ' + e.message);
    })
    .finally(() => {
        btn.disabled = false;
    });
}

// La sincronización ahora es automática cada 10 minutos

// ============================================
// FUNCIONES DE PAGINACIÓN Y SELECCIÓN MÚLTIPLE
// ============================================

// Cambiar resultados por página
function changePerPage(perPage) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', perPage);
    url.searchParams.set('page', 1); // Resetear a primera página
    window.location.href = url.toString();
}

// Seleccionar/deseleccionar todos (solo página actual)
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.conversation-checkbox:not([disabled])');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateSelectionCount();
}

// Actualizar contador de selección y visibilidad de botones
function updateSelectionCount() {
    const selectedCheckboxes = document.querySelectorAll('.conversation-checkbox:checked');
    const count = selectedCheckboxes.length;
    const totalCheckboxes = document.querySelectorAll('.conversation-checkbox:not([disabled])').length;

    // Actualizar badge contador
    const countBadge = document.getElementById('selectedCount');
    const bulkBtn = document.getElementById('bulkAttendedBtn');
    const clearBtn = document.getElementById('clearSelectionBtn');
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');

    if (count > 0) {
        countBadge.textContent = count + ' seleccionada' + (count > 1 ? 's' : '');
        countBadge.style.display = 'inline-block';
        bulkBtn.style.display = 'inline-block';
        clearBtn.style.display = 'inline-block';
    } else {
        countBadge.style.display = 'none';
        bulkBtn.style.display = 'none';
        clearBtn.style.display = 'none';
    }

    // Actualizar estado del checkbox "Seleccionar todos"
    if (count === 0) {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
    } else if (count === totalCheckboxes) {
        selectAllCheckbox.checked = true;
        selectAllCheckbox.indeterminate = false;
    } else {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = true;
    }
}

// Limpiar selección
function clearSelection() {
    document.querySelectorAll('.conversation-checkbox').forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
    updateSelectionCount();
}

// Marcar seleccionadas como atendidas
function markSelectedAsAttended() {
    const selectedCheckboxes = document.querySelectorAll('.conversation-checkbox:checked');
    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);

    if (selectedIds.length === 0) {
        alert('Por favor selecciona al menos una conversación');
        return;
    }

    if (!confirm(`¿Marcar ${selectedIds.length} conversación(es) como atendida(s)?`)) {
        return;
    }

    const btn = document.getElementById('bulkAttendedBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
    btn.disabled = true;

    // Procesar en lote
    Promise.all(selectedIds.map(id =>
        fetch(`/elevenlabs/conversations/${id}/mark-attended`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(r => r.json())
    ))
    .then(results => {
        const successful = results.filter(r => r.success).length;
        const failed = results.length - successful;

        let message = `✅ ${successful} conversación(es) marcada(s) como atendida(s)`;
        if (failed > 0) {
            message += `\n❌ ${failed} fallaron`;
        }

        alert(message);

        // Recargar página para ver cambios
        setTimeout(() => location.reload(), 500);
    })
    .catch(error => {
        alert('Error al procesar las conversaciones: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}

// Inicializar contador al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    updateSelectionCount();
});

</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#dateRangeFilter", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: "es",
        maxDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            // Auto-submit cuando se selecciona el rango completo
            if (selectedDates.length === 2) {
                // instance.element.form.submit();
            }
        }
    });
});
</script>
@endsection

