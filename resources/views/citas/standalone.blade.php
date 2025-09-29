<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Citas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mt-4">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-days"></i> Sistema de Gestión de Citas</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h5>✅ Sistema de Citas Funcionando</h5>
                            <p>El sistema de citas está operativo. Aquí puedes gestionar todas tus citas y reuniones.</p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5><i class="fas fa-calendar"></i> Calendario</h5>
                                        <p>Vista de calendario interactivo con FullCalendar</p>
                                        <button class="btn btn-primary" onclick="mostrarCalendario()">
                                            <i class="fas fa-eye"></i> Ver Calendario
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5><i class="fas fa-plus"></i> Nueva Cita</h5>
                                        <p>Crear una nueva cita o reunión</p>
                                        <button class="btn btn-success" onclick="crearCita()">
                                            <i class="fas fa-plus"></i> Crear Cita
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="calendario-container" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar-alt"></i> Calendario de Citas</h5>
                                </div>
                                <div class="card-body">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear cita -->
    <div class="modal fade" id="modal-cita" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Nueva Cita</h5>
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
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarCita()">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let calendar;
        
        window.mostrarCalendario = function() {
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
                            },
                            {
                                id: 2,
                                title: 'Llamada Cliente',
                                start: new Date(Date.now() + 24 * 60 * 60 * 1000),
                                end: new Date(Date.now() + 24 * 60 * 60 * 1000 + 60 * 60 * 1000),
                                color: '#10b981'
                            }
                        ];
                        successCallback(eventos);
                    },
                    eventClick: function(info) {
                        alert('Cita: ' + info.event.title + '\nInicio: ' + info.event.start.toLocaleString());
                    },
                    dateClick: function(info) {
                        crearCita(info.dateStr);
                    }
                });
                calendar.render();
            }
        }
        
        window.crearCita = function(fecha = null) {
            const modal = new bootstrap.Modal(document.getElementById('modal-cita'));
            if (fecha) {
                document.getElementById('fecha_inicio').value = fecha + 'T09:00';
                document.getElementById('fecha_fin').value = fecha + 'T10:00';
            }
            modal.show();
        }
        
        window.guardarCita = function() {
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
    });
    </script>
</body>
</html>

