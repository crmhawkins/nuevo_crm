@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')

<div class="page-heading card" style="box-shadow: none !important">
        <div class="bg-image overflow-hidden mb-10" style="background-color: black">
            <div class="content content-narrow content-full">
                <div class="text-center mt-5 mb-2">
                    <h2 class="h2 text-white mb-0">Bienvenido {{$user->name}}</h2>
                    <h1 class="h1 text-white mb-0">Quedan {{$diasDiferencia}} días para finalizar el mes</h1>
                    <h2 class="h3 text-white mb-0">Tienes {{$pedienteCierre}} € pendiente por tramitar</h2>
                    <div class="mt-4 row d-flex justify-content-center ">
                        <div class="col-6 mb-3">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <div class="row d-flex justify-content-around " >
                                <button  id="sendLogout" type="button" class="btn btn-warning col-1 py-2 mb-4">Salir</button>
                                <h2 id="timer" class="text-white display-6 font-weight-bold col-4">00:00:00</h2>
                                    <button id="startJornadaBtn" class="btn  btn-primary mb-4 col-2" onclick="startJornada()">Inicio Jornada</button>
                                    <button id="startPauseBtn" class="btn  btn-secondary mb-4col-2" onclick="startPause()" style="display:none;">Iniciar Pausa</button>
                                    <button id="endPauseBtn" class="btn  btn-dark mb-4 col-2" onclick="endPause()" style="display:none;">Finalizar Pausa</button>
                                    <button id="endJornadaBtn" class="btn  btn-danger mb-4 col-2" onclick="endJornada()" style="display:none;">Fin de Jornada</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content content-narrow">
            <div class="row d-flex justify-content-center ">
                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Pendiente de Cierre</h6>
                            <h2 class="font-weight-bold">{{$pedienteCierre}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión En Curso</h6>
                            <h2 class="font-weight-bold">{{$comisionCurso}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Pendiente</h6>
                            <h2 class="font-weight-bold">{{$comisionPendiente}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Tramitada</h6>
                            <h2 class="font-weight-bold">{{$comisionTramitadas}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Restante</h6>
                            <h2 class="font-weight-bold">{{$comisionRestante}} €</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center my-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Agregar Cliente</h3>
                    </div>
                    <div class="card-body">
                        <form id="kit_form" action="{{route('kitDigital.storeComercial')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mt-3 justify-content-center">
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="cliente" class="form-control @error('cliente') is-invalid @enderror" value="{{old('cliente')}}" type="text" placeholder="Nombre del cliente">
                                    @error('cliente')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{old('telefono')}}" type="text" placeholder="Número de Teléfono">
                                    @error('telefono')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="email" class="form-control " value="{{old('email')}}" type="email" placeholder="Email">
                                </div>
                                <div class="col-md-1 col-sm-12 mb-3">
                                    <select name="segmento" class="form-control @error('segmento') is-invalid @enderror">
                                        <option  value="">Segmento</option>
                                        <option {{old('segmento') == '1' ? 'selected' : ''}} value="1">1</option>
                                        <option {{old('segmento') == '2' ? 'selected' : ''}} value="2">2</option>
                                        <option {{old('segmento') == '3' ? 'selected' : ''}} value="3">3 </option>
                                        <option {{old('segmento') == '30' ? 'selected' : ''}} value="30">3 extra</option>
                                        <option {{old('segmento') == '4' ? 'selected' : ''}} value="4">4</option>
                                        <option {{old('segmento') == '5' ? 'selected' : ''}} value="5">5</option>
                                        <option {{old('segmento') == 'A' ? 'selected' : ''}} value="A">A</option>
                                        <option {{old('segmento') == 'B' ? 'selected' : ''}} value="B">B</option>
                                        <option {{old('segmento') == 'C' ? 'selected' : ''}} value="C">C</option>
                                    </select>
                                    @error('segmento')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror

                                </div>
                                <div class="col-md-1 col-sm-12 mb-3">
                                    <select name="estado" class="form-control @error('estado') is-invalid @enderror" >
                                        <option value="">Estado</option>
                                        <option {{old('estado') == '24' ? 'selected' : ''}} value="24">Interesados</option>
                                        <option {{old('estado') == '18' ? 'selected' : ''}} value="18">Leads</option>
                                    </select>
                                    @error('estado')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-sm-12 mb-3">
                                    <textarea name="comentario" class="form-control" placeholder="Comentario" rows="1">{{old('comentario')}}</textarea>
                                </div>
                                <div class="col-md-1 col-sm-12 d-flex align-items-end">
                                    <input id="kit_submit" type="submit" value="Guardar" class="btn btn-primary w-100">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Kit Digital</h3>
                        <div class="w-50">
                            <select id="estadoFilter" class="form-control">
                                <option value="">Todos los Estados</option>
                                @if (isset($estadosKit))
                                    @foreach ($estadosKit as $estado)
                                        <option value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive m-2">
                            <table id="kitDigitalTable" class="table table-striped table-hover table-borderless table-vcenter mb-0">
                                <thead class="thead-dark">
                                    <tr class="text-uppercase text-center">
                                        <th style="min-width: 120px">Fecha</th>
                                        <th class="d-none d-md-table-cell">Concepto</th>
                                        <th>Estado</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th style="max-width: 300px !important">Comentario</th>
                                        <th style="min-width: 120px" class="d-none d-md-table-cell text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ayudas as $ayuda)
                                        <tr class="text-center">
                                            <td style="min-width: 120px">{{ \Carbon\Carbon::parse($ayuda->created_at)->format('d-m-Y') }}</td>
                                            <td class="d-none d-md-table-cell">{{ $ayuda->cliente }}</td>
                                            <td class="text-warning">{{ Optional($ayuda->estados)->nombre  }}</td>
                                            <td>{{ $ayuda->telefono }}</td>
                                            <td>{{ $ayuda->email }}</td>
                                            <td style="max-width: 300px !important">{{ $ayuda->comentario }}</td>
                                            <td style="min-width: 120px" class="d-none d-md-table-cell text-right">{{ $ayuda->importe }} €</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
@section('scripts')

<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css" rel="stylesheet">

<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js"></script>


<script>
    $(document).ready(function() {
        $("#topbar").remove();
        $('#sendLogout').click(function(e){
                e.preventDefault(); // Esto previene que el enlace navegue a otra página.
                $('#logout-form').submit(); // Esto envía el formulario.
            });
        $('#kit_submit').click(function(e){
                e.preventDefault(); // Esto previene que el enlace navegue a otra página.
                $('#kit_form').submit(); // Esto envía el formulario.
            });
        // Inicializar DataTables para la tabla de Kit Digital
        $('#kitDigitalTable').DataTable({
            paging: true,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            language: {
                decimal: "",
                emptyTable: "No hay datos disponibles",
                info: "_TOTAL_ entradas en total",
                infoEmpty: "0 entradas",
                infoFiltered: "(filtrado de _MAX_ entradas en total)",
                lengthMenu: "Nº de entradas  _MENU_",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No hay entradas que cumplan el criterio",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        // Filtro para el dropdown de Estado
        $('#estadoFilter').on('change', function() {
            var table = $('#kitDigitalTable').DataTable();
            table.column(2).search(this.value).draw();
        });

        // Botón de logout
        $('#sendLogout').on('click', function(e) {
            e.preventDefault();
            $.post('/admin/logout', {_token: '{{ csrf_token() }}'}, function(data) {
                window.location.href = '/admin';
            });
        });
    });
</script>
<script>
    let timerState = '{{ $jornadaActiva ? "running" : "stopped" }}'
    let timerTime = {{ $timeWorkedToday }}; // In seconds, initialized with the time worked today
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

        setInterval(function() {
            getTime();
        }, 120000);

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
@endsection
