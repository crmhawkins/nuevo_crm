@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .drag-container {
        display: flex;
        flex-wrap: nowrap; /* Allows horizontal scrolling */
        gap: 20px;
        padding: 20px;
        align-items: flex-start;
        overflow-x: auto; /* Enables horizontal scrolling */
        justify-content: flex-start;
    }
    .drag-column {
        background-color: #f9f9f9;
        border-radius: 8px;
        width: 300px;
        min-width: 300px; /* Ensures consistent width */
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .drag-column-header {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    .drag-column-content {
        margin-top: 10px;
    }
    .drag-item {
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px 12px;
        margin-bottom: 8px;
        cursor: pointer;
        font-size: 14px;
    }
    .drag-item:hover {
        background-color: #f0f0f0;
    }
    .status-indicator {
        width: 30px;
        height: 8px;
        border-radius: 10px;
        display: inline-block;
        margin-right: 0.4rem;
        margin-top: 0.2rem;
    }


    .form-check-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #333;
        }
        .form-check-input {
            margin-top: 0.15rem;
            margin-right: 0.3rem;
        }
        .form-check-inline {
            display: flex;
            align-items: center;
        }



        /*Scroll */
        .scroll-header-bar {
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
    height: 16px;
    position: relative;
    z-index: 10;
}

.scroll-fake-track {
    height: 1px;
}

.scroll-real-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    width: 100%;
}

.drag-container {
    display: flex;
    gap: 20px;
    padding: 20px;
    align-items: flex-start;
    width: max-content;
}

.modal-header .btn-close {
    color: white !important;
    filter: invert(1);
}



</style>
@endsection

@section('content')
@if (session('success'))
    <div class="alert alert-success mt-2">
        {{ session('success') }}
    </div>
