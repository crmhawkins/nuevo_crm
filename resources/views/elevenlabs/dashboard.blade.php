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
    .alert-box.danger {
        border-color: #dc3545;
    }
    .alert-box.warning {
        border-color: #ffc107;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 30px;
    }
    .sync-status {
        padding: 10px;
        border-radius: 5px;
        background: #e7f3ff;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="fas fa-phone-alt"></i> Monitoreo de Llamadas - Eleven Labs</h3>
                <p class="text-subtitle text-muted">Análisis y categorización de conversaciones</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <div class="float-end">
                    <button class="btn btn-primary" onclick="sincronizar()">
                        <i class="fas fa-sync-alt"></i> Sincronizar
                    </button>
                    <a href="{{ route('elevenlabs.conversations') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Ver Todas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro de Fechas -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form method="GET" action="{{ route('elevenlabs.dashboard') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fecha Inicio</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Fin</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estado de Sincronización -->
    @if($lastSync)
    <div class="card-body">
        <div class="sync-status">
            <i class="fas fa-info-circle"></i>
            <strong>Última sincronización:</strong> {{ $lastSync->sync_finished_at->diffForHumans() }}
            - {{ $lastSync->conversations_new }} nuevas, {{ $lastSync->conversations_updated }} actualizadas
        </div>
    </div>
    @endif

    <!-- Tarjetas de Estadísticas -->
    <div class="card-body">
        <div class="row">
            <!-- Total Conversaciones -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Total Conversaciones</div>
                    <div class="stat-value">{{ $stats['total_conversations'] }}</div>
                    <i class="fas fa-comments fa-2x" style="opacity: 0.3; position: absolute; right: 20px; bottom: 20px;"></i>
                </div>
            </div>

            <!-- Procesadas -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Procesadas</div>
                    <div class="stat-value">{{ $stats['processed_conversations'] }}</div>
                    <i class="fas fa-check-circle fa-2x" style="opacity: 0.3; position: absolute; right: 20px; bottom: 20px;"></i>
                </div>
            </div>

            <!-- Duración Promedio -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Duración Promedio</div>
                    <div class="stat-value">{{ gmdate('i:s', $stats['average_duration']) }}</div>
                    <i class="fas fa-clock fa-2x" style="opacity: 0.3; position: absolute; right: 20px; bottom: 20px;"></i>
                </div>
            </div>

            <!-- Índice de Satisfacción -->
            <div class="col-md-3">
                <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
                    <div class="stat-label" style="color: rgba(255,255,255,0.8);">Satisfacción</div>
                    <div class="stat-value">{{ number_format($stats['satisfaction_rate'], 1) }}%</div>
                    <i class="fas fa-smile fa-2x" style="opacity: 0.3; position: absolute; right: 20px; bottom: 20px;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if($alerts['quejas'] > 0 || $alerts['bajas'] > 0 || $alerts['necesitan_asistencia'] > 0)
    <div class="card-body">
        <h5><i class="fas fa-exclamation-triangle"></i> Alertas</h5>
        <div class="row">
            @if($alerts['quejas'] > 0)
            <div class="col-md-4">
                <div class="alert-box danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <strong>{{ $alerts['quejas'] }}</strong> Quejas registradas
                </div>
            </div>
            @endif

            @if($alerts['bajas'] > 0)
            <div class="col-md-4">
                <div class="alert-box danger">
                    <i class="fas fa-user-times"></i>
                    <strong>{{ $alerts['bajas'] }}</strong> Solicitudes de baja
                </div>
            </div>
            @endif

            @if($alerts['necesitan_asistencia'] > 0)
            <div class="col-md-4">
                <div class="alert-box warning">
                    <i class="fas fa-hand-paper"></i>
                    <strong>{{ $alerts['necesitan_asistencia'] }}</strong> Requieren asistencia extra
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Gráficas -->
    <div class="card-body">
        <div class="row">
            <!-- Gráfica de Categorías -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Distribución por Categorías</h5>
                        <div class="chart-container">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Tendencia -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Tendencia de Conversaciones</h5>
                        <div class="chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimas Conversaciones -->
    <div class="card-body">
        <h5><i class="fas fa-history"></i> Últimas Conversaciones</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Duración</th>
                        <th>Categoría</th>
                        <th>Confianza</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentConversations as $conversation)
                    <tr>
                        <td>{{ $conversation->conversation_date->format('d/m/Y H:i') }}</td>
                        <td>{{ $conversation->client->name ?? 'N/A' }}</td>
                        <td>{{ $conversation->duration_formatted }}</td>
                        <td>
                            @if($conversation->category)
                                <span class="category-badge" style="background-color: {{ config('elevenlabs.categories.'.$conversation->category.'.color') }}">
                                    {{ $conversation->category_label }}
                                </span>
                            @else
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
                            <a href="{{ route('elevenlabs.conversation.show', $conversation->id) }}" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay conversaciones recientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
// Datos de categorías
const categoryData = @json($categoryStats);
const categoryLabels = categoryData.map(item => {
    const category = @json(config('elevenlabs.categories'));
    return category[item.category]?.label || item.category;
});
const categoryCounts = categoryData.map(item => item.count);
const categoryColors = categoryData.map(item => {
    const category = @json(config('elevenlabs.categories'));
    return category[item.category]?.color || '#6B7280';
});

// Gráfica de Categorías (Pie Chart)
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
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Gráfica de Tendencia (cargar vía AJAX)
loadTrendData();

function loadTrendData() {
    const startDate = '{{ $startDate->format('Y-m-d') }}';
    const endDate = '{{ $endDate->format('Y-m-d') }}';
    
    fetch(`{{ route('elevenlabs.stats') }}?start_date=${startDate}&end_date=${endDate}`)
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
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        });
}

// Función de sincronización
function sincronizar() {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sincronizando...';
    btn.disabled = true;

    fetch('{{ route('elevenlabs.sync') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    })
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
@endsection

