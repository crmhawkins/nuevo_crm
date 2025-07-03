@extends('layouts.app')

@section('titulo', 'Correos')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
<style>
    #emailContextMenu {
        position: fixed;
        z-index: 1000;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important" >

    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-4 order-md-1 order-last">
                <h3><i class="fa-regular fa-envelope "></i> Correos</h3>
                <p class="text-subtitle text-muted">Listado de mis Emails</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Correos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4 ">
        <div class="card2">
            <div class="card-body mb-3">
                <div class="row justify-content-between">
                    <div class="col-md-4">
                        <a class="btn btn-primary" href="{{ route('admin.emails.create') }}">
                            <i class="fa-solid fa-plus me-1"></i> Nuevo Correo
                        </a>
                    </div>

                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.emails.index') }}" class="d-flex">
                            <div class="col-9">
                                <input type="text" name="search" class="form-control p-2" placeholder="Buscar por remitente o asunto" value="{{ request('search') }}">
                            </div>
                            <div class="col-3 d-flex justify-content-around">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-search "></i> Buscar
                                </button>
                                <a class="btn btn-primary" href="{{ route('admin.emails.getCorreos') }}">
                                    <i class="fa-solid fa-sync me-1"></i>
                                </a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <ul class="nav flex-column nav-tabs list-unstyled" id="emailTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" href="{{ route('admin.emails.index') }}">
                                    <i class="fa-solid fa-inbox me-2"></i> Bandeja de Entrada
                                </a>
                            </li>
                            @foreach ($categorias as $categoria)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $categoriaId == $categoria->id ? 'active' : '' }}"
                                href="{{ route('admin.emails.index', ['categoria_id' => $categoria->id]) }}">
                                    <i class="fa-solid fa-tag me-2"></i> {{ $categoria->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content" id="emailTabContent">
                            <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                                <div class="table-responsive mt-3">
                                    <form id="deleteMultipleForm" method="POST" action="{{ route('admin.emails.destroyMultiple') }}">
                                        @csrf
                                        <table class="table table-hover align-middle table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col">
                                                        <input type="checkbox" id="selectAll">
                                                    </th>
                                                    <th scope="col">Remitente</th>
                                                    <th scope="col">Asunto</th>
                                                    <th scope="col">Categoría</th>
                                                    <th scope="col">Estado</th>
                                                    <th scope="col">Fecha</th>
                                                    <th scope="col" class="text-end">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($emails as $email)
                                                <tr class="clickable-row" data-id="{{ $email->id }}" data-href="{{ route('admin.emails.show', $email->id) }}">
                                                    <td>
                                                        <input type="checkbox" name="ids[]" value="{{ $email->id }}" class="email-checkbox">
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 150px;">{{ $email->sender }}</td>
                                                    <td>{{ Str::limit($email->subject, 50) }}</td>
                                                    <td>{{ $email->category->name ?? 'Sin categoría' }}</td>
                                                    <td>
                                                        @php
                                                            switch ($email->status_id) {
                                                                case 2:
                                                                    $bg = 'secondary';
                                                                    break;
                                                                case 1:
                                                                    $bg = 'primary';
                                                                    break;
                                                                default:
                                                                    $bg = 'light';
                                                            }
                                                        @endphp

                                                        <span id="status-{{ $email->id }}"
                                                            class="badge bg-{{ $bg }}
                                                                    {{ in_array(optional($email->status)->name, ['light', 'info', 'warning']) ? 'text-dark' : '' }}">
                                                            {{ $email->status->name ?? 'Desconocido' }}
                                                        </span>

                                                    </td>
                                                    <td>{{ $email->created_at->format('d M Y, g:i A') }}</td>
                                                    <td class="text-end">
                                                        @if ($email->category_id != 6)
                                                        <a href="{{ route('admin.emails.reply', $email->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-reply"></i>
                                                        </a>
                                                        @endif
                                                        <a href="{{ route('admin.emails.forward', $email->id) }}" class="btn btn-sm btn-outline-primary">
                                                             <i class="fa-solid fa-share"></i>
                                                        </a>
                                                        <button data-id="{{$email->id}}" class="btn btn-sm btn-outline-danger delete">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No hay correos disponibles.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-danger mt-3" id="deleteSelectedButton">
                                            <i class="fa-solid fa-trash"></i> Eliminar Seleccionados
                                        </button>
                                    </form>
                                </div>
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $emails->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="emailContextMenu" class="dropdown-menu" style="display: none;">
        <a class="dropdown-item" href="#" id="changeStatusToNew">No Leído</a>
        <a class="dropdown-item" href="#" id="changeStatusToRead">Leído</a>
        <a class="dropdown-item" href="#" id="changeStatusToRealizado">Realizado</a>
        <a class="dropdown-item" href="#" id="changeStatusToImportante">Importante</a>
        <a class="dropdown-item" href="#" id="changeStatusToPendiente">Pendiente</a>
        <a class="dropdown-item" href="#" id="changeStatusToOtro">Otro</a>
        <a class="dropdown-item" href="#" id="changeStatusToPersonal">Personal</a>
        <a class="dropdown-item" href="#" id="changeStatusToMastarde">Mas tarde</a>

    </div>
</div>
@endsection

@section('scripts')
@include('partials.toast')
<script>

$(document).ready(() => {
    // Evitar que al hacer clic en el checkbox se abra el correo
    $('.email-checkbox').on('click', function(event) {
        event.stopPropagation();
    });

    // Comportamiento de las filas clicables
    $('.clickable-row').on('click', function() {
        window.location = $(this).data('href');
    });

    // Botón para eliminar un solo correo
    $('.delete').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        botonAceptar(id);
    });

    // Seleccionar/Deseleccionar todos los correos
    $('#selectAll').on('change', function() {
        $('.email-checkbox').prop('checked', $(this).prop('checked'));
    });

    $('.email-checkbox').on('change', function() {
        if (!$(this).prop('checked')) {
            $('#selectAll').prop('checked', false);
        }
    });

    $('.table').on('contextmenu', '.clickable-row', function(e) {
        e.preventDefault();
        var emailId = $(this).data('id'); // Asegúrate de que cada fila tenga un `data-id`
        var menu = $('#emailContextMenu');

        // Calcula las posiciones iniciales basadas en la posición del cursor
        var left = e.clientX;
        var top = e.clientY;
        var menuWidth = menu.outerWidth();
        var menuHeight = menu.outerHeight();
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();

        // Ajustar si el menú se sale de la pantalla por la derecha
        if (left + menuWidth > windowWidth) {
            left = windowWidth - menuWidth;
        }

        // Determina si hay suficiente espacio para mostrar el menú hacia abajo
        if (top + menuHeight > windowHeight) {
            // Si no cabe hacia abajo, muestra el menú hacia arriba
            top = e.clientY - menuHeight;
        }

        menu.css({
            display: "block",
            left: left + 'px',
            top: top + 'px'
        });

        // Handlers para cambiar el estado
        $('#changeStatusToNew').off('click').on('click', function() { changeEmailStatus(emailId, '1'); });
        $('#changeStatusToRead').off('click').on('click', function() { changeEmailStatus(emailId, '2'); });
        $('#changeStatusToRealizado').off('click').on('click', function() { changeEmailStatus(emailId, '3'); });
        $('#changeStatusToImportante').off('click').on('click', function() { changeEmailStatus(emailId, '4'); });
        $('#changeStatusToPendiente').off('click').on('click', function() { changeEmailStatus(emailId, '5'); });
        $('#changeStatusToOtro').off('click').on('click', function() { changeEmailStatus(emailId, '6'); });
        $('#changeStatusToPersonal').off('click').on('click', function() { changeEmailStatus(emailId, '7'); });
        $('#changeStatusToMastarde').off('click').on('click', function() { changeEmailStatus(emailId, '8'); });
    });

    // Ocultar menú contextual al hacer clic en cualquier otro lugar
    $(document).on('click', function(e) {
        $('#emailContextMenu').hide();
    });
    $(window).on('scroll', function() {
        $('#emailContextMenu').hide();
    });
});

