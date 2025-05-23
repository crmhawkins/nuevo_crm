@extends('layouts.appWhatsapp')

@section('titulo', 'Campañas')

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
            <h2 class="mb-0">Campañas</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newCampaniaModal">
                Nueva campaña
            </button>
        </div>

        <div class="table-responsive">
            <table id="contactsTable" class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">ID <span class="sort-icon">↕</span></th>
                        <th class="sortable" data-column="name">Nombre <span class="sort-icon">↕</span></th>
                        <th>Fecha de ultimo lanzamiento</th>
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
                            <td>{{ $campania->fecha_lanzamiento ? $campania->fecha_lanzamiento : 'Sin lanzar' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#clientesModal{{ $campania->id }}">
                                    Ver Clientes
                                </button>
                            </td>
                            <td>
                                @if ($campania->estado == 0)
                                    Pendiente
                                @elseif($campania->estado == 1)
                                    Aceptada
                                @elseif($campania->estado == 2)
                                    Rechazada
                                @elseif($campania->estado == 3)
                                    Enviada
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary send-campania" data-id="{{ $campania->id }}"
                                    {{ $campania->estado == 0 || $campania->estado == 2 ? 'disabled' : '' }}>
                                    Lanzar campaña
                                </button>
                                <button class="btn btn-secondary schedule-campania" data-id="{{ $campania->id }}">
                                    Programar campaña
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Remove Laravel pagination -->
            {{-- <div class="d-flex justify-content-center">
                {{ $campanias->links('pagination::bootstrap-5') }}
            </div> --}}
        </div>
    </div>

    <!-- Modal para crear campaña -->
    <div class="modal fade" id="newCampaniaModal" tabindex="-1" aria-labelledby="newCampaniaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newCampaniaModalLabel">Nueva Campaña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="campaniaForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la campaña</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="mensaje" class="form-label">Plantilla</label>
                            <select id="plantilla" name="plantilla" class="form-control">
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="clientes" class="form-label">Clientes</label>
                            <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach ($clients as $client)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="clientes[]"
                                            value="{{ $client->id }}" id="cliente{{ $client->id }}">
                                        <label class="form-check-label" for="cliente{{ $client->id }}">
                                            {{ $client->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveCampania">Guardar campaña</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales para mostrar campañas -->
    @foreach ($campanias as $campania)
        <div class="modal fade" id="clientesModal{{ $campania->id }}" tabindex="-1"
            aria-labelledby="clientesModalLabel{{ $campania->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="clientesModalLabel{{ $campania->id }}">Clientes de la campaña:
                            {{ $campania->nombre }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Teléfono</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $clients = $campania->clientes ?? [];
                                    @endphp
                                    @if (count($clients) > 0)
                                        @foreach ($clients as $clientId)
                                            @php
                                                $cliente = App\Models\Clients\Client::find($clientId);
                                            @endphp
                                            @if ($cliente)
                                                <tr>
                                                    <td>{{ $cliente->id }}</td>
                                                    <td>{{ $cliente->name }}</td>
                                                    <td>{{ $cliente->phone }}</td>
                                                    <td></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center">No hay clientes asignados a esta campaña
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal para programar campaña -->
    <div class="modal fade" id="scheduleCampaniaModal" tabindex="-1" aria-labelledby="scheduleCampaniaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleCampaniaModalLabel">Programar Campaña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="scheduleCampaniaForm">
                        <div class="mb-3">
                            <label for="scheduleDate" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="scheduleDate" name="scheduleDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="scheduleTime" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="scheduleTime" name="scheduleTime" required>
                        </div>
                        <input type="hidden" id="scheduleCampaniaId" name="scheduleCampaniaId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveScheduleCampania">Programar campaña</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css"
        rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js">
    </script>

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Configure Toast
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Configure toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut",
                "iconClasses": {
                    error: 'toast-error',
                    info: 'toast-info',
                    success: 'toast-success',
                    warning: 'toast-warning'
                }
            };

            // Add custom styles for toasts
            $('<style>')
                .text(`
                    .toast {
                        background-color: #fff;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    }
                    .toast-success {
                        background-color: #d4edda !important;
                        border-left: 4px solid #28a745;
                    }
                    .toast-error {
                        background-color: #f8d7da !important;
                        border-left: 4px solid #dc3545;
                    }
                    .toast-info {
                        background-color: #d1ecf1 !important;
                        border-left: 4px solid #17a2b8;
                    }
                    .toast-warning {
                        background-color: #fff3cd !important;
                        border-left: 4px solid #ffc107;
                    }
                    .toast-message {
                        color: #333;
                        font-weight: 500;
                    }
                    .toast-close-button {
                        color: #666;
                        opacity: 0.8;
                    }
                    .toast-close-button:hover {
                        opacity: 1;
                    }
                    .toast-progress {
                        background-color: rgba(0,0,0,0.1);
                    }
                `)
                .appendTo('head');

            // Initialize DataTable
            var table = $('#contactsTable').DataTable({
                processing: true,
                serverSide: false,
                pageLength: 25,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Todos"]
                ],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    lengthMenu: "Mostrar _MENU_ registros por página",
                    zeroRecords: "No se encontraron registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    infoEmpty: "Mostrando 0 a 0 de 0 registros",
                    infoFiltered: "(filtrado de _MAX_ registros totales)",
                    search: "Buscar:",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'nombre'
                    },
                    {
                        data: 'fecha_lanzamiento'
                    },
                    {
                        data: 'clientes'
                    },
                    {
                        data: 'estado',
                        render: function(data, type, row) {
                            let estado = '';
                            let badgeClass = '';

                            // Remove parseInt since we're comparing strings
                            const estadoValue = data;

                            if (estadoValue === 'Pendiente') {
                                estado = 'Pendiente';
                                badgeClass = 'bg-warning';
                            } else if (estadoValue === 'Aceptada') {
                                estado = 'Aceptada';
                                badgeClass = 'bg-info';
                            } else if (estadoValue === 'Rechazada') {
                                estado = 'Rechazada';
                                badgeClass = 'bg-danger';
                            } else if (estadoValue === 'Enviada') {
                                estado = 'Enviada';
                                badgeClass = 'bg-success';
                            } else {
                                estado = 'Sin estado';
                                badgeClass = 'bg-secondary';
                            }

                            return '<span class="badge ' + badgeClass + '">' + estado + '</span>';
                        }
                    },
                    {
                        data: 'acciones'
                    }
                ],
                columnDefs: [{
                        targets: [3],
                        render: function(data, type, row) {
                            return '<button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#clientesModal' +
                                row.id + '">Ver Clientes</button>';
                        }
                    },
                    {
                        targets: [5],
                        render: function(data, type, row) {
                            const estadoValue = row.estado;
                            const isDisabled = estadoValue === 'Pendiente' || estadoValue ===
                                'Rechazada';
                            return '<button class="btn btn-primary send-campania" data-id="' + row
                                .id + '" ' + (isDisabled ? 'disabled' : '') +
                                '>Lanzar campaña</button>' +
                                '<button class="btn btn-secondary ' + (isDisabled ? 'disabled' : '') +
                                ' schedule-campania" data-id="' + row.id +
                                '" data-bs-toggle="modal" data-bs-target="#scheduleCampaniaModal">Programar campaña</button>';
                        }
                    }
                ],
                drawCallback: function() {
                    initializeEventHandlers();
                }
            });

            // Function to initialize event handlers
            function initializeEventHandlers() {
                // Remove any existing click handlers to prevent duplicates
                $(document).off('click', '.send-campania');

                // Handle send campaign button clicks
                $(document).on('click', '.send-campania', function(e) {
                    e.preventDefault();
                    const button = $(this);

                    // Prevent multiple clicks
                    if (button.prop('disabled')) {
                        return;
                    }

                    const campaniaId = button.data('id');

                    // Disable button while processing
                    button.prop('disabled', true);
                    button.html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...'
                    );

                    $.ajax({
                        url: '/plataforma/send-campania',
                        type: 'POST',
                        data: {
                            campania_id: campaniaId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response);
                            if (response.success) {
                                // Show success toast
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });

                                // Update the row in the table
                                const row = button.closest('tr');
                                const estadoCell = row.find('td:eq(4)');
                                const fechaCell = row.find('td:eq(2)');

                                // Update estado cell with data from server
                                let estado = '';
                                let badgeClass = '';

                                switch (response.data.estado) {
                                    case 0:
                                        estado = 'Pendiente';
                                        badgeClass = 'bg-warning';
                                        break;
                                    case 1:
                                        estado = 'Aceptada';
                                        badgeClass = 'bg-info';
                                        break;
                                    case 2:
                                        estado = 'Rechazada';
                                        badgeClass = 'bg-danger';
                                        break;
                                    case 3:
                                        estado = 'Enviada';
                                        badgeClass = 'bg-success';
                                        break;
                                    default:
                                        estado = 'Sin estado';
                                        badgeClass = 'bg-secondary';
                                }

                                estadoCell.html('<span class="badge ' + badgeClass + '">' +
                                    estado + '</span>');

                                // Update fecha_lanzamiento with data from server
                                fechaCell.text(response.data.fecha_lanzamiento);

                                // Reset button state
                                button.prop('disabled', false);
                                button.html('Lanzar campaña');
                            } else {
                                // Show error toast
                                Toast.fire({
                                    icon: "error",
                                    title: "Error al lanzar la campaña!"
                                });
                                button.prop('disabled', false);
                                button.html('Lanzar campaña');
                            }
                        },
                        error: function(xhr) {
                            console.log('Error response:', xhr);
                            // Show error toast
                            Toast.fire({
                                icon: "error",
                                title: "Error al lanzar la campaña!"
                            });
                            button.prop('disabled', false);
                            button.html('Lanzar campaña');
                        }
                    });
                });

                // Handle client modal buttons
                $('[id^="clientesModal"]').on('shown.bs.modal', function() {
                    var table = $(this).find('table');
                    if (!$.fn.DataTable.isDataTable(table)) {
                        table.DataTable({
                            language: {
                                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                            },
                            pageLength: 10,
                            order: [
                                [0, 'asc']
                            ],
                            searching: false,
                            lengthChange: false,
                            info: false,
                            columns: [{
                                    data: '0'
                                }, // ID
                                {
                                    data: '1'
                                }, // Nombre
                                {
                                    data: '2'
                                }, // Teléfono
                                {
                                    data: '3'
                                } // Estado
                            ],
                            columnDefs: [{
                                targets: '_all',
                                defaultContent: ''
                            }]
                        });
                    }
                });

                // Handle client modal close
                $('[id^="clientesModal"]').on('hidden.bs.modal', function() {
                    var table = $(this).find('table');
                    if ($.fn.DataTable.isDataTable(table)) {
                        table.DataTable().destroy();
                    }
                });

                // Handle schedule campaign button clicks
                $(document).on('click', '.schedule-campania', function() {
                    const campaniaId = $(this).data('id');
                    $('#scheduleCampaniaId').val(campaniaId);
                });

                // Save scheduled campaign
                $('#saveScheduleCampania').off('click').on('click', function() {
                    const campaniaId = $('#scheduleCampaniaId').val();
                    const scheduleDate = $('#scheduleDate').val();
                    const scheduleTime = $('#scheduleTime').val();

                    // Validate inputs
                    if (!campaniaId || !scheduleDate || !scheduleTime) {
                        alert('Por favor, complete todos los campos.');
                        return;
                    }

                    $.ajax({
                        url: '/plataforma/programar-campania',
                        type: 'POST',
                        data: {
                            campania_id: campaniaId,
                            fecha: scheduleDate,
                            hora: scheduleTime,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Hide the modal
                                // Cerrar correctamente el modal y limpiar el DOM
                                $('#scheduleCampaniaModal').modal('hide');
                                setTimeout(() => {
                                    $('body').removeClass('modal-open');
                                    $('.modal-backdrop').remove();
                                    $('.modal').removeClass('show').attr('aria-hidden',
                                        'true').css('display', 'none');
                                }, 300);
                                // Close all modals
                                $('.modal').modal('hide');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();


                                // Optionally refresh the table or show a success message
                                Toast.fire({
                                    icon: "success",
                                    title: response.message
                                });
                            } else {
                                console.error('Error:', response.message);
                                Toast.fire({
                                    icon: "error",
                                    title: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr.responseText);
                            toastr.error('Error: ' + xhr.responseText);
                        }
                    });
                });
            }

            // Initialize event handlers on page load
            initializeEventHandlers();

            // Save campaign
            $('#saveCampania').click(function() {
                var nombre = $('#nombre').val();
                var plantilla = $('#plantilla').val();
                var clientes = [];

                // Get selected clients
                $('input[name="clientes[]"]:checked').each(function() {
                    clientes.push($(this).val());
                });

                $.ajax({
                    url: '{{ route('plataforma.createCampania') }}',
                    type: 'POST',
                    data: {
                        nombre: nombre,
                        plantilla: plantilla,
                        clientes: clientes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#newCampaniaModal').modal('hide');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                    }
                });
            });
        });
    </script>
@endsection
