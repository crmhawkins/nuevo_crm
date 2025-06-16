@extends('layouts.appWhatsapp')

@section('titulo', 'Logs')

<style>
    .sortable {
        cursor: pointer;
    }

    .sort-icon {
        font-size: 12px;
        margin-left: 5px;
    }

    .css-96uzu9 {
        z-index: -1 !important;
    }
</style>

@section('content')
    <!-- Contenido principal -->
    <div class="col-md-10 p-4 bg-white rounded">
        <div class="d-flex justify-content-between mb-4 ">
            <h2 class="mb-0">Logs de WhatsApp</h2>
            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                Recargar logs
            </button>
        </div>

        <div class="table-responsive">
            <table id="logsTable" class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">ID <span class="sort-icon">↕</span></th>
                        <th class="sortable" data-column="type">Tipo <span class="sort-icon">↕</span></th>
                        <th>Mensaje</th>
                        <th>Respuesta</th>
                        <th class="sortable" data-column="created_at">Fecha <span class="sort-icon">↕</span></th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                <span class="badge bg-{{ $log->type == 1 ? 'success' : ($log->type == 2 ? 'primary' : ($log->type == 3 ? 'warning' : ($log->type == 4 ? 'danger' : 'danger'))) }}">
                                    @if($log->type == 1)
                                        Enviado
                                    @elseif($log->type == 2)
                                        No enviado
                                    @elseif($log->type == 3)
                                        Respuesta recibida
                                    @elseif($log->type == 4)
                                        Error
                                    @else
                                        Desconocido
                                    @endif
                                </span>
                            </td>
                            <td>{{ Str::limit($log->message, 50) }}</td>
                            <td>{{ Str::limit($log->response, 50) }}</td>
                            <td>{{ $log->formatted_created_at }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                    Ver Detalles
                                </button>

                                <!-- Modal para ver log -->
                                <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-labelledby="logModalLabel{{ $log->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="logModalLabel{{ $log->id }}">Detalles del Log</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <strong>ID:</strong> {{ $log->id }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Tipo:</strong> <span class="badge bg-{{ $log->type == 1 ? 'primary' : ($log->type == 2 ? 'success' : ($log->type == 3 ? 'warning' : ($log->type == 4 ? 'info' : 'danger'))) }}">
                                                            @if($log->type == 1)
                                                                Enviado
                                                            @elseif($log->type == 2)
                                                                No enviado
                                                            @elseif($log->type == 3)
                                                                Respuesta recibida
                                                            @elseif($log->type == 4)
                                                                Error
                                                            @else
                                                                Desconocido
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <strong>Mensaje:</strong><br>
                                                        {{ $log->message }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <strong>Respuesta:</strong><br>
                                                        {{ $log->response ?? 'Sin respuesta' }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <strong>Fecha:</strong> {{ $log->formatted_created_at }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <strong>Categorías:</strong><br>
                                                        <div id="categoryList{{ $log->id }}" class="mt-2">
                                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                                <span class="visually-hidden">Cargando...</span>
                                                            </div>
                                                            <span class="ms-2">Cargando...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <strong>Clientes:</strong>
                                                        <div id="clientsList{{ $log->id }}" class="mt-2">
                                                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                                <span class="visually-hidden">Cargando...</span>
                                                            </div>
                                                            <span class="ms-2">Cargando...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const modal = document.getElementById('logModal{{ $log->id }}');
                                        modal.addEventListener('show.bs.modal', function() {
                                            fetch('/plataforma/logs/client', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                                },
                                                body: JSON.stringify({
                                                    client_id: {{ json_encode($log->clients) }},
                                                    campania_id: {{ $log->id_campania ?? 'null' }}
                                                })
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                const clientsContainer = document.getElementById('clientsList{{ $log->id }}');
                                                const categoryContainer = document.getElementById('categoryList{{ $log->id }}');
                                                clientsContainer.innerHTML = '';
                                                categoryContainer.innerHTML = '';

                                                if (data.success) {
                                                    if (data.data.categoria) {
                                                        categoryContainer.innerHTML = `<span class="badge bg-secondary">${data.data.categoria}</span>`;
                                                    } else {
                                                        categoryContainer.innerHTML = '<span class="badge bg-secondary">Sin categoría</span>';
                                                    }

                                                    if (Array.isArray(data.data.clients)) {
                                                        const clientsList = document.createElement('div');
                                                        clientsList.className = 'row g-2';

                                                        data.data.clients.forEach(client => {
                                                            const clientBadge = document.createElement('div');
                                                            clientBadge.className = 'col-auto';
                                                            clientBadge.innerHTML = `<span class="badge bg-light text-dark border">${client || 'Cliente desconocido'}</span>`;
                                                            clientsList.appendChild(clientBadge);
                                                        });

                                                        clientsContainer.appendChild(clientsList);
                                                    } else {
                                                        clientsContainer.innerHTML = '<div class="text-danger">Error al cargar clientes</div>';
                                                    }
                                                } else {
                                                    clientsContainer.innerHTML = '<div class="text-danger">Error al cargar clientes</div>';
                                                }
                                            })
                                            .catch(error => {
                                                document.getElementById('clientsList{{ $log->id }}').innerHTML =
                                                    `<div class="text-danger">Error al cargar clientes: ${error.message}</div>`;
                                            });
                                        });
                                    });
                                </script>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            <div class="d-flex justify-content-center">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('logsTable');
            const tbody = table.getElementsByTagName('tbody')[0];

            // Handle sorting
            const sortableHeaders = document.querySelectorAll('.sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.dataset.column;
                    const rows = Array.from(tbody.getElementsByTagName('tr'));
                    const direction = this.classList.contains('asc') ? -1 : 1;

                    // Update sort direction
                    sortableHeaders.forEach(h => h.classList.remove('asc', 'desc'));
                    this.classList.add(direction === 1 ? 'asc' : 'desc');

                    // Sort rows
                    rows.sort((a, b) => {
                        const aValue = a.cells[this.cellIndex].textContent;
                        const bValue = b.cells[this.cellIndex].textContent;

                        if (column === 'id') {
                            return direction * (parseInt(aValue) - parseInt(bValue));
                        }
                        if (column === 'created_at') {
                            return direction * new Date(aValue) - new Date(bValue);
                        }
                        return direction * aValue.localeCompare(bValue);
                    });

                    // Reorder rows in the table
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
    </script>
@endsection
