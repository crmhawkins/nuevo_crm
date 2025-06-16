@extends('layouts.appWhatsapp')

@section('titulo', 'Dashboard')

@section('content')
<!-- Modal para mostrar alertas -->
<div class="modal fade" id="alertsModal" tabindex="-1" aria-labelledby="alertsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="alertsModalLabel">
                    <i class="fas fa-bell me-2"></i>Alertas del Sistema
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(count($alerts) > 0)
                    <div class="list-group">
                        @foreach($alerts as $alert)
                            <div id="alert-{{ $alert->id }}" class="list-group-item list-group-item-action border-start border-4 border-primary mb-3 rounded shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-primary">{{ $alert->name }}</h6>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>{{ $alert->created_at }}
                                    </small>
                                </div>
                                <p class="mb-3 text-muted">{{ $alert->description }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        @if (isset($alert->link))
                                            <a href="{{ $alert->link }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt me-1"></i>Ver detalles
                                            </a>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAlert({{ $alert->id }})">
                                        <i class="fas fa-trash-alt me-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <p class="text-muted mb-0">No hay alertas pendientes</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function showAlertsModal() {
        var alertsModal = new bootstrap.Modal(document.getElementById('alertsModal'));
        alertsModal.show();
    }
</script>

<div class="page-heading card" style="box-shadow: none !important">
    <div class="bg-image overflow-hidden mb-10">
        <div class="content content-narrow content-full">
            <div class="text-center mt-5 mb-2">
                <h2 class="h2 text-white mb-0">Bienvenido {{ $client->name }}</h2>
                @if (count($respuestas) == 1)
                    <h1 class="h1 text-white mb-0">Tiene {{ count($respuestas) }} respuesta nueva</h1>
                @elseif (count($respuestas) > 1)
                    <h1 class="h1 text-white mb-0">Tiene {{ count($respuestas) }} respuestas nuevas</h1>
                @endif
                <br>
                @if (count($alerts) > 0)
                    <h2 id="alerts-count" class="h3 text-white mb-0 rounded">
                        <span class="bg-danger px-2 rounded">
                            Tienes {{ count($alerts) }} {{ count($alerts) == 1 ? 'alerta' : 'alertas' }}.
                            Haz click <a class="cursor-pointer text-primary" onclick="showAlertsModal()">aquí</a>
                            para {{ count($alerts) == 1 ? 'verla' : 'verlas' }}
                        </span>
                    </h2>
                @endif
                <div class="mt-4 row d-flex justify-content-center">
                    <div class="col-6 mb-3">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas de Mensajes (centrado) -->
<div class="container my-4">
    <div class="d-flex justify-content-center">
        <div class="card shadow w-100" style="max-width: 500px;">
            <div class="card-header bg-white text-center">
                <h5 class="mb-0">Estadísticas de Mensajes</h5>
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px;">
                    <canvas id="messageStatsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Botón de logout
    $('#sendLogout').on('click', function(e) {
        e.preventDefault();
        $.post('/admin/logout', {
            _token: '{{ csrf_token() }}'
        }, function(data) {
            window.location.href = '/admin';
        });
    });

    // Gráfico de estadísticas
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('messageStatsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Enviados', 'Leídos', 'Recibidos', 'Pendientes'],
                datasets: [{
                    data: [
                        {{ $stats['enviados'] }},
                        {{ $stats['leidos'] }},
                        {{ $stats['recibidos'] }},
                        {{ $stats['pendientes'] }}
                    ],
                    backgroundColor: ['#28a745', '#17a2b8', '#007bff', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 10
                        }
                    },
                    title: {
                        display: true,
                        text: 'Distribución de Mensajes (Total: {{ $stats["total"] }})',
                        font: { size: 16 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });

    function deleteAlert(id) {
        $.ajax({
            url: "{{ route('plataforma.deleteAlert') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id
            },
            success: function(response) {
                if (response.success) {
                    $(`#alert-${id}`).fadeOut(300, function() {
                        $(this).remove();
                        updateAlertsCount();
                    });
                }
            }
        });
    }

    function updateAlertsCount() {
        const alertsCount = $('.list-group-item').length;
        if (alertsCount === 0) {
            $('#alerts-count').fadeOut(300, function() {
                $(this).remove();
            });
            $('.modal-body').html(`
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                    <p class="text-muted mb-0">No hay alertas pendientes</p>
                </div>
            `);
        }
    }
</script>
@endsection
