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
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#createClientModal">
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
                                        <th>Creado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>{{ $client->client_name }}</td>
                                            <td>{{ $client->client_email }}</td>
                                            <td>{{ $client->url }}</td>
                                            <td>{{ $client->last_seo }}</td>
                                            <td>{{ $client->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <!-- Botón Ver -->
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#clientModal{{ $client->id }}">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>

                                                <!-- Modal Ver Cliente -->
                                                <div class="modal fade" id="clientModal{{ $client->id }}" tabindex="-1"
                                                    aria-labelledby="clientModalLabel{{ $client->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content bg-white text-dark shadow">
                                                            <div class="modal-header bg-dark text-white">
                                                                <h5 class="modal-title"
                                                                    id="clientModalLabel{{ $client->id }}">Detalles de
                                                                    {{ $client->client_name }}</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p><strong>Email:</strong> {{ $client->client_email }}</p>
                                                                <p><strong>URL:</strong> {{ $client->url }}</p>
                                                                <p><strong>Usuario Aplicación:</strong>
                                                                    {{ $client->user_app }}</p>
                                                                <p><strong>Contraseña Aplicación:</strong>
                                                                    {{ $client->password_app }}</p>
                                                                <p><strong>Creado:</strong>
                                                                    {{ $client->created_at->format('d/m/Y') }}</p>
                                                                <hr>
                                                                <h6>Dirección de la Empresa</h6>
                                                                <p><strong>Nombre de la Empresa:</strong>
                                                                    {{ $client->CompanyName }}</p>
                                                                <p><strong>Dirección:</strong> {{ $client->AddressLine1 }}
                                                                </p>
                                                                <p><strong>Ciudad:</strong> {{ $client->Locality }}</p>
                                                                <p><strong>Provincia/Región:</strong>
                                                                    {{ $client->AdminDistrict }}</p>
                                                                <p><strong>Código Postal:</strong>
                                                                    {{ $client->PostalCode }}</p>
                                                                <p><strong>País:</strong> {{ $client->CountryRegion }}</p>
                                                                <hr>
                                                                <h6>Reportes</h6>
                                                                <div class="mb-3">
                                                                    <form
                                                                        action="{{ route('autoseo.json.upload', ['field' => 'reporte', 'id' => $client->id]) }}"
                                                                        method="POST" enctype="multipart/form-data"
                                                                        class="d-flex gap-2">
                                                                        @csrf
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="file" accept=".pdf,.doc,.docx"
                                                                            required>
                                                                        <button type="submit"
                                                                            class="btn btn-primary btn-sm">
                                                                            <i class="fas fa-upload"></i> Subir Reporte
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                @if ($client->reports && $client->reports && count($client->reports) > 0)
                                                                    <div class="list-group">
                                                                        @foreach ($client->reports as $index => $report)
                                                                            <div
                                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                                <div>
                                                                                    <i class="fas fa-file-pdf me-2"></i>
                                                                                    Reporte #{{ $index + 1 }}
                                                                                    <small class="text-muted ms-2">
                                                                                        {{ \Carbon\Carbon::parse($report['creation_date'])->format('d/m/Y H:i') }}
                                                                                    </small>
                                                                                </div>
                                                                                <a href="{{ route('autoseo.json.download', ['field' => 'reporte', 'id' => $client->id, 'index' => $index]) }}"
                                                                                    class="btn btn-sm btn-primary">
                                                                                    <i class="fas fa-download"></i>
                                                                                    Descargar
                                                                                </a>
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <p class="text-muted">No hay reportes disponibles</p>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cerrar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editClientModal{{ $client->id }}">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>

                                                <!-- Modal Editar Cliente -->
                                                <div class="modal fade" id="editClientModal{{ $client->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="editClientModalLabel{{ $client->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <form action="{{ route('autoseo.update', $client->id) }}"
                                                            method="POST" enctype="multipart/form-data"
                                                            class="modal-content">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header bg-dark text-white">
                                                                <h5 class="modal-title"
                                                                    id="editClientModalLabel{{ $client->id }}">Editar
                                                                    Cliente: {{ $client->client_name }}</h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <input type="hidden" name="id"
                                                                value="{{ $client->id }}">
                                                            <div class="modal-body">
                                                                <div class="row g-3">
                                                                    <div class="col-md-6">
                                                                        <label for="edit_client_name{{ $client->id }}"
                                                                            class="form-label">Nombre del Cliente</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_client_name{{ $client->id }}"
                                                                            name="client_name"
                                                                            value="{{ $client->client_name }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_client_email{{ $client->id }}"
                                                                            class="form-label">Email</label>
                                                                        <input type="email" class="form-control"
                                                                            id="edit_client_email{{ $client->id }}"
                                                                            name="client_email"
                                                                            value="{{ $client->client_email }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="edit_url{{ $client->id }}"
                                                                            class="form-label">URL del Sitio</label>
                                                                        <input type="url" class="form-control"
                                                                            id="edit_url{{ $client->id }}"
                                                                            name="url" value="{{ $client->url }}"
                                                                            required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_username{{ $client->id }}"
                                                                            class="form-label">Usuario</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_username{{ $client->id }}"
                                                                            name="username"
                                                                            value="{{ $client->username }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_password{{ $client->id }}"
                                                                            class="form-label">Contraseña</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_password{{ $client->id }}"
                                                                            name="password"
                                                                            value="{{ $client->password }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_user_app{{ $client->id }}"
                                                                            class="form-label">Usuario Aplicación</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_user_app{{ $client->id }}"
                                                                            name="user_app"
                                                                            value="{{ $client->user_app }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_password_app{{ $client->id }}"
                                                                            class="form-label">Contraseña
                                                                            Aplicación</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_password_app{{ $client->id }}"
                                                                            name="password_app"
                                                                            value="{{ $client->password_app }}" required
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <h6 class="mt-3 mb-3">Dirección de la Empresa</h6>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="edit_company_name{{ $client->id }}"
                                                                            class="form-label">Nombre de la Empresa</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_company_name{{ $client->id }}"
                                                                            name="CompanyName"
                                                                            value="{{ $client->CompanyName }}"
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="edit_address_line1{{ $client->id }}"
                                                                            class="form-label">Dirección</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_address_line1{{ $client->id }}"
                                                                            name="AddressLine1"
                                                                            value="{{ $client->AddressLine1 }}"
                                                                            autocomplete="off"
                                                                            placeholder="Calle y número">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_locality{{ $client->id }}"
                                                                            class="form-label">Ciudad</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_locality{{ $client->id }}"
                                                                            name="Locality"
                                                                            value="{{ $client->Locality }}"
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            for="edit_admin_district{{ $client->id }}"
                                                                            class="form-label">Provincia/Región</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_admin_district{{ $client->id }}"
                                                                            name="AdminDistrict"
                                                                            value="{{ $client->AdminDistrict }}"
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_postal_code{{ $client->id }}"
                                                                            class="form-label">Código Postal</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_postal_code{{ $client->id }}"
                                                                            name="PostalCode"
                                                                            value="{{ $client->PostalCode }}"
                                                                            autocomplete="off">
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            for="edit_country_region{{ $client->id }}"
                                                                            class="form-label">País</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_country_region{{ $client->id }}"
                                                                            name="CountryRegion"
                                                                            value="{{ $client->CountryRegion }}"
                                                                            autocomplete="off" placeholder="ES"
                                                                            maxlength="2">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <h6 class="mt-3 mb-3">Contexto Empresarial</h6>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <label for="edit_company_context{{ $client->id }}"
                                                                            class="form-label">Descripción de la Empresa</label>
                                                                        <textarea class="form-control" 
                                                                            id="edit_company_context{{ $client->id }}"
                                                                            name="company_context" rows="4" 
                                                                            placeholder="Describe brevemente qué hace la empresa, a qué se dedica, qué servicios o productos ofrece, su sector de actividad, etc. Esta información ayudará a generar contenido más relevante y personalizado.">{{ $client->company_context }}</textarea>
                                                                        <small class="form-text text-muted">Información opcional que ayudará a mejorar la generación de contenido SEO personalizado.</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Guardar
                                                                    Cambios</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                                <form action="{{ route('autoseo.delete') }}" method="POST"
                                                    style="display: inline;">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $client->id }}">
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">
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
    <div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('autoseo.store') }}" method="POST" enctype="multipart/form-data"
                class="modal-content">
                @csrf
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="createClientModalLabel">Crear Nuevo Cliente</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="client_name" class="form-label">Nombre del Cliente</label>
                            <input type="text" class="form-control" id="client_name" name="client_name" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="client_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="client_email" name="client_email" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-12">
                            <label for="url" class="form-label">URL del Sitio</label>
                            <input type="url" class="form-control" id="url" name="url" required
                                placeholder="https://ejemplo.com">
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="username" name="username" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="password" name="password" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="user_app" class="form-label">Usuario Aplicación</label>
                            <input type="text" class="form-control" id="user_app" name="user_app" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="password_app" class="form-label">Contraseña Aplicación</label>
                            <input type="text" class="form-control" id="password_app" name="password_app" required
                                autocomplete="off">
                        </div>
                        <div class="col-12">
                            <h6 class="mt-3 mb-3">Dirección de la Empresa</h6>
                        </div>
                        <div class="col-md-12">
                            <label for="company_name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="company_name" name="CompanyName" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-12">
                            <label for="address_line1" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="address_line1" name="AddressLine1" required
                                autocomplete="off" placeholder="Calle y número">
                        </div>
                        <div class="col-md-6">
                            <label for="locality" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="locality" name="Locality" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="admin_district" class="form-label">Provincia/Región</label>
                            <input type="text" class="form-control" id="admin_district" name="AdminDistrict" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="postal_code" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="postal_code" name="PostalCode" required
                                autocomplete="off">
                        </div>
                        <div class="col-md-6">
                            <label for="country_region" class="form-label">País</label>
                            <input type="text" class="form-control" id="country_region" name="CountryRegion" required
                                autocomplete="off" placeholder="ES" maxlength="2">
                        </div>
                        <div class="col-12">
                            <h6 class="mt-3 mb-3">Contexto Empresarial</h6>
                        </div>
                        <div class="col-md-12">
                            <label for="company_context" class="form-label">Descripción de la Empresa</label>
                            <textarea class="form-control" id="company_context" name="company_context" rows="4" 
                                placeholder="Describe brevemente qué hace la empresa, a qué se dedica, qué servicios o productos ofrece, su sector de actividad, etc. Esta información ayudará a generar contenido más relevante y personalizado."></textarea>
                            <small class="form-text text-muted">Información opcional que ayudará a mejorar la generación de contenido SEO personalizado.</small>
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
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Modal(document.getElementById('createClientModal'));
        });
    </script>
@endsection
