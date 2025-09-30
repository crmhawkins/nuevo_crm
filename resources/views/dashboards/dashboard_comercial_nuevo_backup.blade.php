@extends('layouts.app')

@section('titulo', 'Dashboard Comercial')

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

        <!-- Nueva sección para gestión de leads y visitas -->
        <div class="row justify-content-center my-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Gestión Comercial</h3>
                        <button class="btn btn-success" onclick="abrirModalNuevaVisita()">
                            <i class="fas fa-plus me-2"></i>Nueva Visita
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Tabla de visitas recientes -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-muted">Visitas Recientes</h5>
                                <div class="table-responsive">
                                    <table id="visitasTable" class="table table-striped table-hover">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Cliente</th>
                                                <th>Tipo</th>
                                                <th>Valoración</th>
                                                <th>Comentarios</th>
                                                <th>Seguimiento</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($visitas as $visita)
                                            <tr>
                                                <td>{{ $visita->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $visita->cliente ? $visita->cliente->name : $visita->nombre_cliente }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $visita->tipo_visita == 'presencial' ? 'success' : 'info' }}">
                                                        {{ ucfirst($visita->tipo_visita) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $visita->valoracion/2 ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                        <span class="ms-2">{{ $visita->valoracion }}/10</span>
                                                    </div>
                                                </td>
                                                <td>{{ Str::limit($visita->comentarios, 50) }}</td>
                                                <td>
                                                    @if($visita->requiere_seguimiento)
                                                        <span class="badge badge-warning">
                                                            {{ $visita->fecha_seguimiento ? $visita->fecha_seguimiento->format('d/m/Y') : 'Pendiente' }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">No</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="verVisita({{ $visita->id }})">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
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
        </div>
</div>

<!-- Modal para Nueva Visita -->
<div class="modal fade" id="modalNuevaVisita" tabindex="-1" aria-labelledby="modalNuevaVisitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaVisitaLabel">Nueva Visita Comercial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaVisita" action="{{route('visitas.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="comercial_id" value="{{ auth()->id() }}">
                    
                    <!-- Paso 1: Tipo de cliente -->
                    <div id="paso1" class="visita-paso">
                        <h6>¿Es un cliente nuevo o existente?</h6>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="seleccionarTipoCliente('nuevo')">
                                    <i class="fas fa-user-plus me-2"></i>Cliente Nuevo
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success w-100" onclick="seleccionarTipoCliente('existente')">
                                    <i class="fas fa-user me-2"></i>Cliente Existente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Datos del cliente -->
                    <div id="paso2" class="visita-paso" style="display: none;">
                        <!-- Cliente nuevo -->
                        <div id="clienteNuevo" style="display: none;">
                            <h6>Datos del Cliente Nuevo (Lead)</h6>
                            <div class="mb-3">
                                <label class="form-label">Nombre del cliente</label>
                                <input type="text" name="nombre_cliente" class="form-control" placeholder="Nombre completo" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono_cliente" class="form-control" placeholder="Teléfono">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email_cliente" class="form-control" placeholder="Email">
                            </div>
                        </div>

                        <!-- Cliente existente -->
                        <div id="clienteExistente" style="display: none;">
                            <h6>Seleccionar Cliente Existente</h6>
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <select name="cliente_id" id="clienteSelect" class="form-control">
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(1)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="siguientePaso(3)">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 3: Tipo de visita -->
                    <div id="paso3" class="visita-paso" style="display: none;">
                        <h6>¿Cómo fue la visita?</h6>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="seleccionarTipoVisita('presencial')">
                                    <i class="fas fa-handshake me-2"></i>Presencial
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info w-100" onclick="seleccionarTipoVisita('telefonico')">
                                    <i class="fas fa-phone me-2"></i>Telefónico
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(2)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="siguientePaso(4)">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 4: Valoración -->
                    <div id="paso4" class="visita-paso" style="display: none;">
                        <h6>Valoración de la visita (1-10)</h6>
                        <div class="mb-3">
                            <input type="hidden" name="valoracion" id="valoracionInput">
                            <div class="d-flex justify-content-center">
                                @for($i = 1; $i <= 10; $i++)
                                    <button type="button" class="btn btn-outline-warning mx-1 valoracion-btn" data-valor="{{ $i }}" onclick="seleccionarValoracion({{ $i }})">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                            <div class="text-center mt-2">
                                <span id="valoracionTexto">Selecciona una valoración</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Comentarios</label>
                            <textarea name="comentarios" class="form-control" rows="3" placeholder="Comentarios sobre la visita..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(3)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="siguientePaso(4)">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 5: Seguimiento -->
                    <div id="paso5" class="visita-paso" style="display: none;">
                        <h6>¿Requiere seguimiento?</h6>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success w-100" onclick="seleccionarSeguimiento(true)">
                                    <i class="fas fa-calendar-check me-2"></i>Sí
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="seleccionarSeguimiento(false)">
                                    <i class="fas fa-times me-2"></i>No
                                </button>
                            </div>
                        </div>

                        <div id="fechaSeguimientoDiv" style="display: none;" class="mt-3">
                            <label class="form-label">Fecha de seguimiento</label>
                            <input type="datetime-local" name="fecha_seguimiento" class="form-control">
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(4)">Atrás</button>
                            <button type="submit" class="btn btn-success">Guardar Visita</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
let pasoActual = 1;
let tipoCliente = null;
let tipoVisita = null;
let requiereSeguimiento = null;

$(document).ready(function() {
    $("#topbar").remove();
    
    $('#sendLogout').click(function(e){
        e.preventDefault();
        $('#logout-form').submit();
    });

    // Inicializar DataTables para la tabla de visitas
    $('#visitasTable').DataTable({
        paging: true,
        lengthMenu: [[10, 25, 50], [10, 25, 50]],
        language: {
            decimal: "",
            emptyTable: "No hay visitas registradas",
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

    // Manejar envío del formulario de visita
    $('#formNuevaVisita').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('{{ route("visitas.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    $('#modalNuevaVisita').modal('hide');
                    location.reload(); // Recargar para mostrar la nueva visita
                });
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al guardar la visita',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
});

function abrirModalNuevaVisita() {
    $('#modalNuevaVisita').modal('show');
    resetearFormulario();
}

function resetearFormulario() {
    pasoActual = 1;
    tipoCliente = null;
    tipoVisita = null;
    requiereSeguimiento = null;
    
    $('.visita-paso').hide();
    $('#paso1').show();
    
    // Limpiar formulario
    $('#formNuevaVisita')[0].reset();
    $('.valoracion-btn').removeClass('btn-warning').addClass('btn-outline-warning');
    $('#valoracionTexto').text('Selecciona una valoración');
    
    // Destruir Select2 si existe
    if ($('#clienteSelect').hasClass('select2-hidden-accessible')) {
        $('#clienteSelect').select2('destroy');
    }
}

function seleccionarTipoCliente(tipo) {
    tipoCliente = tipo;
    
    if (tipo === 'nuevo') {
        $('#clienteNuevo').show();
        $('#clienteExistente').hide();
    } else {
        $('#clienteExistente').show();
        $('#clienteNuevo').hide();
        
        // Inicializar Select2 para el selector de clientes
        setTimeout(() => {
            $('#clienteSelect').select2({
                placeholder: 'Buscar cliente...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalNuevaVisita'),
                language: {
                    noResults: function() {
                        return "No se encontraron clientes";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });
            
            // Debug: Verificar que Select2 se inicializó
            console.log('Select2 inicializado');
            
            // Agregar evento de cambio para debug
            $('#clienteSelect').on('change', function() {
                console.log('Cliente cambiado:', $(this).val());
            });
        }, 100);
    }
    
    siguientePaso(2);
}

function seleccionarTipoVisita(tipo) {
    console.log('seleccionarTipoVisita llamado con:', tipo);
    tipoVisita = tipo;
    console.log('tipoVisita establecido a:', tipoVisita);
    $('input[name="tipo_visita"]').remove();
    $('#formNuevaVisita').append(`<input type="hidden" name="tipo_visita" value="${tipo}">`);
    console.log('Input hidden agregado con valor:', tipo);
    // No llamar siguientePaso aquí, solo cuando el usuario haga clic en "Siguiente"
}

function seleccionarValoracion(valor) {
    $('#valoracionInput').val(valor);
    $('.valoracion-btn').removeClass('btn-warning').addClass('btn-outline-warning');
    $(`.valoracion-btn[data-valor="${valor}"]`).removeClass('btn-outline-warning').addClass('btn-warning');
    $('#valoracionTexto').text(`Valoración: ${valor}/10`);
}

function seleccionarSeguimiento(requiere) {
    requiereSeguimiento = requiere;
    $('input[name="requiere_seguimiento"]').remove();
    $('#formNuevaVisita').append(`<input type="hidden" name="requiere_seguimiento" value="${requiere ? 1 : 0}">`);
    
    if (requiere) {
        $('#fechaSeguimientoDiv').show();
    } else {
        $('#fechaSeguimientoDiv').hide();
    }
}

function siguientePaso(paso) {
    console.log('siguientePaso llamado con paso:', paso);
    console.log('tipoCliente:', tipoCliente);
    console.log('tipoVisita:', tipoVisita);
    
    // Validaciones específicas por paso
    if (paso === 2) {
        if (!tipoCliente) {
            alert('Por favor selecciona el tipo de cliente');
            return;
        }
        // NO validar datos del cliente aquí, solo mostrar el paso 2
    }
    
    if (paso === 3) {
        console.log('Validando paso 3...');
        console.log('tipoCliente en paso 3:', tipoCliente);
        
        // Validar datos del cliente según el tipo ANTES de pasar al paso 3
        if (tipoCliente === 'nuevo') {
            const nombre = $('input[name="nombre_cliente"]').val();
            console.log('Nombre cliente nuevo:', nombre);
            if (!nombre || nombre.trim() === '') {
                alert('Por favor ingresa el nombre del cliente');
                return;
            }
        } else if (tipoCliente === 'existente') {
            const clienteId = $('#clienteSelect').val();
            console.log('Cliente seleccionado:', clienteId); // Debug
            console.log('Elemento clienteSelect:', $('#clienteSelect'));
            console.log('Valor del select:', $('#clienteSelect').val());
            if (!clienteId) {
                alert('Por favor selecciona un cliente');
                return;
            }
        }
        
        console.log('tipoVisita en paso 3:', tipoVisita);
        // No validar tipoVisita aquí, se validará en el paso 4
        
        console.log('Validación del paso 3 completada, avanzando...');
    }
    
    if (paso === 4) {
        if (!$('#valoracionInput').val()) {
            alert('Por favor selecciona una valoración');
            return;
        }
    }
    
    console.log('Ocultando todos los pasos y mostrando paso:', paso);
    $('.visita-paso').hide();
    $(`#paso${paso}`).show();
    pasoActual = paso;
    console.log('Paso cambiado exitosamente a:', paso);
}

function volverPaso(paso) {
    $('.visita-paso').hide();
    $(`#paso${paso}`).show();
    pasoActual = paso;
}

function verVisita(id) {
    // Implementar vista de detalles de visita
    alert('Ver visita: ' + id);
}

// Timer functions (mantener las existentes)
let timerState = '{{ $jornadaActiva ? "running" : "stopped" }}'
let timerTime = {{ $timeWorkedToday }};

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
    getTime();

    let now = new Date();
    let currentHour = now.getHours();
    let currentMinute = now.getMinutes();

    let workedHours = timerTime / 3600;

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
        });
    } else {
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
    updateTime();

    setInterval(function() {
        getTime();
    }, 120000);

    if ('{{ $jornadaActiva }}') {
        document.getElementById('startJornadaBtn').style.display = 'none';
        document.getElementById('endJornadaBtn').style.display = 'block';
        if ('{{ $pausaActiva }}') {
            document.getElementById('startPauseBtn').style.display = 'none';
            document.getElementById('endPauseBtn').style.display = 'block';
        } else {
            document.getElementById('startPauseBtn').style.display = 'block';
            document.getElementById('endPauseBtn').style.display = 'none';
            startTimer();
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
