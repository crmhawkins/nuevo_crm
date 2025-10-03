@extends('layouts.app')

@section('title', 'Visitas Comerciales')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-handshake me-2"></i>Visitas Comerciales
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('visitas-comerciales.estadisticas') }}" class="btn btn-info">
                                <i class="fas fa-chart-bar me-1"></i>Estadísticas
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('visitas-comerciales.index') }}" class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Comercial</label>
                                    <select name="comercial_id" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach($comerciales as $comercial)
                                            <option value="{{ $comercial->id }}" {{ request('comercial_id') == $comercial->id ? 'selected' : '' }}>
                                                {{ $comercial->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tipo</label>
                                    <select name="tipo_visita" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="presencial" {{ request('tipo_visita') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                                        <option value="telefonico" {{ request('tipo_visita') == 'telefonico' ? 'selected' : '' }}>Telefónico</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                        <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="aceptado" {{ request('estado') == 'aceptado' ? 'selected' : '' }}>Aceptado</option>
                                        <option value="rechazado" {{ request('estado') == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Con Audio</label>
                                    <select name="con_audio" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="si" {{ request('con_audio') == 'si' ? 'selected' : '' }}>Sí</option>
                                        <option value="no" {{ request('con_audio') == 'no' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha Desde</label>
                                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Fecha Hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Filtrar
                                    </button>
                                    <a href="{{ route('visitas-comerciales.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tabla de visitas -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Comercial</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Valoración</th>
                                    <th>Plan</th>
                                    <th>Estado</th>
                                    <th>Audio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($visitas as $visita)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $visita->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    <span class="text-white fw-bold">
                                                        {{ substr($visita->comercial->name ?? 'N/A', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span>{{ $visita->comercial->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($visita->cliente)
                                                    <div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white fw-bold">
                                                            {{ substr($visita->cliente->name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span>{{ $visita->cliente->name }}</span>
                                                @else
                                                    <div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white fw-bold">L</span>
                                                    </div>
                                                    <span>{{ $visita->nombre_cliente }} <small class="text-muted">(Lead)</small></span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $visita->tipo_visita == 'presencial' ? 'primary' : 'info' }}">
                                                <i class="fas fa-{{ $visita->tipo_visita == 'presencial' ? 'handshake' : 'phone' }} me-1"></i>
                                                {{ ucfirst($visita->tipo_visita) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= ($visita->valoracion / 2))
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span class="ms-1 text-muted">{{ $visita->valoracion }}/10</span>
                                            </div>
                                        </td>
                                        <td>
                                            @if($visita->plan_interesado)
                                                <span class="badge bg-secondary">
                                                    {{ ucfirst($visita->plan_interesado) }}
                                                </span>
                                                @if($visita->precio_plan)
                                                    <br><small class="text-muted">€{{ $visita->precio_plan }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visita->estado)
                                                @php
                                                    $estadoColors = [
                                                        'pendiente' => 'warning',
                                                        'en_proceso' => 'info',
                                                        'aceptado' => 'success',
                                                        'rechazado' => 'danger'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $estadoColors[$visita->estado] ?? 'secondary' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $visita->estado)) }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($visita->audio_file)
                                                <button class="btn btn-sm btn-outline-primary" onclick="playAudio({{ $visita->id }})">
                                                    <i class="fas fa-play me-1"></i>
                                                    {{ gmdate('i:s', $visita->audio_duration ?? 0) }}
                                                </button>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('visitas-comerciales.show', $visita) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($visita->audio_file)
                                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteAudio({{ $visita->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteVisita({{ $visita->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-handshake fa-3x mb-3"></i>
                                                <p>No hay visitas comerciales registradas</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $visitas->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para reproducir audio -->
<div class="modal fade" id="audioModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-volume-up me-2"></i>Audio de la Visita
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <audio id="audioPlayer" controls class="w-100">
                    Tu navegador no soporta el elemento de audio.
                </audio>
                <div id="audioInfo" class="mt-3">
                    <!-- Información del audio se cargará aquí -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function playAudio(visitaId) {
    fetch(`/visitas-comerciales/${visitaId}/audio`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const audioPlayer = document.getElementById('audioPlayer');
                const audioInfo = document.getElementById('audioInfo');
                
                audioPlayer.src = data.audio.url;
                audioInfo.innerHTML = `
                    <div class="row">
                        <div class="col-6">
                            <strong>Duración:</strong><br>
                            <span class="text-muted">${formatTime(data.audio.duration)}</span>
                        </div>
                        <div class="col-6">
                            <strong>Grabado:</strong><br>
                            <span class="text-muted">${new Date(data.audio.recorded_at).toLocaleString('es-ES')}</span>
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('audioModal'));
                modal.show();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo cargar el audio',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error al cargar el audio',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
}

function deleteAudio(visitaId) {
    Swal.fire({
        title: '¿Eliminar audio?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/visitas-comerciales/${visitaId}/audio`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar el audio',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

function deleteVisita(visitaId) {
    Swal.fire({
        title: '¿Eliminar visita?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/visitas-comerciales/${visitaId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo eliminar la visita',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

function formatTime(seconds) {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;
    
    if (hours > 0) {
        return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    } else {
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
}
</script>
@endpush
