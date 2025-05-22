@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<style>
    .user-card {
        width: 100% !important; /* Full width on small screens */
        height: 500px !important; /* Full height on small screens */
        overflow: hidden; /* Ensures no content spills out */
        margin-bottom: 20px; /* Space between cards */
    }
    @media (min-width: 768px) {
        .user-card { /* Adjust width on larger screens */
            width: calc(50% - 1rem) !important; /* Adapt width with a small gap */
        }
    }
    .card-body {
        position: relative; /* Positioning context */
        height: calc(100% - 20px); /* Full height minus padding */
        display: flex;
        flex-direction: column; /* Stack children vertically */
    }
    .card-title {
        font-size: 2rem; /* Larger title */
        margin-bottom: .75rem; /* Space below title */
        font-weight: 400;
    }
    .card-subtitle {
        font-size: 1.2rem; /* Larger title */
        margin-bottom: .75rem; /* Space below title */
        font-weight: 400;
    }
    .table-responsive {
        flex-grow: 1; /* Allows table container to fill available space */
        overflow-y: auto; /* Vertical scroll on overflow */
    }
</style>
@endsection
@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Cola de trabajo</h3>
                    <p class="text-subtitle text-muted">Listado de colas de trabajo</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('tareas.index')}}">Tareas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cola de trabajo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section d-flex flex-wrap justify-content-between mt-4">
            @foreach ($usuarios as $usuario)
                <?php
                    $actual = date('Y-m-d H:i:s');
                    $actualFecha = $actual;
                    $paso = 0;
                    $contador = 0;
                    $fechaCalendario = [];
                    $tareaCalendar = [];
                    $tareaDesc = [];
                ?>
            <div class="card user-card">
                <div class="card-body ">
                    <div class="row">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title">{{$usuario->name}} {{$usuario->surname}}</p>
                                <p class="card-subtitle">{{$usuario->departamento->name}}</p>
                            </div>
                            <a class="btn btn-outline-secondary" href="{{route('tarea.calendar',$usuario->id)}}" target="_blank">
                                Ver calendario
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                 <tr>
                                    <th scope="col">TITULO</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">PRIORIDAD</th>
                                    <th scope="col">ESTIMADO</th>
                                    <th scope="col">REAL</th>
                                    <th scope="col">ENTREGA</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                @foreach ( $usuario->tareas->whereIn('task_status_id', [1, 2]) as $tarea )
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
                                    <tr class="clickable-row-sta" data-href="{{route('tarea.edit', $tarea->id)}}" @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->title}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->presupuesto ? ($tarea->presupuesto->cliente ? ($tarea->presupuesto->cliente->company ??  $tarea->presupuesto->cliente->name)  : 'Cliente Borrado') : 'Presupuesto Borrado'}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{optional($tarea->prioridad)->name ?? 'N/A'}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->estimated_time}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->real_time}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{ $actualFechaFinal}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </section>
    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

