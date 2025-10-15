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

        /* Estilos para el modal de visualización mejorado */
        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-xl {
            max-width: 1200px;
        }
        
        code {
            font-size: 0.9rem;
            padding: 0.2rem 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
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

                                                <!-- Modal Ver Cliente - Versión Mejorada -->
                                                <div class="modal fade" id="clientModal{{ $client->id }}" tabindex="-1"
                                                    aria-labelledby="clientModalLabel{{ $client->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-xl">
                                                        <div class="modal-content">
                                                            <!-- Header con gradiente -->
                                                            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                                <div>
                                                                    <h4 class="modal-title text-white mb-1" id="clientModalLabel{{ $client->id }}">
                                                                        <i class="fas fa-building me-2"></i>{{ $client->client_name }}
                                                                    </h4>
                                                                    <p class="text-white-50 mb-0 small">
                                                                        <i class="fas fa-calendar me-1"></i>Cliente desde {{ $client->created_at->format('d/m/Y') }}
                                                                    </p>
                                                                </div>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            
                                                            <div class="modal-body p-4">
                                                                <!-- Información de Contacto -->
                                                                <div class="row mb-4">
                                                                    <div class="col-12">
                                                                        <h5 class="border-bottom pb-2 mb-3">
                                                                            <i class="fas fa-address-card text-primary me-2"></i>Información de Contacto
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="icon-box bg-primary bg-opacity-10 text-primary rounded p-3 me-3">
                                                                                <i class="fas fa-envelope fa-lg"></i>
                                                                            </div>
                                                                            <div>
                                                                                <small class="text-muted d-block">Email</small>
                                                                                <strong>{{ $client->client_email }}</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="icon-box bg-success bg-opacity-10 text-success rounded p-3 me-3">
                                                                                <i class="fas fa-globe fa-lg"></i>
                                                                            </div>
                                                                            <div>
                                                                                <small class="text-muted d-block">Sitio Web</small>
                                                                                <strong><a href="{{ $client->url }}" target="_blank" class="text-decoration-none">{{ $client->url }}</a></strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Credenciales de Acceso -->
                                                                <div class="row mb-4">
                                                                    <div class="col-12">
                                                                        <h5 class="border-bottom pb-2 mb-3">
                                                                            <i class="fas fa-key text-warning me-2"></i>Credenciales de Acceso
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="card border-0 bg-light">
                                                                            <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-muted">
                                                                                    <i class="fas fa-user me-1"></i>Cuenta de aplicacion
                                                                                </h6>
                                                                                <p class="mb-1"><small class="text-muted">Usuario:</small> <code class="bg-white px-2 py-1 rounded">{{ $client->username }}</code></p>
                                                                                <p class="mb-0"><small class="text-muted">Contraseña:</small> <code class="bg-white px-2 py-1 rounded">{{ $client->password }}</code></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <div class="card border-0 bg-light">
                                                                            <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-muted">
                                                                                    <i class="fab fa-wordpress me-1"></i>WordPress
                                                                                </h6>
                                                                                <p class="mb-1"><small class="text-muted">Usuario:</small> <code class="bg-white px-2 py-1 rounded">{{ $client->user_app }}</code></p>
                                                                                <p class="mb-0"><small class="text-muted">Contraseña:</small> <code class="bg-white px-2 py-1 rounded">{{ $client->password_app }}</code></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="card border-0 bg-info bg-opacity-10">
                                                                            <div class="card-body">
                                                                                <h6 class="card-subtitle mb-2 text-info">
                                                                                    <i class="fas fa-shield-alt me-1"></i>PIN de Acceso
                                                                                </h6>
                                                                                <p class="mb-0"><code class="bg-white px-3 py-2 rounded fs-5 text-info">{{ $client->pin }}</code></p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Dirección de la Empresa -->
                                                                <div class="row mb-4">
                                                                    <div class="col-12">
                                                                        <h5 class="border-bottom pb-2 mb-3">
                                                                            <i class="fas fa-map-marker-alt text-danger me-2"></i>Dirección de la Empresa
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="card border-0 bg-light">
                                                                            <div class="card-body">
                                                                                <div class="row">
                                                                                    <div class="col-md-12 mb-2">
                                                                                        <h6 class="mb-0">{{ $client->CompanyName }}</h6>
                                                                                    </div>
                                                                                    <div class="col-md-8 mb-2">
                                                                                        <i class="fas fa-map-pin text-danger me-2"></i>
                                                                                        <span>{{ $client->AddressLine1 }}</span>
                                                                                    </div>
                                                                                    <div class="col-md-4 mb-2">
                                                                                        <i class="fas fa-mailbox text-primary me-2"></i>
                                                                                        <span>{{ $client->PostalCode }}</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <i class="fas fa-city text-info me-2"></i>
                                                                                        <span>{{ $client->Locality }}, {{ $client->AdminDistrict }}</span>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <i class="fas fa-flag text-success me-2"></i>
                                                                                        <span>{{ $client->CountryRegion }}</span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Contexto Empresarial -->
                                                                @if($client->company_context)
                                                                <div class="row mb-4">
                                                                    <div class="col-12">
                                                                        <h5 class="border-bottom pb-2 mb-3">
                                                                            <i class="fas fa-info-circle text-info me-2"></i>Contexto Empresarial
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <div class="card border-0" style="background: linear-gradient(135deg, #e0f7fa 0%, #e1f5fe 100%);">
                                                                            <div class="card-body">
                                                                                <p class="mb-0 text-dark" style="line-height: 1.6;">
                                                                                    {{ $client->company_context }}
                                                                                </p>
                                                                                <small class="text-muted d-block mt-2">
                                                                                    <i class="fas fa-robot me-1"></i>Optimizado por IA
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @endif

                                                                <!-- Reportes SEO -->
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <h5 class="border-bottom pb-2 mb-3">
                                                                            <i class="fas fa-chart-line text-success me-2"></i>Reportes SEO
                                                                        </h5>
                                                                    </div>
                                                                    <div class="col-12 mb-3">
                                                                    <form
                                                                        action="{{ route('autoseo.json.upload', ['field' => 'reporte', 'id' => $client->id]) }}"
                                                                        method="POST" enctype="multipart/form-data"
                                                                            class="d-flex gap-2 align-items-center">
                                                                        @csrf
                                                                        <input type="file"
                                                                            class="form-control form-control-sm"
                                                                            name="file" accept=".pdf,.doc,.docx"
                                                                            required>
                                                                        <button type="submit"
                                                                                class="btn btn-success btn-sm">
                                                                                <i class="fas fa-upload me-1"></i> Subir Reporte
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                                    <div class="col-12">
                                                                        @php
                                                                            $htmlReports = \App\Models\Autoseo\AutoseoReportsModel::where('autoseo_id', $client->id)
                                                                                ->orderBy('created_at', 'desc')
                                                                                ->get()
                                                                                ->filter(function($report) {
                                                                                    return file_exists(storage_path("app/public/{$report->path}"));
                                                                                });
                                                                        @endphp
                                                                        
                                                                        @if ($htmlReports->count() > 0)
                                                                            <div class="list-group">
                                                                                @foreach ($htmlReports as $index => $report)
                                                                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="icon-box bg-success bg-opacity-10 text-success rounded p-2 me-3">
                                                                                                <i class="fas fa-file-alt fa-lg"></i>
                                                                                            </div>
                                                                                            <div>
                                                                                                <strong>Informe SEO</strong>
                                                                                                <br>
                                                                                                <small class="text-muted">
                                                                                                    <i class="fas fa-clock me-1"></i>
                                                                                                    {{ $report->created_at->format('d/m/Y H:i') }}
                                                                                                </small>
                                                                                            </div>
                                                                                        </div>
                                                                                        <a href="{{ route('autoseo.reports.showReport', ['userid' => $client->id, 'id' => $report->id]) }}" 
                                                                                            target="_blank"
                                                                                            class="btn btn-sm btn-success">
                                                                                            <i class="fas fa-external-link-alt me-1"></i>
                                                                                            Ver Informe
                                                                                        </a>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @else
                                                                            <div class="alert alert-info mb-0">
                                                                                <i class="fas fa-info-circle me-2"></i>No hay informes SEO disponibles para este cliente
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="modal-footer bg-light">
                                                                <button class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i>Cerrar
                                                                </button>
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
                                                                            class="form-label">Descripción de la Empresa <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" 
                                                                            id="edit_company_context{{ $client->id }}"
                                                                            name="company_context" rows="4" 
                                                                            maxlength="2000"
                                                                            minlength="100"
                                                                            required
                                                                            placeholder="Describe brevemente qué hace la empresa, a qué se dedica, qué servicios o productos ofrece, su sector de actividad, etc. Esta información ayudará a generar contenido más relevante y personalizado.">{{ $client->company_context }}</textarea>
                                                                        <small class="form-text text-muted">
                                                                            <span id="edit_company_context_counter{{ $client->id }}">{{ strlen($client->company_context ?? '') }} / 2000 caracteres</span> (mínimo 100 caracteres) - 
                                                                            Información obligatoria que será optimizada automáticamente por IA.
                                                                        </small>
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
                            <label for="company_context" class="form-label">Descripción de la Empresa <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="company_context" name="company_context" rows="4" 
                                maxlength="2000"
                                minlength="100"
                                required
                                placeholder="Describe brevemente qué hace la empresa, a qué se dedica, qué servicios o productos ofrece, su sector de actividad, etc. Esta información ayudará a generar contenido más relevante y personalizado."></textarea>
                            <small class="form-text text-muted">
                                <span id="company_context_counter">0 / 2000 caracteres</span> (mínimo 100 caracteres) - 
                                Información obligatoria que será optimizada automáticamente por IA.
                            </small>
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
            // Contador de caracteres para crear cliente
            const companyContextCreate = document.getElementById('company_context');
            const counterCreate = document.getElementById('company_context_counter');
            
            if (companyContextCreate && counterCreate) {
                // Actualizar contador en tiempo real
                companyContextCreate.addEventListener('input', function() {
                    updateCounter(this, counterCreate);
                });
                
                // Inicializar el contador
                updateCounter(companyContextCreate, counterCreate);
            }
            
            // Función para actualizar contadores
            function updateCounter(textarea, counterElement) {
                const length = textarea.value.length;
                counterElement.textContent = length + ' / 2000 caracteres';
                
                // Cambiar color según el estado
                if (length < 100) {
                    counterElement.style.color = '#dc2626'; // Rojo si no llega al mínimo
                    counterElement.style.fontWeight = 'bold';
                } else if (length > 1800) {
                    counterElement.style.color = '#dc2626'; // Rojo si está cerca del límite
                    counterElement.style.fontWeight = 'normal';
                } else if (length > 1500) {
                    counterElement.style.color = '#f59e0b'; // Naranja
                    counterElement.style.fontWeight = 'normal';
                } else {
                    counterElement.style.color = '#059669'; // Verde cuando está bien
                    counterElement.style.fontWeight = 'normal';
                }
            }
            
            // Contador de caracteres para editar cliente
            document.querySelectorAll('[id^="edit_company_context"]').forEach(function(textarea) {
                const clientId = textarea.id.replace('edit_company_context', '');
                const counter = document.getElementById('edit_company_context_counter' + clientId);
                
                if (counter) {
                    // Actualizar contador en tiempo real
                    textarea.addEventListener('input', function() {
                        updateCounter(this, counter);
                    });
                    
                    // Inicializar el contador
                    updateCounter(textarea, counter);
                }
            });
        });
    </script>
@endsection
