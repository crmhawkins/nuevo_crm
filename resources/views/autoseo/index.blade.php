@extends('layouts.app')

@section('titulo', 'Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <style>
        /* === LAYOUT PRINCIPAL === */
        .css-96uzu9 {
            z-index: -1 !important;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .autoseo-wrapper {
            padding: 2rem 0;
        }

        .autoseo-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            color: white;
        }

        .autoseo-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .autoseo-header .subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        /* === TARJETAS DE CLIENTES === */
        .client-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .client-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            border-color: #667eea;
        }

        .client-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f3f4f6;
        }

        .client-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .client-info {
            flex: 1;
        }

        .client-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .client-url {
            color: #6b7280;
            font-size: 0.9rem;
            margin: 0.25rem 0 0 0;
        }

        .client-url a {
            color: #667eea;
            text-decoration: none;
            transition: color 0.2s;
        }

        .client-url a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .client-meta {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f9fafb;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .meta-item i {
            color: #667eea;
        }

        /* === ESTADOS SEO === */
        .seo-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .seo-status-pendiente {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 2px solid #fbbf24;
        }

        .seo-status-procesando {
            background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
            color: #1e40af;
            border: 2px solid #3b82f6;
            animation: pulse-processing 2s ease-in-out infinite;
        }

        .seo-status-completado {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #10b981;
        }

        .seo-status-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 2px solid #ef4444;
        }

        /* === ALERTAS === */
        .alert-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ef4444;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            animation: pulse-alert 1.5s ease-in-out infinite;
        }

        @keyframes pulse-alert {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
            }
        }

        .card-warning {
            border-left: 4px solid #fbbf24 !important;
            background: linear-gradient(to right, #fef3c7 0%, white 10%);
        }

        .card-danger {
            border-left: 4px solid #ef4444 !important;
            background: linear-gradient(to right, #fee2e2 0%, white 10%);
        }

        .card-info {
            border-left: 4px solid #3b82f6 !important;
            background: linear-gradient(to right, #dbeafe 0%, white 10%);
        }

        /* === BOTONES === */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-modern {
            border-radius: 10px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-modern i {
            margin-right: 0.5rem;
        }

        /* === ANIMACIONES === */
        @keyframes pulse-processing {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 4px 16px rgba(59, 130, 246, 0.6);
            }
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .icon-bounce {
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        /* === MODALES === */
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

        .modal-xl {
            max-width: 1200px;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        code {
            font-size: 0.9rem;
            padding: 0.2rem 0.5rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        /* === ESTADÍSTICAS === */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .stat-icon.yellow {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
        }

        .stat-info p {
            margin: 0;
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* === BUSCADOR === */
        .search-container {
            position: relative;
        }

        .search-box {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
            transform: translateY(-2px);
        }

        .search-icon {
            color: #667eea;
            font-size: 1.25rem;
        }

        .search-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 1rem;
            color: #1f2937;
            background: transparent;
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        .search-results-count {
            color: #667eea;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 8px;
        }

        .client-card.hidden {
            display: none;
        }

        .no-results {
            background: white;
            border-radius: 16px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: none;
        }

        .no-results.show {
            display: block;
        }

        .no-results i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .no-results h3 {
            color: #6b7280;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .no-results p {
            color: #9ca3af;
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .autoseo-header h1 {
                font-size: 1.75rem;
            }

            .client-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-buttons {
                width: 100%;
                justify-content: flex-start;
            }

            .search-box {
                padding: 1rem;
            }

            .search-results-count {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container autoseo-wrapper">
        <div class="row">
            <div class="col-12">
                <!-- Header Principal -->
                <div class="autoseo-header">
                    <h1>
                        <i class="fas fa-robot"></i>
                        AutoSEO
                    </h1>
                    <p class="subtitle mb-3">Gestión automática de SEO y contenido para WordPress</p>
                        <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light btn-modern" data-bs-toggle="modal"
                                data-bs-target="#createPuntualSeoModal">
                                <i class="fas fa-bolt"></i> SEO Manual
                            </button>
                        <button type="button" class="btn btn-warning btn-modern" data-bs-toggle="modal"
                                data-bs-target="#createClientModal">
                            <i class="fas fa-plus"></i> Nuevo Cliente
                            </button>
                        </div>
                    </div>

                <!-- Estadísticas -->
                @php
                    $totalClients = $clients->count();
                    $seoHoy = $clients->filter(fn($c) => $c->seo_hoy)->count();
                    $completados = $clients->filter(fn($c) => $c->seo_hoy && $c->seo_hoy->estado === 'completado')->count();
                    $pendientes = $clients->filter(fn($c) => $c->seo_hoy && $c->seo_hoy->estado === 'pendiente')->count();
                @endphp

                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon purple">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $totalClients }}</h3>
                            <p>Clientes Totales</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $completados }}</h3>
                            <p>SEOs Completados Hoy</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon yellow">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $pendientes }}</h3>
                            <p>SEOs Pendientes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon red">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>{{ $seoHoy }}</h3>
                            <p>Programados Hoy</p>
                        </div>
                    </div>
                </div>

                <!-- Buscador -->
                <div class="search-container mb-4">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" 
                               id="clientSearch" 
                               class="search-input" 
                               placeholder="Buscar clientes por nombre, email, URL..."
                               autocomplete="off">
                        <span class="search-results-count" id="searchResultsCount"></span>
                    </div>
                </div>

                <!-- Mensaje sin resultados -->
                <div class="no-results" id="noResults">
                    <i class="fas fa-search"></i>
                    <h3>No se encontraron resultados</h3>
                    <p>Intenta con otros términos de búsqueda</p>
                </div>

                <!-- Lista de Clientes -->
                <div id="clientsList">
                                    @foreach ($clients as $client)
                    <div class="client-card @if($client->alert_info['is_overdue']) card-danger @elseif($client->alert_info['is_expiring_soon']) card-warning @elseif($client->alert_info['is_expiring_in_one_month']) card-info @endif" 
                         data-client-id="{{ $client->id }}"
                         data-search-text="{{ strtolower($client->client_name . ' ' . $client->client_email . ' ' . $client->url) }}">
                        
                        <div class="client-card-header">
                            <div class="d-flex align-items-center flex-1">
                                <div class="client-avatar">
                                    {{ strtoupper(substr($client->client_name, 0, 2)) }}
                                </div>
                                <div class="client-info">
                                    <h2 class="client-name">
                                                {{ $client->client_name }}
                                                @if($client->alert_info['is_overdue'])
                                            <span class="badge bg-danger">⚠️ Vencido</span>
                                                @elseif($client->alert_info['is_expiring_soon'])
                                            <span class="badge bg-warning">⏰ Pronto</span>
                                                @elseif($client->alert_info['is_expiring_in_one_month'])
                                            <span class="badge bg-info">📅 Expira</span>
                                                @endif
                                    </h2>
                                    <p class="client-url">
                                        <a href="{{ $client->url }}" target="_blank">
                                            <i class="fas fa-globe"></i> {{ $client->url }}
                                        </a>
                                    </p>
                                    <p class="client-url mb-0">
                                        <i class="fas fa-envelope"></i> {{ $client->client_email }}
                                    </p>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <button type="button" class="btn btn-info btn-sm btn-modern" data-bs-toggle="modal"
                                    data-bs-target="#testClientModal{{ $client->id }}">
                                    <i class="fas fa-vial"></i> Test
                                </button>
                                <button type="button" class="btn btn-primary btn-sm btn-modern" data-bs-toggle="modal"
                                    data-bs-target="#editClientModal{{ $client->id }}">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button type="button" class="btn btn-success btn-sm btn-modern" data-bs-toggle="modal"
                                    data-bs-target="#clientModal{{ $client->id }}">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <form action="{{ route('autoseo.delete') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $client->id }}">
                                    <button type="submit" class="btn btn-danger btn-sm btn-modern"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este cliente?')">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Meta información -->
                        <div class="client-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar-check"></i>
                                <span><strong>Creado:</strong> {{ $client->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($client->alert_info['next_date'])
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>
                                        <strong>Próximo SEO:</strong> 
                                        {{ $client->alert_info['next_date']->format('d/m/Y') }}
                                        @if($client->alert_info['days_until'] < 0)
                                            (Hace {{ abs($client->alert_info['days_until']) }} días)
                                        @elseif($client->alert_info['days_until'] == 0)
                                            (Hoy)
                                        @else
                                            (En {{ $client->alert_info['days_until'] }} días)
                                        @endif
                                    </span>
                                </div>
                            @endif
                            @if($client->alert_info['total_pending'] > 0)
                                <div class="meta-item">
                                    <i class="fas fa-tasks"></i>
                                    <span><strong>Programaciones:</strong> {{ $client->alert_info['total_pending'] }}</span>
                                </div>
                            @endif
                                                @if($client->seo_hoy)
                                                    @php
                                                        $estado = $client->seo_hoy->estado;
                                                        $iconos = [
                                                            'pendiente' => 'fa-clock',
                                                            'procesando' => 'fa-spinner',
                                                            'completado' => 'fa-check-circle',
                                                            'error' => 'fa-exclamation-triangle'
                                                        ];
                                                        $textos = [
                                                            'pendiente' => 'Pendiente',
                                                            'procesando' => 'Procesando',
                                                            'completado' => 'Completado',
                                                            'error' => 'Error'
                                                        ];
                                                    @endphp
                                                    <div class="seo-status-badge seo-status-{{ $estado }}">
                                                        <i class="fas {{ $iconos[$estado] ?? 'fa-question' }} @if($estado === 'procesando') spinner @endif"></i>
                                    <span><strong>Hoy:</strong> {{ $textos[$estado] ?? 'Desconocido' }}</span>
                                                    </div>
                                                @else
                                <span class="text-muted">
                                    <i class="fas fa-minus-circle"></i> Sin SEO programado hoy
                                                    </span>
                                                        @endif
                                                    </div>
                    </div>
                   
                    <!-- Modales -->

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

                                                <!-- Modal Test Cliente -->
                                                <div class="modal fade" id="testClientModal{{ $client->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="testClientModalLabel{{ $client->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-info text-white">
                                                                <h5 class="modal-title" id="testClientModalLabel{{ $client->id }}">
                                                                    <i class="fas fa-vial"></i> Test de Conexión - {{ $client->client_name }}
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white"
                                                                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-info">
                                                                    <i class="fas fa-info-circle"></i> 
                                                                    Se verificará que WordPress esté correctamente configurado para AutoSEO
                                                                </div>
                                                                
                                                                <div id="test-progress-{{ $client->id }}">
                                                                    <!-- 1. Credenciales normales -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>1. Verificando credenciales WordPress (username:password)</strong>
                                                                            <small class="text-muted ms-2">(Opcional - deprecado por WordPress)</small>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 2. Credenciales de aplicación -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>2. Verificando Application Password (user_app:password_app)</strong>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 3. Shortcodes sin imágenes -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>3. Endpoint: /wp-json/superindex/v1/shortcodes</strong>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 4. Shortcodes completos -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>4. Endpoint: /wp-json/superindex/v1/shortcodes/completo</strong>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 5. Actualizar shortcode -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>5. Endpoint: /wp-json/custom/v1/update-power-shortcode/</strong>
                                                                            <small class="text-muted ms-2">(Requiere código en functions.php)</small>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 6. Media upload -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>6. Endpoint: /wp-json/wp/v2/media (solo verificación)</strong>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>

                                                                    <!-- 7. Posts creation -->
                                                                    <div class="test-step mb-3">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="spinner-border spinner-border-sm text-secondary me-2 test-spinner" role="status" style="display:none;">
                                                                                <span class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            <i class="fas fa-circle text-secondary test-icon me-2" style="display:none;"></i>
                                                                            <strong>7. Endpoint: /wp-json/wp/v2/posts (solo verificación)</strong>
                                                                        </div>
                                                                        <div class="test-result ms-4 mt-1 text-muted"></div>
                                                                    </div>
                                                                </div>

                                                                <div class="mt-4" id="test-summary-{{ $client->id }}" style="display:none;">
                                                                    <hr>
                                                                    <div class="alert alert-success" id="test-success-{{ $client->id }}" style="display:none;">
                                                                        <i class="fas fa-check-circle"></i> <strong>¡Configuración correcta!</strong> WordPress está listo para AutoSEO.
                                                                    </div>
                                                                    <div class="alert alert-danger" id="test-error-{{ $client->id }}" style="display:none;">
                                                                        <i class="fas fa-exclamation-triangle"></i> <strong>Configuración incompleta</strong> - Revisa los errores arriba.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times"></i> Cerrar
                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal Editar Cliente -->
                                                <div class="modal fade" id="editClientModal{{ $client->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="editClientModalLabel{{ $client->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <form action="{{ route('autoseo.update') }}"
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
                                                                    
                                                                    <!-- Configuración Periódica SEO -->
                                                                    <div class="col-12">
                                                                        <h6 class="mt-3 mb-3">📅 Configuración Periódica SEO</h6>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_seo_frequency{{ $client->id }}" class="form-label">Frecuencia</label>
                                                                        <select class="form-control" id="edit_seo_frequency{{ $client->id }}" name="seo_frequency">
                                                                            <option value="manual" {{ ($client->seo_frequency ?? 'manual') == 'manual' ? 'selected' : '' }}>Manual</option>
                                                                            <option value="weekly" {{ ($client->seo_frequency ?? '') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                                                            <option value="biweekly" {{ ($client->seo_frequency ?? '') == 'biweekly' ? 'selected' : '' }}>Quincenal</option>
                                                                            <option value="monthly" {{ ($client->seo_frequency ?? '') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                                                                            <option value="bimonthly" {{ ($client->seo_frequency ?? '') == 'bimonthly' ? 'selected' : '' }}>Bimensual</option>
                                                                            <option value="quarterly" {{ ($client->seo_frequency ?? '') == 'quarterly' ? 'selected' : '' }}>Trimestral</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="edit_seo_time{{ $client->id }}" class="form-label">Hora</label>
                                                                        <input type="time" class="form-control" id="edit_seo_time{{ $client->id }}" name="seo_time" value="{{ $client->seo_time ?? '09:00' }}">
                                                                    </div>
                                                                    <div class="col-md-6" id="edit_seo_day_of_month_div{{ $client->id }}" style="display: none;">
                                                                        <label for="edit_seo_day_of_month{{ $client->id }}" class="form-label">Día del Mes</label>
                                                                        <select class="form-control" id="edit_seo_day_of_month{{ $client->id }}" name="seo_day_of_month">
                                                                            <option value="1" {{ ($client->seo_day_of_month ?? '15') == '1' ? 'selected' : '' }}>1</option>
                                                                            <option value="5" {{ ($client->seo_day_of_month ?? '15') == '5' ? 'selected' : '' }}>5</option>
                                                                            <option value="10" {{ ($client->seo_day_of_month ?? '15') == '10' ? 'selected' : '' }}>10</option>
                                                                            <option value="15" {{ ($client->seo_day_of_month ?? '15') == '15' ? 'selected' : '' }}>15</option>
                                                                            <option value="20" {{ ($client->seo_day_of_month ?? '15') == '20' ? 'selected' : '' }}>20</option>
                                                                            <option value="25" {{ ($client->seo_day_of_month ?? '15') == '25' ? 'selected' : '' }}>25</option>
                                                                            <option value="last" {{ ($client->seo_day_of_month ?? '15') == 'last' ? 'selected' : '' }}>Último día</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6" id="edit_seo_day_of_week_div{{ $client->id }}" style="display: none;">
                                                                        <label for="edit_seo_day_of_week{{ $client->id }}" class="form-label">Día de la Semana</label>
                                                                        <select class="form-control" id="edit_seo_day_of_week{{ $client->id }}" name="seo_day_of_week">
                                                                            <option value="monday" {{ ($client->seo_day_of_week ?? 'friday') == 'monday' ? 'selected' : '' }}>Lunes</option>
                                                                            <option value="tuesday" {{ ($client->seo_day_of_week ?? 'friday') == 'tuesday' ? 'selected' : '' }}>Martes</option>
                                                                            <option value="wednesday" {{ ($client->seo_day_of_week ?? 'friday') == 'wednesday' ? 'selected' : '' }}>Miércoles</option>
                                                                            <option value="thursday" {{ ($client->seo_day_of_week ?? 'friday') == 'thursday' ? 'selected' : '' }}>Jueves</option>
                                                                            <option value="friday" {{ ($client->seo_day_of_week ?? 'friday') == 'friday' ? 'selected' : '' }}>Viernes</option>
                                                                            <option value="saturday" {{ ($client->seo_day_of_week ?? 'friday') == 'saturday' ? 'selected' : '' }}>Sábado</option>
                                                                            <option value="sunday" {{ ($client->seo_day_of_week ?? 'friday') == 'sunday' ? 'selected' : '' }}>Domingo</option>
                                                                        </select>
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
                                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Modal SEO Puntual -->
    <div class="modal fade" id="createPuntualSeoModal" tabindex="-1" aria-labelledby="createPuntualSeoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('autoseo.createPuntualSeo') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="createPuntualSeoModalLabel">
                            <i class="fas fa-bolt"></i> Ejecutar SEO Inmediatamente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Nota:</strong> Se creará una programación SEO única para <strong>hoy</strong> al cliente seleccionado.
                        </div>
                        
                        <div class="mb-3">
                            <label for="puntual_client_id" class="form-label fw-bold">
                                <i class="fas fa-user me-1"></i>Selecciona un Cliente
                            </label>
                            <select class="form-select" id="puntual_client_id" name="client_id" required>
                                <option value="">-- Selecciona un cliente --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">
                                        {{ $client->client_name }} - {{ $client->url }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="puntual_hora" class="form-label fw-bold">
                                <i class="fas fa-clock me-1"></i>Hora de Ejecución
                            </label>
                            <input type="time" class="form-control" id="puntual_hora" name="hora" value="09:00" required>
                            <small class="text-muted">Hora en la que se ejecutará el SEO hoy</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-bolt"></i> Crear SEO Puntual
                        </button>
                    </div>
                </form>
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
                        
                        <!-- Configuración Periódica SEO -->
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="mb-3">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                Configuración Periódica SEO
                            </h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="seo_frequency" class="form-label">Frecuencia de SEO</label>
                            <select class="form-select" id="seo_frequency" name="seo_frequency">
                                <option value="manual">Manual (Solo cuando se solicite)</option>
                                <option value="weekly">Semanal</option>
                                <option value="biweekly">Quincenal</option>
                                <option value="monthly" selected>Mensual</option>
                                <option value="bimonthly">Bimensual</option>
                                <option value="quarterly">Trimestral</option>
                            </select>
                            <small class="form-text text-muted">
                                Define con qué frecuencia se ejecutará automáticamente el análisis SEO.
                            </small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="seo_day_of_month" class="form-label">Día del Mes</label>
                            <select class="form-select" id="seo_day_of_month" name="seo_day_of_month">
                                <option value="1">1</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15" selected>15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="last">Último día del mes</option>
                            </select>
                            <small class="form-text text-muted">
                                Solo aplica para frecuencias mensuales o superiores.
                            </small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="seo_day_of_week" class="form-label">Día de la Semana</label>
                            <select class="form-select" id="seo_day_of_week" name="seo_day_of_week">
                                <option value="monday">Lunes</option>
                                <option value="tuesday">Martes</option>
                                <option value="wednesday">Miércoles</option>
                                <option value="thursday">Jueves</option>
                                <option value="friday" selected>Viernes</option>
                                <option value="saturday">Sábado</option>
                                <option value="sunday">Domingo</option>
                            </select>
                            <small class="form-text text-muted">
                                Solo aplica para frecuencias semanales o quincenales.
                            </small>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="seo_time" class="form-label">Hora de Ejecución</label>
                            <input type="time" class="form-control" id="seo_time" name="seo_time" value="09:00">
                            <small class="form-text text-muted">
                                Hora en la que se ejecutará el análisis SEO automático.
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
    <script>
        function initCharacterCounter() {
            console.log('=== INICIANDO CONTADOR DE CARACTERES ===');
            
            // Para el modal de crear cliente
            const createTextarea = document.getElementById('company_context');
            const createCounter = document.getElementById('company_context_counter');
            
            console.log('Textarea crear:', createTextarea);
            console.log('Contador crear:', createCounter);
            
            if (createTextarea && createCounter) {
                console.log('✅ Elementos de crear encontrados');
                
                // Función simple para actualizar
                function updateCreateCounter() {
                    const length = createTextarea.value.length;
                    createCounter.textContent = length + ' / 2000 caracteres';
                    console.log('📝 Actualizando crear:', length, 'caracteres');
                }
                
                // Eventos
                createTextarea.addEventListener('input', updateCreateCounter);
                createTextarea.addEventListener('keyup', updateCreateCounter);
                
                // Inicializar
                updateCreateCounter();
            } else {
                console.log('❌ Elementos de crear NO encontrados');
            }
            
            // Para modales de editar cliente
            const editTextareas = document.querySelectorAll('textarea[id^="edit_company_context"]');
            console.log('Textareas de editar encontrados:', editTextareas.length);
            
            editTextareas.forEach(function(textarea, index) {
                const clientId = textarea.id.replace('edit_company_context', '');
                const counter = document.getElementById('edit_company_context_counter' + clientId);
                
                console.log(`Textarea ${index}:`, textarea.id);
                console.log(`Contador ${index}:`, counter ? counter.id : 'NO ENCONTRADO');
                
                if (counter) {
                    console.log(`✅ Elementos de editar ${index} encontrados`);
                    
                    // Función simple para actualizar
                    function updateEditCounter() {
                        const length = textarea.value.length;
                        counter.textContent = length + ' / 2000 caracteres';
                        console.log(`📝 Actualizando editar ${index}:`, length, 'caracteres');
                    }
                    
                    // Eventos
                    textarea.addEventListener('input', updateEditCounter);
                    textarea.addEventListener('keyup', updateEditCounter);
                    
                    // Inicializar
                    updateEditCounter();
                } else {
                    console.log(`❌ Contador de editar ${index} NO encontrado`);
                }
            });
            
            console.log('=== FIN INICIALIZACIÓN CONTADOR ===');
        }
        
        // Ejecutar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM cargado, iniciando contadores...');
            initCharacterCounter();
        });
        
        // También ejecutar cuando se abren modales
        document.addEventListener('shown.bs.modal', function() {
            console.log('📱 Modal abierto, reiniciando contadores...');
            setTimeout(initCharacterCounter, 200);
        });
        
        // Control de campos de frecuencia SEO
        const seoFrequencySelect = document.getElementById('seo_frequency');
        const seoDayOfMonthDiv = document.getElementById('seo_day_of_month').parentElement;
        const seoDayOfWeekDiv = document.getElementById('seo_day_of_week').parentElement;
        
        function toggleFrequencyFields() {
            const frequency = seoFrequencySelect.value;
            
            // Mostrar/ocultar campos según la frecuencia
            if (frequency === 'manual') {
                seoDayOfMonthDiv.style.display = 'none';
                seoDayOfWeekDiv.style.display = 'none';
            } else if (frequency === 'weekly' || frequency === 'biweekly') {
                seoDayOfMonthDiv.style.display = 'none';
                seoDayOfWeekDiv.style.display = 'block';
            } else { // monthly, bimonthly, quarterly
                seoDayOfMonthDiv.style.display = 'block';
                seoDayOfWeekDiv.style.display = 'none';
            }
        }
        
        // Inicializar campos de frecuencia
        if (seoFrequencySelect) {
            seoFrequencySelect.addEventListener('change', toggleFrequencyFields);
            toggleFrequencyFields(); // Ejecutar al cargar la página
        }
        
        // Control de campos de frecuencia para modales de edición
        document.querySelectorAll('[id^="edit_seo_frequency"]').forEach(function(select) {
            const clientId = select.id.replace('edit_seo_frequency', '');
            const seoDayOfMonthDiv = document.getElementById('edit_seo_day_of_month_div' + clientId);
            const seoDayOfWeekDiv = document.getElementById('edit_seo_day_of_week_div' + clientId);
            
            function toggleEditFrequencyFields() {
                const frequency = select.value;
                
                // Mostrar/ocultar campos según la frecuencia
                if (frequency === 'manual') {
                    seoDayOfMonthDiv.style.display = 'none';
                    seoDayOfWeekDiv.style.display = 'none';
                } else if (frequency === 'weekly' || frequency === 'biweekly') {
                    seoDayOfMonthDiv.style.display = 'none';
                    seoDayOfWeekDiv.style.display = 'block';
                } else { // monthly, bimonthly, quarterly
                    seoDayOfMonthDiv.style.display = 'block';
                    seoDayOfWeekDiv.style.display = 'none';
                }
            }
            
            select.addEventListener('change', toggleEditFrequencyFields);
            toggleEditFrequencyFields(); // Ejecutar al cargar
        });

        // Auto-actualizar estados de SEO cada 10 segundos
        function actualizarEstadosSeo() {
            fetch('/api/autoseo/programacion/listar?fecha=' + new Date().toISOString().split('T')[0])
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        data.data.forEach(prog => {
                            updateSeoStatus(prog.autoseo_id, prog.estado);
                        });
                    }
                })
                .catch(error => console.error('Error actualizando estados SEO:', error));
        }

        function updateSeoStatus(autoseoId, estado) {
            // Buscar la fila del cliente
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const clientId = row.getAttribute('data-client-id');
                if (clientId == autoseoId) {
                    const seoCell = row.cells[3]; // Columna "SEO Hoy"
                    
                    const iconos = {
                        'pendiente': 'fa-clock',
                        'procesando': 'fa-spinner',
                        'completado': 'fa-check-circle',
                        'error': 'fa-exclamation-triangle'
                    };
                    
                    const textos = {
                        'pendiente': 'Pendiente',
                        'procesando': 'Procesando',
                        'completado': 'Completado',
                        'error': 'Error'
                    };
                    
                    const spinnerClass = estado === 'procesando' ? 'spinner' : '';
                    
                    seoCell.innerHTML = `
                        <div class="seo-status-badge seo-status-${estado}">
                            <i class="fas ${iconos[estado]} ${spinnerClass}"></i>
                            <span>${textos[estado]}</span>
                        </div>
                    `;
                }
            });
        }

        // Iniciar actualización automática cada 10 segundos
        setInterval(actualizarEstadosSeo, 10000);
        
        // Actualizar una vez al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            actualizarEstadosSeo();
        });

        // ===== BUSCADOR DE CLIENTES =====
        const searchInput = document.getElementById('clientSearch');
        const clientCards = document.querySelectorAll('.client-card');
        const noResultsMsg = document.getElementById('noResults');
        const resultsCount = document.getElementById('searchResultsCount');
        const totalClients = clientCards.length;

        function filterClients() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            if (searchTerm === '') {
                // Mostrar todos
                clientCards.forEach(card => {
                    card.classList.remove('hidden');
                });
                visibleCount = totalClients;
                resultsCount.textContent = '';
                noResultsMsg.classList.remove('show');
            } else {
                // Filtrar
                clientCards.forEach(card => {
                    const searchText = card.getAttribute('data-search-text');
                    
                    if (searchText && searchText.includes(searchTerm)) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Actualizar contador
                resultsCount.textContent = `${visibleCount} de ${totalClients}`;

                // Mostrar mensaje si no hay resultados
                if (visibleCount === 0) {
                    noResultsMsg.classList.add('show');
                } else {
                    noResultsMsg.classList.remove('show');
                }
            }
        }

        if (searchInput) {
            searchInput.addEventListener('input', filterClients);
            searchInput.addEventListener('keyup', filterClients);
        }

        // ===== FUNCIONES DE TEST DE CONEXIÓN =====
        @foreach($clients as $client)
        // Auto-iniciar test al abrir modal
        document.getElementById('testClientModal{{ $client->id }}').addEventListener('shown.bs.modal', function() {
            runTest{{ $client->id }}();
        });
        
        function runTest{{ $client->id }}() {
            const clientId = {{ $client->id }};
            const url = '{{ $client->url }}';
            const username = '{{ $client->username }}';
            const password = '{{ $client->password }}';
            const userApp = '{{ $client->user_app }}';
            const passwordApp = '{{ $client->password_app }}';
            
            // Resetear UI
            const steps = document.querySelectorAll('#test-progress-' + clientId + ' .test-step');
            steps.forEach((step, index) => {
                const spinner = step.querySelector('.test-spinner');
                const icon = step.querySelector('.test-icon');
                const result = step.querySelector('.test-result');
                
                spinner.style.display = 'none';
                icon.style.display = 'none';
                icon.className = 'fas fa-circle text-secondary test-icon me-2';
                result.textContent = '';
                result.className = 'test-result ms-4 mt-1 text-muted';
            });
            
            document.getElementById('test-summary-' + clientId).style.display = 'none';
            document.getElementById('test-success-' + clientId).style.display = 'none';
            document.getElementById('test-error-' + clientId).style.display = 'none';
            
            // Ejecutar tests en secuencia
            runTestSequence{{ $client->id }}(clientId, url, username, password, userApp, passwordApp, 0);
        }
        
        async function runTestSequence{{ $client->id }}(clientId, url, username, password, userApp, passwordApp, stepIndex) {
            const steps = document.querySelectorAll('#test-progress-' + clientId + ' .test-step');
            
            if (stepIndex >= steps.length) {
                // Todos los tests completados
                finishTest{{ $client->id }}(clientId);
                return;
            }
            
            const step = steps[stepIndex];
            const spinner = step.querySelector('.test-spinner');
            const icon = step.querySelector('.test-icon');
            const result = step.querySelector('.test-result');
            
            // Mostrar spinner
            spinner.style.display = 'inline-block';
            icon.style.display = 'none';
            
            try {
                let success = false;
                let message = '';
                
                switch(stepIndex) {
                    case 0: // Credenciales normales (opcional, puede fallar)
                        const test1 = await testWordPressAuth(url, username, password);
                        // 401 es aceptable para credenciales normales (WordPress deprecó esto)
                        if (!test1.success && test1.message.includes('401')) {
                            success = true;
                            message = 'No funciona (WordPress deprecó este método) - Usar Application Password';
                        } else {
                            success = test1.success;
                            message = test1.message;
                        }
                        break;
                        
                    case 1: // Credenciales de aplicación
                        const test2 = await testWordPressAuth(url, userApp, passwordApp);
                        success = test2.success;
                        message = test2.message;
                        break;
                        
                    case 2: // Shortcodes sin imágenes
                        const test3 = await testEndpoint(url, '/wp-json/superindex/v1/shortcodes', userApp, passwordApp, 'GET');
                        success = test3.success;
                        message = test3.message;
                        break;
                        
                    case 3: // Shortcodes completos
                        const test4 = await testEndpoint(url, '/wp-json/superindex/v1/shortcodes/completo', userApp, passwordApp, 'GET');
                        success = test4.success;
                        message = test4.message;
                        break;
                        
                    case 4: // Actualizar shortcode (solo verificar que el endpoint existe)
                        const test5 = await testEndpoint(url, '/wp-json/custom/v1/update-power-shortcode/', userApp, passwordApp, 'OPTIONS');
                        // 404 es aceptable si no está instalado el código custom
                        if (!test5.success && test5.message.includes('404')) {
                            success = true;
                            message = 'Endpoint no instalado (opcional) - Agregar código a functions.php si es necesario';
                        } else {
                            success = test5.success;
                            message = test5.message;
                        }
                        break;
                        
                    case 5: // Media (solo verificar acceso)
                        const test6 = await testEndpoint(url, '/wp-json/wp/v2/media', userApp, passwordApp, 'OPTIONS');
                        success = test6.success;
                        message = test6.message;
                        break;
                        
                    case 6: // Posts (solo verificar acceso)
                        const test7 = await testEndpoint(url, '/wp-json/wp/v2/posts', userApp, passwordApp, 'OPTIONS');
                        success = test7.success;
                        message = test7.message;
                        break;
                }
                
                // Ocultar spinner, mostrar resultado
                spinner.style.display = 'none';
                icon.style.display = 'inline-block';
                
                if (success) {
                    icon.className = 'fas fa-check-circle text-success test-icon me-2';
                    result.className = 'test-result ms-4 mt-1 text-success';
                    result.innerHTML = '<i class="fas fa-check"></i> ' + message;
                } else {
                    // Diferenciar entre errores críticos y advertencias
                    const isCritical = stepIndex !== 0 && stepIndex !== 4; // Solo críticos si no son paso 0 ni 4
                    if (isCritical) {
                        icon.className = 'fas fa-times-circle text-danger test-icon me-2';
                        result.className = 'test-result ms-4 mt-1 text-danger';
                        result.innerHTML = '<i class="fas fa-times"></i> ' + message;
                    } else {
                        icon.className = 'fas fa-exclamation-triangle text-warning test-icon me-2';
                        result.className = 'test-result ms-4 mt-1 text-warning';
                        result.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                    }
                }
                
                // Continuar con el siguiente test después de un pequeño delay
                setTimeout(() => {
                    runTestSequence{{ $client->id }}(clientId, url, username, password, userApp, passwordApp, stepIndex + 1);
                }, 500);
                
            } catch (error) {
                spinner.style.display = 'none';
                icon.style.display = 'inline-block';
                icon.className = 'fas fa-times-circle text-danger test-icon me-2';
                result.className = 'test-result ms-4 mt-1 text-danger';
                result.innerHTML = '<i class="fas fa-times"></i> Error: ' + error.message;
                
                // Continuar a pesar del error
                setTimeout(() => {
                    runTestSequence{{ $client->id }}(clientId, url, username, password, userApp, passwordApp, stepIndex + 1);
                }, 500);
            }
        }
        
        function finishTest{{ $client->id }}(clientId) {
            const steps = document.querySelectorAll('#test-progress-' + clientId + ' .test-step');
            let hasCriticalErrors = false;
            let hasWarnings = false;
            
            steps.forEach(step => {
                const icon = step.querySelector('.test-icon');
                if (icon.classList.contains('text-danger')) {
                    hasCriticalErrors = true;
                }
                if (icon.classList.contains('text-warning')) {
                    hasWarnings = true;
                }
            });
            
            document.getElementById('test-summary-' + clientId).style.display = 'block';
            
            if (!hasCriticalErrors) {
                document.getElementById('test-success-' + clientId).style.display = 'block';
                // Si solo hay warnings, agregar nota
                if (hasWarnings) {
                    const successAlert = document.getElementById('test-success-' + clientId);
                    successAlert.innerHTML = '<i class="fas fa-check-circle"></i> <strong>¡WordPress está listo para AutoSEO!</strong><br><small class="text-muted">Nota: Algunos endpoints opcionales no están configurados (no son críticos)</small>';
                }
            } else {
                document.getElementById('test-error-' + clientId).style.display = 'block';
            }
        }
        @endforeach
        
        // Funciones auxiliares de test
        async function testWordPressAuth(baseUrl, username, password) {
            try {
                const response = await fetch('/autoseo/test-wp-auth', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        url: baseUrl,
                        username: username,
                        password: password
                    })
                });
                
                if (!response.ok) {
                    // Intentar leer el error como JSON
                    let errorMsg = 'Error del servidor (HTTP ' + response.status + ')';
                    try {
                        const errorData = await response.json();
                        errorMsg = errorData.message || errorMsg;
                    } catch (e) {
                        // No es JSON, probablemente HTML
                        const text = await response.text();
                        if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                            errorMsg = 'El servidor devolvió HTML en lugar de JSON. Verifica los logs del servidor.';
                        }
                    }
                    return {
                        success: false,
                        message: errorMsg
                    };
                }
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error en testWordPressAuth:', error);
                return {
                    success: false,
                    message: 'Error de red: ' + error.message
                };
            }
        }
        
        async function testEndpoint(baseUrl, endpoint, username, password, method = 'GET') {
            try {
                const response = await fetch('/autoseo/test-endpoint', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        url: baseUrl,
                        endpoint: endpoint,
                        username: username,
                        password: password,
                        method: method
                    })
                });
                
                if (!response.ok) {
                    let errorMsg = 'Error del servidor (HTTP ' + response.status + ')';
                    try {
                        const errorData = await response.json();
                        errorMsg = errorData.message || errorMsg;
                    } catch (e) {
                        const text = await response.text();
                        if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                            errorMsg = 'El servidor devolvió HTML en lugar de JSON. Verifica los logs del servidor.';
                        }
                    }
                    return {
                        success: false,
                        message: errorMsg
                    };
                }
                
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error en testEndpoint:', error);
                return {
                    success: false,
                    message: 'Error de red: ' + error.message
                };
            }
        }
    </script>
@endsection
