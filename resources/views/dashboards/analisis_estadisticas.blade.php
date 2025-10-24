@extends('layouts.app')

@section('titulo', 'Análisis y Estadísticas')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/dashboard.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .cliente-row:hover {
        background-color: #f8f9fa !important;
        transform: scale(1.01);
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .cliente-row {
        transition: all 0.2s ease;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-4 order-md-1 order-last">
                <h3>Análisis y Estadísticas</h3>
            </div>
            <div class="col-12 col-md-8 order-md-2 order-first">
                <form method="GET" class="d-flex gap-2 flex-wrap justify-content-end">
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                        <input type="text" class="form-control date-range p-1 rangofecha" id="dateRange" name="dateRange" value="{{ $fechaInicio }} a {{ $fechaFin }}">
                        <input type="hidden" name="fecha_inicio" value="{{ $fechaInicio }}">
                        <input type="hidden" name="fecha_fin" value="{{ $fechaFin }}">
                    </div>
                    <select name="tipo_analisis" class="form-select" style="max-width: 200px;" id="tipoAnalisis">
                        <option value="top_clientes" {{ $tipoAnalisis == 'top_clientes' ? 'selected' : '' }}>Top Clientes</option>
                        <option value="por_categoria" {{ $tipoAnalisis == 'por_categoria' ? 'selected' : '' }}>Por Categoría</option>
                        <option value="por_servicio" {{ $tipoAnalisis == 'por_servicio' ? 'selected' : '' }}>Por Servicio</option>
                        <option value="por_facturacion" {{ $tipoAnalisis == 'por_facturacion' ? 'selected' : '' }}>Por Facturación</option>
                    </select>
                    <select name="filtro_id" class="form-select" style="max-width: 200px;" id="filtroSelect">
                        <option value="">Todos</option>
                        @if($tipoAnalisis == 'por_categoria')
                            @foreach($categoriasServicios as $categoria)
                                <option value="{{ $categoria->id }}" {{ $filtroId == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->name }}
                                </option>
                            @endforeach
                        @elseif($tipoAnalisis == 'por_servicio')
                            @if($serviciosDisponibles->count() > 0)
                                @foreach($serviciosDisponibles as $servicio)
                                    <option value="{{ $servicio->id }}" {{ $filtroId == $servicio->id ? 'selected' : '' }}>
                                        {{ $servicio->categoria }} - {{ $servicio->title }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>No hay servicios disponibles</option>
                            @endif
                        @endif
                    </select>
                    <input type="number" name="monto_minimo" class="form-control" style="max-width: 150px;" placeholder="Monto mínimo (€)" value="{{ $montoMinimo }}" min="0" step="0.01" id="montoMinimoInput">
                    <input type="number" name="limite" class="form-control" style="max-width: 100px;" placeholder="Límite" value="{{ $limite }}" min="1" max="1000">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Analizar
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Análisis Dinámico de Clientes -->
    <div class="card2 mt-4">
        <div class="card-body2">
            <h5 class="card-title">
                @switch($tipoAnalisis)
                    @case('top_clientes')
                        <i class="bi bi-trophy"></i> Top Clientes por Facturación Cobrada
                        @break
                    @case('por_categoria')
                        <i class="bi bi-tags"></i> Clientes por Categoría de Servicio
                        @break
                    @case('por_servicio')
                        <i class="bi bi-gear"></i> Clientes por Servicio Específico
                        @break
                    @case('por_facturacion')
                        <i class="bi bi-currency-euro"></i> Clientes por Monto de Facturación (>= {{ number_format($montoMinimo, 2, ',', '.') }} €)
                        @break
                @endswitch
            </h5>
            
            
            
            
            @if($resultados->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                @if($tipoAnalisis == 'por_categoria')
                                    <th>Categoría</th>
                                @elseif($tipoAnalisis == 'por_servicio')
                                    <th>Servicio</th>
                                @endif
                                <th>Total Cobrado</th>
                                <th>Facturas Cobradas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($resultados as $cliente)
                            <tr class="cliente-row" data-cliente-id="{{ $cliente->id }}" style="cursor: pointer;">
                                <td>{{ $cliente->id }}</td>
                                <td>
                                    {{ $cliente->name }} {{ $cliente->primerApellido }} {{ $cliente->segundoApellido }}
                                    @if($cliente->company)
                                        <br><small class="text-muted">{{ $cliente->company }}</small>
                                    @endif
                                </td>
                                <td>{{ $cliente->phone ?? 'N/A' }}</td>
                                @if($tipoAnalisis == 'por_categoria')
                                    <td>
                                        <span class="badge bg-info">{{ $cliente->categoria_servicio }}</span>
                                    </td>
                                @elseif($tipoAnalisis == 'por_servicio')
                                    <td>
                                        <span class="badge bg-success">{{ $cliente->servicio }}</span>
                                    </td>
                                @endif
                                <td class="text-success fw-bold">
                                    @if($tipoAnalisis == 'por_categoria')
                                        {{ number_format($cliente->total_por_categoria, 2, ',', '.') }} €
                                    @elseif($tipoAnalisis == 'por_servicio')
                                        {{ number_format($cliente->total_por_servicio, 2, ',', '.') }} €
                                    @else
                                        {{ number_format($cliente->total_facturado, 2, ',', '.') }} €
                                    @endif
                                </td>
                                <td>
                                    @if($tipoAnalisis == 'por_categoria')
                                        {{ $cliente->facturas_con_categoria }}
                                    @elseif($tipoAnalisis == 'por_servicio')
                                        {{ $cliente->facturas_con_servicio }}
                                    @else
                                        {{ $cliente->num_facturas }}
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">
                        Mostrando {{ $resultados->count() }} 
                        @if($limite && $resultados->count() == $limite)
                            de {{ $limite }} resultados
                        @else
                            resultados
                        @endif
                    </small>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">No hay datos disponibles</h5>
                    <p class="text-muted">Intenta cambiar los filtros o el rango de fechas</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Detalles del Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-labelledby="clienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clienteModalLabel">
                    <i class="bi bi-person-circle"></i> Detalles de Facturación del Cliente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="clienteInfo">
                    <!-- La información del cliente se cargará aquí dinámicamente -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar el selector de fechas
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startDate = selectedDates[0].toISOString().split('T')[0];
                    const endDate = selectedDates[1].toISOString().split('T')[0];
                    
                    // Actualizar los campos hidden
                    document.querySelector('input[name="fecha_inicio"]').value = startDate;
                    document.querySelector('input[name="fecha_fin"]').value = endDate;
                    
                    // Enviar el formulario
                    const form = instance.element.closest('form');
                    form.submit();
                }
            }
        });

        // Manejar cambio de tipo de análisis
        document.getElementById('tipoAnalisis').addEventListener('change', function() {
            const tipoAnalisis = this.value;
            
            // Construir URL limpia con solo los parámetros necesarios
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
            const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
            const limite = document.querySelector('input[name="limite"]').value;
            
            let url = `{{ route('analisis.estadisticas') }}?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&tipo_analisis=${tipoAnalisis}&limite=${limite}`;
            
            // Solo añadir parámetros específicos según el tipo
            if (tipoAnalisis === 'por_facturacion') {
                url += `&monto_minimo=0`;
            }
            
            // Redirigir inmediatamente para recargar la página con los nuevos datos
            window.location.href = url;
        });

        // Inicializar visibilidad de filtros
        function inicializarFiltros() {
            const tipoAnalisis = document.getElementById('tipoAnalisis').value;
            const filtroSelect = document.getElementById('filtroSelect');
            const montoMinimoInput = document.getElementById('montoMinimoInput');
            
            if (tipoAnalisis === 'por_facturacion') {
                filtroSelect.style.display = 'none';
                montoMinimoInput.style.display = 'block';
            } else if (tipoAnalisis === 'por_categoria' || tipoAnalisis === 'por_servicio') {
                filtroSelect.style.display = 'block';
                montoMinimoInput.style.display = 'none';
            } else {
                filtroSelect.style.display = 'none';
                montoMinimoInput.style.display = 'none';
            }
        }

        // Ejecutar al cargar
        inicializarFiltros();

        // Manejar clic en filas de clientes
        document.addEventListener('click', function(e) {
            const row = e.target.closest('.cliente-row');
            if (row) {
                const clienteId = row.getAttribute('data-cliente-id');
                cargarDetallesCliente(clienteId);
            }
        });

        // Función para cargar detalles del cliente
        function cargarDetallesCliente(clienteId) {
            // Mostrar loading
            document.getElementById('clienteInfo').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles del cliente...</p>
                </div>
            `;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('clienteModal'));
            modal.show();

            // Hacer petición AJAX para obtener detalles
            fetch(`/api/cliente-detalles/${clienteId}?fecha_inicio={{ $fechaInicio }}&fecha_fin={{ $fechaFin }}&tipo_analisis={{ $tipoAnalisis }}&filtro_id={{ $filtroId }}`)
                .then(response => response.json())
                .then(data => {
                    mostrarDetallesCliente(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('clienteInfo').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            Error al cargar los detalles del cliente. Inténtalo de nuevo.
                        </div>
                    `;
                });
        }

        // Función para mostrar los detalles del cliente
        function mostrarDetallesCliente(data) {
            const cliente = data.cliente;
            const facturas = data.facturas;
            const facturasFiltradas = data.facturas_filtradas || [];
            const resumen = data.resumen;

            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-person"></i> Información del Cliente</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> ${cliente.id}</p>
                                <p><strong>Nombre:</strong> ${cliente.name} ${cliente.primerApellido} ${cliente.segundoApellido}</p>
                                ${cliente.company ? `<p><strong>Empresa:</strong> ${cliente.company}</p>` : ''}
                                <p><strong>Teléfono:</strong> ${cliente.phone || 'N/A'}</p>
                                <p><strong>Email:</strong> ${cliente.email || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Resumen de Facturación</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Cobrado (Todas las facturas):</strong> <span class="text-success fw-bold">${resumen.total_cobrado} €</span></p>
                                <p><strong>Facturas Cobradas (Total):</strong> ${resumen.num_facturas}</p>
                                <p><strong>Promedio por Factura:</strong> ${resumen.promedio_factura} €</p>
                                ${resumen.tiene_filtros ? `
                                    <hr>
                                    <p><strong>Total Cobrado (Filtrado):</strong> <span class="text-primary fw-bold">${resumen.total_cobrado_filtrado} €</span></p>
                                    <p><strong>Facturas Cobradas (Filtradas):</strong> ${resumen.num_facturas_filtradas}</p>
                                    <p><strong>Promedio por Factura (Filtrado):</strong> ${resumen.promedio_factura_filtrado} €</p>
                                ` : ''}
                                <p><strong>Primera Factura:</strong> ${resumen.primera_factura || 'N/A'}</p>
                                <p><strong>Última Factura:</strong> ${resumen.ultima_factura || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            if (facturas && facturas.length > 0) {
                html += `
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Facturas Detalladas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nº Factura</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                    <th>Total</th>
                                                    <th>Servicios</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                `;
                
                facturas.forEach(factura => {
                    html += `
                        <tr>
                            <td>${factura.numero_factura || factura.id}</td>
                            <td>${factura.fecha_emision}</td>
                            <td><span class="badge bg-success">${factura.estado}</span></td>
                            <td class="text-success fw-bold">${factura.total} €</td>
                            <td>${factura.servicios || 'N/A'}</td>
                        </tr>
                    `;
                });
                
                html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            document.getElementById('clienteInfo').innerHTML = html;
        }
    });
</script>
@endsection