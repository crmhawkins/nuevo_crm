@extends('layouts.app')

@section('title', 'Estadísticas de Visitas Comerciales')

@section('content')
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
                    <!-- Resumen General -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-handshake fa-2x mb-2"></i>
                                    <h3 class="mb-0">{{ $totalVisitas }}</h3>
                                    <p class="mb-0">Total Visitas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-handshake fa-2x mb-2"></i>
                                    <h3 class="mb-0">{{ $visitasPresenciales }}</h3>
                                    <p class="mb-0">Presenciales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-phone fa-2x mb-2"></i>
                                    <h3 class="mb-0">{{ $visitasTelefonicas }}</h3>
                                    <p class="mb-0">Telefónicas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-microphone fa-2x mb-2"></i>
                                    <h3 class="mb-0">{{ $visitasConAudio }}</h3>
                                    <p class="mb-0">Con Audio</p>
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
    // Datos para los gráficos
    const visitasPorMes = @json($visitasPorMes);
    const visitasPorComercial = @json($visitasPorComercial);
    const estadosPropuestas = @json($estadosPropuestas);

    // Gráfico de Visitas por Mes
    const ctxMes = document.getElementById('visitasPorMesChart').getContext('2d');
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

    // Gráfico de Visitas por Comercial
    const ctxComercial = document.getElementById('visitasPorComercialChart').getContext('2d');
    new Chart(ctxComercial, {
        type: 'doughnut',
        data: {
            labels: visitasPorComercial.map(item => item.comercial.name || 'N/A'),
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

    // Gráfico de Estados de Propuestas
    const ctxEstados = document.getElementById('estadosPropuestasChart').getContext('2d');
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
});
</script>
@endpush
