@extends('layouts.app')

@section('title', 'Estadísticas de Visitas Comerciales')

@section('content')
<style>
/* Estilos forzados para las tarjetas de estadísticas */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    border-radius: 15px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
    height: 180px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 10px !important;
    position: relative !important;
    overflow: hidden !important;
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
}

.stats-card-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
.stats-card-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
.stats-card-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
.stats-card-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important; }

.stats-content {
    position: relative !important;
    z-index: 2 !important;
    text-align: center !important;
    color: white !important;
    padding: 20px !important;
}

.stats-icon {
    font-size: 3rem !important;
    color: white !important;
    margin-bottom: 15px !important;
    display: block !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
}

.stats-number {
    font-size: 3rem !important;
    font-weight: bold !important;
    color: white !important;
    margin: 0 !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
    line-height: 1 !important;
}

.stats-label {
    font-size: 1.1rem !important;
    color: white !important;
    margin: 0 !important;
    opacity: 0.9 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3) !important;
    font-weight: 500 !important;
}

.stats-row {
    display: flex !important;
    flex-wrap: wrap !important;
    justify-content: center !important;
    margin: 20px 0 !important;
    gap: 20px !important;
}

.stats-col {
    flex: 0 0 calc(25% - 20px) !important;
    min-width: 250px !important;
    max-width: 300px !important;
}

@media (max-width: 1200px) {
    .stats-col { flex: 0 0 calc(50% - 20px) !important; }
}