@endif
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-12 col-md-4 order-md-1 order-last">
                <h3>Status Proyectos</h3>
                <p class="text-subtitle text-muted">Listado de proyectos</p>
            </div>
            <div class="col-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Estados de proyectos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section mt-4">
        <div class="card-body">
            <form method="GET" action="{{ route('presupuestos.status') }}">
                <div class="row gy-3 gx-3 align-items-end">
                    <div class="col-md-1">
                        <label for="year" class="form-label fw-bold">Año</label>
                        <select name="year" id="year" class="form-control">
                            @for ($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ $añoSeleccionado == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">Estados</label>
                        <div class="d-flex flex-wrap gap-3 align-items-center border rounded px-3 py-2" style="background-color: #fff;">
                            @foreach ($todosLosEstados as $estado)
                                <div class="form-check form-check-inline m-0" style="font-size: 0.85rem;">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="{{ $estado->id }}"
                                        id="status_{{ $estado->id }}"
                                        {{ in_array($estado->id, $estadosSeleccionados) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_{{ $estado->id }}">
                                        {{ $estado->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="sin_tareas" value="1" id="sin_tareas"
                                {{ request('sin_tareas') ? 'checked' : '' }}>
                            <label class="form-check-label" for="sin_tareas">
                                Sin tareas
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="archivados" value="1" id="archivados"
                                {{ request('archivados') ? 'checked' : '' }}>
                            <label class="form-check-label" for="archivados">
                                Ver archivados
                            </label>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="clientes_archivados" value="1" id="clientes_archivados"
                                {{ request('clientes_archivados') ? 'checked' : '' }}>
                            <label class="form-check-label" for="clientes_archivados">
                                Ver clientes archivados
                            </label>
                        </div>
                        
                    </div>
                    <div class="col-md-2">
                        <label for="q" class="form-label fw-bold">Buscar</label>
                        <input type="text" class="form-control" name="buscar" placeholder="Cliente, referencia o concepto" value="{{ request('buscar') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </div>
            </form>

        </div>

    </section>
    <!-- Barra de scroll arriba -->
    <div class="scroll-header-bar">
        <div class="scroll-fake-track" id="fakeScrollTrack"></div>
    </div>

    <!-- Contenedor real desplazable -->
    <div class="scroll-real-wrapper" id="scrollRealWrapper">
        <div class="drag-container" id="sortable-container">
            @foreach ($clientes as $cliente)
                <div class="drag-column ui-widget-content">
                    <div class="drag-column-header">
                        <a href="{{ route('clientes.show', $cliente->id) }}" target="_blank" class="text-decoration-none text-dark fw-bold">
                            {{ $cliente->company ?? $cliente->name }}
                        </a>
                        @if (Auth::user()->archivedClients->contains($cliente->id))
                            <form action="{{ route('cliente.desarchivar', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Desarchivar cliente?');">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-success w-100 mt-2">♻️ Desarchivar cliente</button>
                            </form>
                        @else
                            <form action="{{ route('cliente.archivar', $cliente->id) }}" method="POST" onsubmit="return confirm('¿Archivar cliente?');">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-info w-100 mt-2">📦 Archivar cliente</button>
                            </form>
                        @endif

                    </div>
                    <div class="drag-column-content">
                        @foreach ($cliente->presupuestos as $presupuesto)
                            <div class="drag-item" data-toggle="modal" data-target="#modalPresupuesto-{{ $presupuesto->id }}">
                                <p>{{ $presupuesto->reference }}</p>
                                <p>
                                    <span class="status-indicator" style="background-color: {{ $presupuesto->getStatusColor() }}"></span>
                                    {{ optional($presupuesto->estadoPresupuesto)->name }}
                                </p>
                                <p class="mb-0 text-muted" style="font-size: 13px;">
                                    📝 {{ $presupuesto->tasks_count ?? $presupuesto->tasks->count() }} tarea{{ ($presupuesto->tasks_count ?? $presupuesto->tasks->count()) == 1 ? '' : 's' }}
                                </p>
                            </div>

                            <!-- Botón fuera del drag-item -->
                            <div class="text-center mb-3">
                                <a href="{{ route('presupuesto.edit', $presupuesto->id) }}" target="_blank" class="btn btn-secondary btn-sm w-100">
                                    ✏️ Editar presupuesto
                                </a>
                                @if (!$presupuesto->archivado)
                                    <form action="{{ route('presupuesto.archivar', $presupuesto->id) }}" method="POST" onsubmit="return confirm('¿Archivar este presupuesto?');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-warning w-100 mt-1">📦 Archivar</button>
                                    </form>
                                @endif
                                @if ($presupuesto->archivado)
                                    <form action="{{ route('presupuesto.desarchivar', $presupuesto->id) }}" method="POST" onsubmit="return confirm('¿Desarchivar este presupuesto?');">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-info w-100 mt-1">♻️ Desarchivar</button>
                                    </form>
                                @endif

                            </div>


                            <!-- Modal estilo Trello -->
                            <div class="modal fade" id="modalPresupuesto-{{ $presupuesto->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $presupuesto->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header bg-dark text-white">
                                            <h5 class="modal-title" id="modalLabel-{{ $presupuesto->id }}">{{ $presupuesto->cliente->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">

                                            <!-- Referencia -->
                                            <div class="mb-4">
                                                <h6><i class="bi bi-info-circle"></i> Referencia</h6>
                                                <p class="text-muted">{{ $presupuesto->reference ?? 'Sin Referencia' }}</p>
                                            </div>
                                            <!-- Descripción -->
                                            <div class="mb-4">
                                                <h6><i class="bi bi-info-circle"></i> Descripción</h6>
                                                <p class="text-muted">{{ $presupuesto->description ?? 'Sin descripción' }}</p>
                                            </div>
                                            @php
                                                $puedeAceptar = $presupuesto->budget_status_id == 2;
                                                $tieneTareaMaestra = $presupuesto->tasks->whereNull('split_master_task_id')->isNotEmpty();
                                                $puedeGenerarTarea = !$tieneTareaMaestra && !in_array($presupuesto->budget_status_id, [1, 2, 4, 5, 8]);
                                            @endphp

                                            <div class="mb-3 d-flex gap-2">
                                                @if ($puedeAceptar)
                                                    <form action="{{ route('presupuesto.aceptarPresupuesto') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $presupuesto->id }}">
                                                        <button type="submit" class="btn btn-success btn-sm">Aceptar</button>
                                                    </form>
                                                @endif

                                                @if ($puedeGenerarTarea)
                                                    <form action="{{ route('presupuesto.generarTarea') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id" value="{{ $presupuesto->id }}">
                                                        <button type="submit" class="btn btn-dark btn-sm">Generar tareas</button>
                                                    </form>
                                                @endif
                                            </div>


                                            <!-- Comentarios (si los tienes en un modelo o tabla relacionada) -->
                                            @if ($presupuesto->comentarios ?? false)
                                                <div class="mb-4">
                                                    <h6><i class="bi bi-chat-dots"></i> Comentarios</h6>
                                                    @foreach ($presupuesto->comentarios as $comentario)
                                                        <div class="border rounded p-2 mb-2">
                                                            <strong>{{ $comentario->autor->name ?? 'Anónimo' }}:</strong>
                                                            <p>{{ $comentario->texto }}</p>
                                                            <small class="text-muted">{{ $comentario->created_at->diffForHumans() }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Tareas asociadas -->
                                            @php
                                                $maestras = $presupuesto->tasks->whereNull('split_master_task_id');
                                            @endphp

                                            @forelse ($maestras as $maestra)
                                                <div class="border rounded p-2 mb-3">
                                                    <a href="{{ route('tarea.edit', $maestra->id) }}" target="_blank" class="text-decoration-none text-dark">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <strong>{{ $maestra->title }}</strong>
                                                            <span class="badge rounded-pill {{ getTaskStatusClass($maestra->estado->id ?? null) }}">
                                                                {{ $maestra->estado->name ?? 'Sin estado' }}
                                                            </span>
                                                        </div>
                                                        <small class="text-muted">Tiempo estimado: {{ $maestra->estimated_time ?? '00:00:00' }}</small>
                                                    </a>

                                                    <!-- Subtareas -->
                                                    @php
                                                        $subtareas = $presupuesto->tasks->where('split_master_task_id', $maestra->id);
                                                    @endphp
                                                    @if ($subtareas->isNotEmpty())
                                                        <div class="mt-2 ms-3 border-start ps-3">
                                                            @foreach ($subtareas as $sub)
                                                                <div class="mb-2">
                                                                    <a href="{{ route('tarea.edit', $sub->id) }}" target="_blank" class="text-decoration-none text-secondary">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <span>{{ $sub->title }}</span>
                                                                            <span class="badge rounded-pill {{ getTaskStatusClass($sub->estado->id ?? null) }}">
                                                                                {{ $sub->estado->name ?? 'Sin estado' }}
                                                                            </span>
                                                                        </div>
                                                                        <small class="text-muted">👤 {{ $sub->usuario->name ?? 'Sin asignar' }} / </small>
                                                                        <small class="text-muted">Tiempo estimado: {{ $sub->estimated_time ?? '00:00:00' }} - </small>
                                                                        <small class="text-muted">Tiempo gastado: {{ $sub->real_time ?? '00:00:00' }}</small>

                                                                    </a>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @empty
                                                <p class="text-muted">No hay tareas asociadas.</p>
                                            @endforelse
                                            <hr class="mt-3 mb-4">

                                            <!-- Descripción editable del proyecto -->
                                            <form method="POST" action="{{ route('presupuesto.descripcion', $presupuesto->id) }}" class="mb-3">
                                                @csrf
                                                <div class="mb-2">
                                                    <label for="descripcionProyecto-{{ $presupuesto->id }}" class="form-label fw-bold">
                                                        📝 Descripción del proyecto
                                                    </label>
                                                    <textarea class="form-control" name="project_description" id="descripcionProyecto-{{ $presupuesto->id }}" rows="3" placeholder="Escribe la descripción del proyecto...">{{ old('project_description', $presupuesto->project_description) }}</textarea>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary">Guardar descripción</button>
                                            </form>

                                            <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Comentarios y Actividad</h6>

                                            <form method="POST" action="{{ route('presupuesto.comentario.store') }}">
                                                @csrf
                                                <input type="hidden" name="presupuesto_id" value="{{ $presupuesto->id }}">
                                                <textarea name="comentario" class="form-control mb-2" rows="2" placeholder="Escribe un comentario..."></textarea>
                                                <button class="btn btn-sm btn-dark" type="submit">Comentar</button>
                                            </form>


                                            <div class="mt-3">
                                                @foreach ($presupuesto->comentariosExtra->sortByDesc('created_at') as $comentario)
                                                    <div class="border rounded p-2 mb-2 bg-light d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <strong>{{ $comentario->user->name }}</strong>
                                                            <span class="text-muted small">— {{ $comentario->created_at->diffForHumans() }}</span>
                                                            <p class="mb-0">{{ $comentario->comentario }}</p>
                                                        </div>

                                                        @if ($comentario->user_id === Auth::id())
                                                            <form action="{{ route('presupuesto.comentario.destroy', $comentario->id) }}" method="POST" class="ms-2">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar comentario" onclick="return confirm('¿Seguro que quieres eliminar este comentario?')">
                                                                    🗑️
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        @endforeach
                    </div>
                </div>

            @endforeach
        </div>
    </div>
    @php
    function getTaskStatusClass($statusId) {
        return match($statusId) {
            1 => 'bg-success text-white',     // Reanudada
            2 => 'bg-warning text-dark',      // Pausada
            3 => 'bg-secondary text-white',   // Finalizada
            4 => 'bg-info text-dark',         // Programada
            5 => 'bg-danger text-white',      // Revisión
            default => 'bg-light text-dark'
        };
    }
@endphp


</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sortable-container').sortable({
            axis: 'x', // Only allow horizontal dragging
            containment: 'parent' // Contain within the parent element
        });
        $('#sortable-container').disableSelection();

        $('[data-toggle="modal"]').on('click', function() {
            var modalId = $(this).data('target');
            $(modalId).modal('show');
        });
    });


</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fakeScrollBar = document.querySelector('.scroll-header-bar');
        const fakeTrack = document.querySelector('#fakeScrollTrack');
        const realScrollWrapper = document.querySelector('#scrollRealWrapper');
        const dragContainer = document.querySelector('#sortable-container');

        if (!fakeScrollBar || !fakeTrack || !realScrollWrapper || !dragContainer) return;

        // Establece el ancho del track falso igual al del contenido real
        function updateFakeTrackWidth() {
            fakeTrack.style.width = `${dragContainer.scrollWidth}px`;
        }

        updateFakeTrackWidth();
        window.addEventListener('resize', updateFakeTrackWidth);

        // Sincronizar scroll
        fakeScrollBar.addEventListener('scroll', () => {
            realScrollWrapper.scrollLeft = fakeScrollBar.scrollLeft;
        });

        realScrollWrapper.addEventListener('scroll', () => {
            fakeScrollBar.scrollLeft = realScrollWrapper.scrollLeft;
        });
    });
</script>



@endsection
