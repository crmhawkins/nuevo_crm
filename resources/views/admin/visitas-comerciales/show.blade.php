@extends('layouts.app')

@section('title', 'Detalles de Visita Comercial')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-handshake me-2"></i>Detalles de Visita Comercial
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('visitas-comerciales.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                            @if($visita->audio_file)
                                <button class="btn btn-danger" onclick="deleteAudio({{ $visita->id }})">
                                    <i class="fas fa-trash me-1"></i>Eliminar Audio
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Información Principal -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i>Información General
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Fecha y Hora:</label>
                                                <p class="mb-0">{{ $visita->created_at ? $visita->created_at->format('d/m/Y H:i:s') : 'No disponible' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Tipo de Visita:</label>
                                                <p class="mb-0">
                                                    <span class="badge bg-{{ $visita->tipo_visita == 'presencial' ? 'primary' : 'info' }}">
                                                        <i class="fas fa-{{ $visita->tipo_visita == 'presencial' ? 'handshake' : 'phone' }} me-1"></i>
                                                        {{ ucfirst($visita->tipo_visita) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Comercial:</label>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <span class="text-white fw-bold">
                                                            {{ substr($visita->comercial->name ?? 'N/A', 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <span>{{ $visita->comercial->name ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Cliente:</label>
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
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Valoración:</label>
                                                <div class="d-flex align-items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= ($visita->valoracion / 2))
                                                            <i class="fas fa-star text-warning fs-4"></i>
                                                        @else
                                                            <i class="far fa-star text-muted fs-4"></i>
                                                        @endif
                                                    @endfor
                                                    <span class="ms-2 fs-5 fw-bold">{{ $visita->valoracion }}/10</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Seguimiento Requerido:</label>
                                                <p class="mb-0">
                                                    @if($visita->requiere_seguimiento)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Sí
                                                        </span>
                                                        @if($visita->fecha_seguimiento)
                                                            <br><small class="text-muted">Fecha: {{ $visita->fecha_seguimiento ? $visita->fecha_seguimiento->format('d/m/Y H:i') : 'No disponible' }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times me-1"></i>No
                                                        </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    @if($visita->comentarios)
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Comentarios:</label>
                                            <div class="bg-light p-3 rounded">
                                                <p class="mb-0">{{ $visita->comentarios }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Información del Plan y Audio -->
                        <div class="col-md-4">
                            <!-- Plan Interesado -->
                            @if($visita->plan_interesado)
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-star me-2"></i>Plan de Interés
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Plan:</label>
                                            <p class="mb-0">
                                                <span class="badge bg-secondary fs-6">
                                                    {{ ucfirst($visita->plan_interesado) }}
                                                </span>
                                            </p>
                                        </div>
                                        
                                        @if($visita->precio_plan)
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Precio:</label>
                                                <p class="mb-0 fs-5 fw-bold text-success">€{{ $visita->precio_plan }}</p>
                                            </div>
                                        @endif

                                        @if($visita->estado)
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Estado:</label>
                                                <p class="mb-0">
                                                    @php
                                                        $estadoColors = [
                                                            'pendiente' => 'warning',
                                                            'en_proceso' => 'info',
                                                            'aceptado' => 'success',
                                                            'rechazado' => 'danger'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $estadoColors[$visita->estado] ?? 'secondary' }} fs-6">
                                                        {{ ucfirst(str_replace('_', ' ', $visita->estado)) }}
                                                    </span>
                                                </p>
                                            </div>
                                        @endif

                                        @if($visita->observaciones_plan)
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Observaciones:</label>
                                                <div class="bg-light p-3 rounded">
                                                    <p class="mb-0">{{ $visita->observaciones_plan }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Audio -->
                            @if($visita->audio_file)
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-volume-up me-2"></i>Audio de la Visita
                                        </h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <button class="btn btn-primary btn-lg" onclick="playAudio()">
                                                <i class="fas fa-play me-2"></i>Reproducir Audio
                                            </button>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <label class="form-label fw-bold">Duración:</label>
                                                    <p class="mb-0">{{ gmdate('i:s', $visita->audio_duration ?? 0) }}</p>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center">
                                                    <label class="form-label fw-bold">Grabado:</label>
                                                    <p class="mb-0">
                                                        <small>{{ $visita->audio_recorded_at ? $visita->audio_recorded_at->format('d/m/Y H:i') : 'No disponible' }}</small>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card">
                                    <div class="card-body text-center">
                                        <i class="fas fa-microphone-slash fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay audio disponible para esta visita</p>
                                    </div>
                                </div>
                            @endif
                        </div>
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
                    <div class="row">
                        <div class="col-6">
                            <strong>Duración:</strong><br>
                            <span class="text-muted">{{ gmdate('i:s', $visita->audio_duration ?? 0) }}</span>
                        </div>
                        <div class="col-6">
                            <strong>Grabado:</strong><br>
                            <span class="text-muted">{{ $visita->audio_recorded_at ? $visita->audio_recorded_at->format('d/m/Y H:i') : 'No disponible' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function playAudio() {
    const audioPlayer = document.getElementById('audioPlayer');
    audioPlayer.src = '{{ Storage::url($visita->audio_file) }}';
    
    const modal = new bootstrap.Modal(document.getElementById('audioModal'));
    modal.show();
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
</script>
@endpush
