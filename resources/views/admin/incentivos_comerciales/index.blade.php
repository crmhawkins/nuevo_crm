@extends('layouts.app')

@section('title', 'Incentivos Comerciales')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Incentivos Comerciales
                    </h5>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalCrearIncentivo">
                        <i class="fas fa-plus me-2"></i>Nuevo Incentivo
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
                            <label class="form-label">Período</label>
                            <select class="form-select" id="filtroPeriodo">
                                <option value="">Todos</option>
                                <option value="vigente">Vigentes</option>
                                <option value="vencido">Vencidos</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary" id="btnFiltrar">
                                <i class="fas fa-filter me-2"></i>Filtrar
                            </button>
                        </div>
                    </div>

                    <!-- Tabla de incentivos -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaIncentivos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Comercial</th>
                                    <th>Período</th>
                                    <th>Incentivos</th>
                                    <th>Progreso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incentivos as $incentivo)
                                <tr data-incentivo-id="{{ $incentivo->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($incentivo->comercial->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $incentivo->comercial->name }} {{ $incentivo->comercial->surname }}</strong>
                                                <br><small class="text-muted">{{ $incentivo->comercial->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $incentivo->fecha_inicio->format('d/m/Y') }}</strong>
                                        <br><small class="text-muted">hasta {{ $incentivo->fecha_fin->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <i class="fas fa-percentage text-success me-1"></i>Base: <strong>{{ $incentivo->porcentaje_venta }}%</strong><br>
                                            <i class="fas fa-star text-warning me-1"></i>Adicional: <strong>{{ $incentivo->porcentaje_adicional }}%</strong><br>
                                            <i class="fas fa-users text-info me-1"></i>Mín. Clientes: <strong>{{ $incentivo->min_clientes_mensuales }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-success" onclick="verProgresoIncentivos({{ $incentivo->id }}, {{ $incentivo->comercial_id }})">
                                            <i class="fas fa-chart-line me-1"></i>Ver Progreso
                                        </button>
                                    </td>
                                    <td>
                                        @if($incentivo->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="editarIncentivo({{ $incentivo->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($incentivo->activo)
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="desactivarIncentivo({{ $incentivo->id }})">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-success" onclick="activarIncentivo({{ $incentivo->id }})">
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

<!-- Modal Crear Incentivo -->
<div class="modal fade" id="modalCrearIncentivo" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-money-bill-wave me-2"></i>Nuevo Incentivo Comercial
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formCrearIncentivo">
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
                            <label class="form-label">Fecha Inicio <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_inicio" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Fecha Fin <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="fecha_fin" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mínimo Clientes Mensuales <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="min_clientes_mensuales" min="1" value="50" required>
                        </div>
                    </div>

                    <!-- Configuración de Incentivos -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-percentage me-2"></i>Configuración de Incentivos
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Porcentaje Base de Venta (%)</label>
                                    <input type="number" class="form-control" name="porcentaje_venta" min="0" max="100" step="0.01" value="10.00" required>
                                    <small class="text-muted">Porcentaje base sobre las ventas</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Porcentaje Adicional (%)</label>
                                    <input type="number" class="form-control" name="porcentaje_adicional" min="0" max="100" step="0.01" value="10.00" required>
                                    <small class="text-muted">Porcentaje adicional si cumple mínimo de clientes</small>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label class="form-label">Mínimo Ventas Mensuales (€)</label>
                                    <input type="number" class="form-control" name="min_ventas_mensuales" min="0" step="0.01" value="0">
                                    <small class="text-muted">Mínimo de ventas para aplicar incentivos</small>
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
                                    <input type="number" class="form-control" name="precio_plan_esencial" min="0" step="0.01" value="19.00" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Plan Profesional (€)</label>
                                    <input type="number" class="form-control" name="precio_plan_profesional" min="0" step="0.01" value="49.00" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Precio Plan Avanzado (€)</label>
                                    <input type="number" class="form-control" name="precio_plan_avanzado" min="0" step="0.01" value="129.00" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Notas</label>
                        <textarea class="form-control" name="notas" rows="3" placeholder="Notas adicionales sobre el incentivo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Crear Incentivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ver Progreso Incentivos -->
<div class="modal fade" id="modalProgresoIncentivos" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>Progreso de Incentivos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoProgresoIncentivos">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Crear incentivo
    $('#formCrearIncentivo').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("incentivos.store") }}',
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
                let errorMessage = 'Error al crear el incentivo:\n';
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

function verProgresoIncentivos(incentivoId, comercialId) {
    $.ajax({
        url: `/admin/incentivos-comerciales/progreso/${comercialId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                mostrarProgresoIncentivos(response);
                $('#modalProgresoIncentivos').modal('show');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sin incentivos',
                    text: response.message
                });
            }
        }
    });
}

function mostrarProgresoIncentivos(data) {
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Resumen de Incentivos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-success">€${data.progreso.incentivos.incentivo_base.toFixed(2)}</h4>
                                        <small class="text-muted">Incentivo Base (${data.incentivo.porcentaje_venta}%)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h4 class="text-warning">€${data.progreso.incentivos.incentivo_adicional.toFixed(2)}</h4>
                                        <small class="text-muted">Incentivo Adicional (${data.incentivo.porcentaje_adicional}%)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>€${data.progreso.incentivos.total_incentivo.toFixed(2)}</h3>
                                    <small>Total de Incentivos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Progreso hacia Incentivo Adicional</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span>Clientes Únicos</span>
                                <span>${data.progreso.clientes_unicos}/${data.incentivo.min_clientes_mensuales}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: ${Math.min((data.progreso.clientes_unicos / data.incentivo.min_clientes_mensuales) * 100, 100)}%"></div>
                            </div>
                            <small class="text-muted">
                                ${data.progreso.cumple_minimo_clientes ? '✅ ¡Cumples el mínimo de clientes!' : `Faltan ${data.incentivo.min_clientes_mensuales - data.progreso.clientes_unicos} clientes`}
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span>Ventas Totales</span>
                                <span>€${data.progreso.ventas_totales.toFixed(0)}</span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 100%"></div>
                            </div>
                            <small class="text-muted">Ventas realizadas este mes</small>
                        </div>
                        
                        ${data.progreso.cumple_minimo_clientes ? 
                            '<div class="alert alert-success"><i class="fas fa-star me-2"></i><strong>¡Felicidades!</strong> Cumples el mínimo de clientes para el incentivo adicional</div>' :
                            '<div class="alert alert-warning"><i class="fas fa-info-circle me-2"></i>Necesitas ' + (data.incentivo.min_clientes_mensuales - data.progreso.clientes_unicos) + ' clientes más para el incentivo adicional</div>'
                        }
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#contenidoProgresoIncentivos').html(html);
}

function desactivarIncentivo(id) {
    Swal.fire({
        title: '¿Desactivar incentivo?',
        text: 'El incentivo se desactivará y ya no se aplicará al comercial.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/incentivos-comerciales/${id}`,
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
