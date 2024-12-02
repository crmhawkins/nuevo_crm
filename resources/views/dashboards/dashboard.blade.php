@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/css/dashboard.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-4 order-md-1 order-last">
                <h3>Dashboard</h3>
            </div>

            <div class="col-12 col-md-8 order-md-2 order-first">

            </div>
        </div>
    </div>
    <div class="card2 mt-4">
        <div class="card-body2">
            <div class="row justify-between">
                <div class="col-12">
                    <div class="row row-cols-1 row-cols-xl-2 g-xl-4 g-3 mb-3">
                        <div class="card2">
                            <div class="card-body col">
                                <div class="row justify-content-between">
                                    <div class="col-5">
                                        <h5 class="card-title fw-bold">Produccion</h5>
                                    </div>
                                    <div class="col-5" >
                                        <input type="text" class="form-control produccion" id="dateRange" name="dateRange" value="{{ request('dateRange') }}">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table producc">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Inpuntualidad</th>
                                                <th>H.Oficinas</th>
                                                <th>H.Producidas</th>
                                                <th>Produc</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($produccion) == 0)
                                            <tr>
                                                <td colspan="5">No hay datos disponibles</td>
                                            </tr>
                                            @else
                                                @foreach($produccion as $p)
                                                <tr>
                                                    <td>{{$p['nombre']}}</td>
                                                    <td>{{$p['inpuntualidad']}}</td>
                                                    <td>{{$p['horas_oficinas']}}</td>
                                                    <td>{{$p['horas_producidas']}}</td>
                                                    <td>{{$p['productividad']}} %</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card2">
                            <div class="card-body col">
                                <div class="row justify-content-between">
                                    <div class="col-5">
                                        <h5 class="card-title fw-bold">Gestion</h5>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control gestion" id="dateRange" name="dateRange" value="">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table gest">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Inpuntualidad</th>
                                                <th>H.Oficinas</th>
                                                <th>Presu.Realizados</th>
                                                <th>Llamadas</th>
                                                <th>Kits</th>
                                                <th>Peticiones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($gestion) == 0)
                                            <tr>
                                                <td colspan="5">No hay datos disponibles</td>
                                            </tr>
                                            @else
                                                @foreach($gestion as $g)
                                                <tr>
                                                    <td>{{$g['nombre']}}</td>
                                                    <td>{{$g['inpuntualidad']}}</td>
                                                    <td>{{$g['horas_oficinas']}}</td>
                                                    <td>{{$g['presu_generados']}}</td>
                                                    <td>{{$g['llamadas']}}</td>
                                                    <td>{{$g['kits']}}</td>
                                                    <td>{{$g['peticiones']}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card2">
                            <div class="card-body col">
                                <div class="row justify-content-between">
                                    <div class="col-5">
                                        <h5 class="card-title fw-bold">Comercial</h5>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control comercial" id="dateRange" name="dateRange" value="{{ request('dateRange') }}">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table comerc">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>H.Oficinas</th>
                                                <th>Leads</th>
                                                <th>peticiones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($comercial) == 0)
                                            <tr>
                                                <td colspan="5">No hay datos disponibles</td>
                                            </tr>
                                            @else
                                                @foreach($comercial as $c)
                                                <tr>
                                                    <td>{{$c['nombre']}}</td>
                                                    <td>{{$c['horas_oficinas']}}</td>
                                                    <td>{{$c['kits_creados']}}</td>
                                                    <td>{{$c['peticiones']}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card2">
                            <div class="card-body col">
                                <div class="row justify-content-between">
                                    <div class="col-5">
                                        <h5 class="card-title fw-bold">Contabilidad</h5>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" class="form-control contable" id="dateRange" name="dateRange" value="{{ request('dateRange') }}">
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table contab">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Inpuntualidad</th>
                                                <th>H.Oficinas</th>
                                                <th>Fact.Realizados</th>
                                                <th>Llamadas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($contabilidad) == 0)
                                            <tr>
                                                <td colspan="5">No hay datos disponibles</td>
                                            </tr>
                                            @else
                                                @foreach($contabilidad as $contable)
                                                <tr>
                                                    <td>{{$contable['nombre']}}</td>
                                                    <td>{{$contable['inpuntualidad']}}</td>
                                                    <td>{{$contable['horas_oficinas']}}</td>
                                                    <td>{{$contable['facturas']}}</td>
                                                    <td>{{$contable['llamadas']}}</td>
                                                </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
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
                                        <div class="mx-4 text-center">
                                            <h1 class="fs-5 ">Productividad</h1>
                                            <div class="progress-circle" data-percentage="70">
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
                                                <div id="to-do-container" class="d-flex row"  style="" >
                                                    <div class="col-12 d-flex align-items-center mt-2 pb-0" style="max-height: 50px;">
                                                        <button class="btn btn-outline-secondary w-100" onclick="showTodoModal()">
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
                                                                        <div id="todo-card-{{ $to_do->id }}"  class="pulse justify-center align-items-center" style="{{ $to_do->unreadMessagesCountByUser($user->id) > 0 ? 'display: flex;' : 'display: none;' }}">
                                                                            {{ $to_do->unreadMessagesCountByUser($user->id) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="info">
                                                                    <div class="d-flex justify-content-evenly flex-wrap">
                                                                        @if($to_do->project_id)<a class="btn btn-outline-secondary mb-2" href="{{route('campania.edit',$to_do->project_id)}}"> Campaña {{$to_do->proyecto ? $to_do->proyecto->name : 'borrada'}}</a>@endif
                                                                        @if($to_do->client_id)<a class="btn btn-outline-secondary mb-2" href="{{route('clientes.show',$to_do->client_id)}}"> Cliente {{$to_do->cliente ? $to_do->cliente->name : 'borrado'}}</a>@endif
                                                                        @if($to_do->budget_id)<a class="btn btn-outline-secondary mb-2" href="{{route('presupuesto.edit',$to_do->budget_id)}}"> Presupuesto {{$to_do->presupuesto ? $to_do->presupuesto->concept : 'borrado'}}</a>@endif
                                                                        @if($to_do->task_id) <a class="btn btn-outline-secondary mb-2" href="{{route('tarea.edit',$to_do->task_id)}}"> Tarea {{$to_do->tarea ? $to_do->tarea->title : 'borrada'}}</a> @endif
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
                                    <button class="btn btn-primary mx-2" onclick="showLlamadaModal()">Iniciar LLamada</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="llamadaModal" tabindex="-1" aria-labelledby="llamadaModalLabel" aria-hidden="true">
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
                                <div class="col-md-12 mb-3">
                                    <label for="phone" class="form-label">Telefono</label>
                                    <input type="text" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <input type="hidden" name="admin_user_id" value="{{ $user->id }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button id="iniciarllamada" type="submit" class="btn btn-primary">Iniciar</button>
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
                                <select class="form-select choices" id="client_id" name="client_id">
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
                                <select class="form-select choices" id="budget_id" name="budget_id">
                                    <option value="">Seleccione presupuesto</option>
                                    @foreach ($budgets as $budget)
                                        <option value="{{ $budget->id }}" {{ old('budget_id') == $budget->id ? 'selected' : '' }}>
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
                                <label for="admin_user_ids" class="form-label">Usuarios</label>
                                <select class="form-select" id="admin_user_ids" name="admin_user_ids[]" multiple>
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
<script>
    var enRutaEspecifica = true;
    document.addEventListener('DOMContentLoaded', function() {
        var multipleCancelButton = new Choices('#admin_user_ids', {
            removeItemButton: true, // Permite a los usuarios eliminar una selección
            searchEnabled: true,  // Habilita la búsqueda dentro del selector
            paste: false          // Deshabilita la capacidad de pegar texto en el campo
        });
    });
</script>
<script>
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
    function showLlamadaModal() {
        var llamadaModal = new bootstrap.Modal(document.getElementById('llamadaModal'));
        llamadaModal.show();
    }
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        fetch(`/todos/unread-messages-count/${todoId}`,{
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
        });
    });
