@extends('layouts.app')

@section('titulo', 'Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <style>
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1050 !important;
        }

        .modal-dialog {
            z-index: 1055 !important;
            position: relative;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-white shadow position-relative">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div class="fw-bold">Clientes</div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClientModal">
                            <i class="fas fa-plus"></i> Crear cliente
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Email</th>
                                    <th>URL</th>
                                    <th>Último SEO</th>
                                    <th>Próximo SEO</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                <tr>
                                    <td>{{ $client->client_name }}</td>
                                    <td>{{ $client->client_email }}</td>
                                    <td>{{ $client->url }}</td>
                                    <td>{{ $client->last_seo }}</td>
                                    <td>{{ $client->next_seo }}</td>
                                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <!-- Botón Ver -->
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#clientModal{{ $client->id }}">
                                            <i class="fas fa-eye"></i> Ver
                                        </button>

                                        <!-- Modal Ver Cliente -->
                                        <div class="modal fade" id="clientModal{{ $client->id }}" tabindex="-1" aria-labelledby="clientModalLabel{{ $client->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content bg-white text-dark shadow">
                                                    <div class="modal-header bg-dark text-white">
                                                        <h5 class="modal-title" id="clientModalLabel{{ $client->id }}">Detalles de {{ $client->client_name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Email:</strong> {{ $client->client_email }}</p>
                                                        <p><strong>URL:</strong> {{ $client->url }}</p>
                                                        <p><strong>Creado:</strong> {{ $client->created_at->format('d/m/Y') }}</p>
                                                        <hr>
                                                        <h6>Archivos JSON</h6>
                                                        <p>
                                                            <strong>Home:</strong>
                                                            @if($client->json_home_update)
                                                                <span class="text-success"><i class="fas fa-check-circle"></i> {{ \Carbon\Carbon::parse($client->json_home_update)->format('d/m/Y') }}</span>
                                                            @else
                                                                <span class="text-danger"><i class="fas fa-times-circle"></i> No subido</span>
                                                            @endif
                                                        </p>
                                                        <p>
                                                            <strong>Nosotros:</strong>
                                                            @if($client->json_nosotros_update)
                                                                <span class="text-success"><i class="fas fa-check-circle"></i> {{ \Carbon\Carbon::parse($client->json_nosotros_update)->format('d/m/Y') }}</span>
                                                            @else
                                                                <span class="text-danger"><i class="fas fa-times-circle"></i> No subido</span>
                                                            @endif
                                                        </p>
                                                        <hr>
                                                        <h6>Reportes</h6>
                                                        <div class="mb-3">
                                                            <form action="{{ route('autoseo.json.upload', ['field' => 'reporte', 'id' => $client->id]) }}"
                                                                  method="POST"
                                                                  enctype="multipart/form-data"
                                                                  class="d-flex gap-2">
                                                                @csrf
                                                                <input type="file"
                                                                       class="form-control form-control-sm"
                                                                       name="file"
                                                                       accept=".pdf,.doc,.docx"
                                                                       required>
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-upload"></i> Subir Reporte
                                                                </button>
                                                            </form>
                                                        </div>
                                                        @if($client->reports && count($client->reports) > 0)
                                                            <div class="list-group">
                                                                @foreach($client->reports as $index => $report)
                                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <i class="fas fa-file-pdf me-2"></i>
                                                                            Reporte #{{ $index + 1 }}
                                                                            <small class="text-muted ms-2">
                                                                                {{ \Carbon\Carbon::parse($report['creation_date'])->format('d/m/Y H:i') }}
                                                                            </small>
                                                                        </div>
                                                                        <a href="{{ route('autoseo.json.download', ['field' => 'reporte', 'id' => $client->id, 'index' => $index]) }}"
                                                                           class="btn btn-sm btn-primary">
                                                                            <i class="fas fa-download"></i> Descargar
                                                                        </a>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <p class="text-muted">No hay reportes disponibles</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editClientModal{{ $client->id }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>

                                        <!-- Modal Editar Cliente -->
                                        <div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1" aria-labelledby="editClientModalLabel{{ $client->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <form action="{{ route('autoseo.update', $client->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header bg-dark text-white">
                                                        <h5 class="modal-title" id="editClientModalLabel{{ $client->id }}">Editar Cliente: {{ $client->client_name }}</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <input type="hidden" name="id" value="{{ $client->id }}">
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label for="edit_client_name{{ $client->id }}" class="form-label">Nombre del Cliente</label>
                                                                <input type="text" class="form-control" id="edit_client_name{{ $client->id }}" name="client_name" value="{{ $client->client_name }}" required autocomplete="off">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_client_email{{ $client->id }}" class="form-label">Email</label>
                                                                <input type="email" class="form-control" id="edit_client_email{{ $client->id }}" name="client_email" value="{{ $client->client_email }}" required autocomplete="off">
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label for="edit_url{{ $client->id }}" class="form-label">URL del Sitio</label>
                                                                <input type="url" class="form-control" id="edit_url{{ $client->id }}" name="url" value="{{ $client->url }}" required>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_username{{ $client->id }}" class="form-label">Usuario</label>
                                                                <input type="text" class="form-control" id="edit_username{{ $client->id }}" name="username" value="{{ $client->username }}" required autocomplete="off">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_password{{ $client->id }}" class="form-label">Contraseña</label>
                                                                <input type="text" class="form-control" id="edit_password{{ $client->id }}" name="password" value="{{ $client->password }}" required autocomplete="off">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_json_home{{ $client->id }}" class="form-label">JSON Home</label>
                                                                <input type="file" class="form-control" id="edit_json_home{{ $client->id }}" name="json_home" accept=".json">
                                                                <small class="text-muted">Dejar vacío para mantener el archivo actual</small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_json_nosotros{{ $client->id }}" class="form-label">JSON Nosotros</label>
                                                                <input type="file" class="form-control" id="edit_json_nosotros{{ $client->id }}" name="json_nosotros" accept=".json">
                                                                <small class="text-muted">Dejar vacío para mantener el archivo actual</small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="edit_next_seo{{ $client->id }}" class="form-label">Próximo SEO</label>
                                                                <input type="date" class="form-control" id="edit_next_seo{{ $client->id }}" name="next_seo" value="{{ $client->next_seo }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <form action="{{ route('autoseo.delete') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $client->id }}">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">
                                                <i class="fas fa-trash"></i> Eliminar
                                            </button>
                                        </form>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Cliente -->
<div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('autoseo.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="createClientModalLabel">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="client_name" class="form-label">Nombre del Cliente</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label for="client_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="client_email" name="client_email" required autocomplete="off">
                    </div>
                    <div class="col-md-12">
                        <label for="url" class="form-label">URL del Sitio</label>
                        <input type="url" class="form-control" id="url" name="url" required placeholder="https://ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="text" class="form-control" id="password" name="password" required autocomplete="off">
                    </div>
                    <div class="col-md-6">
                        <label for="json_home" class="form-label">JSON Home</label>
                        <input type="file" class="form-control" id="json_home" name="json_home" accept=".json" required>
                    </div>
                    <div class="col-md-6">
                        <label for="json_nosotros" class="form-label">JSON Nosotros</label>
                        <input type="file" class="form-control" id="json_nosotros" name="json_nosotros" accept=".json" required>
                    </div>
                    <div class="col-md-6">
                        <label for="next_seo" class="form-label">Próximo SEO</label>
                        <input type="date" class="form-control" id="next_seo" name="next_seo" required value="{{ now()->toDateString() }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">Todos los campos son obligatorios.</small>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cliente</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('createClientModal'));
    });
</script>
@endsection