@media (max-width: 768px) {
    .stats-col { flex: 0 0 100% !important; }
}
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Estadísticas de Visitas Comerciales
                        </h4>
                        <a href="{{ route('visitas-comerciales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Debug Info -->
                    <div class="alert alert-info">
                        <h5>Debug Info:</h5>
                        <p>Total Visitas: {{ $totalVisitas ?? 'N/A' }}</p>
                        <p>Presenciales: {{ $visitasPresenciales ?? 'N/A' }}</p>
                        <p>Telefónicas: {{ $visitasTelefonicas ?? 'N/A' }}</p>
                        <p>Con Audio: {{ $visitasConAudio ?? 'N/A' }}</p>
                        <p>Visitas por Mes: {{ $visitasPorMes->count() ?? 'N/A' }}</p>
                        <p>Visitas por Comercial: {{ $visitasPorComercial->count() ?? 'N/A' }}</p>
                        <p>Estados: {{ $estadosPropuestas->count() ?? 'N/A' }}</p>
                        <p><strong>Bootstrap cargado:</strong> <span id="bootstrap-status">Verificando...</span></p>
                        <p><strong>Chart.js cargado:</strong> <span id="chartjs-status">Verificando...</span></p>
                    </div>

                    <!-- Resumen General -->
                    <div class="stats-row">
                        <div class="stats-col">
                            <div class="stats-card stats-card-1">
                                <div class="stats-content">
                                    <i class="fas fa-handshake stats-icon"></i>
                                    <div class="stats-number">{{ $totalVisitas ?? 0 }}</div>
                                    <div class="stats-label">Total Visitas</div>
                                </div>
                            </div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-card stats-card-2">
                                <div class="stats-content">
                                    <i class="fas fa-handshake stats-icon"></i>
                                    <div class="stats-number">{{ $visitasPresenciales ?? 0 }}</div>
                                    <div class="stats-label">Presenciales</div>
                                </div>
                            </div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-card stats-card-3">
                                <div class="stats-content">
                                    <i class="fas fa-phone stats-icon"></i>
                                    <div class="stats-number">{{ $visitasTelefonicas ?? 0 }}</div>
                                    <div class="stats-label">Telefónicas</div>
                                </div>
                            </div>
                        </div>
                        <div class="stats-col">
                            <div class="stats-card stats-card-4">
                                <div class="stats-content">
                                    <i class="fas fa-microphone stats-icon"></i>
                                    <div class="stats-number">{{ $visitasConAudio ?? 0 }}</div>
                                    <div class="stats-label">Con Audio</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Gráfico de Visitas por Mes -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>Visitas por Mes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="visitasPorMesChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Visitas por Comercial -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-users me-2"></i>Visitas por Comercial
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="visitasPorComercialChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Estados de Propuestas -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-chart-pie me-2"></i>Estados de Propuestas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="estadosPropuestasChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Tabla de Comerciales -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>Ranking de Comerciales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Posición</th>
                                                    <th>Comercial</th>
                                                    <th>Visitas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($visitasPorComercial as $index => $comercial)
                                                    <tr>
                                                        <td>
                                                            @if($index == 0)
                                                                <i class="fas fa-trophy text-warning"></i>
                                                            @elseif($index == 1)
                                                                <i class="fas fa-medal text-secondary"></i>
                                                            @elseif($index == 2)
                                                                <i class="fas fa-award text-warning"></i>
                                                            @else
                                                                <span class="badge bg-secondary">{{ $index + 1 }}</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $comercial->comercial->name ?? 'N/A' }}</td>
                                                        <td>
                                                            <span class="badge bg-primary">{{ $comercial->total }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIANDO GRÁFICOS ===');
    
    // Verificar Bootstrap
    const bootstrapStatus = document.getElementById('bootstrap-status');
    if (typeof $ !== 'undefined' && $('.card').length > 0) {
        bootstrapStatus.textContent = '✅ Cargado';
        bootstrapStatus.style.color = 'green';
    } else {
        bootstrapStatus.textContent = '❌ No cargado';
        bootstrapStatus.style.color = 'red';
    }
    
    // Verificar Chart.js
    const chartjsStatus = document.getElementById('chartjs-status');
    if (typeof Chart !== 'undefined') {
        chartjsStatus.textContent = '✅ Cargado';
        chartjsStatus.style.color = 'green';
    } else {
        chartjsStatus.textContent = '❌ No cargado';
        chartjsStatus.style.color = 'red';
    }
    
    // Datos para los gráficos
    const visitasPorMes = @json($visitasPorMes);
    const visitasPorComercial = @json($visitasPorComercial);
    const estadosPropuestas = @json($estadosPropuestas);

    console.log('Datos recibidos:', {
        visitasPorMes: visitasPorMes,
        visitasPorComercial: visitasPorComercial,
        estadosPropuestas: estadosPropuestas
    });

    // Gráfico de Visitas por Mes
    const ctxMes = document.getElementById('visitasPorMesChart');
    console.log('Canvas de visitas por mes encontrado:', ctxMes);
    if (ctxMes) {
        console.log('Creando gráfico de visitas por mes...');
        try {
            new Chart(ctxMes, {
            type: 'line',
            data: {
                labels: visitasPorMes.map(item => {
                    const [year, month] = item.mes.split('-');
                    return new Date(year, month - 1).toLocaleDateString('es-ES', { year: 'numeric', month: 'short' });
                }),
                datasets: [{
                    label: 'Visitas',
                    data: visitasPorMes.map(item => item.total),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        console.log('✅ Gráfico de visitas por mes creado exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfico de visitas por mes:', error);
        }
    } else {
        console.error('❌ No se encontró el canvas de visitas por mes');
    }

    // Gráfico de Visitas por Comercial
    const ctxComercial = document.getElementById('visitasPorComercialChart');
    if (ctxComercial) {
        new Chart(ctxComercial, {
            type: 'doughnut',
            data: {
                labels: visitasPorComercial.map(item => item.comercial ? item.comercial.name : 'N/A'),
                datasets: [{
                    data: visitasPorComercial.map(item => item.total),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        console.log('Gráfico de visitas por comercial creado');
    }

    // Gráfico de Estados de Propuestas
    const ctxEstados = document.getElementById('estadosPropuestasChart');
    if (ctxEstados) {
        new Chart(ctxEstados, {
            type: 'pie',
            data: {
                labels: estadosPropuestas.map(item => {
                    const estados = {
                        'pendiente': 'Pendiente',
                        'en_proceso': 'En Proceso',
                        'aceptado': 'Aceptado',
                        'rechazado': 'Rechazado'
                    };
                    return estados[item.estado] || item.estado;
                }),
                datasets: [{
                    data: estadosPropuestas.map(item => item.total),
                    backgroundColor: [
                        '#FFC107',
                        '#17A2B8',
                        '#28A745',
                        '#DC3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        console.log('Gráfico de estados creado');
    }
});
</script>
@endpush