function changeEmailStatus(emailId, status) {
    $.ajax({
        type: "POST",
        url: "{{ route('admin.emails.changeStatus') }}",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': emailId,
            'status': status
        },
        success: function(response) {
            if (response.status) {
                // Asumiendo que response.statusName y response.statusColor son enviados desde el servidor
                updateEmailStatusUI(emailId, response.statusName, response.statusColor);
                Toast.fire({
                    icon: "success",
                    title: response.message
                });
            } else {
                Toast.fire({
                    icon: "error",
                    title: response.message
                });
            }
        }
    });
}

function botonAceptar(id){
    Swal.fire({
        title: "¿Estás seguro de que quieres eliminar este correo?",
        html: "<p>Esta acción es irreversible.</p>",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Borrar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.when(getDelete(id)).then(function(data, textStatus, jqXHR) {
                if (!data.status) {
                    Toast.fire({
                        icon: "error",
                        title: data.mensaje
                    });
                } else {
                    Toast.fire({
                        icon: "success",
                        title: data.mensaje
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }
    });
}

function getDelete(id) {
    const url = '{{ route("admin.emails.destroy") }}';
    return $.ajax({
        type: "POST",
        url: url,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: { 'id': id },
        dataType: "json"
    });
}

function updateEmailStatusUI(emailId, statusName, statusColor) {
    let statusBadge = $('#status-' + emailId);
    statusBadge.text(statusName);
    statusBadge.removeClass('bg-light bg-info bg-warning bg-secondary bg-dark bg-danger bg-primary bg-success text-dark');
    statusBadge.addClass('bg-' + statusColor);
    if (['light', 'info', 'warning'].includes(statusColor)) {
        statusBadge.addClass('text-dark');
    }
}
</script>
@endsection
