@extends('layouts.app')

@section('titulo', 'Correos')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}">
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-6 order-md-1 order-last row">
                <div class="col-auto">
                    <h3><i class="fa-regular fa-envelope"></i> Correos</h3>
                    <p class="text-subtitle text-muted">Listado de mis Emails</p>
                </div>
                <div class="col-auto">
                    <a class="btn btn-outline-secondary" href="{{route('admin.emails.create')}}">
                        <i class="fa-solid fa-plus"></i> Nuevo Correo
                    </a>
                </div>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Correos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <ul class="nav nav-tabs" id="emailTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="inbox-tab" data-bs-toggle="tab" data-bs-target="#inbox" type="button" role="tab" aria-controls="inbox" aria-selected="true">
                            <i class="fa-solid fa-inbox me-2"></i> Bandeja de Entrada
                        </button>
                    </li>
                    @foreach ($categorias as $categoria)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="category-{{ $categoria->id }}-tab" data-bs-toggle="tab" data-bs-target="#category-{{ $categoria->id }}" type="button" role="tab" aria-controls="category-{{ $categoria->id }}" aria-selected="false">
                            <i class="fa-solid fa-tag me-2"></i> {{ $categoria->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content" id="emailTabContent">
                    <div class="tab-pane fade show active" id="inbox" role="tabpanel" aria-labelledby="inbox-tab">
                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle">
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
                                    @forelse ($emails->where('category_id', '!=', 6) as $email)
                                    <tr class="clickable-row" data-href="{{ route('admin.emails.show', $email->id) }}">
                                        <td class="text-truncate" style="max-width: 150px;">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $email->sender }}</span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($email->subject, 50) }}</td>
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
                        <div class="d-flex justify-content-center mt-4">
                            {{ $emails->links() }}
                        </div>
                    </div>
                    @foreach ($categorias as $categoria)
                    <div class="tab-pane fade" id="category-{{ $categoria->id }}" role="tabpanel" aria-labelledby="category-{{ $categoria->id }}-tab">
                        <div class="table-responsive mt-3">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Remitente</th>
                                        <th scope="col">Asunto</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col" class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($emails->where('category_id', $categoria->id) as $email)
                                    <tr class="clickable-row" data-href="{{ route('admin.emails.show', $email->id) }}">
                                        <td class="text-truncate" style="max-width: 150px;">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $email->sender }}</span>
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($email->subject, 50) }}</td>
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
                                        <td colspan="5" class="text-center text-muted">No hay correos disponibles en esta categoría.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