</script>
<script>
    $(document).ready(function () {
        // Escucha el evento change en el input con la clase 'produccion'
        $('.produccion').on('change', function (e) {
            console.log(e);
            // Obtén el valor del input que cambió
            let dateRange = $(this).val();

            if(dateRange.includes('a')) {
                fetchProductionData(dateRange);
            }
            // Muestra el valor en la consola (solo para verificar que se obtuvo bien)
            // Llama a la función para recargar los datos con fetch
            //fetchProductionData(dateRange);
        });

        // Función que hace el fetch para recargar los datos
        function fetchProductionData(dateRange) {
            fetch('/get-produccion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token CSRF
                },
                body: JSON.stringify({ dateRange: dateRange })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Datos recibidos:", data);
                    // Aquí puedes actualizar la tabla con los datos recibidos
                    // Ejemplo de actualización de tabla
                    updateTableProduccion(data);
                })
                .catch(error => console.error('Error al recargar los datos:', error));
        }

        // Función para actualizar la tabla con los datos recibidos
        function updateTableProduccion(data) {
            let tbody = $('.producc tbody');
            tbody.empty(); // Limpia el contenido actual de la tabla

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5">No hay datos disponibles</td></tr>');
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.inpuntualidad}</td>
                            <td>${item.horas_oficinas}</td>
                            <td>${item.horas_producidas ?? ''}</td>
                            <td>${item.productividad ?? ''}%</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        // Escucha el evento change en el input con la clase 'produccion'
        $('.gestion').on('change', function (e) {
            // Obtén el valor del input que cambió
            let dateRange = $(this).val();

            if(dateRange.includes('a')) {
                fetchGestionData(dateRange);
            }

        });

        // Función que hace el fetch para recargar los datos
        function fetchGestionData(dateRange) {
            fetch('/get-gestion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token CSRF
                },
                body: JSON.stringify({ dateRange: dateRange })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Datos recibidos:", data);
                    // Aquí puedes actualizar la tabla con los datos recibidos
                    // Ejemplo de actualización de tabla
                    updateTablegestion(data);
                })
                .catch(error => console.error('Error al recargar los datos:', error));
        }

        // Función para actualizar la tabla con los datos recibidos
        function updateTablegestion(data) {
            let tbody = $('.gest tbody');
            tbody.empty(); // Limpia el contenido actual de la tabla

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5">No hay datos disponibles</td></tr>');
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.inpuntualidad}</td>
                            <td>${item.horas_oficinas}</td>
                            <td>${item.presu_generados ?? ''}</td>
                            <td>${item.llamadas ?? ''}</td>
                            <td>${item.kits ?? ''}</td>
                            <td>${item.peticiones ?? ''}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        // Escucha el evento change en el input con la clase 'produccion'
        $('.comercial').on('change', function (e) {
            console.log(e);
            // Obtén el valor del input que cambió
            let dateRange = $(this).val();

            if(dateRange.includes('a')) {
                fetchComencialData(dateRange);
            }
            // Muestra el valor en la consola (solo para verificar que se obtuvo bien)
            // Llama a la función para recargar los datos con fetch
            //fetchProductionData(dateRange);
        });

        // Función que hace el fetch para recargar los datos
        function fetchComencialData(dateRange) {
            fetch('/get-comercial', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token CSRF
                },
                body: JSON.stringify({ dateRange: dateRange })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Datos recibidos:", data);
                    // Aquí puedes actualizar la tabla con los datos recibidos
                    // Ejemplo de actualización de tabla
                    updateTableComercial(data);
                })
                .catch(error => console.error('Error al recargar los datos:', error));
        }

        // Función para actualizar la tabla con los datos recibidos
        function updateTableComercial(data) {
            let tbody = $('.comerc tbody');
            tbody.empty(); // Limpia el contenido actual de la tabla

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5">No hay datos disponibles</td></tr>');
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.horas_oficinas}</td>
                            <td>${item.kits_creados}</td>
                            <td>${item.peticiones ?? ''}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        $('.contable').on('change', function (e) {

            let dateRange = $(this).val();

            if(dateRange.includes('a')) {
                fetchContabilidadData(dateRange);
            }

        });

        function fetchContabilidadData(dateRange) {
            fetch('get-contabilidad', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Asegúrate de tener el token CSRF
                },
                body: JSON.stringify({ dateRange: dateRange })
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Datos recibidos:", data);
                    updateTableContabilidad(data);
                })
                .catch(error => console.error('Error al recargar los datos:', error));
        }

        function updateTableContabilidad(data) {
            let tbody = $('.contab tbody');
            tbody.empty(); // Limpia el contenido actual de la tabla

            if (data.length === 0) {
                tbody.append('<tr><td colspan="5">No hay datos disponibles</td></tr>');
            } else {
                data.forEach(item => {
                    let row = `
                        <tr>
                            <td>${item.nombre}</td>
                            <td>${item.inpuntualidad}</td>
                            <td>${item.horas_oficinas}</td>
                            <td>${item.facturas ?? ''}</td>
                            <td>${item.llamadas ?? ''}</td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }
    });
</script>
@endsection

