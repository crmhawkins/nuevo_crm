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

        <!-- Gestión Comercial -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Gestión Comercial</h5>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaVisita">
                            <i class="fas fa-plus me-1"></i>Nueva Visita
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="tablaVisitas">
                                <thead>
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
                                    @forelse($visitas as $visita)
                                        <tr>
                                            <td>{{ $visita->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $visita->cliente ? $visita->cliente->name : $visita->nombre_cliente }}</td>
                                            <td>
                                                <span class="badge bg-{{ $visita->tipo_visita == 'presencial' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($visita->tipo_visita) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">{{ $visita->valoracion }}/10</span>
                                            </td>
                                            <td>{{ Str::limit($visita->comentarios, 30) }}</td>
                                            <td>
                                                @if($visita->requiere_seguimiento)
                                                    <span class="badge bg-success">Sí</span>
                                                    @if($visita->fecha_seguimiento)
                                                        <br><small>{{ $visita->fecha_seguimiento->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="verVisita({{ $visita->id }})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay visitas registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Visita - VERSIÓN SIMPLIFICADA Y FUNCIONAL -->
<div class="modal fade" id="modalNuevaVisita" tabindex="-1" aria-labelledby="modalNuevaVisitaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNuevaVisitaLabel">Nueva Visita Comercial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevaVisita">
                    @csrf
                    <input type="hidden" name="comercial_id" value="{{ $user->id }}">

                    <!-- Paso 1: Tipo de cliente -->
                    <div id="paso1" class="visita-paso">
                        <h6 class="mb-4">¿Es un cliente nuevo o existente?</h6>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100 py-3" onclick="seleccionarTipoCliente('nuevo')">
                                    <i class="fas fa-user-plus me-2"></i>Cliente Nuevo
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-success w-100 py-3" onclick="seleccionarTipoCliente('existente')">
                                    <i class="fas fa-user me-2"></i>Cliente Existente
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Paso 2: Datos del cliente -->
                    <div id="paso2" class="visita-paso" style="display: none;">
                        <!-- Cliente Nuevo -->
                        <div id="clienteNuevo" style="display: none;">
                            <h6 class="mb-3">Datos del Cliente Nuevo</h6>
                            <div class="mb-3">
                                <label for="nombre_cliente" class="form-label">Nombre del cliente *</label>
                                <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono_cliente" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente">
                            </div>
                            <div class="mb-3">
                                <label for="email_cliente" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email_cliente" name="email_cliente">
                            </div>
                        </div>

                        <!-- Cliente Existente -->
                        <div id="clienteExistente" style="display: none;">
                            <h6 class="mb-3">Seleccionar Cliente Existente</h6>
                            <div class="mb-3">
                                <label for="clienteSelect" class="form-label">Cliente *</label>
                                <select class="form-control" id="clienteSelect" name="cliente_id" required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(1)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="avanzarPaso2()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 3: Tipo de visita -->
                    <div id="paso3" class="visita-paso" style="display: none;">
                        <h6 class="mb-4">¿Cómo fue la visita?</h6>
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-primary w-100 py-3" onclick="seleccionarTipoVisita('presencial')">
                                    <i class="fas fa-handshake me-2"></i>Presencial
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-info w-100 py-3" onclick="seleccionarTipoVisita('telefonico')">
                                    <i class="fas fa-phone me-2"></i>Telefónico
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(2)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="avanzarPaso3()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 4: Valoración -->
                    <div id="paso4" class="visita-paso" style="display: none;">
                        <h6 class="mb-3">Valoración de la visita (1-10)</h6>
                        <div class="mb-3">
                            <input type="hidden" id="valoracionInput" name="valoracion">
                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                @for($i = 1; $i <= 10; $i++)
                                    <button type="button" class="btn btn-outline-warning valoracion-btn" data-valor="{{ $i }}" onclick="seleccionarValoracion({{ $i }})">
                                        {{ $i }}
                                    </button>
                                @endfor
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(3)">Atrás</button>
                            <button type="button" class="btn btn-primary" onclick="avanzarPaso4()">Siguiente</button>
                        </div>
                    </div>

                    <!-- Paso 5: Seguimiento -->
                    <div id="paso5" class="visita-paso" style="display: none;">
                        <h6 class="mb-3">¿Requiere seguimiento?</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="requiere_seguimiento" id="seguimiento_si" value="1" onchange="toggleFechaSeguimiento()">
                                <label class="form-check-label" for="seguimiento_si">
                                    Sí, requiere seguimiento
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="requiere_seguimiento" id="seguimiento_no" value="0" onchange="toggleFechaSeguimiento()" checked>
                                <label class="form-check-label" for="seguimiento_no">
                                    No requiere seguimiento
                                </label>
                            </div>
                        </div>

                        <div id="fechaSeguimientoDiv" style="display: none;">
                            <div class="mb-3">
                                <label for="fecha_seguimiento" class="form-label">Fecha de seguimiento</label>
                                <input type="datetime-local" class="form-control" id="fecha_seguimiento" name="fecha_seguimiento">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="comentarios" class="form-label">Comentarios adicionales</label>
                            <textarea class="form-control" id="comentarios" name="comentarios" rows="3" placeholder="Comentarios sobre la visita..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="volverPaso(4)">Atrás</button>
                            <button type="submit" class="btn btn-success">Guardar Visita</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CDN para Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Variables globales
let pasoActual = 1;
let tipoCliente = null;
let tipoVisita = null;

// Funciones del modal
function seleccionarTipoCliente(tipo) {
    tipoCliente = tipo;
    
    if (tipo === 'nuevo') {
        $('#clienteNuevo').show();
        $('#clienteExistente').hide();
    } else {
        $('#clienteExistente').show();
        $('#clienteNuevo').hide();
        
        // Inicializar Select2
        setTimeout(() => {
            $('#clienteSelect').select2({
                placeholder: 'Buscar cliente...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalNuevaVisita')
            });
        }, 100);
    }
    
    mostrarPaso(2);
}

function avanzarPaso2() {
    if (tipoCliente === 'nuevo') {
        const nombre = $('#nombre_cliente').val();
        if (!nombre || nombre.trim() === '') {
            alert('Por favor ingresa el nombre del cliente');
            return;
        }
    } else {
        const clienteId = $('#clienteSelect').val();
        if (!clienteId) {
            alert('Por favor selecciona un cliente');
            return;
        }
    }
    
    mostrarPaso(3);
}

function seleccionarTipoVisita(tipo) {
    tipoVisita = tipo;
    $('input[name="tipo_visita"]').remove();
    $('#formNuevaVisita').append(`<input type="hidden" name="tipo_visita" value="${tipo}">`);
}

function avanzarPaso3() {
    if (!tipoVisita) {
        alert('Por favor selecciona el tipo de visita');
        return;
    }
    
    mostrarPaso(4);
}

function seleccionarValoracion(valor) {
    $('#valoracionInput').val(valor);
    $('.valoracion-btn').removeClass('btn-warning').addClass('btn-outline-warning');
    $(`.valoracion-btn[data-valor="${valor}"]`).removeClass('btn-outline-warning').addClass('btn-warning');
}

function avanzarPaso4() {
    const valoracion = $('#valoracionInput').val();
    if (!valoracion) {
        alert('Por favor selecciona una valoración');
        return;
    }
    
    mostrarPaso(5);
}

function toggleFechaSeguimiento() {
    const requiereSeguimiento = $('#seguimiento_si').is(':checked');
    if (requiereSeguimiento) {
        $('#fechaSeguimientoDiv').show();
    } else {
        $('#fechaSeguimientoDiv').hide();
    }
}

function volverPaso(paso) {
    mostrarPaso(paso);
}

function mostrarPaso(paso) {
    $('.visita-paso').hide();
    $(`#paso${paso}`).show();
    pasoActual = paso;
}

// Envío del formulario
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
                location.reload();
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

// Limpiar modal al cerrar
$('#modalNuevaVisita').on('hidden.bs.modal', function() {
    // Resetear formulario
    $('#formNuevaVisita')[0].reset();
    $('.visita-paso').hide();
    $('#paso1').show();
    pasoActual = 1;
    tipoCliente = null;
    tipoVisita = null;
    
    // Destruir Select2 si existe
    if ($('#clienteSelect').hasClass('select2-hidden-accessible')) {
        $('#clienteSelect').select2('destroy');
    }
});

function verVisita(id) {
    // Implementar vista de visita
    console.log('Ver visita:', id);
}
</script>

@endsection
