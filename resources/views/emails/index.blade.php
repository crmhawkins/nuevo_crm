@extends('layouts.app')

@section('titulo', 'Correos')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

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
                    <form method="GET" action="{{ route('admin.emails.index') }}" class="d-flex col-md-6">
                        <div class="col-10 mr-2">
                            <input type="text" name="search" class="form-control p-2" placeholder="Buscar por remitente o asunto" value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-search "></i> Buscar
                        </button>
                    </form>
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
                                                <tr class="clickable-row" data-href="{{ route('admin.emails.show', $email->id) }}">
                                                    <td>
                                                        <input type="checkbox" name="ids[]" value="{{ $email->id }}" class="email-checkbox">
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 150px;">{{ $email->sender }}</td>
                                                    <td>{{ Str::limit($email->subject, 50) }}</td>
                                                    <td>{{ $email->category->name ?? 'Sin categoría' }}</td>
                                                    <td><span class="badge bg-{{ $email->status->color ?? 'secondary' }}">{{ $email->status->name ?? 'Desconocido' }}</span></td>
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
});

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
</script>
@endsection
