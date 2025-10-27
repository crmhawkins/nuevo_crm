@extends('layouts.app')

@section('titulo', 'Mis Justificaciones')

@section('css')
<style>
    .card-justificacion {
        transition: transform 0.2s;
        border: 1px solid #e0e0e0;
    }
    .card-justificacion:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .badge-tipo {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
</style>
@endsection

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Mis Justificaciones</h3>
                <p class="text-subtitle text-muted">Gestiona y descarga tus justificaciones enviadas</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Justificaciones</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        @if($justificaciones->count() > 0)
            <div class="row">
                @foreach($justificaciones as $justificacion)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card card-justificacion h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title mb-0">{{ $justificacion->nombre_justificacion }}</h5>
                                    <span class="badge bg-primary badge-tipo">
                                        {{ str_replace('_', ' ', ucfirst($justificacion->tipo_justificacion)) }}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt"></i> 
                                        Creada: {{ $justificacion->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </div>

                                @if($justificacion->metadata && isset($justificacion->metadata['url']))
                                    <div class="mb-3">
                                        <strong>URL:</strong><br>
                                        <a href="{{ $justificacion->metadata['url'] }}" target="_blank" class="text-break">
                                            {{ $justificacion->metadata['url'] }}
                                        </a>
                                    </div>
                                @endif

                                @php
                                    // Manejar datos antiguos (string JSON) y nuevos (array)
                                    $archivos = $justificacion->archivos;
                                    if (is_string($archivos)) {
                                        $archivos = json_decode($archivos, true) ?? [];
                                    }
                                    $archivos = $archivos ?? [];
                                    
                                    $metadata = $justificacion->metadata;
                                    if (is_string($metadata)) {
                                        $metadata = json_decode($metadata, true) ?? [];
                                    }
                                    $metadata = $metadata ?? [];
                                    
                                    $estado = $metadata['estado'] ?? 'pendiente';
                                @endphp

                                <div class="mb-3">
                                    <strong>Estado:</strong>
                                    @if($estado == 'completado')
                                        <span class="badge bg-success">✓ Completado</span>
                                    @elseif($estado == 'procesando')
                                        <span class="badge bg-warning">⏳ Procesando...</span>
                                    @elseif($estado == 'en_cola')
                                        <span class="badge bg-info">📋 En Cola</span>
                                    @elseif($estado == 'error')
                                        <span class="badge bg-danger">✗ Error</span>
                                    @else
                                        <span class="badge bg-secondary">⏸ Pendiente</span>
                                    @endif
                                    
                                    @if(isset($metadata['tipo_analisis']))
                                        <span class="badge bg-{{ $metadata['tipo_analisis'] == 'ecommerce' ? 'success' : 'primary' }} ms-2">
                                            @if($metadata['tipo_analisis'] == 'ecommerce')
                                                <i class="fas fa-shopping-cart"></i> ECOMMERCE
                                            @else
                                                <i class="fas fa-globe"></i> WEB
                                            @endif
                                        </span>
                                    @endif
                                </div>
                                
                                @if(isset($metadata['ultimo_mensaje']) && $metadata['ultimo_mensaje'])
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> {{ $metadata['ultimo_mensaje'] }}
                                        </small>
                                    </div>
                                @endif

                                @if(!empty($archivos))
                                    <div class="mb-3">
                                        <strong>Archivos incluidos:</strong>
                                        <ul class="list-unstyled mt-2">
                                            @foreach($archivos as $tipo => $path)
                                                <li><i class="fas fa-file text-success"></i> {{ ucfirst($tipo) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <div class="mb-3 text-muted">
                                        <i class="fas fa-clock"></i> Los archivos se están generando...
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between">
                                    @if($estado == 'completado' && !empty($archivos))
                                        <a href="{{ route('justificaciones.download', $justificacion->id) }}" class="btn btn-success">
                                            <i class="fas fa-download"></i> Descargar ZIP
                                        </a>
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-clock"></i> Procesando...
                                        </button>
                                    @endif
                                    <button onclick="eliminarJustificacion({{ $justificacion->id }})" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes justificaciones</h4>
                    <p class="text-muted">Las justificaciones que envíes aparecerán aquí</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

@section('scripts')
@include('partials.toast')
<script>
    // Auto-refresh cada 10 segundos si hay justificaciones en proceso
    document.addEventListener('DOMContentLoaded', function() {
        const hayEnProceso = document.querySelectorAll('.badge.bg-warning, .badge.bg-info').length > 0;
        
        if (hayEnProceso) {
            setInterval(function() {
                location.reload();
            }, 10000); // Recargar cada 10 segundos
        }
    });

    function eliminarJustificacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/justificaciones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo eliminar la justificación'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error al eliminar la justificación'
                    });
                });
            }
        });
    }
</script>
@endsection

