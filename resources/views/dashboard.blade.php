@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

<style>
    /* Estilos básicos */
    .input-group-text {
        position: relative;
    }
    .file-icon {
        text-align: center; /* Centra el ícono del archivo */
        margin-bottom: 5px; /* Espacio entre el ícono y el texto del mensaje */
    }
    .file-icon i {
        font-size: 50px; /* Ajusta el tamaño del ícono */
    }
    #file-icon {
        position: absolute;
        right: 10px; /* Ajusta según necesites */
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
        display: inline-block; /* Esto hace que el div se ajuste al contenido */
        max-width: 80%;
    }
    .mine {
        background-color: #dcf8c6;
        text-align: left;
        float: right; /* Alinea los mensajes del usuario a la derecha */
        clear: both; /* Asegura que los mensajes no se superpongan */
    }
    .theirs {
        background-color: #f1f0f0;
        text-align: left;
        float: left; /* Alinea los mensajes de otros usuarios a la izquierda */
        clear: both; /* Asegura que los mensajes no se superpongan */
    }
    .input-group-text {
        background: white;
        cursor: pointer;
        width: 40px;
        border: 1px solid rgb(175, 175, 175);
        box-shadow: 0 0 3px rgb(175, 175, 175);
    }
    .input-group-text i {
        color: #6c757d; /* Color gris para el ícono, ajustable según necesidades */
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
        color: rgb(255, 255, 255);
        font-size: 12px;
        animation: pulse-animation 2s infinite;
        text-shadow: 0px 0px 10px  #ffffff;
        background-color: #ff0000c9;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        box-shadow: 0px 0px 1px 1px #ff0000;
        display: flex;
    }
    #to-do {
        max-height: 600px;
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
        border-color:black;
        border-width: thin;
        border-radius: 20px;
    }

    .info {
        display: none;
        padding: 10px;
        background-color: #fcfcfc; /* Fondo para los detalles */
        margin-top: 5px;
    }
    .tooltip.custom-tooltip .tooltip-inner {
        background-color: #343a40; /* Color de fondo del tooltip */
        color: #fff; /* Color del texto */
        font-size: 1.2rem; /* Tamaño de la fuente */
        padding: 15px; /* Espaciado interno */
        border-radius: 5px; /* Bordes redondeados */
        max-width: 500px; /* Ancho máximo */
    }

    .tooltip.custom-tooltip .tooltip-arrow {
        border-top-color: #343a40; /* Color de la flecha del tooltip */
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

    #to-do::-webkit-scrollbar,.chat-container::-webkit-scrollbar {
        width: 8px; /* Ancho del scrollbar */

    }

    #to-do::-webkit-scrollbar-track,.chat-container::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1); /* Fondo semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    #to-do::-webkit-scrollbar-thumb,.chat-container::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.5); /* Pulgar negro semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    #to-do::-webkit-scrollbar-thumb:hover,.chat-container::-webkit-scrollbar-thumb:hover {
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
    .clickable {
        cursor: pointer;
    }

    .info {
        display: none;
        padding: 15px;
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
                            <a href="{{route('presupuestos.status')}}" class="btn btn-outline-primary mb-2">Ver Status Proyectos</a>
                            <a href="{{route('tareas.cola')}}" class="btn btn-outline-primary mb-2">Ver Cola de producción</a>
                            <a href="{{route('tareas.index')}}" class="btn btn-outline-primary mb-2">Ver Tareas</a>
                            <a href="{{route('tareas.index')}}" class="btn btn-outline-primary mb-2">Ver Producción</a>
                            <a href="{{route('proveedores.index')}}" class="btn btn-outline-primary mb-2">Ver Proveedores</a>
                        </div>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="side-column">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex flex-wrap">
                                    <div class="col-12 d-flex justify-content-center mb-4 align-items-center">
                                        <div class="mx-4 text-center">
                                            <h5 class="my-3">{{$user->name}}&nbsp;{{$user->surname}}</h5>
                                            <p class="text-muted mb-1">{{$user->departamento->name}}</p>
                                            <p class="text-muted mb-4">{{$user->acceso->name}}</p>
                                            <div class="d-flex  align-items-center my-2">
                                                <input type="color" class="form-control form-control-color" style="padding: 0.4rem" id="color">
                                                <label for="color" class="form-label m-2">Color</label>
                                            </div>
                                        </div>
                                        <div class="mx-4">
                                            @if ($user->image == null)
                                                <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{asset('assets/images/guest.webp')}}" />
                                            @else
                                                <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{ asset('/storage/avatars/'.$user->image) }}" />
                                            @endif
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
                                                <div id="to-do-container" class="d-flex flex-column"  style="" >
                                                    <button class="btn btn-outline-secondary mt-4 mx-3" onclick="showTodoModal()">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                    <div id="to-do" class="p-3">
                                                        @foreach ($to_dos as $to_do)
                                                            <div class="card mt-2" id="todo-card-{{$to_do->id}}">
                                                                <div class="card-body d-flex justify-content-between clickable" id="todo-card-body-{{$to_do->id}}" data-todo-id="{{$to_do->id}}" style="{{$to_do->isCompletedByUser($user->id) ? 'background-color: #CDFEA4' : '' }}">
                                                                    <h3>{{ $to_do->titulo }}</h3>
                                                                    <div>
                                                                        @if(!($to_do->isCompletedByUser($user->id)))
                                                                        <button onclick="completeTask(event,{{ $to_do->id }})" id="complete-button-{{$to_do->id}}" class="btn btn-success btn-sm">Completar</button>
                                                                        @endif
                                                                        @if ($to_do->admin_user_id == $user->id)
                                                                        <button onclick="finishTask(event,{{ $to_do->id }})" class="btn btn-danger btn-sm">Finalizar</button>
                                                                        @endif
                                                                    </div>
                                                                    <div class="pulse justify-center align-items-center" style="{{ $to_do->unreadMessagesCountByUser($user->id) > 0 ? 'display: flex;' : 'display: none;' }}">
                                                                        {{ $to_do->unreadMessagesCountByUser($user->id) }}
                                                                    </div>
                                                                </div>
                                                                <div class="info">
                                                                    <div class="d-flex justify-content-evenly flex-wrap">
                                                                        @if($to_do->project_id)<a class="btn btn-outline-secondary mb-2" href="{{route('campania.edit',$to_do->project_id)}}"> Campaña {{$to_do->proyecto ? $to_do->proyecto->name : 'borrada'}}</a>@endif
                                                                        @if($to_do->client_id)<a class="btn btn-outline-secondary mb-2" href="{{route('clientes.show',$to_do->client_id)}}"> Cliente {{$to_do->cliente ? $to_do->cliente->name : 'borrado'}}</a>@endif
                                                                        @if($to_do->budget_id)<a class="btn btn-outline-secondary mb-2" href="{{route('presupuesto.edit',$to_do->budget_id)}}"> Presupuesto {{$to_do->presupuesto ? $to_do->presupuesto->concept : 'borrado'}}</a>@endif
                                                                        @if($to_do->task_id) <a class="btn btn-outline-secondary mb-2" href="{{route('tarea.show',$to_do->task_id)}}"> Tarea {{$to_do->tarea ? $to_do->tarea->title : 'borrada'}}</a> @endif
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
        <div class="modal-dialog modal-lg"> <!-- Cambio a modal-lg para mayor ancho -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Nuevo Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="eventform" action="{{ route('event.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="admin_user_id" class="form-label">Usuario</label>
                                <select class="form-select choices" id="admin_user_id" name="admin_user_id">
                                    <option value="">Seleccione usuario</option>
                                    @foreach ($users as $gestor)
                                        <option value="{{ $gestor->id }}" {{ old('admin_user_id') == $gestor->id ? 'selected' : '' }}>
                                            {{ $gestor->name }} {{ $gestor->surname }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin_user_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label">Cliente</label>
                                <select class="form-select" id="client_id" name="client_id">
                                    <option value="">Seleccione cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('client_id') == $cliente->id ? 'selected' : '' }}>
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
                                <select class="form-select" id="budget_id" name="budget_id">
                                    <option value="">Seleccione presupuesto</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}" {{ old('budget_id') == $budget->id ? 'selected' : '' }}>
                                            {{ $budget->reference }}
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
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="">Seleccione campaña</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                                <label for="start" class="form-label">Inicio</label>
                                <input type="datetime-local" class="form-control" id="start" name="start" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end" class="form-label">Fin</label>
                                <input type="datetime-local" class="form-control" id="end" name="end">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" style="padding: 0.4rem" class="form-control form-control-color" id="color" name="color">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button id="eventbutton" type="buttom" class="btn btn-primary">Guardar</button>
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
                                <label for="title" class="form-label">Título</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="4"></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="task_id" class="form-label">Tareas</label>
                                <select class="form-select" id="task_id" name="task_id">
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
                                <label for="client_id" class="form-label">Cliente</label>
                                <select class="form-select" id="client_id" name="client_id">
                                    <option value="">Seleccione cliente</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ old('client_id') == $cliente->id ? 'selected' : '' }}>
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
                                <select class="form-select" id="budget_id" name="budget_id">
                                    <option value="">Seleccione presupuesto</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}" {{ old('budget_id') == $budget->id ? 'selected' : '' }}>
                                            {{ $budget->reference }}
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
                                <select class="form-select" id="project_id" name="project_id">
                                    <option value="">Seleccione campaña</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                            <div class="col-md-6 mb-3 choices">
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
    <!-- Modal para Mensajes -->

