@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('css')
<style>
    /* Estilos básicos */
    .card-header {
        background-color: #f8f9fa;
        color: #333;
        font-size: 1.2rem;
        border-bottom: 1px solid #e3e6f0;
    }

    .side-column {
        margin-bottom: 20px;
    }

    .jornada {
        padding: 10px 0;
        font-size: 1.2rem;
        text-align: center;
        color: white;
        cursor: pointer;
    }

    .view-selector {
        margin-top: 10px;
        text-align: center;
    }

    .todo-item {
        background-color: #f0f0f0;
        border-radius: 5px;
        margin: 5px;
        padding: 10px;
    }

    #todoContainer::-webkit-scrollbar {
        width: 8px; /* Ancho del scrollbar */
        height: 115px;

    }

    #todoContainer::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1); /* Fondo semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    #todoContainer::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.5); /* Pulgar negro semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    #todoContainer::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.7); /* Pulgar más oscuro cuando se desplaza */
    }

    /* Estilos para la responsividad del calendario */
    .calendar-view {
        display: grid;
        gap: 10px;
        padding: 10px;
    }

    .calendar-week-view {
        grid-template-rows: repeat(7, 1fr);
    }

    .calendar-month-view {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); /* Hace que las columnas sean más flexibles */
    }

    .calendar-day {
        background-color: #f8f9fa;
        border: 1px solid #e3e6f0;
        padding: 10px;
        min-height: 100px;
        display: flex;
        flex-direction: column;
    }

    .calendar-item {
        background-color: #f0f0f0;
        border-radius: 5px;
        margin: 5px 0;
        padding: 10px;
    }

    @media (max-width: 768px) {
        .calendar-month-view {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); /* Ajusta las columnas para pantallas pequeñas */
        }

        .view-selector button,
        .jornada {
            font-size: 0.9rem; /* Reduce el tamaño de la fuente para ajustarse a pantallas más pequeñas */
        }

        .side-column,
        .card-body {
            padding: 5px; /* Reduce el padding para más espacio */
        }
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Dashboard</h3>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <div class="row justify-end ">
                     <h2 id="timer" class="display-6 font-weight-bold col-4">00:00:00</h2>
                    <button id="startJornadaBtn" class="btn jornada btn-primary mx-2 col-3" onclick="startJornada()">Inicio Jornada</button>
                    <button id="startPauseBtn" class="btn jornada btn-secondary mx-2 col-3" onclick="startPause()" style="display:none;">Iniciar Pausa</button>
                    <button id="endPauseBtn" class="btn jornada btn-secondary mx-2 col-3" onclick="endPause()" style="display:none;">Finalizar Pausa</button>
                    <button id="endJornadaBtn" class="btn jornada btn-danger mx-2 col-3" onclick="endJornada()" style="display:none;">Fin de Jornada</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card2 mt-4">
        <div class="card-body2">
            <div class="row justify-between">
                <div class="col-md-6">
                    <div class="side-column">
                        <div class="mb-3 card-body">
                            <h5 class="card-title fw-bold">Presupuestos</h5>
                            <div class="row row-cols-1 row-cols-3 g-4 mb-3 ">
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body p-3">
                                            <h5 class="card-title m-0 text-color-4 fw-bold">Pendientes de confirmar</h5>
                                            <span class="display-6 m-0"><b>{{count($user->presupuestosPorEstado(1))}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body p-3">
                                            <h5 class="card-title m-0 text-color-4 fw-bold">Pendientes de aceptar</h5>
                                            <span class="display-6 m-0"><b>{{count($user->presupuestosPorEstado(2))}}</b></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card h-100">
                                        <div class="card-body p-3">
                                            <h5 class="card-title m-0 text-color-4 fw-bold">Aceptados</h5>
                                            <span class="display-6 m-0"><b>{{count($user->presupuestosPorEstado(3))}}</b></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="{{route('presupuesto.create')}}" class="btn btn-outline-primary">Nuevo Presupuesto</a>
                            <a href="{{route('presupuestos.index')}}" class="btn btn-outline-secondary">Ver Presupuestos</a>
                        </div>
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            <div class="col">
                                <div class="card2">
                                    <div class="mb-3 card-body">
                                        <h5 class="card-title fw-bold">Petición</h5>
                                        <div class="row mb-3 ">
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <h5 class="card-title m-0 text-color-4  fw-bold">Pendientes</h5>
                                                        <span class="display-6 m-0"><b>{{count($user->peticionesPendientes())}}</b></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="{{route('peticion.create')}}" class="btn btn-outline-primary">Nueva Petición</a>
                                        <a href="{{route('peticion.index')}}" class="btn btn-outline-secondary">Ver Peticiones</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card2">
                                    <div class="mb-3 card-body">
                                        <h5 class="card-title fw-bold">Ordenes</h5>
                                        <div class="row mb-3 ">
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <h5 class="card-title m-0 text-color-4  fw-bold">Pendientes</h5>
                                                        <span class="display-6 m-0"><b>{{count($user->presupuestosPorEstado(1))}}</b></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <a  class="btn btn-outline-primary">Nueva Orden</a>
                                        <a  class="btn btn-outline-secondary">Ver Ordenes</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 card-body">
                            <a href="{{route('clientes.index')}}" class="btn btn-outline-primary mb-2">Ver Clientes</a>
                            <a href="{{route('tareas.cola')}}" class="btn btn-outline-primary mb-2">Ver Status Proyectos</a>
                            <a href="{{route('tareas.cola')}}" class="btn btn-outline-primary mb-2">Ver Cola de producción</a>
                            <a href="{{route('tareas.index')}}" class="btn btn-outline-primary mb-2">Ver Tareas</a>
                            <a href="{{route('tareas.index')}}" class="btn btn-outline-primary mb-2">Ver Producción</a>
                            <a class="btn btn-outline-primary mb-2">Ver Proveedores</a>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="side-column">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="col-6">
                                        <div class="list-group my-2 text-center">
                                            <a class="list-group-item list-group-item-action"
                                                href="#list-estadisticas">Contrato</a>
                                            <a class="list-group-item list-group-item-action"
                                                href="#list-estadisticas">Nomina</a>
                                            <a class="list-group-item list-group-item-action"
                                                href="#list-estadisticas">Vacaciones</a>
                                            <a class="list-group-item list-group-item-action"
                                                href="#list-notificaciones">Datos</a>
                                            <a class="list-group-item list-group-item-action"
                                                href="#list-vacaciones">Contraseñas</a>
                                        </div>
                                    </div>
                                    <div class="col-6 text-center">
                                        @if ($user->image == null)
                                            <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 100px;" src="{{asset('assets/images/guest.webp')}}" />
                                        @else
                                            <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 100px;" src="{{ asset('/storage/avatars/'.$user->image) }}" />
                                        @endif
                                        <h5 class="my-3">{{$user->name}}&nbsp;{{$user->surname}}</h5>
                                        <p class="text-muted mb-1">{{$user->departamento->name}}</p>
                                        <p class="text-muted mb-4">{{$user->acceso->name}}</p>
                                        <div class="d-flex justify-content-end align-items-center">
                                            <input type="color" class="form-control  form-control-color" style="padding: 0.4rem" id="color">
                                            <label for="color" class="form-label m-2">Color</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="card2 mt-4">
                                    <div class="card-body2">
                                        <div id="calendar" class="p-4" style="margin-top: 0.75rem; margin-bottom: 0.75rem;  overflow-y: auto; border-color:black; border-width: thin; border-radius: 20px;" >
                                            <!-- Aquí se renderizarán las tareas según la vista seleccionada -->
                                        </div>
                                    </div>
                                </div>
                                <div class=" d-flex justify-content-center">
                                    <button class="btn btn-primary mx-2">Enviar Archivos</button>
                                    <button class="btn btn-secondary mx-2">Correo</button>
                                    <button class="btn btn-primary mx-2">Llamadas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="eventModalLabel">Nuevo Evento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{route('event.store')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="row">
                    <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                    <label for="url" class="form-label">URL</label>
                    <input type="text" class="form-control" id="url" name="url">
                    </div>
                    <div class="mb-3">
                    <label for="color" class="form-label">Color</label>
                    <input type="color" class="form-control  form-control-color" style="padding: 0.4rem" id="color" name="color">
                    </div>
                    <div class="mb-3 row">
                        <div class="mb-3 col-6">
                            <label for="start" class="form-label">Inicio</label>
                            <input type="datetime-local" class="form-control" id="start" name="start" required>
                        </div>
                        <div class="mb-3 col-6">
                            <label for="end" class="form-label">Fin</label>
                            <input type="datetime-local" class="form-control" id="end" name="end">
                        </div>
                    </div>
                    <input type="hidden" class="form-control" id="admin_user_id" name="admin_user_id" value="{{Auth::user()->id}}">
                </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" id="guardar" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
@include('partials.toast')

<script>
    let timerState = '{{ $jornadaActiva ? "running" : "stopped" }}'
    let timerTime = {{ $timeWorkedToday }}; // In seconds, initialized with the time worked today

    function updateTime() {
        let hours = Math.floor(timerTime / 3600);
        let minutes = Math.floor((timerTime % 3600) / 60);
        let seconds = timerTime % 60;

        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        document.getElementById('timer').textContent = `${hours}:${minutes}:${seconds}`;
    }

    function startTimer() {
            timerState = 'running';
            timerInterval = setInterval(() => {
                timerTime++;
                updateTime();
            }, 1000);
    }

    function stopTimer() {
            clearInterval(timerInterval);
            timerState = 'stopped';
    }

    function startJornada() {
        fetch('/start-jornada', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer();
                    document.getElementById('startJornadaBtn').style.display = 'none';
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endJornadaBtn').style.display = 'block';
                }
            });
    }

    function endJornada() {
        fetch('/end-jornada', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    stopTimer();
                    document.getElementById('startJornadaBtn').style.display = 'block';
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endJornadaBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'none';
                }
            });
    }

    function startPause() {
        fetch('/start-pause', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    stopTimer();
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'block';
                }
            });
    }

    function endPause() {
        fetch('/end-pause', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer();
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endPauseBtn').style.display = 'none';
                }
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateTime(); // Initialize the timer display

        // Initialize button states based on jornada and pause
        if ('{{ $jornadaActiva }}') {
            document.getElementById('startJornadaBtn').style.display = 'none';
            document.getElementById('endJornadaBtn').style.display = 'block';
            if ('{{ $pausaActiva }}') {
                document.getElementById('startPauseBtn').style.display = 'none';
                document.getElementById('endPauseBtn').style.display = 'block';
            } else {
                document.getElementById('startPauseBtn').style.display = 'block';
                document.getElementById('endPauseBtn').style.display = 'none';
                startTimer(); // Start timer if not in pause
            }
        } else {
            document.getElementById('startJornadaBtn').style.display = 'block';
            document.getElementById('endJornadaBtn').style.display = 'none';
            document.getElementById('startPauseBtn').style.display = 'none';
            document.getElementById('endPauseBtn').style.display = 'none';
        }
    });
