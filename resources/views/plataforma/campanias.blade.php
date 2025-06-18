@extends('layouts.appWhatsapp')

@section('titulo', 'Acciones')

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
    <div class="col-md-10 p-4 bg-white rounded">
        <div class="d-flex justify-content-between mb-4">
            <h2 class="mb-0">Acciones</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCampaniaModal">
                Nueva acción
            </button>
        </div>

        <div class="table-responsive">
            <table id="contactsTable" class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha de último lanzamiento</th>
                        <th>Clientes</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($campanias as $campania)
                        <tr>
                            <td>{{ $campania->id }}</td>
                            <td>{{ $campania->nombre }}</td>
                            <td>{{ $campania->fecha_lanzamiento ?? 'Sin lanzar' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#clientesModal{{ $campania->id }}">
                                    Ver Clientes
                                </button>
                            </td>
                            <td>
                                @switch($campania->estado)
                                    @case(0)
                                        Pendiente
                                    @break

                                    @case(1)
                                        Aceptada
                                    @break

                                    @case(2)
                                        Rechazada
                                    @break

                                    @case(3)
                                        Enviada
                                    @break
                                @endswitch
                            </td>
                            <td>
                                <button class="btn btn-primary send-campania" data-id="{{ $campania->id }}"
                                    {{ $campania->estado == 0 || $campania->estado == 2 ? 'disabled' : '' }}>
                                    Ejecutar acción
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODALES CLIENTES -->
    @foreach ($campanias as $campania)
        <div class="modal fade" id="clientesModal{{ $campania->id }}" tabindex="-1"
            aria-labelledby="clientesModalLabel{{ $campania->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Clientes de la acción: {{ $campania->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group">
                            @foreach ($campania->clientes as $clienteId)
                                @php
                                    $cliente =
                                        $clients->firstWhere('id', $clienteId) ??
                                        $whatsapp_clients->firstWhere('id', $clienteId);
                                @endphp
                                @if ($cliente)
                                    <div class="list-group-item">
                                        <h6 class="mb-1">{{ $cliente->name }}</h6>
                                        <small class="text-muted">{{ $cliente->phone }}</small>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- MODAL NUEVA ACCIÓN -->
    <div class="modal fade" id="newCampaniaModal" tabindex="-1" aria-labelledby="newCampaniaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nueva acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="campaniaForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la acción</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="plantilla" class="form-label">Plantilla</label>
                            <select id="plantilla" name="plantilla" class="form-control">
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->nombre }} - {{ $template->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_contacto" class="form-label">Origen de clientes</label>
                            <select id="tipo_contacto" class="form-control">
                                <option value="clientes" selected>Clientes del sistema</option>
                                <option value="whatsapp">Contactos de WhatsApp</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllClients">
                                Seleccionar todos
                            </button>
                        </div>

                        <div id="clientes-container" class="border p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($clients as $client)
                                <div class="form-check">
                                    <input class="form-check-input client-checkbox" type="checkbox" name="clientes[]"
                                        value="{{ $client->id }}" id="cliente{{ $client->id }}">
                                    <label class="form-check-label" for="cliente{{ $client->id }}">
                                        {{ $client->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div id="whatsapp-container" class="border p-3"
                            style="max-height: 200px; overflow-y: auto; display: none;">
                            @foreach ($whatsapp_clients as $contact)
                                <div class="form-check">
                                    <input class="form-check-input client-checkbox" type="checkbox" name="clientes[]"
                                        value="{{ $contact->wid }}" id="whatsapp{{ $contact->wid }}">
                                    <label class="form-check-label" for="whatsapp{{ $contact->wid }}">
                                        {{ $contact->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveCampania">Guardar acción</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            // Cambiar origen de clientes
            $('#tipo_contacto').on('change', function() {
                const tipo = $(this).val();
                if (tipo === 'clientes') {
                    $('#clientes-container').show();
                    $('#whatsapp-container').hide();
                } else {
                    $('#clientes-container').hide();
                    $('#whatsapp-container').show();
                }

                $('.client-checkbox').prop('checked', false);
                $('#selectAllClients').text('Seleccionar todos');
            });

            // Seleccionar/deseleccionar todos
            $('#selectAllClients').on('click', function() {
                const visibleCheckboxes = $('.client-checkbox:visible');
                const total = visibleCheckboxes.length;
                const checked = visibleCheckboxes.filter(':checked').length;
                const selectAll = checked !== total;

                visibleCheckboxes.prop('checked', selectAll);
                $(this).text(selectAll ? 'Deseleccionar todos' : 'Seleccionar todos');
            });

            // Reset modal
            $('#newCampaniaModal').on('hidden.bs.modal', function() {
                $('.client-checkbox').prop('checked', false);
                $('#selectAllClients').text('Seleccionar todos');
                $('#campaniaForm')[0].reset();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $('body').css('overflow', '').css('padding-right', '');
                $('#clientes-container').show();
                $('#whatsapp-container').hide();
            });

            // Guardar nueva campaña
            $('#saveCampania').on('click', function() {
                const nombre = $('#nombre').val();
                const plantilla = $('#plantilla').val();
                const clientes = $('.client-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    url: '{{ route('plataforma.createCampania') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        nombre,
                        plantilla,
                        clientes,
                        _token: '{{ csrf_token() }}',

                    },
                    success: function(response) {
                        if (response.success) {
                            $('#newCampaniaModal').modal('hide');
                            Toast.fire({
                                icon: 'success',
                                title: 'Acción creada correctamente'
                            });
                            setTimeout(() => location.reload(), 1000);
                        } else {

                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Error al crear la acción'
                            });
                        }
                    },
                    error: function(e) {
                        console.log(e)
                        Toast.fire({
                            icon: 'error',
                            title: 'Error al enviar los datos al servidor'
                        });
                    }
                });
            });

            // Ejecutar acción
            $(document).on('click', '.send-campania', function(e) {
                e.preventDefault();
                const button = $(this);
                const id = button.data('id');
                const original = button.html();

                if (button.prop('disabled')) return;

                button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Ejecutando...');

                $.ajax({
                    url: '/plataforma/send-campania',
                    method: 'POST',
                    data: {
                        campania_id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });

                            const row = button.closest('tr');
                            const estadoCell = row.find('td:eq(4)');
                            const fechaCell = row.find('td:eq(2)');

                            let estado = 'Desconocido';
                            switch (response.data.estado) {
                                case 0:
                                    estado = 'Pendiente';
                                    break;
                                case 1:
                                    estado = 'Aceptada';
                                    break;
                                case 2:
                                    estado = 'Rechazada';
                                    break;
                                case 3:
                                    estado = 'Enviada';
                                    break;
                            }

                            estadoCell.text(estado);
                            fechaCell.text(response.data.fecha_lanzamiento);
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Error al ejecutar la acción'
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Error en la solicitud al servidor'
                        });
                    },
                    complete: function() {
                        button.prop('disabled', false).html(original);
                    }
                });
            });
        });
    </script>
@endsection