</div>
@endsection

@section('scripts')
@include('partials.toast')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales-all.global.min.js"></script>
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
<script>
        $('#eventbutton').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('#eventform').submit(); // Esto envía el formulario.
        });
        $('#enviar').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('#mensaje').submit(); // Esto envía el formulario.
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
                    right: 'myCustomButton dayGridMonth,timeGridDay,listWeek'
                },
                events: events,
                customButtons: {
                    myCustomButton: {
                        icon: 'bi bi-plus',
                        text: 'Add event',
                        click: function() {
                            var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                            eventModal.show();
                        }
                    }
                },
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
    document.addEventListener('DOMContentLoaded', function() {
        const clientSelect = document.getElementById('client_id');
        const budgetSelect = document.getElementById('budget_id');
        const projectSelect = document.getElementById('project_id');

        // Función para actualizar presupuestos basados en el cliente seleccionado
        function updateBudgets(clientId) {
            fetch('/budgets-by-client', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ client_id: clientId })
            })
            .then(response => response.json())
            .then(budgets => {
                budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
                budgets.forEach(budget => {
                    budgetSelect.innerHTML += `<option value="${budget.id}">${budget.reference}</option>`;
                });
                budgetSelect.disabled = false;
            });
        }
        // Función para actualizar presupuestos basados en el cliente seleccionado
        function updateBudgetsbyprojects(projectId) {
            fetch('/budgets-by-project', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ project_id: projectId })
            })
            .then(response => response.json())
            .then(budgets => {
                budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
                budgets.forEach(budget => {
                    budgetSelect.innerHTML += `<option value="${budget.id}">${budget.reference}</option>`;
                });
                budgetSelect.disabled = false;
            });
        }

        // Función para actualizar campañas basadas en el cliente seleccionado
        function updateProjects(clientId) {
            fetch('/projects-from-client', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ client_id: clientId })
            })
            .then(response => response.json())
            .then(projects => {
                projectSelect.innerHTML = '<option value="">Seleccione campaña</option>';
                projects.forEach(project => {
                    projectSelect.innerHTML += `<option value="${project.id}">${project.name}</option>`;
                });
                projectSelect.disabled = false;
            });
        }

        // Cuando se selecciona un cliente, actualiza presupuestos y campañas
        clientSelect.addEventListener('change', function() {
            const clientId = this.value;
            if (clientId) {
                updateBudgets(clientId);
                updateProjects(clientId);
            } else {
                budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
                projectSelect.innerHTML = '<option value="">Seleccione campaña</option>';
                budgetSelect.disabled = true;
                projectSelect.disabled = true;
            }
        });

        // Cuando se selecciona un presupuesto, actualiza el cliente y la campaña
        budgetSelect.addEventListener('change', function() {
            const budgetId = this.value;
            if (budgetId) {
                fetch('/budget-by-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ budget_id: budgetId })
                })
                .then(response => response.json())
                .then(budget => {
                    clientSelect.value = budget.client_id;
                    //updateProjects(budget.client_id);
                    projectSelect.value = budget.project_id;
                    //console.log(budget.project_id;);

                });
            }
        });

        // Cuando se selecciona una campaña, actualiza el cliente y el presupuesto
        projectSelect.addEventListener('change', function() {
            const projectId = this.value;
            if (projectId) {
                fetch('/project-by-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ project_id: projectId })
                })
                .then(response => response.json())
                .then(project => {
                    clientSelect.value = project.client_id;
                    updateBudgetsbyprojects(project.id);
                    budgetSelect.value = ''; // O puedes poner una lógica para seleccionar un presupuesto por defecto
                });
            }
        });
    });

    // document.addEventListener('DOMContentLoaded', function() {
    //     const selects = document.querySelectorAll('.choices');

    //     const validSelects = Array.from(selects).filter(select => {
    //         console.log(select.tagName, select.type); // Para depurar el tipo de cada elemento seleccionado
    //         return (select.tagName === 'SELECT' || (select.tagName === 'INPUT' && select.type === 'text'));
    //     });
    //     const choicesInstances = Array.from(validSelects).map(select => new Choices(select));
    //     // const clientSelect = document.getElementById('client_id');
    //     // const budgetSelect = document.getElementById('budget_id');
    //     // const projectSelect = document.getElementById('project_id');

    //      // Obtén referencias a los selects por índice
    //     const clientSelect = validSelects[1]; // Elemento HTML del primer select
    //     const budgetSelect = validSelects[2]; // Instancia Choice.js del segundo select
    //     const projectSelect = validSelects[3]; // Instancia Choice.js del segundo select

    //     const clientChoices = choicesInstances[1]; // Elemento HTML del primer select
    //     const budgetChoices = choicesInstances[2]; // Instancia Choice.js del segundo select
    //     const projectChoices = choicesInstances[3]; // Instancia Choice.js del segundo select

    //     // Función para actualizar presupuestos basados en el cliente seleccionado
    //     function updateBudgets(clientId) {
    //         fetch('/budgets-by-client', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //             },
    //             body: JSON.stringify({ client_id: clientId })
    //         })
    //         .then(response => response.json())
    //         .then(budgets => {
    //             budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
    //             budgets.forEach(budget => {
    //                 budgetSelect.innerHTML += `<option value="${budget.id}">${budget.reference}</option>`;
    //             });
    //             budgetSelect.disabled = false;
    //         });
    //     }

    //     function updateBudgetsbyproyects(clientId) {
    //         fetch('/budgets-by-client', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //             },
    //             body: JSON.stringify({ client_id: clientId })
    //         })
    //         .then(response => response.json())
    //         .then(budgets => {
    //             budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
    //             budgets.forEach(budget => {
    //                 budgetSelect.innerHTML += `<option value="${budget.id}">${budget.reference}</option>`;
    //             });
    //             budgetSelect.disabled = false;
    //         });
    //     }

    //     // Función para actualizar campañas basadas en el cliente seleccionado
    //     function updateProjects(clientId) {
    //         fetch('/projects-from-client', {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //             },
    //             body: JSON.stringify({ client_id: clientId })
    //         })
    //         .then(response => response.json())
    //         .then(projects => {
    //             projectSelect.innerHTML = '<option value="">Seleccione campaña</option>';
    //             projects.forEach(project => {
    //                 projectSelect.innerHTML += `<option value="${project.id}">${project.name}</option>`;
    //             });
    //             projectSelect.disabled = false;
    //         });
    //     }

    //     //Cuando se selecciona un cliente, actualiza presupuestos y campañas
    //     clientSelect.addEventListener('change', function() {
    //         const clientId = this.value;
    //         if (clientId) {
    //             updateBudgets(clientId);
    //             updateProjects(clientId);
    //         } else {
    //             budgetSelect.innerHTML = '<option value="">Seleccione presupuesto</option>';
    //             projectSelect.innerHTML = '<option value="">Seleccione campaña</option>';
    //             budgetSelect.disabled = true;
    //             projectSelect.disabled = true;
    //         }
    //     });

    //     //Cuando se selecciona un presupuesto, actualiza el cliente y la campaña
    //     budgetSelect.addEventListener('change', function() {
    //         const budgetId = this.value;
    //         if (budgetId) {
    //             fetch('/budget-by-id', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //                 },
    //                 body: JSON.stringify({ budget_id: budgetId })
    //             })
    //             .then(response => response.json())
    //             .then(budget => {
    //                 clientSelect.value = budget.client_id;
    //                 projectSelect.value = budget.project_id;

    //             });
    //         }
    //     });
    //     console.log('select', selects[7]);

    //     // Cuando se selecciona una campaña, actualiza el cliente y el presupuesto
    //     selects[7].addEventListener('change', function() {

    //         console.log('entra');

    //         const projectId = this.value;
    //         console.log(projectId);

    //         if (projectId) {
    //             fetch('/project-by-id', {
    //                 method: 'POST',
    //                 headers: {
    //                     'Content-Type': 'application/json',
    //                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    //                 },
    //                 body: JSON.stringify({ project_id: projectId })
    //             })
    //             .then(response => response.json())
    //             .then(project => {
    //                 console.log(project.client_id);
    //                 console.log(toString(project.client_id));
    //                 clientChoices.setChoiceByValue(toString(project.client_id));
    //                 //clientSelect.setChoiceByValue(newValue);
    //                 //clientSelect.value = project.client_id;
    //                 //updateBudgets(project.client_id);
    //                 //budgetSelect.value = ''; // O puedes poner una lógica para seleccionar un presupuesto por defecto
    //             });
    //         }
    //     });
    // });

