@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3">Bandeja de Entrada</h1>
        <button class="btn btn-success text-white d-flex align-items-center">
            <i class="fa-solid fa-plus me-2"></i> Nuevo Correo
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-12 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active">
                            <i class="fa-solid fa-inbox me-2"></i> Bandeja de Entrada
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fa-solid fa-paper-plane me-2"></i> Enviados
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fa-solid fa-trash me-2"></i> Papelera
                        </a>
                    </div>
                </div>
                <div class="col-md-9 col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 40px;"><input type="checkbox"></th>
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
                                <tr>
                                    <td><input type="checkbox"></td>
                                    <td class="text-truncate" style="max-width: 150px;">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-user-circle me-2 text-secondary"></i>
                                            <span>{{ $email->sender }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.emails.show', $email->id) }}" class="text-decoration-none">
                                            {{ Str::limit($email->subject, 50) }}
                                        </a>
                                    </td>
                                    <td>{{ $email->category->name ?? 'Sin categoría' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $email->status->color ?? 'secondary' }}">{{ $email->status->name ?? 'Desconocido' }}</span>
                                    </td>
                                    <td>{{ $email->created_at->format('d M Y, g:i A') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.emails.show', $email->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No hay correos disponibles.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $emails->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
