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
                    <input type="text" name="buscar_cliente" class="form-control" style="max-width: 200px;" placeholder="Buscar por nombre..." value="{{ request('buscar_cliente') }}">
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
            
            <!-- Botón para Batch Calls -->
            @if($resultados->count() > 0)
                <div class="mt-3 mb-3">
                    <button type="button" class="btn btn-success" id="btnBatchCall" onclick="abrirModalBatchCall()">
                        <i class="bi bi-telephone-outbound"></i> Enviar Batch Call a Clientes Filtrados
                        <span class="badge bg-light text-dark ms-2" id="totalClientesConTelefono">0</span>
                    </button>
                    <small class="text-muted ms-2">Se enviarán llamadas automáticas a todos los clientes con teléfono</small>
                </div>
            @endif
            
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

<!-- Modal de Batch Call -->
<div class="modal fade" id="batchCallModal" tabindex="-1" aria-labelledby="batchCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchCallModalLabel">
                    <i class="bi bi-telephone-outbound"></i> Configurar Batch Call a ElevenLabs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBatchCall">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Información:</strong> Se enviarán llamadas automáticas a <span id="totalLlamadas" class="fw-bold">0</span> clientes con teléfono válido.
                        Los números serán procesados y validados automáticamente con IA.
                    </div>

                    <div class="mb-3">
                        <label for="callName" class="form-label">Nombre de la Campaña <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="callName" name="call_name" required 
                               placeholder="Ej: Campaña Marzo 2024">
                        <small class="text-muted">Identificador de esta campaña de llamadas</small>
                    </div>

                    <div class="mb-3">
                        <label for="agentId" class="form-label">Agente <span class="text-danger">*</span></label>
                        <select class="form-select" id="agentId" name="agent_id" required disabled>
                            <option value="">Cargando agente...</option>
                        </select>
                        <small class="text-muted">Agente predeterminado: Hera Saliente (bloqueado)</small>
                    </div>

                    <div class="mb-3">
                        <label for="agentPhoneNumberId" class="form-label">Número de Teléfono <span class="text-danger">*</span></label>
                        <select class="form-select" id="agentPhoneNumberId" name="agent_phone_number_id" required disabled>
                            <option value="">Primero selecciona un agente...</option>
                        </select>
                        <small class="text-muted">Número de teléfono desde el cual se realizarán las llamadas</small>
                    </div>

                    <div class="mb-3">
                        <label for="firstMessage" class="form-label">Mensaje Inicial (Opcional)</label>
                        <textarea class="form-control" id="firstMessage" name="first_message" rows="3" 
                                  placeholder="Ej: Hola {nombre}, llamo de Hawkins para informarte sobre..."></textarea>
                        <small class="text-muted">
                            <strong>Usa {nombre} para personalizar:</strong> Se reemplazará con el nombre de cada cliente.<br>
                            Ejemplo: "Hola {nombre}, te llamo de Hawkins..." → "Hola Juan Pérez, te llamo de Hawkins..."<br>
                            Si no incluyes {nombre}, se añadirá automáticamente al inicio.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Clientes a Llamar</label>
                        <div id="listaClientesBatchCall" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando clientes...</p>
                            </div>
                        </div>
                    </div>

                    <div id="alertaBatchCall"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnEnviarBatchCall" onclick="enviarBatchCall()">
                    <i class="bi bi-send"></i> Enviar Batch Call
                </button>
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

        // ==================== BATCH CALL FUNCTIONALITY ====================
        
        let clientesParaBatchCall = [];

        // Función para abrir el modal y cargar los clientes
        window.abrirModalBatchCall = function() {
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('batchCallModal'));
            modal.show();

            // Cargar los agentes (automáticamente seleccionará Hera Saliente y sus números)
            cargarAgentes();

            // Cargar los clientes filtrados
            cargarClientesParaBatchCall();
        }

        // Función para cargar la lista de agentes y seleccionar automáticamente "Hera Saliente"
        function cargarAgentes() {
            fetch('/api/elevenlabs-monitoring/batch-calls/agentes')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        const selectAgente = document.getElementById('agentId');
                        
                        // Buscar el agente "Hera Saliente"
                        const heraSaliente = data.data.find(agente => 
                            agente.name.toLowerCase().includes('hera') && 
                            agente.name.toLowerCase().includes('saliente')
                        );
                        
                        if (heraSaliente) {
                            // Seleccionar automáticamente Hera Saliente
                            selectAgente.innerHTML = `<option value="${heraSaliente.agent_id}" selected>${heraSaliente.name}</option>`;
                            selectAgente.disabled = true; // Mantener bloqueado
                            
                            console.log('Agente Hera Saliente seleccionado automáticamente:', heraSaliente);
                            
                            // Cargar números de teléfono automáticamente
                            cargarPhoneNumbers();
                        } else {
                            // Si no se encuentra Hera Saliente, cargar todos los agentes
                            selectAgente.innerHTML = '<option value="">Agente Hera Saliente no encontrado</option>';
                            console.warn('Agente Hera Saliente no encontrado');
                            mostrarAlertaBatchCall('warning', 'No se encontró el agente Hera Saliente.');
                        }
                    } else {
                        console.error('Error al cargar agentes:', data.message);
                        mostrarAlertaBatchCall('warning', 'No se pudieron cargar los agentes. Verifica la configuración.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarAlertaBatchCall('warning', 'Error al cargar la lista de agentes.');
                });
        }

        // Función para cargar phone numbers del agente Hera Saliente
        window.cargarPhoneNumbers = function() {
            const agentId = document.getElementById('agentId').value;
            const selectPhoneNumber = document.getElementById('agentPhoneNumberId');
            
            if (!agentId) {
                selectPhoneNumber.innerHTML = '<option value="">Agente no seleccionado</option>';
                return;
            }
            
            // Mostrar loading
            selectPhoneNumber.disabled = true;
            selectPhoneNumber.innerHTML = '<option value="">Cargando números...</option>';

            // Obtener números del agente seleccionado (Hera Saliente)
            fetch(`/api/elevenlabs-monitoring/batch-calls/agentes/${agentId}/phone-numbers`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        selectPhoneNumber.innerHTML = '<option value="">Selecciona un número...</option>';
                        
                        if (Array.isArray(data.data) && data.data.length > 0) {
                            data.data.forEach(phoneNumber => {
                                const option = document.createElement('option');
                                option.value = phoneNumber.phone_number_id;
                                
                                // Mostrar: label + agente asignado + provider
                                let displayText = phoneNumber.label || phoneNumber.phone_number;
                                
                                // Añadir nombre del agente asignado
                                if (phoneNumber.assigned_agent_name) {
                                    displayText += ` → ${phoneNumber.assigned_agent_name}`;
                                }
                                
                                // Añadir provider
                                if (phoneNumber.provider) {
                                    displayText += ` (${phoneNumber.provider})`;
                                }
                                
                                // Añadir indicador de outbound
                                if (phoneNumber.supports_outbound) {
                                    displayText += ' ✓';
                                }
                                
                                option.textContent = displayText;
                                selectPhoneNumber.appendChild(option);
                            });
                            selectPhoneNumber.disabled = false;
                            
                            console.log('Phone numbers cargados (todos):', data.data.length);
                        } else {
                            selectPhoneNumber.innerHTML = '<option value="">No hay números de teléfono disponibles</option>';
                            console.warn('No hay números de teléfono en la cuenta');
                        }
                    } else {
                        selectPhoneNumber.innerHTML = '<option value="">Error al cargar números</option>';
                        console.error('Error al cargar phone numbers:', data.message);
                        mostrarAlertaBatchCall('warning', 'No se pudieron cargar los números de teléfono del agente.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    selectPhoneNumber.innerHTML = '<option value="">Error al cargar números</option>';
                    mostrarAlertaBatchCall('warning', 'Error al cargar los números de teléfono.');
                });
        }

        // Función para cargar los clientes con los filtros actuales
        function cargarClientesParaBatchCall() {
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
            const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
            const tipoAnalisis = document.querySelector('select[name="tipo_analisis"]').value;
            const filtroId = document.querySelector('select[name="filtro_id"]').value;
            const montoMinimo = document.querySelector('input[name="monto_minimo"]').value;
            const limite = document.querySelector('input[name="limite"]').value;

            const url = new URL('{{ route("api.telefonos.filtrados") }}');
            url.searchParams.append('fecha_inicio', fechaInicio);
            url.searchParams.append('fecha_fin', fechaFin);
            url.searchParams.append('tipo_analisis', tipoAnalisis);
            if (filtroId) url.searchParams.append('filtro_id', filtroId);
            if (montoMinimo) url.searchParams.append('monto_minimo', montoMinimo);
            if (limite) url.searchParams.append('limite', limite);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        clientesParaBatchCall = data.clientes;
                        mostrarClientesBatchCall(data.clientes);
                        document.getElementById('totalLlamadas').textContent = data.total;
                        document.getElementById('totalClientesConTelefono').textContent = data.total;
                    } else {
                        mostrarErrorBatchCall('Error al cargar los clientes: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarErrorBatchCall('Error al cargar los clientes. Por favor, inténtalo de nuevo.');
                });
        }

        // Función para mostrar la lista de clientes
        function mostrarClientesBatchCall(clientes) {
            const lista = document.getElementById('listaClientesBatchCall');
            
            if (clientes.length === 0) {
                lista.innerHTML = `
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle"></i>
                        No hay clientes con teléfono válido en los resultados filtrados.
                    </div>
                `;
                document.getElementById('btnEnviarBatchCall').disabled = true;
                return;
            }

            document.getElementById('btnEnviarBatchCall').disabled = false;

            let html = '<div class="list-group">';
            clientes.forEach((cliente, index) => {
                html += `
                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${cliente.nombre}</strong>
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-telephone"></i> ${cliente.telefono}
                            </small>
                        </div>
                        <span class="badge bg-primary rounded-pill">${index + 1}</span>
                    </div>
                `;
            });
            html += '</div>';

            lista.innerHTML = html;
        }

        // Función para enviar el batch call
        window.enviarBatchCall = function() {
            // Validar formulario
            const callName = document.getElementById('callName').value.trim();
            const agentId = document.getElementById('agentId').value.trim();
            const agentPhoneNumberId = document.getElementById('agentPhoneNumberId').value.trim();
            const firstMessage = document.getElementById('firstMessage').value.trim();

            if (!callName || !agentId || !agentPhoneNumberId) {
                mostrarAlertaBatchCall('danger', 'Por favor, completa todos los campos obligatorios.');
                return;
            }

            if (clientesParaBatchCall.length === 0) {
                mostrarAlertaBatchCall('danger', 'No hay clientes para enviar el batch call.');
                return;
            }

            // Deshabilitar botón y mostrar loading
            const btnEnviar = document.getElementById('btnEnviarBatchCall');
            btnEnviar.disabled = true;
            
            // Mensaje de loading diferente si hay first_message
            if (firstMessage) {
                btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Actualizando agente y enviando...';
            } else {
                btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';
            }

            // Preparar datos
            const datos = {
                call_name: callName,
                agent_id: agentId,
                agent_phone_number_id: agentPhoneNumberId,
                clientes: clientesParaBatchCall
            };

            // Agregar first_message si está presente
            if (firstMessage) {
                datos.first_message = firstMessage;
            }

            // Enviar petición
            fetch('/api/elevenlabs-monitoring/batch-calls/submit-clientes-filtrados', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let mensaje = `¡Batch call enviado exitosamente! <br>
                        <strong>Estadísticas:</strong><br>
                        - Total clientes: ${data.estadisticas.total_clientes}<br>
                        - Llamadas programadas: ${data.estadisticas.llamadas_programadas}<br>`;
                    
                    if (data.estadisticas.con_mensaje_personalizado > 0) {
                        mensaje += `- Con mensaje personalizado: ${data.estadisticas.con_mensaje_personalizado}<br>`;
                    }
                    
                    mensaje += `- Errores: ${data.estadisticas.errores}`;
                    
                    mostrarAlertaBatchCall('success', mensaje);
                    
                    // Cerrar modal después de 3 segundos
                    setTimeout(() => {
                        bootstrap.Modal.getInstance(document.getElementById('batchCallModal')).hide();
                        // Limpiar formulario
                        document.getElementById('formBatchCall').reset();
                        // Resetear selects
                        document.getElementById('agentPhoneNumberId').disabled = true;
                        document.getElementById('agentPhoneNumberId').innerHTML = '<option value="">Primero selecciona un agente...</option>';
                        document.getElementById('firstMessage').value = '';
                        document.getElementById('alertaBatchCall').innerHTML = '';
                    }, 3000);
                } else {
                    mostrarAlertaBatchCall('danger', 'Error al enviar batch call: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarAlertaBatchCall('danger', 'Error al enviar batch call. Por favor, inténtalo de nuevo.');
            })
            .finally(() => {
                // Rehabilitar botón
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = '<i class="bi bi-send"></i> Enviar Batch Call';
            });
        }

        // Función para mostrar alertas en el modal
        function mostrarAlertaBatchCall(tipo, mensaje) {
            const alerta = document.getElementById('alertaBatchCall');
            alerta.innerHTML = `
                <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                    ${mensaje}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        // Función para mostrar errores al cargar clientes
        function mostrarErrorBatchCall(mensaje) {
            const lista = document.getElementById('listaClientesBatchCall');
            lista.innerHTML = `
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-exclamation-triangle"></i> ${mensaje}
                </div>
            `;
            document.getElementById('btnEnviarBatchCall').disabled = true;
        }

        // Actualizar el badge del botón al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Cargar el número de clientes con teléfono
            const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
            const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
            const tipoAnalisis = document.querySelector('select[name="tipo_analisis"]').value;
            const filtroId = document.querySelector('select[name="filtro_id"]').value;
            const montoMinimo = document.querySelector('input[name="monto_minimo"]').value;
            const limite = document.querySelector('input[name="limite"]').value;

            const url = new URL('{{ route("api.telefonos.filtrados") }}');
            url.searchParams.append('fecha_inicio', fechaInicio);
            url.searchParams.append('fecha_fin', fechaFin);
            url.searchParams.append('tipo_analisis', tipoAnalisis);
            if (filtroId) url.searchParams.append('filtro_id', filtroId);
            if (montoMinimo) url.searchParams.append('monto_minimo', montoMinimo);
            if (limite) url.searchParams.append('limite', limite);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('totalClientesConTelefono').textContent = data.total;
                    }
                })
                .catch(error => {
                    console.error('Error al cargar total de clientes:', error);
                });
        });
    });
</script>
@endsection