</script>
<script>
    function showTodoModal() {
        var todoModal = new bootstrap.Modal(document.getElementById('todoModal'));
        todoModal.show();
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar el contenedor que tiene todos los elementos clickable
        var container = document.getElementById('to-do');

        // Delegación de eventos para manejar clics en elementos clickable
        container.addEventListener('click', function(event) {
            // Comprobar si el elemento clickeado o sus padres tienen la clase 'clickable'
            var target = event.target;
            while (target !== container) {
                if (target.classList.contains('clickable')) {
                    // Cambiar la visibilidad del siguiente hermano (div.info)
                    var info = target.nextElementSibling;
                    var isVisible = info.style.display === 'block';

                    // Si la información está oculta, vamos a mostrarla y marcar los mensajes como leídos
                    if (!isVisible) {
                        info.style.display = 'block'; // Mostrar info

                        // Marcar mensajes como leídos solo si estamos expandiendo la información
                        markMessagesAsRead(target.getAttribute('data-todo-id'));
                    } else {
                        info.style.display = 'none'; // Ocultar info
                    }
                    break;
                }
                target = target.parentNode;
            }
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
    document.getElementById('file-input').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('file-icon').style.display = 'inline-block';
            document.getElementById('file-clip').style.display = 'none';
        } else {
            document.getElementById('file-icon').style.display = 'none';
            document.getElementById('file-clip').style.display = 'inline-block';
        }
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
</script>

@endsection

