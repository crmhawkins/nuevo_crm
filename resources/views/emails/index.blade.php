@extends('layouts.app')

@section('titulo', 'Correos')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
<style>
    .nav-tabs .nav-link {
        color: #6c757d;
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom: 2px solid #0d6efd;
    }
</style>
@endsection

@section('content')
<div class="page-heading card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="fw-bold"><i class="fa-regular fa-envelope me-2"></i>Correos</h3>
                <p class="text-muted">Listado de mis Emails</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.emails.create') }}">
                <i class="fa-solid fa-plus me-1"></i> Nuevo Correo
            </a>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Correos</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="card shadow-sm">
        <div class="card-body row">
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
                            <table class="table table-hover align-middle table-bordered">
                                <thead class="table-light">
                                    <tr>
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
                                        <td class="text-truncate" style="max-width: 150px;">{{ $email->sender }}</td>
                                        <td>{{ Str::limit($email->subject, 50) }}</td>
                                        <td>{{ $email->category->name ?? 'Sin categoría' }}</td>
                                        <td><span class="badge bg-{{ $email->status->color ?? 'secondary' }}">{{ $email->status->name ?? 'Desconocido' }}</span></td>
                                        <td>{{ $email->created_at->format('d M Y, g:i A') }}</td>
                                        <td class="text-end">
                                            <button data-id="{{$email->id}}" class="btn btn-sm btn-outline-danger delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay correos disponibles.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $emails->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@include('partials.toast')
<script>
$(document).ready(() => {
    $('.clickable-row').on('click', function() {
        window.location = $(this).data('href');
    });

    $('.delete').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        botonAceptar(id);
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
