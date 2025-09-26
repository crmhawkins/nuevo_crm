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
        </div>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Sistema de Citas</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5>✅ Sistema de Citas Funcionando</h5>
                            <p>El sistema de citas está operativo. Aquí puedes gestionar todas tus citas y reuniones.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>📅 Calendario</h5>
                                        <p>Vista de calendario interactivo con FullCalendar</p>
                                        <button class="btn btn-primary" onclick="mostrarCalendario()">Ver Calendario</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>➕ Nueva Cita</h5>
                                        <p>Crear una nueva cita o reunión</p>
                                        <button class="btn btn-success" onclick="crearCita()">Crear Cita</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="calendario-container" style="display: none;">
                            <div id="calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para crear cita -->
<div class="modal fade" id="modal-cita" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Cita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-cita">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha y Hora de Inicio *</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha y Hora de Fin *</label>
                        <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="reunion">Reunión</option>
                            <option value="llamada">Llamada</option>
                            <option value="visita">Visita</option>
                            <option value="presentacion">Presentación</option>
                            <option value="seguimiento">Seguimiento</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarCita()">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    
    function mostrarCalendario() {
        const container = document.getElementById('calendario-container');
        container.style.display = 'block';
        
        if (!calendar) {
            const calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día'
                },
                events: function(info, successCallback, failureCallback) {
                    // Simular datos de prueba
                    const eventos = [
                        {
                            id: 1,
                            title: 'Reunión de Prueba',
                            start: new Date(),
                            end: new Date(Date.now() + 2 * 60 * 60 * 1000),
                            color: '#3b82f6'
                        }
                    ];
                    successCallback(eventos);
                },
                eventClick: function(info) {
                    alert('Cita: ' + info.event.title);
                },
                dateClick: function(info) {
                    crearCita(info.dateStr);
                }
            });
            calendar.render();
        }
    }
    
    window.mostrarCalendario = mostrarCalendario;
    
    function crearCita(fecha = null) {
        const modal = new bootstrap.Modal(document.getElementById('modal-cita'));
        if (fecha) {
            document.getElementById('fecha_inicio').value = fecha + 'T09:00';
            document.getElementById('fecha_fin').value = fecha + 'T10:00';
        }
        modal.show();
    }
    
    window.crearCita = crearCita;
    
    function guardarCita() {
        const form = document.getElementById('form-cita');
        const formData = new FormData(form);
        
        // Simular guardado
        alert('Cita guardada: ' + formData.get('titulo'));
        bootstrap.Modal.getInstance(document.getElementById('modal-cita')).hide();
        
        // Recargar calendario si está visible
        if (calendar) {
            calendar.refetchEvents();
        }
    }
    
    window.guardarCita = guardarCita;
});
</script>
@endpush
