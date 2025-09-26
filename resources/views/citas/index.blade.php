@extends('layouts.app')

@section('title', 'Gestión de Citas')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gestión de Citas</h3>
                <p class="text-subtitle text-muted">Calendario avanzado para gestión de citas y reuniones</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Citas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="total-citas">0</h4>
                                <p class="mb-0">Total Citas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="citas-hoy">0</h4>
                                <p class="mb-0">Hoy</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="citas-semana">0</h4>
                                <p class="mb-0">Esta Semana</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-week fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="citas-vencidas">0</h4>
                                <p class="mb-0">Vencidas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="citas-seguimiento">0</h4>
                                <p class="mb-0">Seguimiento</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tasks fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0" id="estados-diferentes">0</h4>
                                <p class="mb-0">Estados</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-pie fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Filtros</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro-gestor">Gestor</label>
                                    <select class="form-select select2" id="filtro-gestor">
                                        <option value="">Todos los gestores</option>
                                        @foreach($gestores as $gestor)
                                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro-cliente">Cliente</label>
                                    <select class="form-select select2" id="filtro-cliente">
                                        <option value="">Todos los clientes</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filtro-estado">Estado</label>
                                    <select class="form-select" id="filtro-estado">
                                        <option value="">Todos los estados</option>
                                        <option value="programada">Programada</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completada">Completada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary" id="aplicar-filtros">
                                            <i class="bi bi-funnel"></i> Aplicar
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="limpiar-filtros">
                                            <i class="bi bi-x-circle"></i> Limpiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Calendario de Citas</h4>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary" id="nueva-cita">
                                <i class="bi bi-plus-circle"></i> Nueva Cita
                            </button>
                            <button type="button" class="btn btn-info" id="ver-proximas">
                                <i class="bi bi-clock"></i> Próximas
                            </button>
                            <button type="button" class="btn btn-warning" id="ver-vencidas">
                                <i class="bi bi-exclamation-triangle"></i> Vencidas
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para crear/editar cita -->
<div class="modal fade" id="modal-cita" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-modal-cita">Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-cita">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo *</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="reunion">Reunión</option>
                                    <option value="llamada">Llamada</option>
                                    <option value="visita">Visita</option>
                                    <option value="presentacion">Presentación</option>
                                    <option value="seguimiento">Seguimiento</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha y Hora de Inicio *</label>
                                <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin" class="form-label">Fecha y Hora de Fin *</label>
                                <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select select2" id="cliente_id" name="cliente_id">
                                    <option value="">Sin cliente</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="gestor_id" class="form-label">Gestor Asignado</label>
                                <select class="form-select select2" id="gestor_id" name="gestor_id">
                                    <option value="">Sin asignar</option>
                                    @foreach($gestores as $gestor)
                                        <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input type="text" class="form-control" id="ubicacion" name="ubicacion">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="#3b82f6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="programada">Programada</option>
                                    <option value="confirmada">Confirmada</option>
                                    <option value="en_progreso">En Progreso</option>
                                    <option value="completada">Completada</option>
                                    <option value="cancelada">Cancelada</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="guardar-cita">
                        <i class="bi bi-save"></i> Guardar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .fc-event {
        cursor: pointer;
        border-radius: 4px;
        font-size: 0.85em;
    }
    .fc-event:hover {
        opacity: 0.8;
        transform: scale(1.02);
        transition: all 0.2s ease;
    }
    .fc-button {
        border-radius: 6px !important;
    }
    .fc-toolbar {
        margin-bottom: 1.5rem !important;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    let citasData = [];
    let citaActual = null;

    console.log('🚀 Iniciando sistema de citas...');

    // Inicializar Select2
    $('.select2').select2({
        placeholder: 'Seleccionar...',
        allowClear: true,
        width: '100%'
    });

    // Inicializar calendario
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
        console.error('❌ No se encontró el elemento calendar');
        return;
    }

    try {
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
            height: 'auto',
            events: function(info, successCallback, failureCallback) {
                console.log('📅 Cargando eventos...');
                cargarCitas(info.start, info.end, successCallback);
            },
            eventClick: function(info) {
                console.log('🖱️ Clic en evento:', info.event.title);
                mostrarDetalleCita(info.event);
            },
            dateClick: function(info) {
                console.log('📅 Clic en fecha:', info.dateStr);
                crearNuevaCita(info.dateStr);
            },
            eventDrop: function(info) {
                console.log('🔄 Evento movido:', info.event.title);
                actualizarFechaCita(info.event);
            },
            eventResize: function(info) {
                console.log('📏 Evento redimensionado:', info.event.title);
                actualizarDuracionCita(info.event);
            }
        });

        calendar.render();
        console.log('✅ Calendario renderizado correctamente');
    } catch (error) {
        console.error('❌ Error al inicializar calendario:', error);
    }

    // Cargar estadísticas
    cargarEstadisticas();

    // Event listeners
    document.getElementById('nueva-cita').addEventListener('click', function() {
        console.log('➕ Botón Nueva Cita clickeado');
        mostrarModalCita();
    });

    document.getElementById('ver-proximas').addEventListener('click', function() {
        console.log('⏰ Botón Próximas clickeado');
        mostrarProximasCitas();
    });

    document.getElementById('ver-vencidas').addEventListener('click', function() {
        console.log('⚠️ Botón Vencidas clickeado');
        mostrarVencidasCitas();
    });

    document.getElementById('aplicar-filtros').addEventListener('click', function() {
        console.log('🔍 Aplicando filtros...');
        aplicarFiltros();
    });

    document.getElementById('limpiar-filtros').addEventListener('click', function() {
        console.log('🧹 Limpiando filtros...');
        limpiarFiltros();
    });

    document.getElementById('form-cita').addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('💾 Guardando cita...');
        guardarCita();
    });

    // Funciones
    function cargarCitas(start, end, callback) {
        console.log('📅 Cargando citas desde', start, 'hasta', end);
        
        // Simular datos de prueba
        const eventos = [
            {
                id: 1,
                title: 'Reunión de Prueba',
                start: new Date(),
                end: new Date(Date.now() + 2 * 60 * 60 * 1000),
                color: '#3b82f6',
                extendedProps: {
                    tipo: 'reunion',
                    cliente: 'Cliente de Prueba',
                    gestor: 'Gestor Asignado',
                    descripcion: 'Reunión de prueba del sistema',
                    ubicacion: 'Oficina Principal'
                }
            },
            {
                id: 2,
                title: 'Llamada Cliente',
                start: new Date(Date.now() + 24 * 60 * 60 * 1000),
                end: new Date(Date.now() + 24 * 60 * 60 * 1000 + 60 * 60 * 1000),
                color: '#10b981',
                extendedProps: {
                    tipo: 'llamada',
                    cliente: 'Cliente Importante',
                    gestor: 'Gestor Principal',
                    descripcion: 'Llamada de seguimiento',
                    ubicacion: 'Remoto'
                }
            },
            {
                id: 3,
                title: 'Presentación Proyecto',
                start: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000),
                end: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000 + 90 * 60 * 1000),
                color: '#f59e0b',
                extendedProps: {
                    tipo: 'presentacion',
                    cliente: 'Cliente Corporativo',
                    gestor: 'Gestor Senior',
                    descripcion: 'Presentación del nuevo proyecto',
                    ubicacion: 'Sala de Conferencias'
                }
            }
        ];
        
        console.log('📅 Eventos cargados:', eventos.length);
        callback(eventos);
    }

    function cargarEstadisticas() {
        // Simular estadísticas
        document.getElementById('total-citas').textContent = '3';
        document.getElementById('citas-hoy').textContent = '1';
        document.getElementById('citas-semana').textContent = '3';
        document.getElementById('citas-vencidas').textContent = '0';
        document.getElementById('citas-seguimiento').textContent = '1';
        document.getElementById('estados-diferentes').textContent = '2';
    }

    function mostrarDetalleCita(event) {
        console.log('👁️ Mostrando detalles de:', event.title);
        alert(`Cita: ${event.title}\nInicio: ${event.start.toLocaleString()}\nFin: ${event.end.toLocaleString()}`);
    }

    function mostrarModalCita() {
        console.log('📝 Abriendo modal de nueva cita');
        document.getElementById('titulo-modal-cita').textContent = 'Nueva Cita';
        document.getElementById('form-cita').reset();
        document.getElementById('color').value = '#3b82f6';
        citaActual = null;
        
        // Reinicializar Select2 en el modal
        $('#cliente_id').select2({
            placeholder: 'Sin cliente',
            allowClear: true,
            width: '100%'
        });
        $('#gestor_id').select2({
            placeholder: 'Sin asignar',
            allowClear: true,
            width: '100%'
        });
        
        new bootstrap.Modal(document.getElementById('modal-cita')).show();
    }

    function crearNuevaCita(fecha) {
        console.log('📅 Creando nueva cita para:', fecha);
        mostrarModalCita();
        document.getElementById('fecha_inicio').value = fecha + 'T09:00';
        document.getElementById('fecha_fin').value = fecha + 'T10:00';
    }

    function guardarCita() {
        const form = document.getElementById('form-cita');
        const formData = new FormData(form);
        const titulo = formData.get('titulo');
        
        console.log('💾 Guardando cita:', titulo);
        alert(`Cita guardada: ${titulo}`);
        bootstrap.Modal.getInstance(document.getElementById('modal-cita')).hide();
        
        // Recargar calendario
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    function mostrarProximasCitas() {
        console.log('⏰ Mostrando próximas citas');
        alert('Próximas citas:\n• Reunión de Prueba (Hoy)\n• Llamada Cliente (Mañana)\n• Presentación Proyecto (Pasado mañana)');
    }

    function mostrarVencidasCitas() {
        console.log('⚠️ Mostrando citas vencidas');
        alert('No hay citas vencidas');
    }

    function aplicarFiltros() {
        const gestor = document.getElementById('filtro-gestor').value;
        const cliente = document.getElementById('filtro-cliente').value;
        const estado = document.getElementById('filtro-estado').value;
        
        console.log('🔍 Filtros aplicados:', { gestor, cliente, estado });
        alert(`Filtros aplicados:\nGestor: ${gestor || 'Todos'}\nCliente: ${cliente || 'Todos'}\nEstado: ${estado || 'Todos'}`);
        
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    function limpiarFiltros() {
        $('#filtro-gestor').val(null).trigger('change');
        $('#filtro-cliente').val(null).trigger('change');
        document.getElementById('filtro-estado').value = '';
        
        console.log('🧹 Filtros limpiados');
        alert('Filtros limpiados');
        
        if (calendar) {
            calendar.refetchEvents();
        }
    }

    function actualizarFechaCita(event) {
        console.log('🔄 Actualizando fecha de:', event.title);
    }

    function actualizarDuracionCita(event) {
        console.log('📏 Actualizando duración de:', event.title);
    }

    console.log('✅ Sistema de citas inicializado correctamente');
});
</script>
@endpush