@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

<style>
    /* Estilos básicos */

    .progress-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: conic-gradient(
            var(--progress-color) calc(var(--percentage, 0) * 1%),
            #e0e0e0 calc(var(--percentage, 0) * 1%)
        );
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .progress-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--progress-color);
        position: absolute;
    }

    .progress-circle::before {
        content: '';
        width: 100px;
        height: 100px;
        background-color: #fff;
        border-radius: 50%;
        position: absolute;
        z-index: 1;
    }

    .progress-circle::after {
        content: attr(data-percentage) '%';
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--progress-color);
        position: absolute;
        z-index: 2;
    }
    span.tarea-gestor {
        display: block;
        font-size: 0.9rem;
        color: gray;
    }

    .input-group-text {
        position: relative;
        background-color: white;
        cursor: pointer;
        width: 40px;
        border: 1px solid #afb0b0;
        box-shadow: 0 0 3px #afb0b0;
    }

    .input-group-text i {
        color: #6c757d;
    }

    .file-icon {
        text-align: center;
        margin-bottom: 5px;
    }

    .file-icon i {
        font-size: 50px;
    }

    #file-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .chat-container {
        max-height: 400px;
        min-height: 200px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 8px;
        border-radius: 5px;
    }

    .message {
        margin-bottom: 5px;
        border-radius: 5px;
        display: inline-block;
        max-width: 80%;
    }

    .mine {
        background-color: #dcf8c6;
        text-align: left;
        float: right;
        clear: both;
    }

    .theirs {
        background-color: #f1f0f0;
        text-align: left;
        float: left;
        clear: both;
    }

    @keyframes pulse-animation {
        0% {
            text-shadow: 0 0 0px #ffffff;
            box-shadow: 0 0 0 0px #ff000077;
        }
        100% {
            text-shadow: 0 0 20px #ffffff;
            box-shadow: 0 0 0 20px #ff000000;
        }
    }

    .pulse {
        color: #fff;
        font-size: 12px;
        animation: pulse-animation 2s infinite;
        text-shadow: 0 0 10px #ffffff;
        background-color: #ff0000c9;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        box-shadow: 0 0 1px 1px #ff0000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #to-do {
        max-height: 550px;
        overflow-y: auto;
        border-radius: 10px;
        padding: 15px;
    }

    #to-do-container {
        max-height: 600px;
        min-height: 600px;
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
        overflow: hidden;
        border: 1px solid black;
        border-radius: 20px;
    }

    .info {
        display: none;
        padding: 15px;
        background-color: #fcfcfc;
        margin-top: 5px;
    }

    .tooltip.custom-tooltip .tooltip-inner {
        background-color: #343a40;
        color: #fff;
        font-size: 1.2rem;
        padding: 15px;
        border-radius: 5px;
        max-width: 500px;
    }

    .tooltip.custom-tooltip .tooltip-arrow {
        border-top-color: #343a40;
    }

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

    /* Estilo personalizado para scrollbars */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.5);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.7);
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
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
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

    .clickable {
        cursor: pointer;
    }

    .tareas_revision, .tareas {
        height: 50% !important;
        overflow-x: hidden;
        overflow-y: auto;
    }

     .tarea{
        border: 1px solid black;
        border-radius: 20px;
    }
    .info {
        display: none;
        padding: 15px;
        background-color: white;
        margin-top: 5px;
        transition: all 0.3s ease; /* Agregar transición para el efecto visual */
        overflow: hidden; /* Asegura que el contenido no se desborde */
    }

    .scroll{
        max-height: 770px;
        overflow-y: auto;
    }
    .side-column{
        height: 100% !important;
    }
    @media (max-width: 768px) {
        .calendar-month-view {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }
        .side-column {
            display: flex;
            flex-direction: column;
        }

        .view-selector button,
        .jornada {
            font-size: 0.9rem;
        }

        .side-column,
        .card-body {
            padding: 5px;
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
                        <h2 id="timer" class="display-6 fw-bolder col-4">00:00:00</h2>
                        {{-- <button id="startJornadaBtn" class="btn jornada btn-primary mx-2 col-3" onclick="startJornada()">Inicio Jornada</button> --}}
                        <button id="startPauseBtn" class="btn jornada btn-secondary mx-2 col-3" onclick="startPause()" style="display:none;">Iniciar Pausa</button>
                        <button id="endPauseBtn" class="btn jornada btn-dark mx-2 col-3" onclick="endPause()" style="display:none;">Finalizar Pausa</button>
                        {{-- <button id="endJornadaBtn" class="btn jornada btn-danger mx-2 col-3" onclick="endJornada()" style="display:none;">Fin de Jornada</button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="card2 mt-4">
            <div class="card-body2">
                <div class="row justify-between">
                    <div class="col-md-7">
                        <div class="side-column d-flex" >
                            <div class="card mb-3" style="flex:1;" >
                                <div class="card-body">
                                    <h3 class="card-title h4">Tareas</h3>
                                    <ul class="nav nav-tabs" id="taskTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $tasks['taskPlay'] ? 'active' : '' }}" id="active-task-tab" data-bs-toggle="tab" data-bs-target="#active-task" type="button" role="tab" aria-controls="active-task" aria-selected="{{ $tasks['taskPlay'] ? 'true' : 'false' }}">
                                                Tarea Activa
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ !$tasks['taskPlay'] ? 'active' : '' }}" id="pending-tasks-tab" data-bs-toggle="tab" data-bs-target="#pending-tasks" type="button" role="tab" aria-controls="pending-tasks" aria-selected="{{ !$tasks['taskPlay'] ? 'true' : 'false' }}">
                                                Tareas Pendientes
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="revision-tasks-tab" data-bs-toggle="tab" data-bs-target="#revision-tasks" type="button" role="tab" aria-controls="revision-tasks" aria-selected="false">
                                                Tareas en Revisión
                                            </button>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-3 ">
                                        <!-- Tarea Activa -->
                                        <div class=" scroll tab-pane p-3 fade {{ $tasks['taskPlay'] ? 'show active' : '' }}" id="active-task" role="tabpanel" aria-labelledby="active-task-tab">
                                            @if ($tasks['taskPlay'])
                                                <div class="card2 tarea tarea-activa mb-3 p-2">
                                                    <div id="{{ $tasks['taskPlay']->id }}" class="tarea-sing card-body2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="col-2 text-center">
                                                                <span class="tarea-numero">
                                                                    <i class="fas fa-caret-square-right" style="color: black; font-size: 3rem;"></i>
                                                                </span>
                                                            </div>
                                                            <div class="col-10">
                                                                <span class="d-block tarea-cliente status_{{ $tasks['taskPlay']->estado->name }}">
                                                                    Cliente: @if ($tasks['taskPlay']->presupuesto && $tasks['taskPlay']->presupuesto->cliente)
                                                                        {{ $tasks['taskPlay']->presupuesto->cliente->name }}
                                                                    @endif
                                                                </span>
                                                                <span class="d-block tarea-nombre fw-bolder fs-4">{{ $tasks['taskPlay']->title }}</span>
                                                                <span class="d-block tarea-gestor">
                                                                    Gestor: @if ($tasks['taskPlay']->gestor)
                                                                        {{ $tasks['taskPlay']->gestor->name }}
                                                                    @endif | {{ $tasks['taskPlay']->id }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="infotask">
                                                        <!-- Información Detallada de la Tarea -->
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-center">
                                                    <h3>No hay tareas activas</h3>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Tareas Pendientes -->
                                        <div class="scroll tab-pane p-4 fade {{ !$tasks['taskPlay'] ? 'show active' : '' }}" id="pending-tasks" role="tabpanel" aria-labelledby="pending-tasks-tab">
                                            <div class="mb-3">
                                                <select class="js-select2 form-control js-select2-enabled select-task " style="width: 100%;" data-placeholder="Buscar..." tabindex="-1" name="cliente" id="selectTask">
                                                    <option value="0">Seleccion o busque tarea...</option>
                                                    @if ($tasks['tasksPause'])
                                                        @foreach ($tasks['tasksPause'] as $taskSingle)
                                                            <option value="{{ $taskSingle->id }}">
                                                                @if ($taskSingle->presupuesto)
                                                                    @if ($taskSingle->presupuesto->cliente)
                                                                        {{ $taskSingle->presupuesto->cliente->name }}
                                                                    @endif
                                                                @endif
                                                                | {{ $taskSingle->title }} |
                                                                @if ($taskSingle->gestor)
                                                                        {{ $taskSingle->gestor->name }}
                                                                @endif

                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>

                                            </div>
                                            <?php
                                            if (!function_exists('fechaEstimadaDashboard')) {
                                                function fechaEstimadaDashboard($horasFaltan)
                                                {
                                                    $arrayHoras = explode(':', $horasFaltan);
                                                    $horas = $arrayHoras['0'];
                                                    $minutos = $arrayHoras['1'];
                                                    $segundos = $arrayHoras['2'];
                                                    $dia = 0;

                                                    if ($horas > 8) {
                                                        $horas -= 8;
                                                        $dia += 1;
                                                    }

                                                    return $dia . ':' . $horas . ':' . $minutos . ':' . $segundos;
                                                }
                                            }
                                            ?>
                                            <?php
                                                $acumuladorTiempo = 0;
                                                $diasAcumulados = 0;
                                                $bufferTiempo = 0;
                                                $segundosAlDia = 28800;
                                                $inicioJornadaLaboral = '09:00:00';
                                                $paradaJornadaLaboral = '14:00:00';
                                                $vueltaJornadaLaboral = '16:00:00';
                                                $finJornadaLaboral = '19:00:00';
                                                $fechaEstimada;
                                                $diasAcumulados = 0;
                                                $horasAcumulados = 0;
                                                $minutosAcumulados = 0;
                                                $segundosAcumulados = 0;

                                                $prioridad = "";

                                                $actualFecha;

                                                $paso = 0;
                                                $contador = 1;
                                                $fechaCalendario = [];

                                            ?>
                                            @if ($tasks['tasksPause']->isNotEmpty())
                                                @php($numero = 1)
                                                @foreach ($tasks['tasksPause'] as $tarea)
                                                    <?php
                                                    // TIEMPO ESTIMADO
                                                    $tiempoEstimado = explode(':', $tarea->estimated_time);
                                                    // PASAR EL TIEMPO ESTIPADO A SEGUNDOS
                                                    $minutosASegundos = $tiempoEstimado['1'] * 60;

                                                    $horasAMinutos = $tiempoEstimado['0'] * 60;
                                                    $horasASegundos = $horasAMinutos * 60;
                                                    // TOTAL DE SEGUNDOS DE TIEMPO ESTIMADO
                                                    $segundosTotalEstimado = $horasASegundos + $minutosASegundos + intval($tiempoEstimado['2']);

                                                    // TIEMPO CONSUMIDO
                                                    $tiempoConsumido = explode(':', $tarea->real_time);

                                                    // PASAR EL TIEMPO ESTIPADO A SEGUNDOS
                                                    if (! isset($tiempoConsumido['1'])) {
                                                        dd($tarea);
                                                    }
                                                    $minutosASegundosConsumido = $tiempoConsumido['1'] * 60;

                                                    $horasAMinutosConsumido = $tiempoConsumido['0'] * 60;
                                                    $horasASegundosConsumido = $horasAMinutosConsumido * 60;
                                                    // TOTAL DE SEGUNDOS DE TIEMPO ESTIMADO
                                                    $segundosTotalConsumido = $horasASegundosConsumido + $minutosASegundosConsumido + intval($tiempoConsumido['2']);

                                                    $tiempoRestante = $segundosTotalEstimado - $segundosTotalConsumido;

                                                    $bufferTiempo += $tiempoRestante;

                                                    $hoy;

                                                    $horasHoy = date('H:i:s');
                                                    $horasHoyArray = explode(':', $horasHoy);

                                                    //$prueba = date('H:i:s', $ts_fin);
                                                    //$prueba2 = date('H:i:s', $ts_ini);

                                                    $horas = floor($tiempoRestante / 3600);
                                                    $minutos = floor(($tiempoRestante - $horas * 3600) / 60);
                                                    $segundos = $tiempoRestante - $horas * 3600 - $minutos * 60;
                                                    $horaImprimir = $horas . ':' . $minutos . ':' . $segundos;

                                                    $actual = date('Y-m-d H:i:s');
                                                    $fecha = date('Y-m-d');
                                                    $hora = date('H:i:s');
                                                    $diasAcumulador = 0;

                                                    $tiempoACaclcular = fechaEstimadaDashboard($horaImprimir);
                                                    $tiempoACaclcularArray = explode(':', $tiempoACaclcular);

                                                    $diasAcumulados = $tiempoACaclcularArray['0'];
                                                    $horasAcumulados = $tiempoACaclcularArray['1'];
                                                    $minutosAcumulados = $tiempoACaclcularArray['2'];
                                                    $segundosAcumulados = $tiempoACaclcularArray['3'];
                                                    $dia = 0;

                                                    if ($horasAcumulados >= 24) {
                                                        $dia += 1;
                                                        $horasAcumulados -= 24;
                                                    }
                                                    if ($minutosAcumulados >= 60) {
                                                        $horasAcumulados += 1;
                                                        $minutosAcumulados -= 60;
                                                    }
                                                    if ($segundosAcumulados >= 60) {
                                                        $minutosAcumulados += 1;
                                                        $segundosAcumulados -= 60;
                                                    }

                                                    if ($horasAcumulados < 0) {
                                                        $horasAcumulados = $tiempoEstimado['0'];
                                                    }

                                                    $dia += $tiempoACaclcularArray['0'];
                                                    $param = $dia . 'days';
                                                    $paramHoras = $horasAcumulados . 'hour';
                                                    $paramMinutos = $minutosAcumulados . 'minute';
                                                    $paramSegundos = $segundosAcumulados . 'second';

                                                    if ($paso == 0) {
                                                        $actualNew = strtotime($param, strtotime($actual));
                                                        $actualNew = strtotime($paramHoras, $actualNew);
                                                        $actualNew = strtotime($paramMinutos, $actualNew);
                                                        $actualNew = strtotime($paramSegundos, $actualNew);

                                                        while (date('N', $actualNew) >= 6) {
                                                            $actualNew = strtotime('+1 day', $actualNew);
                                                        }


                                                        $newActualFechaFinal = date('d-m-Y H:i:s', $actualNew);

                                                        $actualFecha = $newActualFechaFinal;
                                                        $paso = 1;
                                                        $actualFechaArray = explode(' ', $newActualFechaFinal);
                                                        $actualFechaFinal = $actualFechaArray[0];
                                                        $fechaCalendario[$contador] = date('Y-m-d', $actualNew);
                                                    } else {
                                                        $newActualFecha = strtotime($param, strtotime($actualFecha));
                                                        $newActualFecha = strtotime($paramHoras, $newActualFecha);
                                                        $newActualFecha = strtotime($paramMinutos, $newActualFecha);
                                                        $newActualFecha = strtotime($paramSegundos, $newActualFecha);

                                                        while (date('N', $newActualFecha) >= 6) {
                                                            $newActualFecha = strtotime('+1 day', $newActualFecha);
                                                        }

                                                        $newActualFechaFinal = date('d-m-Y H:i:s', $newActualFecha);

                                                        $actualFecha = $newActualFechaFinal;

                                                        $actualFechaArray = explode(' ', $newActualFechaFinal);
                                                        $actualFechaFinal = $actualFechaArray[0];
                                                        $fechaCalendario[$contador] = date('Y-m-d', $newActualFecha);
                                                        //var_dump($actualFechaFinal );
                                                    }

                                                    switch ($tarea->priority_id) {
                                                        case 1:
                                                            $prioridad = "Baja";
                                                            break;
                                                        case 2:
                                                            $prioridad = "Media";
                                                            break;
                                                        case 3:
                                                            $prioridad = "Alta";
                                                            break;
                                                        case 4:
                                                            $prioridad = "Urgente";
                                                            break;
                                                        default:
                                                            $prioridad = "n/a";
                                                            break;
                                                    }

                                                    $fechaNow = getdate();
                                                    $fechaCalendario[0] = $fechaNow['year'] . '-0' . $fechaNow['mon'] . '-' . $fechaNow['mday'];
                                                    $tareaCalendar[$contador] = ['id' => $tarea->id, 'title' => $tarea->title, 'start' => $fechaCalendario[$contador - 1], 'end' => $fechaCalendario[$contador], 'horas_estimadas' => $tarea->estimated_time, 'horas_reales' => $tarea->real_time, 'prioridad' => $prioridad, 'fecha_descripcion' => $actualFechaFinal];
                                                    $tareaDesc[$tarea->id] = ['description' => $tarea->description];
                                                    $contador += 1;

                                                    ?>
                                                    <div class="card2 tarea task-item mb-3 p-2" id="task-{{ $tarea->id }}">
                                                        <div id="{{ $tarea->id }}" class="tarea-sing card-body2 ">
                                                            <div class="d-flex align-items-center">
                                                                <div class="col-2 text-center">
                                                                    <span class="tarea-numero">{{ $numero }}ª</span>
                                                                </div>
                                                                <div class="col-10">
                                                                    <span class="d-block tarea-cliente status_{{ $tarea->priority_id }}">
                                                                        Cliente: @if ($tarea->presupuesto && $tarea->presupuesto->cliente)
                                                                            {{ $tarea->presupuesto->cliente->name }}
                                                                        @endif
                                                                    </span>
                                                                    <span class="d-block tarea-nombre fw-bolder fs-4">{{ $tarea->title }}</span>
                                                                    <span class="d-block tarea-gestor">
                                                                        Gestor: @if ($tarea->gestor)
                                                                            {{ $tarea->gestor->name }}
                                                                        @endif | {{ $tarea->id }}
                                                                    </span>
                                                                    <span class="tarea-gestor text-success fw-bolder animate__animated animate__pulse animate__infinite" style="color:green; font-weight:bold; !important">
                                                                        Fecha estimada entrega: {{ $actualFechaFinal }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="infotask">
                                                            <!-- Información Detallada de la Tarea -->
                                                        </div>
                                                    </div>
                                                    @php($numero += 1)
                                                @endforeach
                                            @else
                                                <div class="text-center">
                                                    <h3>No hay tareas en pendientes</h3>
                                                </div>
                                            @endif
                                        </div>
                                        <!-- Tareas en Revisión -->
                                        <div class="scroll tab-pane fade" id="revision-tasks" role="tabpanel" aria-labelledby="revision-tasks-tab">
                                            <div class="accordion" id="revisionTasksAccordion">
                                                @if ($tasks['tasksRevision']->isNotEmpty())
                                                    @php($nombre = 1)
                                                    @foreach ($tasks['tasksRevision'] as $tarea)
                                                        <div class="card2 tarea mb-3 p-2">
                                                            <div id="{{ $tarea->id }}" class="tarea-sing card-body2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="col-2 text-center">
                                                                        <span class="tarea-numero">{{ $nombre }}ª</span>
                                                                    </div>
                                                                    <div class="col-10">
                                                                        <span class="d-block tarea-cliente status_{{ $tarea->estado->name }}">
                                                                            Cliente: @if ($tarea->presupuesto && $tarea->presupuesto->cliente)
                                                                                {{ $tarea->presupuesto->cliente->name }}
                                                                            @endif
                                                                        </span>
                                                                        <span class="d-block tarea-nombre fw-bolder fs-4">{{ $tarea->title }}</span>
                                                                        <span class="d-block tarea-gestor">
                                                                            Gestor: @if ($tarea->gestor)
                                                                                {{ $tarea->gestor->name }}
                                                                            @endif | {{ $tarea->id }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="infotask">
                                                                <!-- Información Detallada de la Tarea -->
                                                            </div>
                                                        </div>
                                                        @php($nombre += 1)
                                                    @endforeach
                                                @else
                                                    <div class="text-center">
                                                        <h3>No hay tareas en revisión</h3>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="side-column">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 d-flex flex-wrap justify-content-center mb-4 align-items-center">
                                            <div class="mx-4 text-center">
                                                <h5 class="my-3">{{$user->name}}&nbsp;{{$user->surname}}</h5>
                                                <p class="text-muted mb-1">{{$user->departamento->name}}</p>
                                                <p class="text-muted mb-4">{{$user->acceso->name}}</p>
                                                {{-- <div class="d-flex  align-items-center my-2">
                                                    <input type="color" class="form-control form-control-color" style="padding: 0.4rem" id="color">
                                                    <label for="color" class="form-label m-2">Color</label>
                                                </div> --}}
                                            </div>
                                            <div class="mx-4">
                                                @if ($user->image == null)
                                                    <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{asset('assets/images/guest.webp')}}" />
                                                @else
                                                    <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{ asset('/storage/avatars/'.$user->image) }}" />
                                                @endif
                                            </div>
                                            <div>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="mx-4 text-center">
                                                        <h1 class="fs-5">Productividad</h1>
                                                        <div class="progress-circle"
                                                            data-percentage="{{ number_format($productividadIndividual, 2) }}">
                                                        </div>

                                                    </div>
                                                    <div class="mx-4 text-center">
                                                        <div class="card" style="border: 1px solid {{ $user->bono > 0 ? 'green' : 'gray' }}; padding: 10px;">
                                                            <h5 class="m-0" style="color: {{ $user->bono > 0 ? 'green' : 'gray' }};">
                                                                {{ $user->bono > 0 ? 'Bono: ' . $user->bono.' €' : 'Sin bono' }}
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mx-4 my-2">
                                                    <p style="color:#4D989E">Horas Producidas Hoy</p>
                                                    <p style="font-weight: bold;font-size: 2rem;">{{ $tiempoProducidoHoy['horas'] }}</p>
                                                    <p style="color:#4D989E">Horas Producidas Mesual</p>
                                                    <p style="font-weight: bold;font-size: 2rem;">{{ $horasMes }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex flex-wrap justify-content-center">
                                            <div class="my-2 text-center">
                                                <a class="btn btn-outline-secondary"
                                                href="{{route('contratos.index_user', $user->id)}}">Contrato</a>
                                                <a class="btn btn-outline-secondary"
                                                href="{{route('nominas.index_user', $user->id)}}">Nomina</a>
                                                <a class="btn btn-outline-secondary"
                                                href="{{route('holiday.index')}}">Vacaciones</a>
                                                <a class="btn btn-outline-secondary"
                                                href="{{route('passwords.index')}}">Contraseñas</a>
                                            </div>
                                            <div class="my-2 ml-4 text-center col-auto" role="tablist">
                                                <a class=" btn btn-outline-secondary active"
                                                    id="list-todo-list" data-bs-toggle="list" href="#list-todo"
                                                    role="tab">TO-DO</a>
                                                <a class=" btn btn-outline-danger"
                                                    id="list-todo-list-finalizados" data-bs-toggle="list" href="#list-todo-finalizados"
                                                    role="tab">Finalizados</a>
                                                <a class="btn btn-outline-secondary"
                                                    id="list-agenda-list" data-bs-toggle="list"
                                                    href="#list-agenda" role="tab">Agenda</a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-content text-justify" id="nav-tabContent">
                                        <div class="tab-pane show active" id="list-todo" role="tabpanel"
                                            aria-labelledby="list-todo-list">
                                            <div class="card2 mt-4">
                                                <div class="card-body2">
                                                    <div id="to-do-container" class="d-flex row  justify-content-center"   >
                                                        <div class="col-12 d-flex align-items-center mt-2 pb-0" style="max-height: 50px;">                                                            <button class="btn btn-outline-secondary w-100" onclick="showTodoModal()">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div id="to-do" class="p-3">
                                                            @foreach ($to_dos as $to_do)
                                                                <div class="card mt-2" id="todo-card-{{$to_do->id}}">
                                                                    <div class="card-body d-flex justify-content-between clickable" id="todo-card-body-{{$to_do->id}}" data-todo-id="{{$to_do->id}}" style="{{$to_do->isCompletedByUser($user->id) ? 'background-color: #CDFEA4' : '' }}">
                                                                        <div style="flex: 0 0 60%;">
                                                                            <h3>{{ $to_do->titulo }}</h3>
                                                                        </div>
                                                                        <div class="d-flex align-items-center justify-content-around" style="flex: 0 0 40%;">
                                                                            @if(!($to_do->isCompletedByUser($user->id)))
                                                                            <button onclick="completeTask(event,{{ $to_do->id }})" id="complete-button-{{$to_do->id}}" class="btn btn-success btn-sm">Completar</button>
                                                                            @endif
                                                                            @if ($to_do->admin_user_id == $user->id)
                                                                            <button onclick="finishTask(event,{{ $to_do->id }})" class="btn btn-danger btn-sm">Finalizar</button>
                                                                            @endif
                                                                            <div id="todo-card-{{ $to_do->id }}" class="pulse justify-center align-items-center" style="{{ $to_do->unreadMessagesCountByUser($user->id) > 0 ? 'display: flex;' : 'display: none;' }}">
                                                                                {{ $to_do->unreadMessagesCountByUser($user->id) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info">
                                                                        <div class="d-flex justify-content-evenly flex-wrap">
                                                                            @if($to_do->project_id)<a class="btn btn-outline-secondary mb-2"> Campaña {{$to_do->proyecto ? $to_do->proyecto->name : 'borrada'}}</a>@endif
                                                                            @if($to_do->client_id)<a class="btn btn-outline-secondary mb-2"> Cliente {{$to_do->cliente ? $to_do->cliente->name : 'borrado'}}</a>@endif
                                                                            @if($to_do->budget_id)<a class="btn btn-outline-secondary mb-2"> Presupuesto {{$to_do->presupuesto ? $to_do->presupuesto->concept : 'borrado'}}</a>@endif
                                                                            @if($to_do->task_id) <a class="btn btn-outline-secondary mb-2"> Tarea {{$to_do->tarea ? $to_do->tarea->title : 'borrada'}}</a> @endif
                                                                        </div>
                                                                        <div class="participantes d-flex flex-wrap mt-2">
                                                                            <h3 class="m-2">Participantes</h3>
                                                                            @foreach ($to_do->TodoUsers as $usuario )
                                                                                <span class="badge m-2 {{$usuario->completada ? 'bg-success' :'bg-secondary'}}">
                                                                                    {{$usuario->usuarios->name}}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                        <h3 class="m-2">Descripcion </h3>
                                                                        <p class="m-2">{{ $to_do->descripcion }}</p>
                                                                        <div class="chat mt-4">
                                                                            <div class="chat-container" >
                                                                                @foreach ($to_do->mensajes as $mensaje)
                                                                                    <div class="p-3 message {{ $mensaje->admin_user_id == $user->id ? 'mine' : 'theirs' }}">
                                                                                        @if ($mensaje->archivo)
                                                                                            <div class="file-icon">
                                                                                                <a href="{{ asset('storage/' . $mensaje->archivo) }}" target="_blank"><i class="fa-regular fa-file-lines fa-2x"></i></a>
                                                                                            </div>
                                                                                        @endif
                                                                                        <strong>{{ $mensaje->user->name }}:</strong> {{ $mensaje->mensaje }}
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <form id="mensaje" action="{{ route('message.store') }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="todo_id" value="{{ $to_do->id }}">
                                                                                <input type="hidden" name="admin_user_id" value="{{ $user->id }}">
                                                                                <div class="input-group my-2">
                                                                                    <input type="text" class="form-control" name="mensaje" placeholder="Escribe un mensaje...">
                                                                                    <label class="input-group-text" style="background: white; ">
                                                                                        <i class="fa-solid fa-paperclip" id="file-clip"></i>
                                                                                        <input type="file" class="form-control" style="display: none;" id="file-input" name="archivo">
                                                                                        <i class="fa-solid fa-check" id="file-icon" style="display: none; color: green;"></i>
                                                                                    </label>
                                                                                    <button id="enviar" class="btn btn-primary" type="button"><i class="fa-regular fa-paper-plane"></i></button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane show" id="list-todo-finalizados" role="tabpanel"
                                            aria-labelledby="list-todo-finalizados-list">
                                            <div class="card2 mt-4">
                                                <div class="card-body2">
                                                    <div id="to-do-container" class="d-flex row"  style="" >
                                                        <div id="to-do" class="p-3">
                                                            @foreach ($to_dos_finalizados as $to_do_finalizado)
                                                                <div class="card mt-2" id="todo-card-{{$to_do_finalizado->id}}">
                                                                    <div class="card-body d-flex justify-content-between clickable" id="todo-card-body-{{$to_do_finalizado->id}}" data-todo-id="{{$to_do_finalizado->id}}" style="{{$to_do_finalizado->isCompletedByUser($user->id) ? 'background-color: #CDFEA4' : '' }}">
                                                                        <h3>{{ $to_do_finalizado->titulo }}</h3>
                                                                    </div>
                                                                    <div class="info">
                                                                        <div class="d-flex justify-content-evenly flex-wrap">
                                                                            @if($to_do_finalizado->project_id)<a class="btn btn-outline-secondary mb-2"> Campaña {{$to_do_finalizado->proyecto ? $to_do_finalizado->proyecto->name : 'borrada'}}</a>@endif
                                                                            @if($to_do_finalizado->client_id)<a class="btn btn-outline-secondary mb-2"> Cliente {{$to_do_finalizado->cliente ? $to_do_finalizado->cliente->name : 'borrado'}}</a>@endif
                                                                            @if($to_do_finalizado->budget_id)<a class="btn btn-outline-secondary mb-2"> Presupuesto {{$to_do_finalizado->presupuesto ? $to_do_finalizado->presupuesto->concept : 'borrado'}}</a>@endif
                                                                            @if($to_do_finalizado->task_id) <a class="btn btn-outline-secondary mb-2"> Tarea {{$to_do_finalizado->tarea ? $to_do_finalizado->tarea->title : 'borrada'}}</a> @endif
                                                                        </div>
                                                                        <div class="participantes d-flex flex-wrap mt-2">
                                                                            <h3 class="m-2">Participantes</h3>
                                                                            @foreach ($to_do_finalizado->TodoUsers as $usuario )
                                                                                <span class="badge m-2 {{$usuario->completada ? 'bg-success' :'bg-secondary'}}">
                                                                                    {{$usuario->usuarios->name}}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                        <h3 class="m-2">Descripcion </h3>
                                                                        <p class="m-2">{{ $to_do_finalizado->descripcion }}</p>
                                                                        <div class="chat mt-4">
                                                                            <div class="chat-container" >
                                                                                @foreach ($to_do_finalizado->mensajes as $mensaje)
                                                                                    <div class="p-3 message {{ $mensaje->admin_user_id == $user->id ? 'mine' : 'theirs' }}">
                                                                                        @if ($mensaje->archivo)
                                                                                            <div class="file-icon">
                                                                                                <a href="{{ asset('storage/' . $mensaje->archivo) }}" target="_blank"><i class="fa-regular fa-file-lines fa-2x"></i></a>
                                                                                            </div>
                                                                                        @endif
                                                                                        <strong>{{ $mensaje->user->name }}:</strong> {{ $mensaje->mensaje }}
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <form id="mensaje" action="{{ route('message.store') }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="todo_id" value="{{ $to_do_finalizado->id }}">
                                                                                <input type="hidden" name="admin_user_id" value="{{ $user->id }}">
                                                                                <div class="input-group my-2">
                                                                                    <input type="text" class="form-control" name="mensaje" placeholder="Escribe un mensaje..." disabled>
                                                                                    <label class="input-group-text" style="background: white; ">
                                                                                        <i class="fa-solid fa-paperclip" id="file-clip"></i>
                                                                                        <input type="file" class="form-control" style="display: none;" id="file-input" name="archivo" disabled>
                                                                                        <i class="fa-solid fa-check" id="file-icon" style="display: none; color: green;"></i>
                                                                                    </label>
                                                                                    <button id="enviar" class="btn btn-primary" type="button" disabled><i class="fa-regular fa-paper-plane"></i></button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="list-agenda" role="tabpanel"
                                            aria-labelledby="list-agenda-list">
                                            <div class="card2 mt-4">
                                                <div class="card-body2 text-center">
                                                    <div id="calendar" class="p-4" style="min-height: 600px; margin-top: 0.75rem; margin-bottom: 0.75rem; overflow-y: auto; border-color:black; border-width: thin; border-radius: 20px;" >
                                                        <!-- Aquí se renderizarán las tareas según la vista seleccionada -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="todoModal" tabindex="-1" aria-labelledby="todoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Cambio a modal-lg para mayor ancho -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventModalLabel">Añadir To-do</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="todoform" action="{{ route('todos.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="task_id" class="form-label">Tareas</label>
                                    <select class="form-select choices" id="task_id" name="task_id">
                                        <option value="">Seleccione una tarea</option>
                                        @foreach ($tareas as $tarea)
                                            <option value="{{ $tarea->id }}" {{ old('task_id') == $tarea->id ? 'selected' : '' }}>
                                                {{ $tarea->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_user_ids" class="form-label">Usuarios</label>
                                    <select class="form-select choices__inner" id="admin_user_ids" name="admin_user_ids[]" multiple>
                                        <option value="">Seleccione usuarios</option>
                                        @foreach ($users as $gestor)
                                            @if ($gestor->id !== auth()->id()) <!-- Excluir al usuario logueado -->
                                                <option value="{{ $gestor->id }}" {{ in_array($gestor->id, old('admin_user_ids', [])) ? 'selected' : '' }}>
                                                    {{ $gestor->name }} {{ $gestor->surname }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('admin_user_ids')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="start" class="form-label">Inicio</label>
                                    <input type="datetime-local" class="form-control" id="start" name="start" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end" class="form-label">Fin</label>
                                    <input type="datetime-local" class="form-control" id="end" name="end">
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center justify-content-center">
                                    <input type="color" style="padding: 0.4rem" class="form-control form-control-color" id="color1" name="color">
                                    <label for="color1" class="form-label ml-2">Color</label>
                                </div>
                                <div class=" col-md-6 mb-3 d-flex align-items-center justify-content-center">
                                    <input type="checkbox" style="height:25px; width:25px; " class="form-check-input" id="agendar" name="agendar">
                                    <label for="agendar" class="form-check-label ml-2">Agendar</label>
                                </div>
                                <input type="hidden" name="admin_user_id" value="{{ $user->id }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button id="todoboton" type="button" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var multipleCancelButton = new Choices('#admin_user_ids', {
                removeItemButton: true, // Permite a los usuarios eliminar una selección
                searchEnabled: true,  // Habilita la búsqueda dentro del selector
                paste: false          // Deshabilita la capacidad de pegar texto en el campo
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            updateTime(); // Initialize the timer display

            setInterval(function() {
                getTime();
            }, 120000);

            // Initialize button states based on jornada and pause
            if ('{{ $jornadaActiva }}') {
                if ('{{ $pausaActiva }}') {
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'block';
                } else {
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endPauseBtn').style.display = 'none';
                    startTimer(); // Start timer if not in pause
                }
            } else {
                document.getElementById('startPauseBtn').style.display = 'none';
                document.getElementById('endPauseBtn').style.display = 'none';
            }
        });

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
                        document.getElementById('startPauseBtn').style.display = 'block';
                    }
                });
        }

        function endJornada() {
            // Obtener el tiempo actualizado
            getTime();

            let now = new Date();
            let currentHour = now.getHours();
            let currentMinute = now.getMinutes();

            // Convertir los segundos trabajados a horas
            let workedHours = timerTime / 3600;

            // Verificar si es antes de las 18:00 o si ha trabajado menos de 8 horas
            if (currentHour < 18 || workedHours < 8) {
                let title = '';
                let text = '';

                if (currentHour < 18) {
                    title = 'Horario de Salida Prematuro';
                    text = 'Es menos de las 18:00.  ';
                }else{
                    if(workedHours < 8) {
                    title = ('Jornada Incompleta');
                    text = 'Has trabajado menos de 8 horas. Si no compensas el tiempo faltante,';
                    }
                }

                text += 'Se te descontará de tus vacaciones al final del mes.';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Finalizar Jornada',
                    cancelButtonText: 'Continuar Jornada'
                }).then((result) => {
                    if (result.isConfirmed) {
                        finalizarJornada();
                    }
                    // Si elige continuar, no hacemos nada, simplemente mantiene la jornada activa
                });
            } else {
                // Si el tiempo es mayor o igual a 8 horas y es después de las 18:00, finalizamos directamente la jornada
                finalizarJornada();
            }
        }

        function finalizarJornada() {
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
                    document.getElementById('startPauseBtn').style.display = 'none';
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

        function getTime() {
            fetch('/dashboard/timeworked', {
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
                        timerTime = data.time
                        updateTime()
                    }
                });
        }
    </script>
    <script>
            document.querySelectorAll('#enviar').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.closest('form').submit();
                });
            });

            $('#todoboton').click(function(e){
                e.preventDefault(); // Esto previene que el enlace navegue a otra página.
                $('#todoform').submit(); // Esto envía el formulario.
            });

            var events = @json($events);
            document.addEventListener('DOMContentLoaded', function() {
                var calendarEl = document.getElementById('calendar');
                var tooltip = document.getElementById('tooltip');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'listWeek',
                    locale: 'es',
                    navLinks: true,
                    nowIndicator: true,
                    businessHours: [
                        { daysOfWeek: [1], startTime: '08:00', endTime: '15:00' },
                        { daysOfWeek: [2], startTime: '08:00', endTime: '15:00' },
                        { daysOfWeek: [3], startTime: '08:00', endTime: '15:00' },
                        { daysOfWeek: [4], startTime: '08:00', endTime: '15:00' },
                        { daysOfWeek: [5], startTime: '08:00', endTime: '15:00' }
                    ],
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridDay,listWeek'
                    },
                    events: events,
                    eventClick: function(info) {
                        var event = info.event;
                        var clientId = event.extendedProps.client_id;
                        var budgetId = event.extendedProps.budget_id;
                        var projectId = event.extendedProps.project_id;
                        var clienteName = event.extendedProps.cliente_name || '';
                        var presupuestoRef = event.extendedProps.presupuesto_ref || '';
                        var presupuestoConp = event.extendedProps.presupuesto_conp || '';
                        var proyectoName = event.extendedProps.proyecto_name || '';
                        var descripcion = event.extendedProps.descripcion || '';

                        // Construye las rutas solo si los IDs existen
                        var ruta = clientId ? `{{ route("clientes.show", ":id") }}`.replace(':id', clientId) : '#';
                        var ruta2 = budgetId ? `{{ route("presupuesto.edit", ":id1") }}`.replace(':id1', budgetId) : '#';
                        var ruta3 = projectId ? `{{ route("campania.show", ":id2") }}`.replace(':id2', projectId) : '#';

                        // Construye el contenido del tooltip condicionalmente
                        var tooltipContent = '<div style="text-align: left;">' +
                            '<h5>' + event.title + '</h5>';

                        if (clienteName) {
                            tooltipContent += '<a href="' + ruta + '"><p><strong>Cliente:</strong> ' + clienteName + '</p></a>';
                        }

                        if (presupuestoRef || presupuestoConp) {
                            tooltipContent += '<a href="' + ruta2 + '"><p><strong>Presupuesto:</strong> ' +
                                (presupuestoRef ? 'Ref:' + presupuestoRef + '<br>' : '') +
                                (presupuestoConp ? 'Concepto: ' + presupuestoConp : '') +
                                '</p></a>';
                        }

                        if (proyectoName) {
                            tooltipContent += '<a href="' + ruta3 + '"><p><strong>Campaña:</strong> ' + proyectoName + '</p></a>';
                        }

                        if (descripcion) {
                            tooltipContent += '<p>' + descripcion + '</p>';
                        }

                        tooltipContent += '</div>';

                        var tooltip = new bootstrap.Tooltip(info.el, {
                            title: tooltipContent,
                            placement: 'top',
                            trigger: 'manual',
                            html: true,
                            container: 'body',
                            customClass: 'custom-tooltip', // Aplica una clase personalizada para el estilo
                            sanitize: false // Asegúrate de que el contenido HTML se procesa correctamente
                        });

                        // Cambia el color de fondo del tooltip
                        tooltip.show();
                        var tooltipElement = document.querySelector('.tooltip-inner');
                        if (tooltipElement) {
                            tooltipElement.style.backgroundColor = event.extendedProps.color || '#000'; // Usa el color del evento o negro por defecto
                        }

                        function handleClickOutside(event) {
                        if (!info.el.contains(event.target)) {
                            tooltip.dispose();
                            document.removeEventListener('click', handleClickOutside);
                        }
                    }
                    document.addEventListener('click', handleClickOutside);
                },
            });
                calendar.render();
            });

    </script>

    <script>
        function showTodoModal() {
            var todoModal = new bootstrap.Modal(document.getElementById('todoModal'));
            todoModal.show();
        }
        document.addEventListener('DOMContentLoaded', function() {
            const progressCircles = document.querySelectorAll('.progress-circle');

            progressCircles.forEach(circle => {
                const percentage = circle.getAttribute('data-percentage');
                circle.style.setProperty('--percentage', percentage);

                let progressColor;

                if (percentage  >= 100) {
                    progressColor = '#28a745'; //Verde
                } else if (percentage >= 75) {
                    progressColor = '#ff9f00'; // Naranja
                } else {
                    progressColor = '#dc3545'; //Rojo
                }

                circle.style.setProperty('--progress-color', progressColor);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.clickable').forEach(function(element) {
                element.addEventListener('click', function(event) {
                    event.stopPropagation();


                    var info = this.nextElementSibling;
                    var isVisible = info.style.display === 'block';

                    if (!isVisible) {
                        document.querySelectorAll('.info').forEach(function(infoElement) {
                            infoElement.style.display = 'none';
                        });
                        info.style.display = 'block';
                        markMessagesAsRead(this.getAttribute('data-todo-id'));
                    } else {
                        info.style.display = 'none';
                    }
                });
            });

            // Función para marcar mensajes como leídos
            function markMessagesAsRead(todoId) {
                if (!todoId) return;  // Asegúrate de que todoId es válido

                fetch(`mark-as-read/${todoId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        let unreadCounter = document.querySelector(`[data-todo-id="${todoId}"] .pulse`);
                        if (unreadCounter) {
                            unreadCounter.textContent = ''; // Limpiar el texto
                            unreadCounter.style.display = 'none'; // Ocultar el elemento
                        }
                        console.log('Mensajes marcados como leídos.');
                        // Opcional: actualizar la interfaz de usuario aquí si es necesario, como remover notificaciones visuales de mensajes no leídos
                    }
                })
                .catch(error => console.error('Error al marcar mensajes como leídos:', error));
            }
        });
    </script>
    <script>
            document.querySelectorAll('#file-input').forEach(function(inputElement) {
                inputElement.addEventListener('change', function() {
                    console.log('File input changed'); // Verifica que el evento se activa
                    const fileIcon = this.closest('.input-group-text').querySelector('#file-icon');
                    const fileClip = this.closest('.input-group-text').querySelector('#file-clip');

                    if (this.files.length > 0) {
                        console.log('File selected'); // Verifica que se ha seleccionado un archivo
                        fileIcon.style.display = 'inline-block';
                        fileClip.style.display = 'none';
                    } else {
                        console.log('No file selected'); // Verifica que no hay archivo seleccionado
                        fileIcon.style.display = 'none';
                        fileClip.style.display = 'inline-block';
                    }
                });
            });

        function completeTask(event, todoId) {
            event.stopPropagation();  // Detiene la propagación del evento
            const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                    });
            fetch(`/todos/complete/${todoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`todo-card-body-${todoId}`);
                    if (card) {
                        card.style.backgroundColor = '#CDFEA4'; // Color verde claro
                    }

                    // Encuentra y oculta el botón de completar
                    const completeButton = document.getElementById(`complete-button-${todoId}`);
                    if (completeButton) {
                        completeButton.style.display = 'none';
                    }
                    Toast.fire({
                        icon: "success",
                        title: "Tarea completada con éxito!"
                    });
                    return;
                }else{

                    Toast.fire({
                        icon: "error",
                        title: "Error el completar la tarea!"
                    });
                    return;
                }
            }).catch(error => console.error('Error:', error));
        }

        function finishTask(event, todoId) {
            event.stopPropagation();  // Detiene la propagación del evento
            const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                    });
            fetch(`/todos/finish/${todoId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {

                    const card = document.getElementById(`todo-card-${todoId}`);
                    if (card) {
                        card.style.display = 'none'; // Color verde claro
                    }
                    Toast.fire({
                        icon: "success",
                        title: "Tarea finalizada con éxito!"
                    });
                }else{
                    Toast.fire({
                        icon: "error",
                        title: "Error el finalizar la tarea!"
                    });
                }
            }).catch(error => console.error('Error:', error));
        }

        function updateUnreadMessagesCount(todoId) {
            fetch(`/todos/unread-messages-count/${todoId}`,{
                method: 'POST', // Cambiamos a POST
                headers: {
                    'Content-Type': 'application/json' // Indicamos que enviamos JSON
                },
                body: JSON.stringify({}) // Enviamos un cuerpo vacío o puedes agregar datos si es necesario

                })
                .then(response => response.json())
                .then(data => {
                    const pulseDiv = document.querySelector(`#todo-card-${todoId} .pulse`);

                    if (data.unreadCount > 0) {
                        pulseDiv.style.display = 'flex';
                        pulseDiv.textContent = data.unreadCount;
                    } else {
                        pulseDiv.style.display = 'none';
                        pulseDiv.textContent = '';
                    }
                });
        }

        function loadMessages(todoId) {
            $.ajax({
                url: `/todos/getMessages/${todoId}`,
                type: 'POST',
                success: function(data) {
                    let messagesContainer = $(`#todo-card-${todoId} .chat-container`);
                    messagesContainer.html(''); // Limpiamos el contenedor
                    data.forEach(function(message) {
                        let fileIcon = '';
                        if (message.archivo) {
                            fileIcon = `
                                <div class="file-icon">
                                    <a href="/storage/${message.archivo}" target="_blank">
                                        <i class="fa-regular fa-file-lines fa-2x"></i>
                                    </a>
                                </div>
                            `;
                        }
                        const messageClass = message.admin_user_id == {{ auth()->id() }} ? 'mine' : 'theirs';

                        messagesContainer.append(`
                            <div class="p-3 message ${messageClass}">
                                ${fileIcon}
                                <strong>${message.user.name}:</strong> ${message.mensaje}
                            </div>
                        `);
                    });
                }
            });
        }

        function startPolling() {
            @if (count($to_dos) > 0)
                @foreach ($to_dos as $to_do)
                    setInterval(function() {
                        updateUnreadMessagesCount('{{ $to_do->id }}');
                        loadMessages('{{ $to_do->id }}');
                    }, 5000);  // Polling cada 5 segundos para cada to-do
                @endforeach
            @else
                console.log('No hay to-dos activos.');
            @endif
        }

        $(document).ready(function() {
            startPolling();
        });

    </script>
    <script>
    var enRutaEspecifica = true;

        $(document).on("click", '.tarea-sing', function() {
                    var id = $(this).attr("id");
                    showTaskInfoNew(id);
        });

        function revisarTarea(id) {
                console.log(id);
                $.when(getDataTask(id)).then(function(data, textStatus, jqXHR) {
                    estado = "Revision";
                    $.when(setStatusTask(id, estado)).then(function(data, textStatus, jqXHR) {
                        if (data.estado == "OK") {
                            refreshTasks();
                            Swal.fire(
                                'Éxito',
                                'Tarea en revisión.',
                                'success',
                            ).then((result) => {
                                if (result.value) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire(
                                'Error',
                                'Error en la tarea.',
                                'error'
                            );
                        }
                    });
                });
        }

        function renaudarTarea(id) {
                console.log(id);
                $.when(getDataTask(id)).then(function(data, textStatus, jqXHR) {

                    estado = "Reanudar";
                    $.when(setStatusTask(id, estado)).then(function(data, textStatus, jqXHR) {
                        if (data.estado == "OK") {
                            refreshTasks();
                            Swal.fire(
                                'Éxito',
                                'Tarea Reanudada',
                                'success',
                            )
                            location.reload();
                            // showTaskInfoNew(id);
                        } else {
                            Swal.fire(
                                'Error',
                                'Error en la tarea.',
                                'error'
                            );
                        }
                    });
                });
        }

        function pausarTarea(id) {
                console.log(id);
                $.when(getDataTask(id)).then(function(data, textStatus, jqXHR) {
                    estado = "Pausada";
                    $.when(setStatusTask(id, estado)).then(function(data, textStatus, jqXHR) {
                        if (data.estado == "OK") {
                            refreshTasks();
                            Swal.fire(
                                'Éxito',
                                'Tarea Pausada',
                                'success',
                            )
                            location.reload();
                            // showTaskInfoNew(id);
                        } else {
                            Swal.fire(
                                'Error',
                                'Error en la tarea.',
                                'error'
                            );
                        }
                    });
                });
        }


        $(document).ready(function() {

            $.when(getTasksRefresh()).then(function(data, textStatus, jqXHR) {
                if (data.taskPlay != null) {
                    var id = data.taskPlay.id;
                    $('.infotask').hide();

                    $('.tarea-sing').off('click').on('click', function() {
                        var infoContainer = $(this).next('.infotask');
                        if (infoContainer.is(':visible')){
                            infoContainer.slideUp();
                        } else {
                            $('.infotask').slideUp(); // Cierra otros contenedores abiertos
                            infoContainer.slideDown();
                        }
                    });
                }else{
                    $('.infotask').hide();
                    $('.tarea-sing').off('click').on('click', function() {
                        var infoContainer = $(this).next('.infotask');
                        if (infoContainer.is(':visible')){
                            infoContainer.slideUp();
                        } else {
                            $('.infotask').slideUp(); // Cierra otros contenedores abiertos
                            infoContainer.slideDown();
                        }
                    });
                }
            });

            // Evento change para el select2
            $('#selectTask').on('change', function() {
                var selectedTaskId = $(this).val();
                // Oculta todas las tareas
                $('.task-item').hide();
                // Muestra la tarea seleccionada
                if (selectedTaskId > 0) {
                    $('#task-' + selectedTaskId).show();
                } else {
                    // Si no hay tarea seleccionada, muestra todas
                    $('.task-item').show();
                }
            });
            // Inicializa select2
            $('.js-select2').select2();


        })

            function decodeHTML(str) {
                return str.replace(/&#([0-9]+);/g, function(full, int) {
                    return String.fromCharCode(parseInt(int));
                });
            }

            function showTaskInfoNew(id) {
                $.when(getDataTask(id)).then(function(data) {
                    var contenedor = $('.infotask');
                    var descripcionFinal = '';
                    if (data.descripcion) {
                        var des = data.descripcion.split('.');
                        des.forEach(function(ele) {
                            var template = `<span class="descripcionLineas">${ele}</span>`;
                            descripcionFinal += template;
                        });
                    }

                    function colorClass(dataEstimada, dataReal) {
                        var tiempoEstimado = dataEstimada.split(":");
                        var tiempoReal = dataReal.split(":");
                        var diferencia = tiempoReal[0] - tiempoEstimado[0];
                        if (diferencia < 0) {
                            return 'bg-success';
                        } else if (diferencia === 0 || diferencia === 2) {
                            return 'bg-warning text-dark';
                        } else {
                            return 'bg-danger';
                        }
                    }

                    var activarDisabled = 'disabled';
                    var pausarDisabled = 'disabled';
                    var revisarDisabled = 'disabled';
                    // Control de botones según el estado de la tarea
                    switch (data.estado) {
                            case 'Reanudada':
                                pausarDisabled = ''; // Activar solo el botón de pausar
                                break;
                            case 'Pausada':
                                activarDisabled = '';
                                revisarDisabled = ''; // Activar los botones de activar y revisar
                                break;
                            case 'Revisión':
                                activarDisabled = ''; // Activar solo el botón de activar
                                break;
                        }
                        contenedor.html(
                            `<div class="container mt-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h3 class="head-proceso">PROCESO</h3>
                                    <span class="badge bg-secondary mx-2">TIEMPO ESTIMADO: ${data.estimado}</span>
                                    <span class="badge ${colorClass(data.estimado, data.real)} mx-2">LLEVAS: ${data.real}</span>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-sm-12 col-md-5">
                                        <h5 class="tarea-cliente text-primary fs-6">Cliente: ${data.cliente}</h5>
                                        <p class="tarea-nombre fw-bolder fs-3 mt-2">${data.titulo}</p>
                                    </div>
                                    <div class="col-sm-12 col-md-2">
                                        <h6 class="text-secondary">Gestor: ${data.gestor}</h6>
                                    </div>
                                    <div class="col-sm-12 col-md-5 text-right">
                                        <button id="btnActivar" type="button" class="btn btn-success btn-sm mx-1" ${activarDisabled} onclick="renaudarTarea(${data.id})">Activar</button>
                                        <button id="btnPausar" type="button" class="btn btn-warning btn-sm mx-1" ${pausarDisabled} onclick="pausarTarea(${data.id})">Pausar</button>
                                        <button id="btnRevisar" type="button" class="btn btn-info btn-sm mx-1" ${revisarDisabled} onclick="revisarTarea(${data.id})">Revisar</button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <p class="text-muted">${descripcionFinal}</p>
                                    </div>
                                </div>
                                <div id="notas" class="mt-4">
                                    <!-- Notas dinámicas se insertarán aquí -->
                                </div>

                            </div>`
                        );

                    // Evento para manejar el despliegue de la información
                    $('.tarea-sing').off('click').on('click', function() {
                        var infoContainer = $(this).next('.infotask');
                        if (infoContainer.is(':visible')){
                            infoContainer.slideUp();
                        } else {
                            $('.infotask').slideUp(); // Cierra otros contenedores abiertos
                            infoContainer.slideDown();
                        }
                    });
                });
            }


            function refreshTasks() {
                $.when(getTasksRefresh()).then(function(data, textStatus, jqXHR) {
                    var datos = "";
                    $(".TareaActivada").empty();
                    $(".TareasPausadas").empty();
                    $(".TareasRevision").empty();


                    if (data.taskPlay != null) {
                        datos += "<li id=" + data.taskPlay.id + " class='tarea'>";
                        datos += "<p>" + data.taskPlay.title + "</p>";
                        datos += "</li>";
                        $(".TareaActivada").append(datos);
                    }

                    if (data.tasksPause != null) {
                        datos = "";
                        $.each(data.tasksPause, function(key, value) {
                            datos += "<li id=" + value.id + " class='tarea'>";
                            datos += "<p>" + value.title + "</p>";
                            datos += "</li>";
                        });
                        $(".TareasPausadas").append(datos);
                    }

                    if (data.tasksRevision != null) {
                        datos = "";
                        $.each(data.tasksRevision, function(key, value) {
                            datos += "<li id=" + value.id + " class='tarea'>";
                            datos += "<p>" + value.title + "</p>";
                            datos += "</li>";
                        });
                        $(".TareasRevision").append(datos);
                    }

                });
            }

            function getTasksRefresh() {
                return $.ajax({
                    type: "POST",
                    url: '/dashboard/getTasksRefresh',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    dataType: "json"
                });
            }

            function getDataTask(id) {
                return $.ajax({
                    type: "POST",
                    url: '/dashboard/getDataTask',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        'id': id
                    },
                    dataType: "json"
                });
            }

            function setStatusTask(id, estado) {
                return $.ajax({
                    type: "POST",
                    url: '/dashboard/setStatusTask',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        'id': id,
                        'estado': estado
                    },
                    dataType: "json"
                });
            }

    </script>
@endsection
