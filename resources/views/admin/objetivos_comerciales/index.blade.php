@extends('layouts.app')

@section('title', 'Objetivos Comerciales')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-target me-2"></i>Objetivos Comerciales
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrearObjetivo">
                        <i class="fas fa-plus me-2"></i>Nuevo Objetivo
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Comercial</label>
                            <select class="form-select" id="filtroComercial">
                                <option value="">Todos los comerciales</option>
                                @foreach($comerciales as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }} {{ $comercial->surname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="filtroEstado">
                                <option value="">Todos</option>
                                <option value="activo">Activos</option>
                                <option value="inactivo">Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tipo</label>
                            <select class="form-select" id="filtroTipo">
                                <option value="">Todos</option>
                                <option value="diario">Diario</option>
                                <option value="mensual">Mensual</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary" id="btnFiltrar">
                                <i class="fas fa-filter me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de objetivos -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaObjetivos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Comercial</th>
                                    <th>Período</th>
                                    <th>Tipo</th>
                                    <th>Visitas Diarias</th>
                                    <th>Ventas Mensuales</th>
                                    <th>Progreso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($objetivos as $objetivo)
                                <tr data-objetivo-id="{{ $objetivo->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($objetivo->comercial->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $objetivo->comercial->name }} {{ $objetivo->comercial->surname }}</strong>
                                                <br><small class="text-muted">{{ $objetivo->comercial->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $objetivo->fecha_inicio->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">hasta {{ $objetivo->fecha_fin->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $objetivo->tipo_objetivo == 'diario' ? 'info' : 'warning' }}">
                                            {{ ucfirst($objetivo->tipo_objetivo) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-walking text-success me-1"></i>Presenciales: <strong>{{ $objetivo->visitas_presenciales_diarias }}</strong><br>
                                            <i class="fas fa-phone text-info me-1"></i>Telefónicas: <strong>{{ $objetivo->visitas_telefonicas_diarias }}</strong><br>
                                            <i class="fas fa-users text-warning me-1"></i>Mixtas: <strong>{{ $objetivo->visitas_mixtas_diarias }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-star text-primary me-1"></i>Esenciales: <strong>{{ $objetivo->planes_esenciales_mensuales }}</strong><br>
                                            <i class="fas fa-star text-success me-1"></i>Profesionales: <strong>{{ $objetivo->planes_profesionales_mensuales }}</strong><br>
                                            <i class="fas fa-star text-warning me-1"></i>Avanzados: <strong>{{ $objetivo->planes_avanzados_mensuales }}</strong><br>
                                            <i class="fas fa-euro-sign text-info me-1"></i>€: <strong>{{ number_format($objetivo->ventas_euros_mensuales, 0) }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="verProgreso({{ $objetivo->id }}, {{ $objetivo->comercial_id }})">
                                            <i class="fas fa-chart-line me-1"></i>Ver Progreso
                                        </button>
                                    </td>
                                    <td>
                                        @if($objetivo->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarObjetivo({{ $objetivo->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($objetivo->activo)
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="desactivarObjetivo({{ $objetivo->id }})">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="activarObjetivo({{ $objetivo->id }})">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            @endif
                                        </div>
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

<!-- Modal Crear Objetivo -->
<div class="modal fade" id="modalCrearObjetivo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-target me-2"></i>Nuevo Objetivo Comercial
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearObjetivo">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Comercial <span class="text-danger">*</span></label>
                            <select class="form-select" name="comercial_id" required>
                                <option value="">Seleccionar comercial</option>
                                @foreach($comerciales as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->name }} {{ $comercial->surname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Objetivo <span class="text-danger">*</span></label>
                            <select class="form-select" name="tipo_objetivo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="diario">Diario</option>
                                <option value="mensual">Mensual</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                    </div>

                    <!-- Objetivos de Visitas -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-walking me-2"></i>Objetivos de Visitas Diarias
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Visitas Presenciales</label>
                                    <input type="number" class="form-control" name="visitas_presenciales_diarias" min="0" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Visitas Telefónicas</label>
                                    <input type="number" class="form-control" name="visitas_telefonicas_diarias" min="0" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Visitas Mixtas</label>
                                    <input type="number" class="form-control" name="visitas_mixtas_diarias" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Objetivos de Ventas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-euro-sign me-2"></i>Objetivos de Ventas Mensuales
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Planes Esenciales</label>
                                    <input type="number" class="form-control" name="planes_esenciales_mensuales" min="0" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Planes Profesionales</label>
                                    <input type="number" class="form-control" name="planes_profesionales_mensuales" min="0" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Planes Avanzados</label>
                                    <input type="number" class="form-control" name="planes_avanzados_mensuales" min="0" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Ventas en Euros</label>
                                    <input type="number" class="form-control" name="ventas_euros_mensuales" min="0" step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Precios de Planes -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-tags me-2"></i>Precios de Planes
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Precio Plan Esencial (€)</label>
                                    <input type="number" class="form-control" name="precio_plan_esencial" min="0" step="0.01" value="19.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Plan Profesional (€)</label>
                                    <input type="number" class="form-control" name="precio_plan_profesional" min="0" step="0.01" value="49.00">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Plan Avanzado (€)</label>
                                    <input type="number" class="form-control" name="precio_plan_avanzado" min="0" step="0.01" value="129.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="3" placeholder="Notas adicionales sobre el objetivo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Crear Objetivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Progreso -->
<div class="modal fade" id="modalProgreso" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>Progreso del Comercial
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoProgreso">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Crear objetivo
    $('#formCrearObjetivo').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("objetivos.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = 'Error al crear el objetivo:\n';
                for (let field in errors) {
                    errorMessage += `- ${errors[field][0]}\n`;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Establecer fecha de hoy por defecto
    $('input[name="fecha_inicio"]').val('{{ now()->format("Y-m-d") }}');
    $('input[name="fecha_fin"]').val('{{ now()->addMonth()->format("Y-m-d") }}');
});

function verProgreso(objetivoId, comercialId) {
    $.ajax({
        url: `/admin/objetivos-comerciales/progreso/${comercialId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarProgreso(response);
                $('#modalProgreso').modal('show');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin objetivos',
                    text: response.message
                });
            }
        }
    });
}

function mostrarProgreso(data) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Objetivos de Visitas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Visitas Presenciales</span>
                                <span>${data.progreso.visitas.presenciales.realizado}/${data.progreso.visitas.presenciales.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: ${data.progreso.visitas.presenciales.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.visitas.presenciales.progreso}% completado</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Visitas Telefónicas</span>
                                <span>${data.progreso.visitas.telefonicas.realizado}/${data.progreso.visitas.telefonicas.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: ${data.progreso.visitas.telefonicas.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.visitas.telefonicas.progreso}% completado</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Visitas Mixtas</span>
                                <span>${data.progreso.visitas.mixtas.realizado}/${data.progreso.visitas.mixtas.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: ${data.progreso.visitas.mixtas.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.visitas.mixtas.progreso}% completado</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Objetivos de Ventas</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Planes Esenciales</span>
                                <span>${data.progreso.ventas.planes_esenciales.realizado}/${data.progreso.ventas.planes_esenciales.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: ${data.progreso.ventas.planes_esenciales.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.ventas.planes_esenciales.progreso}% completado</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Planes Profesionales</span>
                                <span>${data.progreso.ventas.planes_profesionales.realizado}/${data.progreso.ventas.planes_profesionales.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: ${data.progreso.ventas.planes_profesionales.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.ventas.planes_profesionales.progreso}% completado</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Planes Avanzados</span>
                                <span>${data.progreso.ventas.planes_avanzados.realizado}/${data.progreso.ventas.planes_avanzados.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: ${data.progreso.ventas.planes_avanzados.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.ventas.planes_avanzados.progreso}% completado</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Ventas en Euros</span>
                                <span>€${data.progreso.ventas.ventas_euros.realizado.toFixed(2)}/€${data.progreso.ventas.ventas_euros.objetivo}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: ${data.progreso.ventas.ventas_euros.progreso}%"></div>
                            </div>
                            <small class="text-muted">${data.progreso.ventas.ventas_euros.progreso}% completado</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#contenidoProgreso').html(html);
}

function desactivarObjetivo(id) {
    Swal.fire({
        title: '¿Desactivar objetivo?',
        text: 'El objetivo se desactivará y ya no se mostrará en el dashboard del comercial.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/objetivos-comerciales/${id}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    }
                }
            });
        }
    });
}
</script>
@endpush

