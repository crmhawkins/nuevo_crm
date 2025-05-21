@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />

@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Dashboard</h3>
                </div>

                <div class="col-12 col-md-8 order-md-2 order-first">
                    <div class="row justify-end ">
                        <button id="endllamadaBtn" class="btn jornada btn-danger mx-2 col-2" onclick="endLlamada()"
                            style="display:none;">Finalizar llamada</button>
                        <h2 id="timer" class="display-6 font-weight-bold col-3">00:00:00</h2>
                        <button id="startJornadaBtn" class="btn jornada btn-primary mx-2 col-2"
                            onclick="startJornada()">Inicio Jornada</button>
                        <button id="startPauseBtn" class="btn jornada btn-secondary mx-2 col-2" onclick="startPause()"
                            style="display:none;">Iniciar Pausa</button>
                        <button id="endPauseBtn" class="btn jornada btn-dark mx-2 col-2" onclick="endPause()"
                            style="display:none;">Finalizar Pausa</button>
                        <button id="endJornadaBtn" class="btn jornada btn-danger mx-2 col-2" onclick="endJornada()"
                            style="display:none;">Fin de Jornada</button>
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
                                <div class="row row-cols-1 row-cols-xl-3 g-xl-4 g-3 mb-3">
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-3">
                                                <h5 class="card-title m-0 text-color-4 fw-bold">Pendientes de confirmar</h5>
                                                <span
                                                    class="display-6 m-0"><b>{{ count($user->presupuestosPorEstado(1)) }}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-3">
                                                <h5 class="card-title m-0 text-color-4 fw-bold">Pendientes de aceptar</h5>
                                                <span
                                                    class="display-6 m-0"><b>{{ count($user->presupuestosPorEstado(2)) }}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body p-3">
                                                <h5 class="card-title m-0 text-color-4 fw-bold">Aceptados</h5>
                                                <span
                                                    class="display-6 m-0"><b>{{ count($user->presupuestosPorEstado(3)) }}</b></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('presupuesto.create') }}" class="btn btn-outline-primary mb-2">Nuevo
                                    Presupuesto</a>
                                <a href="{{ route('presupuestos.indexUser') }}" class="btn btn-outline-secondary mb-2">Ver
                                    mis Presupuestos</a>
                                <a href="{{ route('presupuestos.index') }}" class="btn btn-outline-secondary mb-2">Ver todos
                                    los Presupuestos</a>
                            </div>
                            <div class="row row-cols-1 row-cols-xl-2 g-xl-4 g-1">
                                <div class="col">
                                    <div class="card2">
                                        <div class="mb-3 card-body">
                                            <h5 class="card-title fw-bold">Petición</h5>
                                            <div class="row mb-3 ">
                                                <div class="col">
                                                    <div class="card">
                                                        <div class="card-body p-3">
                                                            <h5 class="card-title m-0 text-color-4  fw-bold">Pendientes</h5>
                                                            <span
                                                                class="display-6 m-0"><b>{{ count($user->peticionesPendientes()) }}</b></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('peticion.create') }}"
                                                class="btn btn-outline-primary mb-2">Nueva Petición</a>
                                            <a href="{{ route('peticion.indexUser') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Mis Peticiones</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card2">
                                        <div class="mb-3 card-body">
                                            <h5 class="card-title fw-bold">Ordenes de Compra</h5>
                                            <div class="row mb-3 ">
                                                <div class="col">
                                                    <div class="card">
                                                        <div id="ordenes" class="card-body p-3">
                                                            <h5 class="card-title m-0 text-color-4  fw-bold">Pendientes</h5>
                                                            <span
                                                                class="display-6 m-0"><b>{{ count($user->ordenes()) }}</b></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <a href="{{ route('order.index') }}" class="btn btn-outline-secondary">Ver
                                                Ordenes</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row row-cols-1 row-cols-xl-2 g-xl-4 g-1">
                                <div class="col">
                                    <div class="card2">
                                        <div class="mb-3 card-body">
                                            <h5 class="card-title fw-bold">Producción</h5>
                                            <a href="{{ route('presupuestos.status') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Status Proyectos</a>
                                            <a href="{{ route('tareas.index') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Tareas</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card2">
                                        <div class="mb-3 card-body">
                                            <h5 class="card-title fw-bold">Gestión</h5>
                                            <a href="{{ route('reunion.create') }}"
                                                class="btn btn-outline-primary mb-2">Nueva Reunion</a>
                                            <a href="{{ route('reunion.index') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Actas de reunion</a>
                                            <a href="{{ route('clientes.index') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Clientes</a>
                                            <a href="{{ route('proveedores.index') }}"
                                                class="btn btn-outline-secondary mb-2">Ver Proveedores</a>
                                            <a href="{{ route('kitDigital.create') }}"
                                                class="btn btn-outline-secondary mb-2">Tramitar Subvención</a>
                                            <a target="_blank" href="{{ route('kitDigital.index') }}"
                                                class="btn btn-outline-secondary mb-2">Kit Digital</a>
                                            <a target="_blank" href="{{ route('logs.kitdigital') }}"
                                                class="btn btn-outline-secondary mb-2">Kit Digital Estados</a>
                                            <a target="_blank" href="{{ route('kitDigital.sin_actualizar') }}"
                                                class="btn btn-outline-secondary mb-2"> Kit Digitales Sin Actualizar</a>
                                            <a target="_blank" href="{{ route('kitDigital.pagados') }}"
                                                class="btn btn-outline-secondary mb-2"> Kit Digitales Pagados</a>
                                            <a target="_blank" href="{{ route('kitDigital.indexWhatsapp') }}"
                                                class="btn btn-outline-secondary mb-2">Kit Digital Whatsapp</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="side-column">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap">
                                        <div
                                            class="col-12 d-flex flex-wrap justify-content-center mb-4 align-items-center">
                                            <div class="mx-4 text-center">
                                                <h5 class="my-3">{{ $user->name }}&nbsp;{{ $user->surname }}</h5>
                                                <p class="text-muted mb-1">{{ $user->departamento->name }}</p>
                                                <p class="text-muted mb-4">{{ $user->acceso->name }}</p>
                                                {{-- <div class="d-flex  align-items-center my-2">
                                                <input type="color" class="form-control form-control-color" style="padding: 0.4rem" id="color">
                                                <label for="color" class="form-label m-2">Color</label>
                                            </div> --}}
                                            </div>
                                            <div class="mx-4">
                                                @if ($user->image == null)
                                                    <img alt="avatar" class="rounded-circle img-fluid  m-auto"
                                                        style="width: 150px;"
                                                        src="{{ asset('assets/images/guest.webp') }}" />
                                                @else
                                                    <img alt="avatar" class="rounded-circle img-fluid  m-auto"
                                                        style="width: 150px;"
                                                        src="{{ asset('/storage/avatars/' . $user->image) }}" />
                                                @endif
                                            </div>
                                            <div class="mx-4 text-center">
                                                <h1 class="fs-5 ">Productividad</h1>
                                                <div class="progress-circle" data-percentage="70">
                                                </div>
                                            </div>
                                            <div class="mx-4 text-center">
                                                <div class="card"
                                                    style="border: 1px solid {{ $user->bono > 0 ? 'green' : 'gray' }}; padding: 10px;">
                                                    <h5 class="m-0"
                                                        style="color: {{ $user->bono > 0 ? 'green' : 'gray' }};">
                                                        {{ $user->bono > 0 ? 'Bono: ' . $user->bono . ' €' : 'Sin bono' }}
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mx-4 d-flex my-2">
                                            <div class="text-center mr-2">
                                                <p style="color:#4D989E">Horas a descontar</p>
                                                <p
                                                    style="font-weight: bold;font-size: 1rem; color: {{ $horasSemanales != '00:00:00' ? 'red' : 'black' }};">
                                                    {{ $horasSemanales }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex flex-wrap justify-content-center">
                                            <div class="my-2 text-center">
                                                <a class="btn btn-outline-secondary"
                                                    href="{{ route('contratos.index_user', $user->id) }}">Contrato</a>
                                                <a class="btn btn-outline-secondary"
                                                    href="{{ route('nominas.index_user', $user->id) }}">Nomina</a>
                                                <a class="btn btn-outline-secondary"
                                                    href="{{ route('holiday.index') }}">Vacaciones</a>
                                                <a class="btn btn-outline-secondary"
                                                    href="{{ route('passwords.index') }}">Contraseñas</a>
                                            </div>
                                            <div class="my-2 ml-4 text-center col-auto" role="tablist">
                                                <a class=" btn btn-outline-secondary active" id="list-todo-list"
                                                    data-bs-toggle="list" href="#list-todo" role="tab">TO-DO</a>
                                                <a class=" btn btn-outline-danger" id="list-todo-list-finalizados"
                                                    data-bs-toggle="list" href="#list-todo-finalizados"
                                                    role="tab">Finalizados</a>
                                                <a class="btn btn-outline-secondary" id="list-agenda-list"
                                                    data-bs-toggle="list" href="#list-agenda" role="tab">Agenda</a>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="tab-content text-justify" id="nav-tabContent">
                                        <div class="tab-pane show active" id="list-todo" role="tabpanel"
                                            aria-labelledby="list-todo-list">
                                            <div class="card2 mt-4">
                                                <div class="card-body2">
                                                    <div id="to-do-container" class="d-flex row" style="">
                                                        <div class="col-12 d-flex align-items-center mt-2 pb-0"
                                                            style="max-height: 50px;"> <button
                                                                class="btn btn-outline-secondary w-100"
                                                                onclick="showTodoModal()">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </button>
                                                        </div>
                                                        <div id="to-do" class="p-3">
                                                            @foreach ($to_dos as $to_do)
                                                                <div class="card mt-2"
                                                                    id="todo-card-{{ $to_do->id }}">
                                                                    <div class="card-body d-flex justify-content-between clickable"
                                                                        id="todo-card-body-{{ $to_do->id }}"
                                                                        data-todo-id="{{ $to_do->id }}"
                                                                        style="{{ $to_do->isCompletedByUser($user->id) ? 'background-color: #CDFEA4' : '' }}">
                                                                        <div style="flex: 0 0 60%;">
                                                                            <h3>{{ $to_do->titulo }}</h3>
                                                                        </div>
                                                                        <div class="d-flex align-items-center justify-content-around"
                                                                            style="flex: 0 0 40%;">
                                                                            @if (!$to_do->isCompletedByUser($user->id))
                                                                                <button
                                                                                    onclick="completeTask(event,{{ $to_do->id }})"
                                                                                    id="complete-button-{{ $to_do->id }}"
                                                                                    class="btn btn-success btn-sm">Completar</button>
                                                                            @endif
                                                                            @if ($to_do->admin_user_id == $user->id)
                                                                                <button
                                                                                    onclick="finishTask(event,{{ $to_do->id }})"
                                                                                    class="btn btn-danger btn-sm">Finalizar</button>
                                                                            @endif
                                                                            <div id="todo-card-{{ $to_do->id }}"
                                                                                class="pulse justify-center align-items-center"
                                                                                style="{{ $to_do->unreadMessagesCountByUser($user->id) > 0 ? 'display: flex;' : 'display: none;' }}">
                                                                                {{ $to_do->unreadMessagesCountByUser($user->id) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="info">
                                                                        <div
                                                                            class="d-flex justify-content-evenly flex-wrap">
                                                                            @if ($to_do->project_id)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ route('campania.edit', $to_do->project_id) }}">
                                                                                    Campaña
                                                                                    {{ $to_do->proyecto ? $to_do->proyecto->name : 'borrada' }}</a>
                                                                            @endif
                                                                            @if ($to_do->client_id)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ route('clientes.show', $to_do->client_id) }}">
                                                                                    Cliente
                                                                                    {{ $to_do->cliente ? $to_do->cliente->name : 'borrado' }}</a>
                                                                            @endif
                                                                            @if ($to_do->budget_id)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ route('presupuesto.edit', $to_do->budget_id) }}">
                                                                                    Presupuesto
                                                                                    {{ $to_do->presupuesto ? $to_do->presupuesto->concept : 'borrado' }}</a>
                                                                            @endif
                                                                            @if ($to_do->task_id)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ route('tarea.edit', $to_do->task_id) }}">
                                                                                    Tarea
                                                                                    {{ $to_do->tarea ? $to_do->tarea->title : 'borrada' }}</a>
                                                                            @endif
                                                                            @if ($to_do->url)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ $to_do->url }}"> Informe de
                                                                                    llamadas</a>
                                                                            @endif
                                                                        </div>
                                                                        <div class="participantes d-flex flex-wrap mt-2">
                                                                            <h3 class="m-2">Participantes</h3>
                                                                            @foreach ($to_do->TodoUsers as $usuario)
                                                                                <span
                                                                                    class="badge m-2 {{ $usuario->completada ? 'bg-success' : 'bg-secondary' }}">
                                                                                    {{ $usuario->usuarios->name }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                        <h3 class="m-2">Descripcion </h3>
                                                                        <p class="m-2">{{ $to_do->descripcion }}</p>
                                                                        <div class="chat mt-4">
                                                                            <div class="chat-container">
                                                                                @foreach ($to_do->mensajes as $mensaje)
                                                                                    <div
                                                                                        class="p-3 message {{ $mensaje->admin_user_id == $user->id ? 'mine' : 'theirs' }}">
                                                                                        @if ($mensaje->archivo)
                                                                                            <div class="file-icon">
                                                                                                <a href="{{ asset('storage/' . $mensaje->archivo) }}"
                                                                                                    target="_blank"><i
                                                                                                        class="fa-regular fa-file-lines fa-2x"></i></a>
                                                                                            </div>
                                                                                        @endif
                                                                                        <strong>{{ $mensaje->user->name }}:</strong>
                                                                                        {{ $mensaje->mensaje }}
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <form id="mensaje"
                                                                                action="{{ route('message.store') }}"
                                                                                method="post"
                                                                                enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="todo_id"
                                                                                    value="{{ $to_do->id }}">
                                                                                <input type="hidden" name="admin_user_id"
                                                                                    value="{{ $user->id }}">
                                                                                <div class="input-group my-2">
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="mensaje"
                                                                                        placeholder="Escribe un mensaje...">
                                                                                    <label class="input-group-text"
                                                                                        style="background: white; ">
                                                                                        <i class="fa-solid fa-paperclip"
                                                                                            id="file-clip"></i>
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            style="display: none;"
                                                                                            id="file-input"
                                                                                            name="archivo">
                                                                                        <i class="fa-solid fa-check"
                                                                                            id="file-icon"
                                                                                            style="display: none; color: green;"></i>
                                                                                    </label>
                                                                                    <button id="enviar"
                                                                                        class="btn btn-primary"
                                                                                        type="button"><i
                                                                                            class="fa-regular fa-paper-plane"></i></button>
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
                                                    <div id="to-do-container" class="d-flex row" style="">
                                                        <div id="to-do" class="p-3">
                                                            @foreach ($to_dos_finalizados as $to_do_finalizado)
                                                                <div class="card mt-2"
                                                                    id="todo-card-{{ $to_do_finalizado->id }}">
                                                                    <div class="card-body d-flex justify-content-between clickable"
                                                                        id="todo-card-body-{{ $to_do_finalizado->id }}"
                                                                        data-todo-id="{{ $to_do_finalizado->id }}"
                                                                        style="{{ $to_do_finalizado->isCompletedByUser($user->id) ? 'background-color: #CDFEA4' : '' }}">
                                                                        <h3>{{ $to_do_finalizado->titulo }}</h3>
                                                                    </div>
                                                                    <div class="info">
                                                                        <div
                                                                            class="d-flex justify-content-evenly flex-wrap">
                                                                            @if ($to_do_finalizado->project_id)
                                                                                <a class="btn btn-outline-secondary mb-2">
                                                                                    Campaña
                                                                                    {{ $to_do_finalizado->proyecto ? $to_do_finalizado->proyecto->name : 'borrada' }}</a>
                                                                            @endif
                                                                            @if ($to_do_finalizado->client_id)
                                                                                <a class="btn btn-outline-secondary mb-2">
                                                                                    Cliente
                                                                                    {{ $to_do_finalizado->cliente ? $to_do_finalizado->cliente->name : 'borrado' }}</a>
                                                                            @endif
                                                                            @if ($to_do_finalizado->budget_id)
                                                                                <a class="btn btn-outline-secondary mb-2">
                                                                                    Presupuesto
                                                                                    {{ $to_do_finalizado->presupuesto ? $to_do_finalizado->presupuesto->concept : 'borrado' }}</a>
                                                                            @endif
                                                                            @if ($to_do_finalizado->task_id)
                                                                                <a class="btn btn-outline-secondary mb-2">
                                                                                    Tarea
                                                                                    {{ $to_do_finalizado->tarea ? $to_do_finalizado->tarea->title : 'borrada' }}</a>
                                                                            @endif
                                                                            @if ($to_do_finalizado->url)
                                                                                <a class="btn btn-outline-secondary mb-2"
                                                                                    href="{{ $to_do_finalizado->url }}">
                                                                                    Informe de llamadas</a>
                                                                            @endif
                                                                        </div>
                                                                        <div class="participantes d-flex flex-wrap mt-2">
                                                                            <h3 class="m-2">Participantes</h3>
                                                                            @foreach ($to_do_finalizado->TodoUsers as $usuario)
                                                                                <span
                                                                                    class="badge m-2 {{ $usuario->completada ? 'bg-success' : 'bg-secondary' }}">
                                                                                    {{ $usuario->usuarios->name }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                        <h3 class="m-2">Descripcion </h3>
                                                                        <p class="m-2">
                                                                            {{ $to_do_finalizado->descripcion }}</p>
                                                                        <div class="chat mt-4">
                                                                            <div class="chat-container">
                                                                                @foreach ($to_do_finalizado->mensajes as $mensaje)
                                                                                    <div
                                                                                        class="p-3 message {{ $mensaje->admin_user_id == $user->id ? 'mine' : 'theirs' }}">
                                                                                        @if ($mensaje->archivo)
                                                                                            <div class="file-icon">
                                                                                                <a href="{{ asset('storage/' . $mensaje->archivo) }}"
                                                                                                    target="_blank"><i
                                                                                                        class="fa-regular fa-file-lines fa-2x"></i></a>
                                                                                            </div>
                                                                                        @endif
                                                                                        <strong>{{ $mensaje->user->name }}:</strong>
                                                                                        {{ $mensaje->mensaje }}
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                            <form id="mensaje"
                                                                                action="{{ route('message.store') }}"
                                                                                method="post"
                                                                                enctype="multipart/form-data">
                                                                                @csrf
                                                                                <input type="hidden" name="todo_id"
                                                                                    value="{{ $to_do_finalizado->id }}">
                                                                                <input type="hidden" name="admin_user_id"
                                                                                    value="{{ $user->id }}">
                                                                                <div class="input-group my-2">
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="mensaje"
                                                                                        placeholder="Escribe un mensaje..."
                                                                                        disabled>
                                                                                    <label class="input-group-text"
                                                                                        style="background: white; ">
                                                                                        <i class="fa-solid fa-paperclip"
                                                                                            id="file-clip"></i>
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            style="display: none;"
                                                                                            id="file-input" name="archivo"
                                                                                            disabled>
                                                                                        <i class="fa-solid fa-check"
                                                                                            id="file-icon"
                                                                                            style="display: none; color: green;"></i>
                                                                                    </label>
                                                                                    <button id="enviar"
                                                                                        class="btn btn-primary"
                                                                                        type="button" disabled><i
                                                                                            class="fa-regular fa-paper-plane"></i></button>
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
                                                    <div id="calendar" class="p-4"
                                                        style="min-height: 600px; margin-top: 0.75rem; margin-bottom: 0.75rem; overflow-y: auto; border-color:black; border-width: thin; border-radius: 20px;">
                                                        <!-- Aquí se renderizarán las tareas según la vista seleccionada -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=" d-flex justify-content-center">
                                        <button class="btn btn-primary mx-2">Enviar Archivos</button>
                                        <button class="btn btn-secondary mx-2">Correo</button>
                                        <button class="btn btn-primary mx-2" id="iniciarLlamada">Iniciar LLamada</button>
                                        <button class="btn btn-outline-secondary mx-2" onclick="showInforme()">Informe de
                                            llamada</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="llamadaModal" tabindex="-1" aria-labelledby="llamadaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Cambio a modal-lg para mayor ancho -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="todoLlamadaLabel">Iniciar Llamada</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="Llamadaform" action="{{ route('llamada.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="client_id" class="form-label">Cliente</label>
                                    <select class="form-select choices" id="client" name="client_id">
                                        <option value="">Seleccione cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                {{ old('client_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-12 mb-3" id='cliente_kit'>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="phone" class="form-label">Telefono</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                                <input type="hidden" name="admin_user_id" value="{{ $user->id }}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" id="iniciada" class="btn btn-primary">Iniciar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="finllamadaModal" tabindex="-1" aria-labelledby="finllamadaModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="todofinLlamadaLabel">Llamada</h5>
                    </div>
                    <form id="FinLlamadaform" action="{{ route('llamada.end') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="phone" class="form-label">Comentario</label>
                                    <textarea class="form-control" id="comentario" name="comentario" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="informeLlamada" tabindex="-1" aria-labelledby="informeLlamadaModalLabel"
            aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="informeLlamada">Informe de llamada</h5>
                    </div>
                    <form id="informeLlamada" action="{{ route('llamada.informe') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="admin_user_ids" class="form-label">Usuarios</label>
                                    <select class="form-select" id="admin_user_ids2" name="admin_user_ids[]" multiple>
                                        <option value="">Seleccione usuarios</option>
                                        @foreach ($users as $gestor)
                                            @if ($gestor->id !== auth()->id())
                                                <!-- Excluir al usuario logueado -->
                                                <option value="{{ $gestor->id }}"
                                                    {{ in_array($gestor->id, old('admin_user_ids', [])) ? 'selected' : '' }}>
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
                                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control"
                                        value="{{ request('fecha_inicio') }}">
                                </div>
                                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" id="fecha_fin" name="fecha_fin" class="form-control"
                                        value="{{ request('fecha_fin') }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Finalizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
                                            <option value="{{ $tarea->id }}"
                                                {{ old('task_id') == $tarea->id ? 'selected' : '' }}>
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
                                    <label for="client_id" class="form-label">Cliente</label>
                                    <select class="form-select choices" id="client_id" name="client_id">
                                        <option value="">Seleccione cliente</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                {{ old('client_id') == $cliente->id ? 'selected' : '' }}>
                                                {{ $cliente->name }}
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
                                    <label for="budget_id" class="form-label">Presupuesto</label>
                                    <select class="form-select choices" id="budget_id" name="budget_id">
                                        <option value="">Seleccione presupuesto</option>
                                        @foreach ($budgets as $budget)
                                            <option value="{{ $budget->id }}"
                                                {{ old('budget_id') == $budget->id ? 'selected' : '' }}>
                                                {{ $budget->concept }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('budget_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="project_id" class="form-label">Campaña</label>
                                    <select class="choices form-select" id="project_id" name="project_id">
                                        <option value="">Seleccione campaña</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}"
                                                {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                {{ $project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="admin_user_ids" class="form-label">Usuarios</label>
                                    <select class="form-select" id="admin_user_ids" name="admin_user_ids[]" multiple>
                                        <option value="">Seleccione usuarios</option>
                                        @foreach ($users as $gestor)
                                            @if ($gestor->id !== auth()->id())
                                                <!-- Excluir al usuario logueado -->
                                                <option value="{{ $gestor->id }}"
                                                    {{ in_array($gestor->id, old('admin_user_ids', [])) ? 'selected' : '' }}>
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
                                    <input type="datetime-local" class="form-control" id="start" name="start"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end" class="form-label">Fin</label>
                                    <input type="datetime-local" class="form-control" id="end" name="end">
                                </div>
                                <div class="col-md-6 mb-3 d-flex align-items-center justify-content-center">
                                    <input type="color" style="padding: 0.4rem" class="form-control form-control-color"
                                        id="color1" name="color">
                                    <label for="color1" class="form-label ml-2">Color</label>
                                </div>
                                <div class=" col-md-6 mb-3 d-flex align-items-center justify-content-center">
                                    <input type="checkbox" style="height:25px; width:25px; " class="form-check-input"
                                        id="agendar" name="agendar">
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
        <!-- Modal para mostrar ingresos y gastos no clasificados -->
        <div class="modal fade" id="unclassifiedModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="unclassifiedModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unclassifiedModalLabel">Ingresos y Gastos No Clasificados</h5>
                    </div>
                    <div class="modal-body">
                        @if (!empty($unclassifiedIncomes) && count($unclassifiedIncomes) > 0)
                            <strong>Ingresos No Clasificados</strong>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Empresa</th>
                                            <th>Banco</th>
                                            <th>IBAN</th>
                                            <th>Cantidad</th>
                                            <th>Fecha</th>
                                            <th>Mensaje</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($unclassifiedIncomes as $income)
                                            <tr>
                                                <td>{{ $income->company_name }}</td>
                                                <td>{{ $income->bank }}</td>
                                                <td>{{ $income->iban }}</td>
                                                <td>{{ number_format($income->amount, 2) }}€</td>
                                                <td>{{ $income->received_date ? \Carbon\Carbon::parse($income->received_date)->format('d/m/Y') : 'N/A' }}
                                                </td>
                                                <td>{{ $income->message }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm toggle-details"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#details-{{ $income->id }}"
                                                        aria-expanded="false"
                                                        aria-controls="details-{{ $income->id }}">
                                                        <span class="toggle-text">Ver Detalles</span>
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="7">
                                                    <div id="details-{{ $income->id }}"
                                                        class="collapse details-content p-3 border rounded bg-light">
                                                        <div class="row g-3">
                                                            {{-- Información del Movimiento --}}
                                                            <div class="col-md-4">
                                                                <h6 class="mb-3 fw-bold">Información del Movimiento</h6>
                                                                <div class="p-3 bg-white border rounded shadow-sm">
                                                                    <p><strong>Empresa:</strong>
                                                                        {{ $income->company_name }}</p>
                                                                    <p><strong>Banco:</strong> {{ $income->bank }}</p>
                                                                    <p><strong>IBAN:</strong> {{ $income->iban }}</p>
                                                                    <p><strong>Cantidad:</strong>
                                                                        {{ number_format($income->amount, 2) }}€</p>
                                                                    <p><strong>Fecha:</strong>
                                                                        {{ $income->received_date ? \Carbon\Carbon::parse($income->received_date)->format('d/m/Y') : 'N/A' }}
                                                                    </p>
                                                                    <p><strong>Mensaje:</strong> {{ $income->message }}
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            {{-- Coincidencias --}}
                                                            <div class="col-md-8">
                                                                <h6 class="mb-3 fw-bold">Elige una coincidencia:</h6>
                                                                @if ($income->relaciones && count($income->relaciones) > 0)
                                                                    <div class="table-responsive"
                                                                        style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.5rem; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                                                                        <table id="table-ingresos"
                                                                            class="table table-sm table-hover mb-0 w-100">
                                                                            <thead class="table-light sticky-top"
                                                                                style="top: 0; z-index: 1;">
                                                                                <tr>
                                                                                    <th>Aceptar</th>
                                                                                    <th>ID</th>
                                                                                    <th>Tipo</th>
                                                                                    <th>Referencia</th>
                                                                                    <th>Importe</th>
                                                                                    <th>Fecha</th>
                                                                                    <th>Ver</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @foreach ($income->relaciones as $relacion)
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="radio"
                                                                                                name="aceptar_{{ $income->id }}"
                                                                                                value="{{ $relacion['modelo']->id }}"
                                                                                                data-tabla="{{ $relacion['tabla'] }}"
                                                                                                onchange="toggleAceptarButton({{ $income->id }})">
                                                                                        </td>
                                                                                        <td>{{ $relacion['modelo']->id }}
                                                                                        </td>
                                                                                        <td>{{ $relacion['tabla'] }}</td>
                                                                                        <td>{{ $relacion['modelo']->reference ?? ($relacion['modelo']->invoice_number ?? 'N/A') }}
                                                                                        </td>
                                                                                        <td>{{ $relacion['modelo']->amount ?? ($relacion['modelo']->quantity ?? ($relacion['modelo']->total ?? 'N/A')) }}
                                                                                        </td>
                                                                                        <td>{{ $relacion['modelo']->date ? \Carbon\Carbon::parse($relacion['modelo']->date)->format('d/m/Y') : ($relacion['modelo']->paid_date ? \Carbon\Carbon::parse($relacion['modelo']->paid_date)->format('d/m/Y') : ($relacion['modelo']->creation_date ? \Carbon\Carbon::parse($relacion['modelo']->creation_date)->format('d/m/Y') : 'N/A')) }}
                                                                                        </td>
                                                                                        <td>
                                                                                            <a href="{{ route('tesoreria.contabilizar-ia.showGenerico', [
                                                                                                'tabla' => $relacion['tabla'],
                                                                                                'id' => $relacion['modelo']->id,
                                                                                            ]) }}"
                                                                                                class="btn btn-primary btn-sm">Ver</a>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="col-12 text-end">
                                                                <button type="button" class="btn btn-success me-2"
                                                                    id="btn-aceptar-{{ $income->id }}"
                                                                    onclick="aceptarRelacion({{ $income->id }}, 'ingreso', this)"
                                                                    disabled>Aceptar
                                                                    coincidencia</button>
                                                                <button type="button" class="btn btn-primary"
                                                                    onclick="crearIngreso(this)"
                                                                    data-company="{{ $income->company_name }}"
                                                                    data-bank="{{ $income->bank }}"
                                                                    data-iban="{{ $income->iban }}"
                                                                    data-amount="{{ $income->amount }}"
                                                                    data-date="{{ $income->received_date }}"
                                                                    data-message="{{ $income->message }}"
                                                                    data-unclassified-id="{{ $income->id }}">
                                                                    <i class="fas fa-plus-circle me-2"></i>Rechazar
                                                                    relaciones y crear ingreso.
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-warning dropdown-toggle d-flex align-items-center gap-2" type="button"
                                                                id="dropdownMenuButton-{{ $income->id }}"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-file-invoice"></i>
                                                                Rechazar y Asociar Facturas
                                                            </button>
                                                            <div class="dropdown-menu p-4 shadow-lg"
                                                            aria-labelledby="dropdownMenuButton-{{ $income->id }}"
                                                            style="width: 800px; max-width: 100vw; border-radius: 8px;">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover table-sm w-100">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <td colspan="6" class="text-end border-0">
                                                                                <div class="d-flex justify-content-end align-items-center gap-3">
                                                                                    <strong id="total-{{ $income->id }}"
                                                                                        class="fs-5 mb-0"
                                                                                        style="color: red;">
                                                                                        Total: 0.00€
                                                                                    </strong>
                                                                                    <button type="button"
                                                                                    class="btn btn-primary position-sticky top-0 end-0 m-4 shadow-lg"
                                                                                    style="z-index: 10;"    id="relate-button-{{ $income->id }}"
                                                                                    onclick="relacionarFacturasYCrearIngreso(this)"
                                                                                    data-company="{{ $income->company_name }}"
                                                                                    data-bank="{{ $income->bank }}"
                                                                                    data-iban="{{ $income->iban }}"
                                                                                    data-amount="{{ $income->amount }}"
                                                                                    data-date="{{ $income->received_date }}"
                                                                                    data-message="{{ $income->message }}"
                                                                                    data-unclassified-id="{{ $income->id }}"
                                                                                    data-invoice-id="{{ $income->invoice_id }}"
                                                                                    data-tipo="ingreso"
                                                                                    data-tabla="ingreso"
                                                                                    style="z-index: 1050;"
                                                                                    disabled>
                                                                                    <i class="fas fa-link me-2"></i>
                                                                                    Relacionar Facturas y Crear Ingreso
                                                                                </button>

                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="text-center" style="width: 50px;">✓</th>
                                                                            <th>Referencia</th>
                                                                            <th>Concepto</th>
                                                                            <th>Fecha</th>
                                                                            <th class="text-end">Importe</th>
                                                                            <th>Pendiente de pago</th>
                                                                            <th class="text-end" style="width: 150px;">Importe Asignado</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($invoices as $invoice)
                                                                            <tr>
                                                                                <td class="text-center">
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox"
                                                                                            class="form-check-input invoice-checkbox"
                                                                                            data-amount="{{ $invoice->amount }}"
                                                                                            data-income-id="{{ $income->id }}"
                                                                                            onchange="toggleAmountInput(this)"
                                                                                            data-invoice-id="{{ $invoice->id }}">
                                                                                    </div>
                                                                                </td>
                                                                                <td>{{ $invoice->reference }}</td>
                                                                                <td>{{ $invoice->concept }}</td>
                                                                                <td>{{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                                <td class="text-end">{{ number_format($invoice->total, 2) }}€</td>
                                                                                <td>

                                                                                    @if(count($invoice['ingresos']) > 0)
                                                                                        @php
                                                                                            $totalIngresos = 0;
                                                                                        @endphp
                                                                                        @foreach($invoice['ingresos'] as $ingreso)
                                                                                            @php
                                                                                                $totalIngresos += $ingreso->quantity;
                                                                                            @endphp
                                                                                        @endforeach
                                                                                        <span style="color: red; font-size: 14px;">{{ $invoice->total - $totalIngresos }}</span>
                                                                                        @php
                                                                                            $totalIngresos = 0;
                                                                                        @endphp
                                                                                    @else
                                                                                        <span><i class="fas fa-dollar-sign text-danger"></i></span>
                                                                                    @endif

                                                                                </td>
                                                                                <td style="width: 200px;">
                                                                                    <input type="number"
                                                                                        class="form-control form-control-lg amount-input"
                                                                                        id="amount-input-{{ $income->id }}-{{ $invoice->id }}"
                                                                                        style="display: none; width: 100%; height: 50px; font-size: 18px; padding: 10px;"
                                                                                        min="0"
                                                                                        max="{{ $invoice->total }}"
                                                                                        step="0.01"
                                                                                        data-original-amount="{{ $income->amount }}"
                                                                                        oninput="updateTotal({{ $income->id }}, {{ $income->amount }})">
                                                                                </td>
                                                                            </tr>
                                                                            @if(count($invoice['ingresos']) > 0)

                                                                                @foreach($invoice['ingresos'] as $ingreso)
                                                                                    <tr>
                                                                                        <td style="color: green; font-size: 12px;" colspan="3" class="text-end border-0">
                                                                                            {{ $ingreso->title }}
                                                                                        </td>
                                                                                        <td style="color: green; font-size: 12px;" class="text-end border-0">
                                                                                            {{ $ingreso->date }}
                                                                                        </td>
                                                                                        <td style="color: green; font-size: 12px;" class="text-end border-0">
                                                                                            {{ $ingreso->quantity }}
                                                                                        </td>
                                                                                    </tr>

                                                                                @endforeach
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="alert alert-info mb-3">
                                                        <strong>No se ha encontrado una coincidencia.</strong>
                                                        <p class="mb-0 mt-2">Crea un movimiento para asociar la
                                                            transacción:</p>
                                                    </div>

                                                    <div class="d-flex gap-3 mb-4">
                                                        <button type="button" class="btn btn-primary"
                                                            onclick="crearIngreso(this)"
                                                            data-company="{{ $income->company_name }}"
                                                            data-bank="{{ $income->bank }}"
                                                            data-iban="{{ $income->iban }}"
                                                            data-amount="{{ $income->amount }}"
                                                            data-date="{{ $income->received_date }}"
                                                            data-message="{{ $income->message }}"
                                                            data-unclassified-id="{{ $income->id }}">
                                                            <i class="fas fa-plus-circle me-2"></i>Crear Ingreso
                                                        </button>
                                                    </div>

                                                    <div class="card mb-4">
                                                        <div class="card-body">
                                                            <h6 class="card-title mb-3">Configuración de Transferencia</h6>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <label for="origen-{{ $income->id }}"
                                                                        class="form-label">Banco Origen</label>
                                                                    <select class="form-select"
                                                                        id="origen-{{ $income->id }}" name="origen">
                                                                        <option value="">Seleccione banco origen
                                                                        </option>
                                                                        @foreach ($banks as $bank)
                                                                            <option value="{{ $bank->id }}">
                                                                                {{ $bank->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label for="destino-{{ $income->id }}"
                                                                        class="form-label">Banco Destino</label>
                                                                    <select class="form-select"
                                                                        id="destino-{{ $income->id }}" name="destino">
                                                                        <option value="">Seleccione banco destino
                                                                        </option>
                                                                        @foreach ($banks as $bank)
                                                                            <option value="{{ $bank->id }}">
                                                                                {{ $bank->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="button" class="btn btn-success"
                                                        onclick="crearTransferencia(this)"
                                                        data-company="{{ $income->company_name }}"
                                                        data-bank="{{ $income->bank }}"
                                                        data-iban="{{ $income->iban }}"
                                                        data-amount="{{ $income->amount }}"
                                                        data-date="{{ $income->received_date }}"
                                                        data-message="{{ $income->message }}"
                                                        data-unclassified-id="{{ $income->id }}" data-tabla="ingreso">
                                                        <i class="fas fa-exchange-alt me-2"></i>Crear Transferencia
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-warning dropdown-toggle d-flex align-items-center gap-2" type="button"
                                                            id="dropdownMenuButton-{{ $income->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-file-invoice"></i>
                                                            Rechazar y Asociar Facturas
                                                        </button>
                                                        <div class="dropdown-menu p-4 shadow-lg"
                                                            aria-labelledby="dropdownMenuButton-{{ $income->id }}"
                                                            style="width: 800px; max-width: 100vw; border-radius: 8px;">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover table-sm w-100">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <td colspan="6" class="text-end border-0">
                                                                                <div class="d-flex justify-content-end align-items-center gap-3">
                                                                                    <strong id="total-{{ $income->id }}"
                                                                                        class="fs-5 mb-0"
                                                                                        style="color: red;">
                                                                                        Total: 0.00€
                                                                                    </strong>
                                                                                    <button type="button"
                                                                                    class="btn btn-primary position-fixed top-0 end-0 m-4 shadow-lg"
                                                                                    id="relate-button-{{ $income->id }}"
                                                                                    onclick="relacionarFacturasYCrearIngreso(this)"
                                                                                    data-company="{{ $income->company_name }}"
                                                                                    data-bank="{{ $income->bank }}"
                                                                                    data-iban="{{ $income->iban }}"
                                                                                    data-amount="{{ $income->amount }}"
                                                                                    data-date="{{ $income->received_date }}"
                                                                                    data-message="{{ $income->message }}"
                                                                                    data-unclassified-id="{{ $income->id }}"
                                                                                    data-invoice-id="{{ $income->invoice_id }}"
                                                                                    data-tipo="ingreso"
                                                                                    data-tabla="ingreso"
                                                                                    style="z-index: 1050;"
                                                                                    disabled>
                                                                                        <i class="fas fa-link me-2"></i>
                                                                                        Relacionar Facturas y Crear Ingreso
                                                                                    </button>

                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th class="text-center" style="width: 50px;">✓</th>
                                                                            <th>Referencia</th>
                                                                            <th>Concepto</th>
                                                                            <th>Fecha</th>
                                                                            <th class="text-end">Importe</th>
                                                                            <th>Pendiente de pago</th>
                                                                            <th class="text-end" style="width: 150px;">Importe Asignado</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($invoices as $invoice)
                                                                            <tr>
                                                                                <td class="text-center">
                                                                                    <div class="form-check">
                                                                                        <input type="checkbox"
                                                                                            class="form-check-input invoice-checkbox"
                                                                                            data-amount="{{ $invoice->amount }}"
                                                                                            data-income-id="{{ $income->id }}"
                                                                                            onchange="toggleAmountInput(this)"
                                                                                            data-invoice-id="{{ $invoice->id }}">
                                                                                    </div>
                                                                                </td>
                                                                                <td>{{ $invoice->reference }}</td>
                                                                                <td>{{ $invoice->concept }}</td>
                                                                                <td>{{ $invoice->created_at ? \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') : 'N/A' }}</td>
                                                                                <td class="text-end">{{ number_format($invoice->total, 2) }}€</td>
                                                                                <td>

                                                                                    @if(count($invoice['ingresos']) > 0)
                                                                                        @php
                                                                                            $totalIngresos = 0;
                                                                                        @endphp
                                                                                        @foreach($invoice['ingresos'] as $ingreso)
                                                                                            @php
                                                                                                $totalIngresos += $ingreso->quantity;
                                                                                            @endphp
                                                                                        @endforeach
                                                                                        <span style="color: red; font-size: 14px;">{{ $invoice->total - $totalIngresos }}</span>
                                                                                        @php
                                                                                            $totalIngresos = 0;
                                                                                        @endphp
                                                                                    @else
                                                                                        <span><i class="fas fa-dollar-sign text-danger"></i></span>
                                                                                    @endif

                                                                                </td>
                                                                                <td style="width: 200px;">
                                                                                    <input type="number"
                                                                                        class="form-control form-control-lg amount-input"
                                                                                        id="amount-input-{{ $income->id }}-{{ $invoice->id }}"
                                                                                        style="display: none; width: 100%; height: 50px; font-size: 18px; padding: 10px;"
                                                                                        min="0"
                                                                                        max="{{ $invoice->total }}"
                                                                                        step="0.01"
                                                                                        data-original-amount="{{ $income->amount }}"
                                                                                        oninput="updateTotal({{ $income->id }}, {{ $income->amount }})">
                                                                                </td>
                                                                            </tr>
                                                                            @if(count($invoice['ingresos']) > 0)

                                                                                @foreach($invoice['ingresos'] as $ingreso)
                                                                                    <tr>
                                                                                        <td style="color: green; font-size: 12px;" colspan="3" class="text-end border-0">
                                                                                            {{ $ingreso->title }}
                                                                                        </td>
                                                                                        <td style="color: green; font-size: 12px;" class="text-end border-0">
                                                                                            {{ $ingreso->date }}
                                                                                        </td>
                                                                                        <td style="color: green; font-size: 12px;" class="text-end border-0">
                                                                                            {{ $ingreso->quantity }}
                                                                                        </td>
                                                                                    </tr>

                                                                                @endforeach
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                        @endif
                                        </td>
                                        </tr>
                        @endforeach
                        </tbody>
                        </table>
                    </div>
                    @endif

                    @if (!empty($unclassifiedExpenses) && count($unclassifiedExpenses) > 0)
                        <strong>Gastos No Clasificados</strong>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Empresa</th>
                                        <th>Banco</th>
                                        <th>IBAN</th>
                                        <th>Cantidad</th>
                                        <th>Fecha</th>
                                        <th>Mensaje</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unclassifiedExpenses as $expense)
                                        <tr>
                                            <td>{{ $expense->company_name }}</td>
                                            <td>{{ $expense->bank }}</td>
                                            <td>{{ $expense->iban }}</td>
                                            <td>{{ number_format($expense->amount, 2) }}€</td>
                                            <td>{{ $expense->received_date ? \Carbon\Carbon::parse($expense->received_date)->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $expense->message }}</td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm toggle-details"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#details-{{ $expense->id }}" aria-expanded="false"
                                                    aria-controls="details-{{ $expense->id }}">
                                                    <span class="toggle-text">Ver Detalles</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="7">
                                                <div id="details-{{ $expense->id }}"
                                                    class="collapse details-content p-3 border rounded bg-light">
                                                    <div class="row g-3">
                                                        {{-- Información del Movimiento --}}
                                                        <div class="col-md-4">
                                                            <h6 class="mb-3 fw-bold">Información del Movimiento</h6>
                                                            <div class="p-3 bg-white border rounded shadow-sm">
                                                                <p><strong>Empresa:</strong> {{ $expense->company_name }}
                                                                </p>
                                                                <p><strong>Banco:</strong> {{ $expense->bank }}</p>
                                                                <p><strong>IBAN:</strong> {{ $expense->iban }}</p>
                                                                <p><strong>Cantidad:</strong>
                                                                    {{ number_format($expense->amount, 2) }}€</p>
                                                                <p><strong>Fecha:</strong>
                                                                    {{ $expense->received_date ? \Carbon\Carbon::parse($expense->received_date)->format('d/m/Y') : 'N/A' }}
                                                                </p>
                                                                <p><strong>Mensaje:</strong> {{ $expense->message }}</p>
                                                            </div>
                                                        </div>

                                                        {{-- Coincidencias --}}
                                                        <div class="col-md-8">
                                                            <h6 class="mb-3 fw-bold">Elige una coincidencia:</h6>
                                                            @if ($expense->relaciones && count($expense->relaciones) > 0)
                                                                <div class="table-responsive"
                                                                    style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.5rem; box-shadow: 0 0 5px rgba(0,0,0,0.05);">
                                                                    <table id="table-gastos"
                                                                        class="table table-sm table-hover mb-0">
                                                                        <thead class="table-light sticky-top"
                                                                            style="top: 0; z-index: 1;">
                                                                            <tr>
                                                                                <th>Aceptar</th>
                                                                                <th>ID</th>
                                                                                <th>Tipo</th>
                                                                                <th>Referencia</th>
                                                                                <th>Importe</th>
                                                                                <th>Fecha</th>
                                                                                <th>Ver</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($expense->relaciones as $relacion)
                                                                                <tr>
                                                                                    <td>
                                                                                        <input type="radio"
                                                                                            name="aceptar_{{ $expense->id }}"
                                                                                            value="{{ $relacion['modelo']->id }}"
                                                                                            data-tabla="{{ $relacion['tabla'] }}">
                                                                                    </td>
                                                                                    <td>{{ $relacion['modelo']->id }}</td>
                                                                                    <td>{{ $relacion['tabla'] }}</td>
                                                                                    <td>{{ $relacion['modelo']->reference ?? ($relacion['modelo']->invoice_number ?? 'N/A') }}
                                                                                    </td>
                                                                                    <td>{{ $relacion['modelo']->amount ?? ($relacion['modelo']->quantity ?? ($relacion['modelo']->total ?? 'N/A')) }}
                                                                                    </td>
                                                                                    <td>{{ $relacion['modelo']->date ? \Carbon\Carbon::parse($relacion['modelo']->date)->format('d/m/Y') : ($relacion['modelo']->paid_date ? \Carbon\Carbon::parse($relacion['modelo']->paid_date)->format('d/m/Y') : ($relacion['modelo']->creation_date ? \Carbon\Carbon::parse($relacion['modelo']->creation_date)->format('d/m/Y') : 'N/A')) }}
                                                                                    </td>
                                                                                    <td>
                                                                                        <a href="{{ route('tesoreria.contabilizar-ia.showGenerico', [
                                                                                            'tabla' => $relacion['tabla'],
                                                                                            'id' => $relacion['modelo']->id,
                                                                                        ]) }}"
                                                                                            class="btn btn-primary btn-sm">Ver</a>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <div class="row mt-3">
                                                                    <div class="col-12 text-end">
                                                                        <button type="button"
                                                                            class="btn btn-success me-2"
                                                                            onclick="aceptarRelacion({{ $expense->id }}, 'gasto', this)">
                                                                            <i class="fas fa-check-circle me-2"></i>Aceptar
                                                                            coincidencia
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-primary me-2"
                                                                            onclick="crearMovimiento(this)"
                                                                            data-company="{{ $expense->company_name }}"
                                                                            data-bank="{{ $expense->bank }}"
                                                                            data-iban="{{ $expense->iban }}"
                                                                            data-amount="{{ $expense->amount }}"
                                                                            data-date="{{ $expense->received_date }}"
                                                                            data-message="{{ $expense->message }}"
                                                                            data-unclassified-id="{{ $expense->id }}">
                                                                            <i class="fas fa-plus-circle me-2"></i>Rechazar
                                                                            relaciones y crear gasto
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="alert alert-info mb-3">
                                                                    <strong>No se ha encontrado una coincidencia.</strong>
                                                                    <p class="mb-0 mt-2">Crea un movimiento para asociar la
                                                                        transacción:</p>
                                                                </div>

                                                                <div class="d-flex gap-3 mb-4">
                                                                    <button type="button" class="btn btn-primary h-100"
                                                                        onclick="crearMovimiento(this)"
                                                                        data-company="{{ $expense->company_name }}"
                                                                        data-bank="{{ $expense->bank }}"
                                                                        data-iban="{{ $expense->iban }}"
                                                                        data-amount="{{ $expense->amount }}"
                                                                        data-date="{{ $expense->received_date }}"
                                                                        data-message="{{ $expense->message }}"
                                                                        data-unclassified-id="{{ $expense->id }}">
                                                                        <i class="fas fa-plus-circle me-2"></i>Crear Gasto
                                                                    </button>
                                                                </div>

                                                                <div class="card mb-4">
                                                                    <div class="card-body">
                                                                        <h6 class="card-title mb-3">Configuración de
                                                                            Transferencia</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-6">
                                                                                <label for="origen-{{ $expense->id }}"
                                                                                    class="form-label">Banco Origen</label>
                                                                                <select class="form-select"
                                                                                    id="origen-{{ $expense->id }}"
                                                                                    name="origen">
                                                                                    <option value="">Seleccione banco
                                                                                        origen
                                                                                    </option>
                                                                                    @foreach ($banks as $bank)
                                                                                        <option
                                                                                            value="{{ $bank->id }}">
                                                                                            {{ $bank->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <label for="destino-{{ $expense->id }}"
                                                                                    class="form-label">Banco
                                                                                    Destino</label>
                                                                                <select class="form-select"
                                                                                    id="destino-{{ $expense->id }}"
                                                                                    name="destino">
                                                                                    <option value="">Seleccione banco
                                                                                        destino
                                                                                    </option>
                                                                                    @foreach ($banks as $bank)
                                                                                        <option
                                                                                            value="{{ $bank->id }}">
                                                                                            {{ $bank->name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <button type="button" class="btn btn-success"
                                                                    onclick="crearTransferencia(this)"
                                                                    data-company="{{ $expense->company_name }}"
                                                                    data-bank="{{ $expense->bank }}"
                                                                    data-iban="{{ $expense->iban }}"
                                                                    data-amount="{{ $expense->amount }}"
                                                                    data-date="{{ $expense->received_date }}"
                                                                    data-message="{{ $expense->message }}"
                                                                    data-unclassified-id="{{ $expense->id }}"
                                                                    data-tabla="gasto">
                                                                    <i class="fas fa-exchange-alt me-2"></i>Crear
                                                                    Transferencia
                                                                </button>
                                                            @endif
                                                        </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('scripts')
    @include('partials.toast')
    <script src="{{ asset('assets/vendors/choices.js/choices.min.js') }}"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>

    <script>
        // Mostrar modal de ingresos/gastos no clasificados si existen
        document.addEventListener('DOMContentLoaded', function() {
            @if (
                (is_countable($unclassifiedIncomes ?? []) && count($unclassifiedIncomes ?? []) > 0) ||
                    (is_countable($unclassifiedExpenses ?? []) && count($unclassifiedExpenses ?? []) > 0))
                var unclassifiedModal = new bootstrap.Modal(document.getElementById('unclassifiedModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                unclassifiedModal.show();
            @endif
        });


    </script>

    <script>
        var enRutaEspecifica = true;
        document.addEventListener('DOMContentLoaded', function() {
            var multipleCancelButton = new Choices('#admin_user_ids', {
                removeItemButton: true, // Permite a los usuarios eliminar una selección
                searchEnabled: true, // Habilita la búsqueda dentro del selector
                paste: false // Deshabilita la capacidad de pegar texto en el campo
            });
            var multipleCancelButton = new Choices('#admin_user_ids2', {
                removeItemButton: true, // Permite a los usuarios eliminar una selección
                searchEnabled: true, // Habilita la búsqueda dentro del selector
                paste: false // Deshabilita la capacidad de pegar texto en el campo
            });
        });
    </script>
    <script>
        let timerState = '{{ $jornadaActiva ? 'running' : 'stopped' }}'
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
                } else {
                    if (workedHours < 8) {
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

        function endLlamada() {
            fetch('/dashboard/llamadafin', {
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
                        document.getElementById('endllamadaBtn').style.display = 'none';
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.mensaje, // Aquí se muestra el mensaje del JSON
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
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

            if ('{{ $llamadaActiva }}') {
                document.getElementById('endllamadaBtn').style.display = 'block';
            } else {
                document.getElementById('endllamadaBtn').style.display = 'none';
            }

        });
    </script>
    <script>
        $('#todoboton').click(function(e) {
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('#todoform').submit(); // Esto envía el formulario.
        });

        var events = @json($events);
        document.addEventListener('DOMContentLoaded', function() {

            var ordenes = document.getElementById('ordenes');
            var numeroOrdenes = @json($user->ordenes()->count());
            var date = new Date();
            if (date.getDate() >= 20 && date.getDate() <= 24 && numeroOrdenes > 0) {
                ordenes.classList.add('pulseCard');
            }

            var calendarEl = document.getElementById('calendar');
            var tooltip = document.getElementById('tooltip');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'listWeek',
                locale: 'es',
                navLinks: true,
                nowIndicator: true,
                businessHours: [{
                        daysOfWeek: [1],
                        startTime: '08:00',
                        endTime: '15:00'
                    },
                    {
                        daysOfWeek: [2],
                        startTime: '08:00',
                        endTime: '15:00'
                    },
                    {
                        daysOfWeek: [3],
                        startTime: '08:00',
                        endTime: '15:00'
                    },
                    {
                        daysOfWeek: [4],
                        startTime: '08:00',
                        endTime: '15:00'
                    },
                    {
                        daysOfWeek: [5],
                        startTime: '08:00',
                        endTime: '15:00'
                    }
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
                    var ruta = clientId ? `{{ route('clientes.show', ':id') }}`.replace(':id',
                        clientId) : '#';
                    var ruta2 = budgetId ? `{{ route('presupuesto.edit', ':id1') }}`.replace(':id1',
                        budgetId) : '#';
                    var ruta3 = projectId ? `{{ route('campania.show', ':id2') }}`.replace(':id2',
                        projectId) : '#';

                    // Construye el contenido del tooltip condicionalmente
                    var tooltipContent = '<div style="text-align: left;">' +
                        '<h5>' + event.title + '</h5>';

                    if (clienteName) {
                        tooltipContent += '<a href="' + ruta + '"><p><strong>Cliente:</strong> ' +
                            clienteName + '</p></a>';
                    }

                    if (presupuestoRef || presupuestoConp) {
                        tooltipContent += '<a href="' + ruta2 + '"><p><strong>Presupuesto:</strong> ' +
                            (presupuestoRef ? 'Ref:' + presupuestoRef + '<br>' : '') +
                            (presupuestoConp ? 'Concepto: ' + presupuestoConp : '') +
                            '</p></a>';
                    }

                    if (proyectoName) {
                        tooltipContent += '<a href="' + ruta3 + '"><p><strong>Campaña:</strong> ' +
                            proyectoName + '</p></a>';
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
                        tooltipElement.style.backgroundColor = event.extendedProps.color ||
                            '#000'; // Usa el color del evento o negro por defecto
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

        function showLlamadaModal() {
            var llamadaModal = new bootstrap.Modal(document.getElementById('llamadaModal'));
            llamadaModal.show();
        }

        function showInforme() {
            var informeModal = new bootstrap.Modal(document.getElementById('informeLlamada'));
            informeModal.show();
        }

        function closeLlamadaModal() {
            $('#llamadaModal').modal('hide');
        }


        function showFinLlamadaModal() {
            var llamadaModal = new bootstrap.Modal(document.getElementById('finllamadaModal'));
            llamadaModal.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalLlamada = document.getElementById('iniciarLlamada');
            const iniciarLlamada = document.getElementById('iniciada');
            const cliente_kit = document.getElementById('cliente_kit');
            var loader = document.getElementById('loadingOverlay');

            modalLlamada.addEventListener('click', function() {
                loader.style.display = "block";
                fetch('/dashboard/getkits', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            let optionsHtml = `<label for="kit_id" class="form-label">Cliente de kit</label>
                                    <select class="form-select choices" id="kit_id" name="kit_id">
                                        <option value="">Seleccione cliente</option>`;

                            data.kits.forEach(kit => {
                                const servicioNombre = kit.servicios ? kit.servicios.name :
                                    'Servicio no especificado';
                                optionsHtml += `<option value="${kit.id}">
                                            ${kit.cliente} - ${servicioNombre}
                                        </option>`;
                            });

                            optionsHtml += `</select>`;
                            cliente_kit.innerHTML = optionsHtml;
                            new Choices('#kit_id', {
                                removeItemButton: true, // Permite a los usuarios eliminar una selección
                                searchEnabled: true, // Habilita la búsqueda dentro del selector
                                paste: false // Deshabilita la capacidad de pegar texto en el campo
                            });
                            loader.style.display = "none";
                            showLlamadaModal();
                        } else {
                            loader.style.display = "none";
                            cliente_kit.innerHTML =
                                `<span>Error: No se pudieron cargar los datos.</span>`;
                        }
                    })
                    .catch(error => {
                        loader.style.display = "none";
                        console.error('Error:', error);
                        cliente_kit.innerHTML = `<span>Error: ${error.message}</span>`;
                    });
            });

            iniciarLlamada.addEventListener('click', function() {
                const formllamada = document.getElementById('Llamadaform');
                const formData = new FormData(formllamada);

                fetch(formllamada.action, {
                        method: formllamada.method,
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la solicitud');
                        }
                        return response.json(); // o .text(), dependiendo de la respuesta esperada
                    })
                    .then(data => {
                        if (data.success) {
                            closeLlamadaModal();
                            showFinLlamadaModal();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al generar la llamada.',
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Hubo un problema con el envío:', error);
                        alert('Ocurrió un error al enviar el formulario.');
                    });

            });

        });


        document.addEventListener('DOMContentLoaded', function() {
            const progressCircles = document.querySelectorAll('.progress-circle');

            progressCircles.forEach(circle => {
                const percentage = circle.getAttribute('data-percentage');
                circle.style.setProperty('--percentage', percentage);

                let progressColor;

                if (percentage < 50) {
                    progressColor = '#ff0000'; // Rojo
                } else if (percentage < 75) {
                    progressColor = '#ffa500'; // Naranja
                } else {
                    progressColor = '#4caf50'; // Verde
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
                if (!todoId) return;

                fetch(`mark-as-read/${todoId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            let unreadCounter = document.querySelector(`[data-todo-id="${todoId}"] .pulse`);
                            if (unreadCounter) {
                                unreadCounter.textContent = '';
                                unreadCounter.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error al marcar mensajes como leídos:', error));
            }


            // Manejo de archivos
            document.querySelectorAll('#file-input').forEach(function(inputElement) {
                inputElement.addEventListener('change', function() {
                    console.log('File input changed'); // Verifica que el evento se activa
                    const fileIcon = this.closest('.input-group-text').querySelector('#file-icon');
                    const fileClip = this.closest('.input-group-text').querySelector('#file-clip');

                    if (this.files.length > 0) {
                        fileIcon.style.display = 'inline-block';
                        fileClip.style.display = 'none';
                    } else {
                        fileIcon.style.display = 'none';
                        fileClip.style.display = 'inline-block';
                    }
                });
            });
        });


        // Completar tarea
        function completeTask(event, todoId) {
            event.stopPropagation();
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

                        const completeButton = document.getElementById(`complete-button-${todoId}`);
                        if (completeButton) {
                            completeButton.style.display = 'none';
                        }
                        Toast.fire({
                            icon: "success",
                            title: "Tarea completada con éxito!"
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Error al completar la tarea!"
                        });
                    }
                }).catch(error => console.error('Error:', error));
        }

        // Finalizar tarea
        function finishTask(event, todoId) {
            event.stopPropagation();
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
                            card.style.display = 'none';
                        }
                        Toast.fire({
                            icon: "success",
                            title: "Tarea finalizada con éxito!"
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Error al finalizar la tarea!"
                        });
                    }
                }).catch(error => console.error('Error:', error));
        }

        // Enviar mensaje
        document.querySelectorAll('#enviar').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                this.closest('form').submit();
            });
        });

        function updateUnreadMessagesCount(todoId) {
            fetch(`/todos/unread-messages-count/${todoId}`, {
                    method: 'POST', // Cambiamos a POST
                    headers: {
                        'Content-Type': 'application/json', // Indicamos que enviamos JSON
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
                contentType: 'application/json', // Especifica el tipo de contenido
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                data: JSON.stringify({}),
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
                        const messageClass = message.admin_user_id == {{ auth()->id() }} ? 'mine' :
                            'theirs';

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
                    }, 5000); // Polling cada 5 segundos para cada to-do
                @endforeach
            @else
                console.log('No hay to-dos activos.');
            @endif
        }

        $(document).ready(function() {
            startPolling();
        });

        function showTodoModal() {
            var todoModal = new bootstrap.Modal(document.getElementById('todoModal'));
            todoModal.show();
        }
        document.addEventListener('DOMContentLoaded', function() {
            const taskSelect = document.getElementById('task_id');
            const clientSelect = document.getElementById('client_id');
            const budgetSelect = document.getElementById('budget_id');
            const projectSelect = document.getElementById('project_id');

            function disableOtherFields(selectedField) {
                const fields = [taskSelect, clientSelect, budgetSelect, projectSelect];
                fields.forEach(field => {
                    if (field !== selectedField) {
                        field.disabled = true;
                        field.value = ''; // Limpiar selección en otros campos
                    }
                });
            }

            function enableAllFields() {
                [taskSelect, clientSelect, budgetSelect, projectSelect].forEach(field => {
                    field.disabled = false;
                });
            }

            // Añadir eventos a cada campo
            [taskSelect, clientSelect, budgetSelect, projectSelect].forEach(field => {
                field.addEventListener('change', function() {
                    if (this.value) {
                        disableOtherFields(this);
                    } else {
                        enableAllFields(); // Si no se selecciona nada, habilitar todos los campos
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-details').forEach(button => {
                const targetId = button.getAttribute('data-bs-target');
                const target = document.querySelector(targetId);

                const collapse = new bootstrap.Collapse(target, {
                    toggle: false
                });

                button.addEventListener('click', function() {
                    const isShown = target.classList.contains('show');

                    // Si estamos ocultando, ocultar todos los detalles
                    if (isShown) {
                        document.querySelectorAll('.details-content').forEach(detail => {
                            const detailCollapse = bootstrap.Collapse.getInstance(detail);
                            if (detailCollapse) {
                                detailCollapse.hide();
                            }
                        });
                        // Actualizar todos los botones a "Ver Detalles"
                        document.querySelectorAll('.toggle-text').forEach(span => {
                            span.textContent = 'Ver Detalles';
                        });
                    } else {
                        // Si estamos mostrando, primero ocultar todos los demás
                        document.querySelectorAll('.details-content').forEach(detail => {
                            if (detail !== target) {
                                const detailCollapse = bootstrap.Collapse.getInstance(
                                    detail);
                                if (detailCollapse) {
                                    detailCollapse.hide();
                                }
                            }
                        });
                        // Actualizar todos los botones a "Ver Detalles" excepto el actual
                        document.querySelectorAll('.toggle-text').forEach(span => {
                            if (span !== button.querySelector('.toggle-text')) {
                                span.textContent = 'Ver Detalles';
                            }
                        });
                        collapse.show();
                    }
                    if (isShown) {
                        target.classList.remove('show');
                    }
                    const span = button.querySelector('.toggle-text');
                });
            });
        });
    </script>
    <script>
        function rechazarRelacion(id, tipo) {
            if (confirm('¿Estás seguro de que deseas rechazar esta relación?')) {
                fetch(`/tesoreria/rechazar-relacion/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            tipo: tipo
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recargar la página para mostrar los cambios
                            window.location.reload();
                        } else {
                            alert('Error al rechazar la relación: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al procesar la solicitud');
                    });
            }
        }
    </script>
    <script>
        function toggleAceptarButton(id) {
            const selectedRadio = document.querySelector(`input[name="aceptar_${id}"]:checked`);
            const aceptarButton = document.getElementById(`btn-aceptar-${id}`);

            if (selectedRadio) {
                aceptarButton.disabled = false;
            } else {
                aceptarButton.disabled = true;
            }
        }
    </script>

    <script>
        function aceptarRelacion(unclassifiedId, tipo, button) {
            // Obtener el contenedor específico del botón que se hizo clic
            const container = document.querySelector(`#details-${unclassifiedId}`);
            // Obtener el radio button seleccionado dentro de este contenedor específico
            const selectedRadio = container.querySelector(`input[name="aceptar_${unclassifiedId}"]:checked`);

            if (!selectedRadio) {
                alert("Debes seleccionar una coincidencia para aceptar.");
                return;
            }

            const selectedId = selectedRadio.value;
            const selectedTabla = selectedRadio.dataset.tabla;

            $.ajax({
                url: "{{ route('tesoreria.acceptCoincidencias') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    unclassified_id: unclassifiedId,
                    tipo: tipo,
                    coincidencia_id: selectedId,
                    tabla: selectedTabla
                },
                success: function(response) {
                    const detailsRow = $(button).closest('tr'); // fila de detalles
                    const mainRow = detailsRow.prev('tr'); // fila principal justo encima

                    detailsRow.remove();
                    mainRow.remove();

                    // Verificar si hay más filas en las tablas
                    const remainingRows = $('.table-responsive table tbody tr').length;

                    if (remainingRows === 0) {
                        // Si no hay más filas, cerrar el modal
                        $('#unclassifiedModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert("Ocurrió un error al aceptar la coincidencia.");
                }

            });
        }

        // Cuando se cierre el modal unclassified mostar alertas con funcion mostrarTiposDeAlertas
        document.addEventListener('DOMContentLoaded', function() {
            const unclassifiedModal = document.getElementById('unclassifiedModal');
            if (unclassifiedModal) {
                unclassifiedModal.addEventListener('hidden.bs.modal', function() {
                    mostrarTiposDeAlertas();
                });
            }
        });

        function rechazarRelacionYCrearGasto(button) {
            const unclassifiedId = button.getAttribute('data-unclassified-id');
            const tipo = button.getAttribute('data-tipo');
            const tabla = button.getAttribute('data-tabla');

            rechazarRelacion(unclassifiedId, tipo, tabla);
            crearMovimiento(button);
        }

        function rechazarRelacionYCrearIngreso(button) {
            const unclassifiedId = button.getAttribute('data-unclassified-id');
            const tipo = button.getAttribute('data-tipo');
            const tabla = button.getAttribute('data-tabla');

            rechazarRelacion(unclassifiedId, tipo, tabla);
            crearIngreso(button);
        }


        function crearMovimiento(button) {
            const company = button.getAttribute('data-company');
            const bank = button.getAttribute('data-bank');
            const iban = button.getAttribute('data-iban');
            const amount = button.getAttribute('data-amount');
            const date = button.getAttribute('data-date');
            const message = button.getAttribute('data-message');
            const unclassifiedId = button.getAttribute('data-unclassified-id');

            $.ajax({
                url: "{{ route('tesoreria.gasto-store-api') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    company_name: company,
                    bank: bank,
                    iban: iban,
                    quantity: amount,
                    date: date,
                    title: message,
                    reference: message,
                    id: unclassifiedId
                },
                success: function(response) {
                    if (response.success) {
                        const detailsRow = $(button).closest('tr'); // fila de detalles
                        const mainRow = detailsRow.prev('tr'); // fila principal justo encima

                        detailsRow.remove();
                        mainRow.remove();

                        // Verificar si hay más filas en las tablas
                        const remainingRows = $('.table-responsive table tbody tr').length;

                        if (remainingRows === 0) {
                            // Si no hay más filas, cerrar el modal
                            $('#unclassifiedModal').modal('hide');
                        }
                    } else {
                        toastr.error('Error al crear el gasto');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Ocurrió un error al crear el gasto');
                }
            });
        }

        function crearMovimientoAsociado(button) {
            const company = button.getAttribute('data-company');
            const bank = button.getAttribute('data-bank');
            const iban = button.getAttribute('data-iban');
            const amount = button.getAttribute('data-amount');
            const date = button.getAttribute('data-date');
            const message = button.getAttribute('data-message');
            const unclassifiedId = button.getAttribute('data-unclassified-id');

            $.ajax({
                url: "{{ route('tesoreria.gasto-store-api') }}", // Using the same endpoint as crearMovimiento
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    company_name: company,
                    bank: bank,
                    iban: iban,
                    quantity: amount, // Changed from amount to quantity to match the API
                    date: date,
                    title: message,
                    reference: message, // Added reference field
                    id: unclassifiedId
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the expense rows
                        const detailsRow = $(button).closest('tr'); // fila de detalles
                        const mainRow = detailsRow.prev('tr'); // fila principal justo encima

                        detailsRow.remove();
                        mainRow.remove();

                        // Verificar si hay más filas en las tablas
                        const remainingRows = $('.table-responsive table tbody tr').length;

                        if (remainingRows === 0) {
                            // Si no hay más filas, cerrar el modal
                            $('#unclassifiedModal').modal('hide');
                        }
                    } else {
                        toastr.error('Error al crear el gasto asociado');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Ocurrió un error al crear el gasto asociado');
                }
            });
        }

        function crearIngreso(button) {
            const company = button.getAttribute('data-company');
            const bank = button.getAttribute('data-bank');
            const iban = button.getAttribute('data-iban');
            const amount = button.getAttribute('data-amount');
            const date = button.getAttribute('data-date');
            const message = button.getAttribute('data-message');
            const unclassifiedId = button.getAttribute('data-unclassified-id');

            $.ajax({
                url: "{{ route('tesoreria.ingreso-store-api') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    company_name: company,
                    bank: bank,
                    iban: iban,
                    quantity: amount,
                    date: date,
                    title: message,
                    reference: message,
                    id: unclassifiedId
                },
                success: function(response) {
                    if (response.success) {
                        const detailsRow = $(button).closest('tr'); // fila de detalles
                        const mainRow = detailsRow.prev('tr'); // fila principal justo encima

                        detailsRow.remove();
                        mainRow.remove();

                        // Verificar si hay más filas en las tablas
                        const remainingRows = $('.table-responsive table tbody tr').length;

                        if (remainingRows === 0) {
                            // Si no hay más filas, cerrar el modal
                            $('#unclassifiedModal').modal('hide');
                        }
                    } else {
                        toastr.error('Error al crear el gasto');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Ocurrió un error al crear el gasto');
                }
            });
        }

        function crearTransferencia(button) {
            const unclassifiedId = button.dataset.unclassifiedId;
            const origenId = document.getElementById(`origen-${unclassifiedId}`).value;
            const destinoId = document.getElementById(`destino-${unclassifiedId}`).value;

            if (!origenId || !destinoId) {
                alert('Por favor seleccione tanto el banco origen como el destino');
                return;
            }

            $.ajax({
                url: "{{ route('tesoreria.transferencia-store-api') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    company_name: button.dataset.company,
                    bank: button.dataset.bank,
                    iban: button.dataset.iban,
                    amount: button.dataset.amount,
                    date: button.dataset.date,
                    message: button.dataset.message,
                    unclassified_id: unclassifiedId,
                    origen_id: origenId,
                    destino_id: destinoId,
                    id: unclassifiedId,
                    tabla: button.dataset.tabla
                },
                success: function(response) {
                    if (response.success) {
                        const detailsRow = $(button).closest('tr'); // fila de detalles
                        const mainRow = detailsRow.prev('tr'); // fila principal justo encima

                        detailsRow.remove();
                        mainRow.remove();

                        // Verificar si hay más filas en las tablas
                        const remainingRows = $('.table-responsive table tbody tr').length;

                        if (remainingRows === 0) {
                            // Si no hay más filas, cerrar el modal
                            $('#unclassifiedModal').modal('hide');
                        }

                        toastr.success('Transferencia creada correctamente');
                    } else {
                        toastr.error(response.message || 'Error al crear la transferencia');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    toastr.error('Error al crear la transferencia');
                }
            });
        }

        // Debounce para evitar ralentizar con muchas teclas
        function debounce(func, delay) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        // Abrir dropdown manualmente (porque no se abre solo al escribir)
        function openBudgetDropdown(input) {
            const dropdown = input.nextElementSibling;
            dropdown.classList.add('show');
        }

        // Filtrar presupuestos con debounce
        const filterBudgets = debounce(function(input) {
            const filter = input.value.toLowerCase();
            const dropdown = input.nextElementSibling;
            const items = dropdown.querySelectorAll('.budget-item');

            let anyVisible = false;

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const match = text.includes(filter);
                item.style.display = match ? '' : 'none';
                if (match) anyVisible = true;
            });

            dropdown.classList.toggle('show', anyVisible);
        }, 200);

        function updateTotal(incomeId, invoiceAmount) {
            // Obtener todos los inputs que pertenecen a este income
            const inputs = document.querySelectorAll(`[id^="amount-input-${incomeId}-"]`);
            const checkboxes = document.querySelectorAll(`.invoice-checkbox[data-income-id="${incomeId}"]:checked`);
            let total = 0;
            let hasCheckedBoxes = checkboxes.length > 0;
            let hasValidInputs = true;

            inputs.forEach(input => {
                if (input.style.display !== 'none') { // Solo sumar inputs visibles
                    // Obtener el valor máximo permitido
                    const maxValue = parseFloat(input.getAttribute('max')) || 0;
                    let value = parseFloat(input.value) || 0;

                    // Si el valor excede el máximo, establecer el valor máximo
                    if (value > maxValue) {
                        value = maxValue;
                        input.value = maxValue.toFixed(2);
                    }

                    total += value;
                    // Verificar si el input tiene un valor válido (mayor que 0)
                    if (value <= 0) {
                        hasValidInputs = false;
                    }
                }
            });

            // Actualizar el total y el botón
            const totalElement = document.querySelector(`#total-${incomeId}`);
            const relateButton = document.querySelector(`#relate-button-${incomeId}`);
            const originalAmount = parseFloat(invoiceAmount || 0);

            if (totalElement && relateButton) {
                // Actualizar el texto del total
                totalElement.textContent = `Total: ${total.toFixed(2)}€`;

                // Verificar condiciones
                const isAmountEqual = Math.abs(total - originalAmount) < 0.01;
                const isValid = hasCheckedBoxes && isAmountEqual && hasValidInputs;

                // Actualizar estilos y estado del botón
                if (isValid) {
                    totalElement.style.color = 'green';
                    relateButton.disabled = false;
                    relateButton.classList.remove('btn-danger');
                    relateButton.classList.add('btn-primary');
                } else {
                    totalElement.style.color = 'red';
                    relateButton.disabled = true;
                    relateButton.classList.remove('btn-primary');
                    relateButton.classList.add('btn-danger');
                }

                // Añadir tooltip con la razón de la desactivación
                let tooltipText = '';
                if (!hasCheckedBoxes) {
                    tooltipText = 'Debe seleccionar al menos una factura';
                } else if (!hasValidInputs) {
                    tooltipText = 'Los importes asignados deben ser mayores que 0';
                } else if (!isAmountEqual) {
                    tooltipText = 'El total debe coincidir con el importe original';
                }

                // Actualizar o crear tooltip
                if (tooltipText) {
                    relateButton.setAttribute('title', tooltipText);
                    relateButton.setAttribute('data-bs-toggle', 'tooltip');
                    relateButton.setAttribute('data-bs-placement', 'top');

                    // Inicializar tooltip si no existe
                    if (!relateButton._tooltip) {
                        relateButton._tooltip = new bootstrap.Tooltip(relateButton);
                    } else {
                        relateButton._tooltip.dispose();
                        relateButton._tooltip = new bootstrap.Tooltip(relateButton);
                    }
                } else {
                    // Eliminar tooltip si existe
                    if (relateButton._tooltip) {
                        relateButton._tooltip.dispose();
                        relateButton._tooltip = null;
                    }
                    relateButton.removeAttribute('title');
                    relateButton.removeAttribute('data-bs-toggle');
                    relateButton.removeAttribute('data-bs-placement');
                }
            }
        }

        // Añadir evento input para validar el máximo en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.amount-input').forEach(input => {
                input.addEventListener('input', function() {
                    const maxValue = parseFloat(this.getAttribute('max')) || 0;
                    let value = parseFloat(this.value) || 0;

                    if (value > maxValue) {
                        this.value = maxValue.toFixed(2);
                        // Disparar el evento updateTotal
                        const incomeId = this.id.split('-')[2];
                        const originalAmount = parseFloat(this.dataset.originalAmount || 0);
                        updateTotal(incomeId, originalAmount);
                    }
                });
            });
        });

        function toggleAmountInput(checkbox) {

            const row = checkbox.closest('tr');
            const amountInput = row.querySelector('.amount-input');
            const incomeId = checkbox.dataset.incomeId;

            // Mostrar/ocultar input
            amountInput.style.display = checkbox.checked ? 'block' : 'none';

            // Resetear valor si se desmarca
            if (!checkbox.checked) {
                amountInput.value = 0;
            }

            // Actualizar total
            const originalAmount = parseFloat(checkbox.dataset.amount || 0);
            updateTotal(incomeId, originalAmount);
        }

        // Inicializar tooltips cuando el documento esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar todos los tooltips existentes
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        function rechazarYAsociarFacturas(button) {
            const unclassifiedId = button.getAttribute('data-unclassified-id');
            const tipo = button.getAttribute('data-tipo');
            const tabla = button.getAttribute('data-tabla');

            rechazarRelacion(unclassifiedId, tipo, tabla);
            asociarFacturas(button);
        }

            function relacionarFacturasYCrearIngreso(button) {
                const incomeId = button.getAttribute('data-unclassified-id');
                const checkboxes = document.querySelectorAll(`.invoice-checkbox[data-income-id='${incomeId}']:checked`);
                const bank = button.getAttribute('data-bank');
                const date = button.getAttribute('data-date');
                const title = button.getAttribute('data-message');
                let facturasSeleccionadas = [];

                checkboxes.forEach(checkbox => {
                    const invoiceId = checkbox.getAttribute('data-invoice-id');
                    const input = document.querySelector(`#amount-input-${incomeId}-${invoiceId}`);
                    const amountAsignado = input ? parseFloat(input.value || 0) : 0;

                    facturasSeleccionadas.push({
                        id: invoiceId,
                        importe: amountAsignado
                    });
                });

                $.ajax({
                    url: '{{ route('tesoreria.multi-ingreso') }}',
                    method: 'POST',
                    data: {
                        facturas: facturasSeleccionadas,
                        income_id: incomeId,
                        date: date,
                        bank: bank,
                        title: title,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Encontrar el contenedor de detalles
                            const detailsContainer = document.querySelector(`#details-${incomeId}`);
                            if (detailsContainer) {
                                // Encontrar la fila principal (la que contiene el botón "Ver Detalles")
                                const mainRow = detailsContainer.closest('tr');
                                if (mainRow) {
                                    // Eliminar tanto la fila principal como la fila de detalles
                                    mainRow.remove();
                                    detailsContainer.remove();
                                }
                            }

                            // Verificar si hay más filas en la tabla
                            const tableBody = document.querySelector('.table-responsive table tbody');
                            if (tableBody && tableBody.children.length === 0) {
                                // Si no hay más filas, cerrar el modal
                                const modal = document.getElementById('unclassifiedModal');
                                if (modal) {
                                    const modalInstance = bootstrap.Modal.getInstance(modal);
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }
                                }
                            }

                            // Mostrar mensaje de éxito
                            Swal.fire({
                                icon: 'success',
                                title: 'Operación exitosa',
                                text: 'Las facturas han sido relacionadas correctamente',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Error al procesar la solicitud',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al procesar la solicitud',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }
                });
            }
    </script>

@endsection
