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
                        <td>{{ $campania->fecha_lanzamiento ?? 'Sin lanzar' }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal"
                                data-bs-target="#clientesModal{{ $campania->id }}">
                                Ver Clientes
                            </button>
                        </td>
                        <td>
                            @switch($campania->estado)
                                @case(0) Pendiente @break
                                @case(1) Aceptada @break
                                @case(2) Rechazada @break
                                @case(3) Enviada @break
                            @endswitch
                        </td>
                        <td>
                            <button class="btn btn-primary send-campania" data-id="{{ $campania->id }}"
                                {{ $campania->estado == 0 || $campania->estado == 2 ? 'disabled' : '' }}>
                                Lanzar campaña
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- MODALES DE CLIENTES -->
@foreach ($campanias as $campania)
<div class="modal fade" id="clientesModal{{ $campania->id }}" tabindex="-1" aria-labelledby="clientesModalLabel{{ $campania->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientesModalLabel{{ $campania->id }}">Clientes de la campaña: {{ $campania->nombre }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="list-group">
                    @foreach ($campania->clientes as $clienteId)
                        @php
                            $cliente = $clients->firstWhere('id', $clienteId);
                        @endphp
                        @if($cliente)
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

<!-- MODAL NUEVA CAMPAÑA -->
<div class="modal fade" id="newCampaniaModal" tabindex="-1" aria-labelledby="newCampaniaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Campaña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="campaniaForm">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la campaña</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="plantilla" class="form-label">Plantilla</label>
                        <select id="plantilla" name="plantilla" class="form-control">
                            @foreach ($templates as $template)
                                <option style="color: #000;" value="{{ $template->id }}">{{ $template->nombre }} - {{ $template->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="clientes" class="form-label">Clientes</label>
                        <div class="mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllClients">
                                Seleccionar todos
                            </button>
                        </div>
                        <div class="border p-3" style="max-height: 200px; overflow-y: auto;">
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
@endsection

@section('scripts')
<!-- DEPENDENCIAS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

<!-- SCRIPT COMPLETO -->
<script>
$(document).ready(function () {
    // TOAST CONFIG
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

    // SELECCIONAR / DESELECCIONAR TODOS
    $('#selectAllClients').on('click', function () {
        const total = $('.client-checkbox').length;
        const checked = $('.client-checkbox:checked').length;
        const selectAll = checked !== total;

        $('.client-checkbox').prop('checked', selectAll);
        $(this).text(selectAll ? 'Deseleccionar todos' : 'Seleccionar todos');
    });

    // RESET MODAL AL CERRAR
    $('#newCampaniaModal').on('hidden.bs.modal', function () {
        $('.client-checkbox').prop('checked', false);
        $('#selectAllClients').text('Seleccionar todos');
        $('#campaniaForm')[0].reset();
        $('body').removeClass('modal-open');
        $('.modal-backdrop').remove();
        $('body').css('overflow', '');
        $('body').css('padding-right', '');
    });

    // GUARDAR CAMPAÑA
    $('#saveCampania').on('click', function () {
        const nombre = $('#nombre').val();
        const plantilla = $('#plantilla').val();
        const clientes = $('.client-checkbox:checked').map(function () {
            return $(this).val();
        }).get();

        $.ajax({
            url: '{{ route('plataforma.createCampania') }}',
            method: 'POST',
            data: {
                nombre,
                plantilla,
                clientes,
                _token: '{{ csrf_token() }}'
            },
            success: function (response) {
                if (response.success) {
                    $('#newCampaniaModal').modal('hide');
                    Toast.fire({ icon: 'success', title: 'Campaña creada correctamente' });
                    setTimeout(() => location.reload(), 1000);
                } else {
                    Toast.fire({ icon: 'error', title: response.message || 'Error al crear la campaña' });
                }
            },
            error: function () {
                Toast.fire({ icon: 'error', title: 'Error al enviar los datos al servidor' });
            }
        });
    });
});
</script>
@endsection