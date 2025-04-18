@extends('layouts.app')

@section('titulo', 'Calendario de jornada')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-md-6 order-md-1 order-last">
                <h3>Calendario de Jornadas</h3>
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
        //console.log(eventos);
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

            slotLabelFormat: 'HH:mm', // Formato de hora 24h (puedes usar 'h:mm A' para AM/PM)
            minTime: "07:30:00", // Muestra desde las 6 AM
            maxTime: "20:00:00", // Muestra hasta las 10 PM
            slotDuration: "00:10:00", // Intervalo de media hora entre slots

        });
    });


</script>
@endsection
