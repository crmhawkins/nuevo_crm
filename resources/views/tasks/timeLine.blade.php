@extends('layouts.app')

@section('titulo', 'Calendario de Tareas')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-md-6 order-md-1 order-last">
                <h3>Calendario de Tareas</h3>
                <h5 id="trabajadasHoy">Horas trabajadas: {{$horas_hoy2}}</h5>
                <h5 id="producidasHoy">Horas producidas: {{$horas_hoy}}</h5>
            </div>
            <div class="col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Calendario</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.min.js"></script>
<script>
    $(document).ready(function() {
        // Recibir los eventos desde el controlador
        var eventos = @json($events);
        console.log(eventos);
        // Inicializar el calendario
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            locale: 'es', // Para español
            defaultView: 'agendaDay', // Iniciar en la vista de día
            editable: false,
            events: eventos,
            eventClick: function(event) {
                // Acción cuando se hace clic en un evento
                showTaskInfo(event.id); // Llamada a tu función existente
            }
        });
    });

    // Función para mostrar información de la tarea
    function showTaskInfo(id) {
        $.when(getDataTask(id)).then(function(data) {
            // Actualizar la información del modal
            $('#p1').html('<strong>Título: </strong>' + data.titulo);
            $('#p2').html('<strong>Cliente: </strong>' + data.cliente);
            // Mostrar el modal con los datos
            $('#myModal').modal();
        });
    }

    // Función AJAX para obtener los detalles de la tarea
    function getDataTask(id) {
        return $.ajax({
            type: "POST",
            url: '/getDataTask',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: { 'id': id },
            dataType: "json"
        });
    }
</script>
@endsection