</script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
    <script>
         $('#guardar').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('form').submit(); // Esto envía el formulario.
        });

        var events = @json($events);
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {

            initialView: 'listWeek',
            locale: 'es',
            navLinks: true,
            // dayMaxEvents: true,
            nowIndicator: true,
            businessHours: [
                {
                    daysOfWeek: [1], // Lunes a Viernes
                    startTime: '08:00', // Hora de inicio
                    endTime: '15:00' // Hora de fin
                },
                {
                    daysOfWeek: [2], // Lunes a Viernes
                    startTime: '08:00', // Hora de inicio
                    endTime: '15:00' // Hora de fin
                },
                {
                    daysOfWeek: [3], // Lunes a Viernes
                    startTime: '08:00', // Hora de inicio
                    endTime: '15:00' // Hora de fin
                },
                {
                    daysOfWeek: [4], // Lunes a Viernes
                    startTime: '08:00', // Hora de inicio
                    endTime: '15:00' // Hora de fin
                },
                {
                    daysOfWeek: [5], // Lunes a Viernes
                    startTime: '08:00', // Hora de inicio
                    endTime: '15:00' // Hora de fin
                },
            ],
            headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'myCustomButton dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: events,
            customButtons: {
                myCustomButton: {
                    icon:'bi bi-plus',
                    text: 'Add event',
                    click: function() {
                        var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                        eventModal.show();
                    }
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault(); // don't let the browser navigate

                if (info.event.url) {
                window.open(info.event.url);
                }
            },
        });
        calendar.render();
      });

    </script>
@endsection

