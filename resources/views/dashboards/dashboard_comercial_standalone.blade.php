<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Comercial - CRM Hawkins</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-custom {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .btn-custom.w-100 {
            border-radius: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
        .visita-paso {
            min-height: 300px;
        }
        .valoracion-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin: 5px;
        }
        .table-custom {
            border-radius: 15px;
            overflow: hidden;
        }
        .badge-custom {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Mobile-First Responsive Design */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 10px;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            /* Header móvil optimizado */
            .header-mobile {
                padding: 15px 0;
                text-align: center;
            }
            
            .header-mobile h2 {
                font-size: 1.3rem;
                margin-bottom: 5px;
            }
            
            .header-mobile p {
                font-size: 0.9rem;
                margin-bottom: 10px;
            }
            
            /* Timer móvil */
            .timer-mobile {
                background: rgba(255,255,255,0.1);
                border-radius: 15px;
                padding: 15px;
                margin: 10px 0;
            }
            
            .timer-mobile #timer {
                font-size: 2rem;
                font-weight: bold;
                margin-bottom: 5px;
            }
            
            /* Botones móviles - ancho completo */
            .btn-mobile {
                width: 100%;
                margin: 5px 0;
                padding: 12px 20px;
                font-size: 1rem;
                border-radius: 10px;
                font-weight: 600;
            }
            
            .btn-group-mobile {
                display: flex;
                flex-direction: column;
                gap: 8px;
                margin-top: 10px;
            }
            
            /* Cards de visitas móviles */
            .visita-card {
                background: white;
                border-radius: 15px;
                padding: 15px;
                margin-bottom: 15px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                border-left: 4px solid #667eea;
            }
            
            .visita-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            
            .visita-card-fecha {
                font-size: 0.8rem;
                color: #666;
                background: #f8f9fa;
                padding: 4px 8px;
                border-radius: 10px;
            }
            
            .visita-card-cliente {
                font-weight: 600;
                font-size: 1.1rem;
                color: #333;
                margin-bottom: 8px;
            }
            
            .visita-card-tipo {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 15px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-bottom: 8px;
            }
            
            .visita-card-tipo.presencial {
                background: #e3f2fd;
                color: #1976d2;
            }
            
            .visita-card-tipo.telefonico {
                background: #e8f5e8;
                color: #388e3c;
            }
            
            .visita-card-valoracion {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
            }
            
            .visita-card-plan {
                margin: 8px 0;
                padding: 8px;
                background: rgba(255, 193, 7, 0.1);
                border-radius: 8px;
                border-left: 3px solid #ffc107;
            }
            
            .visita-card-estado {
                margin: 8px 0;
                padding: 8px;
                background: rgba(108, 117, 125, 0.1);
                border-radius: 8px;
                border-left: 3px solid #6c757d;
            }
            
            .visita-card-audio {
                margin: 8px 0;
                padding: 8px;
                background: rgba(13, 110, 253, 0.1);
                border-radius: 8px;
                border-left: 3px solid #0d6efd;
            }
            
            .valoracion-stars {
                color: #ffc107;
                margin-right: 8px;
            }
            
            .valoracion-numero {
                background: #ffc107;
                color: white;
                padding: 2px 8px;
                border-radius: 10px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            .visita-card-comentarios {
                font-size: 0.9rem;
                color: #666;
                margin-bottom: 8px;
                line-height: 1.4;
            }
            
            .visita-card-seguimiento {
                display: flex;
                align-items: center;
                font-size: 0.9rem;
            }
            
            .seguimiento-badge {
                padding: 4px 8px;
                border-radius: 10px;
                font-size: 0.8rem;
                font-weight: 600;
                margin-right: 8px;
            }
            
            .seguimiento-badge.si {
                background: #d4edda;
                color: #155724;
            }
            
            .seguimiento-badge.no {
                background: #f8d7da;
                color: #721c24;
            }
            
            .visita-card-acciones {
                margin-top: 10px;
                text-align: center;
            }
            
            .btn-ver-detalle {
                background: #667eea;
                color: white;
                border: none;
                padding: 8px 20px;
                border-radius: 20px;
                font-size: 0.9rem;
                font-weight: 600;
                width: 100%;
            }
            
            /* Modal móvil */
            .modal-dialog {
                margin: 10px;
            }
            
            .modal-content {
                border-radius: 15px;
            }
            
            .modal-header {
                border-radius: 15px 15px 0 0;
            }
            
            /* Botones del modal móvil */
            .modal-btn-mobile {
                width: 100%;
                padding: 15px;
                margin: 5px 0;
                border-radius: 10px;
                font-size: 1rem;
                font-weight: 600;
            }
            
            /* Valoración móvil */
            .valoracion-mobile {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
                margin: 15px 0;
            }
            
            .valoracion-btn-mobile {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                font-weight: 600;
                font-size: 1rem;
            }
        }
        
        /* Desktop styles */
        @media (min-width: 769px) {
            .visita-card {
                display: none;
            }
        }
        
        /* Mobile styles */
        @media (max-width: 768px) {
            .table-responsive {
                display: none;
            }
        }
        
        /* SweetAlert2 Custom Styles */
        .swal2-popup-custom {
            border-radius: 15px !important;
        }
        
        .swal2-html-container {
            text-align: left !important;
        }
        
        .swal2-title {
            color: #333 !important;
            font-weight: 600 !important;
        }
        
        .swal2-close {
            color: #666 !important;
            font-size: 1.5rem !important;
        }
        
        .swal2-close:hover {
            color: #333 !important;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-chart-line me-2"></i>CRM Hawkins
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ $user->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="logout()">
                            <i class="fas fa-sign-out-alt me-2"></i>Salir
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Header con información del usuario -->
    <div class="container-fluid py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container">
            <!-- Desktop Header -->
            <div class="row align-items-center d-none d-md-flex">
                <div class="col-md-8">
                    <h2 class="mb-1">Bienvenido {{ $user->name }}</h2>
                    <p class="mb-0">Quedan {{ $diasDiferencia }} días para finalizar el mes</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-3">
                        <div class="text-center">
                            <div id="timer" class="h3 mb-0">00:00:00</div>
                            <small>Jornada</small>
                        </div>
                        <div class="btn-group">
                            <button id="startJornadaBtn" class="btn btn-light btn-sm" onclick="startJornada()" style="{{ $jornadaActiva ? 'display:none;' : '' }}">
                                <i class="fas fa-play me-1"></i>Inicio
                            </button>
                            <button id="startPauseBtn" class="btn btn-warning btn-sm" onclick="startPause()" style="{{ $jornadaActiva && !$pausaActiva ? '' : 'display:none;' }}">
                                <i class="fas fa-pause me-1"></i>Pausa
                            </button>
                            <button id="endPauseBtn" class="btn btn-info btn-sm" onclick="endPause()" style="{{ $pausaActiva ? '' : 'display:none;' }}">
                                <i class="fas fa-play me-1"></i>Reanudar
                            </button>
                            <button id="endJornadaBtn" class="btn btn-danger btn-sm" onclick="endJornada()" style="{{ $jornadaActiva ? '' : 'display:none;' }}">
                                <i class="fas fa-stop me-1"></i>Fin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Header -->
            <div class="header-mobile d-md-none">
                <h2 class="mb-1">Bienvenido {{ $user->name }}</h2>
                <p class="mb-0">Quedan {{ $diasDiferencia }} días para finalizar el mes</p>
                
                <div class="timer-mobile">
                    <div id="timer-mobile" class="h3 mb-0">00:00:00</div>
                    <small>Jornada</small>
                </div>
                
                <div class="btn-group-mobile">
                    <button id="startJornadaBtn-mobile" class="btn btn-light btn-mobile" onclick="startJornada()" style="{{ $jornadaActiva ? 'display:none;' : '' }}">
                        <i class="fas fa-play me-2"></i>Iniciar Jornada
                    </button>
                    <button id="startPauseBtn-mobile" class="btn btn-warning btn-mobile" onclick="startPause()" style="{{ $jornadaActiva && !$pausaActiva ? '' : 'display:none;' }}">
                        <i class="fas fa-pause me-2"></i>Pausar
                    </button>
                    <button id="endPauseBtn-mobile" class="btn btn-info btn-mobile" onclick="endPause()" style="{{ $pausaActiva ? '' : 'display:none;' }}">
                        <i class="fas fa-play me-2"></i>Reanudar
                    </button>
                    <button id="endJornadaBtn-mobile" class="btn btn-danger btn-mobile" onclick="endJornada()" style="{{ $jornadaActiva ? '' : 'display:none;' }}">
                        <i class="fas fa-stop me-2"></i>Finalizar Jornada
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container-fluid py-4">
        <div class="container">

            <!-- Botón Nueva Visita -->
            <div class="row mb-4">
                <div class="col-12">
                    <button type="button" class="btn btn-success btn-custom w-100 py-3" data-bs-toggle="modal" data-bs-target="#modalNuevaVisita">
                        <i class="fas fa-plus me-2"></i>Nueva Visita Comercial
                    </button>
                </div>
            </div>

    <!-- Objetivos Comerciales -->
    @if($objetivo && $progreso)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-target me-2"></i>Mis Objetivos Comerciales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Objetivos de Visitas -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-walking me-2"></i>Objetivos de Visitas Diarias
                            </h6>
                            
                            @if($objetivo->visitas_presenciales_diarias > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-walking text-success me-1"></i>Presenciales
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['visitas']['presenciales']['realizado'] }}/{{ $progreso['visitas']['presenciales']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($progreso['visitas']['presenciales']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['visitas']['presenciales']['progreso'] }}% completado</small>
                            </div>
                            @endif

                            @if($objetivo->visitas_telefonicas_diarias > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-phone text-info me-1"></i>Telefónicas
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['visitas']['telefonicas']['realizado'] }}/{{ $progreso['visitas']['telefonicas']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ min($progreso['visitas']['telefonicas']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['visitas']['telefonicas']['progreso'] }}% completado</small>
                            </div>
                            @endif

                            @if($objetivo->visitas_mixtas_diarias > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-users text-warning me-1"></i>Mixtas
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['visitas']['mixtas']['realizado'] }}/{{ $progreso['visitas']['mixtas']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min($progreso['visitas']['mixtas']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['visitas']['mixtas']['progreso'] }}% completado</small>
                            </div>
                            @endif
                        </div>

                        <!-- Objetivos de Ventas -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-euro-sign me-2"></i>Objetivos de Ventas Mensuales
                            </h6>
                            
                            @if($objetivo->planes_esenciales_mensuales > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-star text-primary me-1"></i>Planes Esenciales
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['ventas']['planes_esenciales']['realizado'] }}/{{ $progreso['ventas']['planes_esenciales']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ min($progreso['ventas']['planes_esenciales']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['ventas']['planes_esenciales']['progreso'] }}% completado</small>
                            </div>
                            @endif

                            @if($objetivo->planes_profesionales_mensuales > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-star text-success me-1"></i>Planes Profesionales
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['ventas']['planes_profesionales']['realizado'] }}/{{ $progreso['ventas']['planes_profesionales']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ min($progreso['ventas']['planes_profesionales']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['ventas']['planes_profesionales']['progreso'] }}% completado</small>
                            </div>
                            @endif

                            @if($objetivo->planes_avanzados_mensuales > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-star text-warning me-1"></i>Planes Avanzados
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progreso['ventas']['planes_avanzados']['realizado'] }}/{{ $progreso['ventas']['planes_avanzados']['objetivo'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-warning" style="width: {{ min($progreso['ventas']['planes_avanzados']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['ventas']['planes_avanzados']['progreso'] }}% completado</small>
                            </div>
                            @endif

                            @if($objetivo->ventas_euros_mensuales > 0)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-euro-sign text-info me-1"></i>Ventas en Euros
                                    </span>
                                    <span class="small fw-bold">
                                        €{{ number_format($progreso['ventas']['ventas_euros']['realizado'], 0) }}/€{{ number_format($progreso['ventas']['ventas_euros']['objetivo'], 0) }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-info" style="width: {{ min($progreso['ventas']['ventas_euros']['progreso'], 100) }}%"></div>
                                </div>
                                <small class="text-muted">{{ $progreso['ventas']['ventas_euros']['progreso'] }}% completado</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Incentivos Comerciales -->
    @if($incentivo && $progresoIncentivos)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-money-bill-wave me-2"></i>Mis Incentivos Comerciales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Resumen de Incentivos -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-chart-line me-2"></i>Resumen de Incentivos
                            </h6>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-success">€{{ number_format($progresoIncentivos['incentivos']['incentivo_base'], 2) }}</h4>
                                            <small class="text-muted">Incentivo Base ({{ $progresoIncentivos['porcentaje_venta'] }}%)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h4 class="text-warning">€{{ number_format($progresoIncentivos['incentivos']['incentivo_adicional'], 2) }}</h4>
                                            <small class="text-muted">Incentivo Adicional ({{ $progresoIncentivos['porcentaje_adicional'] }}%)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>€{{ number_format($progresoIncentivos['incentivos']['total_incentivo'], 2) }}</h3>
                                        <small>Total de Incentivos</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Progreso hacia Incentivo Adicional -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-trophy me-2"></i>Progreso hacia Incentivo Adicional
                            </h6>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-users text-info me-1"></i>Clientes Únicos
                                    </span>
                                    <span class="small fw-bold">
                                        {{ $progresoIncentivos['clientes_unicos'] }}/{{ $progresoIncentivos['min_clientes_mensuales'] }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-info" style="width: {{ min(($progresoIncentivos['clientes_unicos'] / $progresoIncentivos['min_clientes_mensuales']) * 100, 100) }}%"></div>
                                </div>
                                <small class="text-muted">
                                    @if($progresoIncentivos['cumple_minimo_clientes'])
                                        ✅ ¡Cumples el mínimo de clientes!
                                    @else
                                        Faltan {{ $progresoIncentivos['min_clientes_mensuales'] - $progresoIncentivos['clientes_unicos'] }} clientes
                                    @endif
                                </small>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small">
                                        <i class="fas fa-euro-sign text-success me-1"></i>Ventas Totales
                                    </span>
                                    <span class="small fw-bold">
                                        €{{ number_format($progresoIncentivos['ventas_totales'], 0) }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                                <small class="text-muted">Ventas realizadas este mes</small>
                            </div>

                            @if($progresoIncentivos['cumple_minimo_clientes'])
                            <div class="alert alert-success">
                                <i class="fas fa-star me-2"></i>
                                <strong>¡Felicidades!</strong> Cumples el mínimo de clientes para el incentivo adicional del {{ $progresoIncentivos['porcentaje_adicional'] }}%
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-2"></i>
                                Necesitas {{ $progresoIncentivos['min_clientes_mensuales'] - $progresoIncentivos['clientes_unicos'] }} clientes más para el incentivo adicional
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gestión Comercial -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-briefcase me-2"></i>Gestión Comercial
                    </h5>
                </div>
                        <div class="card-body">
                            <!-- Desktop Table -->
                            <div class="table-responsive">
                                <table class="table table-hover table-custom">
                                    <thead class="table-dark">
                                        <tr>
                                            <th><i class="fas fa-calendar me-1"></i>Fecha</th>
                                            <th><i class="fas fa-user me-1"></i>Cliente</th>
                                            <th><i class="fas fa-tag me-1"></i>Tipo</th>
                                            <th><i class="fas fa-star me-1"></i>Plan</th>
                                            <th><i class="fas fa-flag me-1"></i>Estado</th>
                                            <th><i class="fas fa-microphone me-1"></i>Audio</th>
                                            <th><i class="fas fa-star me-1"></i>Valoración</th>
                                            <th><i class="fas fa-cog me-1"></i>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($visitas as $visita)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $visita->created_at->format('d/m/Y') }}</span>
                                                    <br><small class="text-muted">{{ $visita->created_at->format('H:i') }}</small>
                                                </td>
                                                <td>
                                                    <strong>{{ $visita->cliente ? $visita->cliente->name : $visita->nombre_cliente }}</strong>
                                                    @if(!$visita->cliente)
                                                        <br><span class="badge bg-warning">Lead</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-custom bg-{{ $visita->tipo_visita == 'presencial' ? 'primary' : 'info' }}">
                                                        <i class="fas fa-{{ $visita->tipo_visita == 'presencial' ? 'handshake' : 'phone' }} me-1"></i>
                                                        {{ ucfirst($visita->tipo_visita) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($visita->plan_interesado)
                                                        <span class="badge bg-{{ $visita->plan_interesado == 'esencial' ? 'primary' : ($visita->plan_interesado == 'profesional' ? 'success' : 'warning') }}">
                                                            {{ ucfirst($visita->plan_interesado) }}
                                                        </span>
                                                        @if($visita->precio_plan)
                                                            <br><small class="text-muted">€{{ number_format($visita->precio_plan, 2) }}</small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($visita->estado)
                                                        <span class="badge bg-{{ $visita->estado == 'aceptado' ? 'success' : ($visita->estado == 'rechazado' ? 'danger' : ($visita->estado == 'en_proceso' ? 'warning' : 'secondary')) }}">
                                                            {{ ucfirst($visita->estado) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($visita->audio_file)
                                                        <button class="btn btn-sm btn-outline-primary" onclick="playVisitaAudio({{ $visita->id }})" title="Reproducir audio">
                                                            <i class="fas fa-play me-1"></i>
                                                            <small>{{ $visita->audio_duration ? gmdate('i:s', $visita->audio_duration) : 'Audio' }}</small>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-warning me-2">{{ $visita->valoracion }}/10</span>
                                                        <div class="progress" style="width: 60px; height: 8px;">
                                                            <div class="progress-bar bg-warning" style="width: {{ ($visita->valoracion / 10) * 100 }}%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($visita->comentarios)
                                                        <span title="{{ $visita->comentarios }}">{{ Str::limit($visita->comentarios, 30) }}</span>
                                                    @else
                                                        <span class="text-muted">Sin comentarios</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($visita->requiere_seguimiento)
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Sí
                                                        </span>
                                                        @if($visita->fecha_seguimiento)
                                                            <br><small class="text-muted">{{ $visita->fecha_seguimiento->format('d/m/Y H:i') }}</small>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-times me-1"></i>No
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="verVisita({{ $visita->id }})" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No hay visitas registradas</h5>
                                                    <p class="text-muted">Comienza registrando tu primera visita comercial</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile Cards -->
                            <div class="d-md-none">
                                @forelse($visitas as $visita)
                                    <div class="visita-card">
                                        <div class="visita-card-header">
                                            <div class="visita-card-fecha">
                                                {{ $visita->created_at->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                        
                                        <div class="visita-card-cliente">
                                            {{ $visita->cliente ? $visita->cliente->name : $visita->nombre_cliente }}
                                            @if(!$visita->cliente)
                                                <span class="badge bg-warning ms-2">Lead</span>
                                            @endif
                                        </div>
                                        
                                        <div class="visita-card-tipo {{ $visita->tipo_visita }}">
                                            <i class="fas fa-{{ $visita->tipo_visita == 'presencial' ? 'handshake' : 'phone' }} me-1"></i>
                                            {{ ucfirst($visita->tipo_visita) }}
                                        </div>
                                        
                                        @if($visita->plan_interesado)
                                        <div class="visita-card-plan">
                                            <i class="fas fa-star me-1"></i>
                                            <strong>Plan:</strong> {{ ucfirst($visita->plan_interesado) }}
                                            @if($visita->precio_plan)
                                                - €{{ number_format($visita->precio_plan, 2) }}
                                            @endif
                                        </div>
                                        @endif
                                        
                                        @if($visita->estado)
                                        <div class="visita-card-estado">
                                            <i class="fas fa-flag me-1"></i>
                                            <strong>Estado:</strong> 
                                            <span class="badge bg-{{ $visita->estado == 'aceptado' ? 'success' : ($visita->estado == 'rechazado' ? 'danger' : ($visita->estado == 'en_proceso' ? 'warning' : 'secondary')) }}">
                                                {{ ucfirst($visita->estado) }}
                                            </span>
                                        </div>
                                        @endif
                                        
                                        @if($visita->audio_file)
                                        <div class="visita-card-audio">
                                            <i class="fas fa-microphone me-1"></i>
                                            <strong>Audio:</strong> 
                                            <button class="btn btn-sm btn-outline-primary ms-2" onclick="playVisitaAudio({{ $visita->id }})" title="Reproducir audio">
                                                <i class="fas fa-play me-1"></i>
                                                <small>{{ $visita->audio_duration ? gmdate('i:s', $visita->audio_duration) : 'Audio' }}</small>
                                            </button>
                                        </div>
                                        @endif
                                        
                                        <div class="visita-card-valoracion">
                                            <div class="valoracion-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= ($visita->valoracion / 2) ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="valoracion-numero">{{ $visita->valoracion }}/10</span>
                                        </div>
                                        
                                        @if($visita->comentarios)
                                            <div class="visita-card-comentarios">
                                                <i class="fas fa-comment me-1"></i>{{ Str::limit($visita->comentarios, 50) }}
                                            </div>
                                        @endif
                                        
                                        <div class="visita-card-seguimiento">
                                            @if($visita->requiere_seguimiento)
                                                <span class="seguimiento-badge si">
                                                    <i class="fas fa-check me-1"></i>Seguimiento
                                                </span>
                                                @if($visita->fecha_seguimiento)
                                                    <small class="text-muted">{{ $visita->fecha_seguimiento->format('d/m/Y H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="seguimiento-badge no">
                                                    <i class="fas fa-times me-1"></i>Sin seguimiento
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="visita-card-acciones">
                                            <button class="btn-ver-detalle" onclick="verVisita({{ $visita->id }})">
                                                <i class="fas fa-eye me-1"></i>Ver Detalles
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No hay visitas registradas</h5>
                                        <p class="text-muted">Comienza registrando tu primera visita comercial</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nueva Visita -->
    <div class="modal fade" id="modalNuevaVisita" tabindex="-1" aria-labelledby="modalNuevaVisitaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalNuevaVisitaLabel">
                        <i class="fas fa-plus me-2"></i>Nueva Visita Comercial
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaVisita">
                        @csrf
                        <input type="hidden" name="comercial_id" value="{{ $user->id }}">

                        <!-- Paso 1: Tipo de cliente -->
                        <div id="paso1" class="visita-paso">
                            <h6 class="mb-4 text-center">
                                <i class="fas fa-question-circle me-2"></i>¿Es un cliente nuevo o existente?
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100 py-4" onclick="seleccionarTipoCliente('nuevo')">
                                        <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                        <strong>Cliente Nuevo</strong>
                                        <br><small>Registrar como lead</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-success w-100 py-4" onclick="seleccionarTipoCliente('existente')">
                                        <i class="fas fa-user fa-2x mb-2 d-block"></i>
                                        <strong>Cliente Existente</strong>
                                        <br><small>Seleccionar de la lista</small>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Datos del cliente -->
                        <div id="paso2" class="visita-paso" style="display: none;">
                            <!-- Cliente Nuevo -->
                            <div id="clienteNuevo" style="display: none;">
                                <h6 class="mb-3">
                                    <i class="fas fa-user-plus me-2"></i>Datos del Cliente Nuevo
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre_cliente" class="form-label">Nombre del cliente *</label>
                                        <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono_cliente" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono_cliente" name="telefono_cliente">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="email_cliente" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email_cliente" name="email_cliente">
                                    </div>
                                </div>
                            </div>

                            <!-- Cliente Existente -->
                            <div id="clienteExistente" style="display: none;">
                                <h6 class="mb-3">
                                    <i class="fas fa-search me-2"></i>Seleccionar Cliente Existente
                                </h6>
                                <div class="mb-3">
                                    <label for="clienteSelect" class="form-label">Cliente *</label>
                                    <select class="form-control" id="clienteSelect" name="cliente_id">
                                        <option value="">Seleccionar cliente...</option>
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(1)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom" onclick="avanzarPaso2()">
                                    Siguiente<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Paso 3: Tipo de visita -->
                        <div id="paso3" class="visita-paso" style="display: none;">
                            <h6 class="mb-4 text-center">
                                <i class="fas fa-question-circle me-2"></i>¿Cómo fue la visita?
                            </h6>
                            <div class="row">
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-primary w-100 py-4" onclick="seleccionarTipoVisita('presencial')">
                                        <i class="fas fa-handshake fa-2x mb-2 d-block"></i>
                                        <strong>Presencial</strong>
                                        <br><small>Reunión en persona</small>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn btn-outline-info w-100 py-4" onclick="seleccionarTipoVisita('telefonico')">
                                        <i class="fas fa-phone fa-2x mb-2 d-block"></i>
                                        <strong>Telefónico</strong>
                                        <br><small>Llamada telefónica</small>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(2)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom" onclick="avanzarPaso3()">
                                    Siguiente<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Paso 4: Grabación de Audio -->
                        <div id="paso4" class="visita-paso" style="display: none;">
                            <h6 class="mb-3 text-center">
                                <i class="fas fa-microphone me-2"></i>Grabación de Audio (Opcional)
                            </h6>
                            <div class="text-center mb-4">
                                <p class="text-muted">Puedes grabar la conversación para tener un registro de la visita presencial</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-info btn-sm" onclick="showMicrophoneInfo()">
                                        <i class="fas fa-info-circle me-1"></i>Información sobre permisos
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="forzarSolicitudPermisos()">
                                        <i class="fas fa-microphone-slash me-1"></i>Reintentar permisos
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <div id="audioControls">
                                                <button type="button" class="btn btn-outline-danger btn-lg mb-3" id="startRecording2" onclick="startRecording()">
                                                    <i class="fas fa-microphone me-2"></i>Iniciar Grabación
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-lg mb-3" id="stopRecording2" onclick="stopRecording()" style="display: none;">
                                                    <i class="fas fa-stop me-2"></i>Detener Grabación
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-lg mb-3" id="playRecording2" onclick="playRecording()" style="display: none;">
                                                    <i class="fas fa-play me-2"></i>Reproducir
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-lg mb-3" id="deleteRecording2" onclick="deleteRecording()" style="display: none;">
                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                </button>
                                            </div>
                                            
                                            <div id="recordingStatus" class="mt-3" style="display: none;">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-circle text-danger me-2"></i>
                                                    <span id="recordingTime">00:00</span> - Grabando...
                                                </div>
                                            </div>
                                            
                                            <div id="audioPlayer" class="mt-3" style="display: none;">
                                                <audio id="audioPlayback" controls class="w-100"></audio>
                                                <div class="mt-2">
                                                    <small class="text-muted">Duración: <span id="audioDuration">00:00</span></small>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" id="audioBlob" name="audio_blob">
                                            <input type="hidden" id="audioDuration" name="audio_duration">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom" onclick="avanzarPaso4()">
                                    <i class="fas fa-arrow-right me-2"></i>Siguiente
                                </button>
                            </div>
                        </div>

                        <!-- Paso 5: Valoración -->
                        <div id="paso5" class="visita-paso" style="display: none;">
                            <h6 class="mb-3 text-center">
                                <i class="fas fa-star me-2"></i>Valoración de la visita (1-10)
                            </h6>
                            <div class="text-center mb-4">
                                <p class="text-muted" id="valoracionMensaje">
                                    <!-- El mensaje se actualizará dinámicamente según el tipo de visita -->
                                </p>
                            </div>
                            <div class="mb-3">
                                <input type="hidden" id="valoracionInput" name="valoracion">
                                <!-- Desktop valoración -->
                                <div class="d-none d-md-flex justify-content-center flex-wrap gap-2">
                                    @for($i = 1; $i <= 10; $i++)
                                        <button type="button" class="btn btn-outline-warning valoracion-btn" data-valor="{{ $i }}" onclick="seleccionarValoracion({{ $i }})">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                                <!-- Mobile valoración -->
                                <div class="d-md-none valoracion-mobile">
                                    @for($i = 1; $i <= 10; $i++)
                                        <button type="button" class="btn btn-outline-warning valoracion-btn-mobile" data-valor="{{ $i }}" onclick="seleccionarValoracion({{ $i }})">
                                            {{ $i }}
                                        </button>
                                    @endfor
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom d-none d-md-inline-block" onclick="volverPaso(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-secondary modal-btn-mobile d-md-none" onclick="volverPaso(3)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom d-none d-md-inline-block" onclick="avanzarPaso4()">
                                    Siguiente<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="button" class="btn btn-primary modal-btn-mobile d-md-none" onclick="avanzarPaso4()">
                                    Siguiente<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Paso 6: Plan Interesado -->
                        <div id="paso6" class="visita-paso" style="display: none;">
                            <h6 class="mb-3 text-center">
                                <i class="fas fa-star me-2"></i>Plan de Interés del Cliente
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Plan que le interesa</label>
                                    <select class="form-select" id="plan_interesado" name="plan_interesado" onchange="actualizarPrecioPlan()">
                                        <option value="">Seleccionar plan</option>
                                        <option value="esencial" data-precio="19">Plan Esencial (€19)</option>
                                        <option value="profesional" data-precio="49">Plan Profesional (€49)</option>
                                        <option value="avanzado" data-precio="129">Plan Avanzado (€129)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Precio del Plan</label>
                                    <input type="number" class="form-control" id="precio_plan" name="precio_plan" step="0.01" min="0" readonly>
                                    <small class="text-muted">El precio se asigna automáticamente según el plan seleccionado</small>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Estado de la Propuesta</label>
                                <select class="form-select" id="estado" name="estado">
                                    <option value="pendiente">Pendiente</option>
                                    <option value="en_proceso">En Proceso</option>
                                    <option value="aceptado">Aceptado</option>
                                    <option value="rechazado">Rechazado</option>
                                </select>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Observaciones del Plan</label>
                                <textarea class="form-control" id="observaciones_plan" name="observaciones_plan" rows="3" placeholder="Observaciones sobre el plan de interés..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(4)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom" id="btnSiguientePaso5">
                                    Siguiente<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Paso 6: Grabación de Audio -->
                        <div id="paso6" class="visita-paso" style="display: none;">
                            <h6 class="mb-3 text-center">
                                <i class="fas fa-microphone me-2"></i>Grabación de Audio (Opcional)
                            </h6>
                            <div class="text-center mb-4">
                                <p class="text-muted">Puedes grabar la conversación para tener un registro de la visita</p>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="showMicrophoneInfo()">
                                    <i class="fas fa-info-circle me-1"></i>Información sobre permisos
                                </button>
                            </div>
                            
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <div id="audioControls">
                                                <button type="button" class="btn btn-outline-danger btn-lg mb-3" id="startRecording2" onclick="startRecording()">
                                                    <i class="fas fa-microphone me-2"></i>Iniciar Grabación
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-lg mb-3" id="stopRecording2" onclick="stopRecording()" style="display: none;">
                                                    <i class="fas fa-stop me-2"></i>Detener Grabación
                                                </button>
                                                <button type="button" class="btn btn-outline-success btn-lg mb-3" id="playRecording2" onclick="playRecording()" style="display: none;">
                                                    <i class="fas fa-play me-2"></i>Reproducir
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary btn-lg mb-3" id="deleteRecording2" onclick="deleteRecording()" style="display: none;">
                                                    <i class="fas fa-trash me-2"></i>Eliminar
                                                </button>
                                            </div>
                                            
                                            <div id="recordingStatus" class="mt-3" style="display: none;">
                                                <div class="alert alert-info">
                                                    <i class="fas fa-circle text-danger me-2"></i>
                                                    <span id="recordingTime">00:00</span> - Grabando...
                                                </div>
                                            </div>
                                            
                                            <div id="audioPlayer" class="mt-3" style="display: none;">
                                                <audio id="audioPlayback" controls class="w-100"></audio>
                                                <div class="mt-2">
                                                    <small class="text-muted">Duración: <span id="audioDuration">00:00</span></small>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" id="audioBlob" name="audio_blob">
                                            <input type="hidden" id="audioDuration" name="audio_duration">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(5)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="button" class="btn btn-primary btn-custom" onclick="avanzarPaso6()">
                                    <i class="fas fa-arrow-right me-2"></i>Siguiente
                                </button>
                            </div>
                        </div>

                        <!-- Paso 7: Seguimiento -->
                        <div id="paso7" class="visita-paso" style="display: none;">
                            <h6 class="mb-3">
                                <i class="fas fa-bell me-2"></i>¿Requiere seguimiento?
                            </h6>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="requiere_seguimiento" id="seguimiento_si" value="1" onchange="toggleFechaSeguimiento()">
                                    <label class="form-check-label" for="seguimiento_si">
                                        <i class="fas fa-check-circle me-2 text-success"></i>Sí, requiere seguimiento
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="requiere_seguimiento" id="seguimiento_no" value="0" onchange="toggleFechaSeguimiento()" checked>
                                    <label class="form-check-label" for="seguimiento_no">
                                        <i class="fas fa-times-circle me-2 text-danger"></i>No requiere seguimiento
                                    </label>
                                </div>
                            </div>

                            <div id="fechaSeguimientoDiv" style="display: none;">
                                <div class="mb-3">
                                    <label for="fecha_seguimiento" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Fecha de seguimiento
                                    </label>
                                    <input type="datetime-local" class="form-control" id="fecha_seguimiento" name="fecha_seguimiento">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="comentarios" class="form-label">
                                    <i class="fas fa-comment me-2"></i>Comentarios adicionales
                                </label>
                                <textarea class="form-control" id="comentarios" name="comentarios" rows="3" placeholder="Comentarios sobre la visita..."></textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary btn-custom" onclick="volverPaso(6)">
                                    <i class="fas fa-arrow-left me-2"></i>Atrás
                                </button>
                                <button type="submit" class="btn btn-success btn-custom">
                                    <i class="fas fa-save me-2"></i>Guardar Visita
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Variables globales
        let pasoActual = 1;
        let tipoCliente = null;
        let tipoVisita = null;

        // Funciones del modal
        function seleccionarTipoCliente(tipo) {
            console.log('Seleccionando tipo de cliente:', tipo);
            console.log('jQuery disponible:', typeof $ !== 'undefined');
            tipoCliente = tipo;
            
            if (tipo === 'nuevo') {
                document.getElementById('clienteNuevo').style.display = 'block';
                document.getElementById('clienteExistente').style.display = 'none';
                console.log('Mostrando formulario de cliente nuevo');
            } else {
                document.getElementById('clienteExistente').style.display = 'block';
                document.getElementById('clienteNuevo').style.display = 'none';
                console.log('Mostrando formulario de cliente existente');
                
                // Inicializar Select2 con jQuery
                if (typeof $ !== 'undefined') {
                    setTimeout(() => {
                        $('#clienteSelect').select2({
                            placeholder: 'Buscar cliente...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#modalNuevaVisita')
                        });
                    }, 100);
                }
            }
            
            mostrarPaso(2);
        }

        function avanzarPaso2() {
            if (tipoCliente === 'nuevo') {
                const nombre = $('#nombre_cliente').val();
                if (!nombre || nombre.trim() === '') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor ingresa el nombre del cliente',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            } else {
                const clienteId = $('#clienteSelect').val();
                if (!clienteId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor selecciona un cliente',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }
            
            mostrarPaso(3);
        }

        function seleccionarTipoVisita(tipo) {
            tipoVisita = tipo;
            $('input[name="tipo_visita"]').remove();
            $('#formNuevaVisita').append(`<input type="hidden" name="tipo_visita" value="${tipo}">`);
            
            // Mostrar mensaje informativo para visitas telefónicas
            if (tipo === 'telefonico') {
                Swal.fire({
                    title: 'Visita Telefónica',
                    html: `
                        <div class="text-start">
                            <p><strong>Nota importante:</strong></p>
                            <ul>
                                <li>📞 Las visitas telefónicas no incluyen grabación de audio</li>
                                <li>🎙️ Solo las visitas presenciales pueden ser grabadas</li>
                                <li>📝 Podrás registrar la valoración y plan al final de la llamada</li>
                            </ul>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Entendido'
                });
            }
        }

        function avanzarPaso3() {
            if (!tipoVisita) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor selecciona el tipo de visita',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Si es telefónica, saltar directamente a valoración
            if (tipoVisita === 'telefonico') {
                mostrarPaso(5); // Ir directamente a valoración
            } else {
                mostrarPaso(4); // Ir al paso de grabación de audio (solo presencial)
            }
        }

        function avanzarPaso4() {
            mostrarPaso(5); // Ir al paso de valoración
        }

        function avanzarPaso5() {
            console.log('=== AVANZAR PASO 5 INICIADO ===');
            console.log('jQuery disponible:', typeof $ !== 'undefined');
            console.log('Elemento valoracionInput existe:', document.getElementById('valoracionInput'));
            
            const valoracion = $('#valoracionInput').val();
            console.log('Valoración obtenida con jQuery:', valoracion);
            
            // También verificar con JavaScript vanilla
            const valoracionVanilla = document.getElementById('valoracionInput').value;
            console.log('Valoración obtenida con vanilla JS:', valoracionVanilla);
            
            if (!valoracion && !valoracionVanilla) {
                console.log('No hay valoración seleccionada');
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor selecciona una valoración',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            console.log('Avanzando al paso 6 (plan interesado)');
            mostrarPaso(6); // Ir al paso de plan interesado
        }

        function avanzarPaso6() {
            mostrarPaso(7); // Ir al paso de seguimiento
        }

        function seleccionarValoracion(valor) {
            console.log('seleccionarValoracion llamada con valor:', valor);
            $('#valoracionInput').val(valor);
            console.log('Valor establecido en input:', $('#valoracionInput').val());
            $('.valoracion-btn').removeClass('btn-warning').addClass('btn-outline-warning');
            $(`.valoracion-btn[data-valor="${valor}"]`).removeClass('btn-outline-warning').addClass('btn-warning');
            console.log('Estilos de botones actualizados');
        }

        // Función para actualizar precio automáticamente
        function actualizarPrecioPlan() {
            const planSelect = document.getElementById('plan_interesado');
            const precioInput = document.getElementById('precio_plan');
            
            if (planSelect.value) {
                const precio = planSelect.options[planSelect.selectedIndex].getAttribute('data-precio');
                precioInput.value = precio;
            } else {
                precioInput.value = '';
            }
        }


        function toggleFechaSeguimiento() {
            const requiereSeguimiento = $('#seguimiento_si').is(':checked');
            if (requiereSeguimiento) {
                $('#fechaSeguimientoDiv').show();
            } else {
                $('#fechaSeguimientoDiv').hide();
            }
        }

        function volverPaso(paso) {
            mostrarPaso(paso);
        }

        function mostrarPaso(paso) {
            console.log('=== MOSTRAR PASO ===');
            console.log('Paso solicitado:', paso);
            console.log('jQuery disponible:', typeof $ !== 'undefined');
            
            $('.visita-paso').hide();
            console.log('Todos los pasos ocultados');
            
            $(`#paso${paso}`).show();
            console.log(`Paso ${paso} mostrado`);
            
            pasoActual = paso;
            console.log('Paso actual actualizado a:', pasoActual);
            
            // Actualizar mensaje de valoración según el tipo de visita
            if (paso === 5) {
                const mensajeValoracion = document.getElementById('valoracionMensaje');
                if (tipoVisita === 'telefonico') {
                    mensajeValoracion.innerHTML = 'Valora la llamada telefónica del 1 al 10';
                } else {
                    mensajeValoracion.innerHTML = 'Valora la visita presencial del 1 al 10';
                }
            }
            
            console.log('=== FIN MOSTRAR PASO ===');
        }

        // Envío del formulario
        $('#formNuevaVisita').on('submit', function(e) {
            e.preventDefault();
            
            // Validaciones antes de enviar
            if (tipoCliente === 'nuevo') {
                const nombre = $('#nombre_cliente').val();
                if (!nombre || nombre.trim() === '') {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor ingresa el nombre del cliente',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            } else if (tipoCliente === 'existente') {
                const clienteId = $('#clienteSelect').val();
                if (!clienteId) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Por favor selecciona un cliente',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
            }
            
            if (!tipoVisita) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor selecciona el tipo de visita',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            const valoracion = $('#valoracionInput').val();
            if (!valoracion) {
                Swal.fire({
                    title: 'Error',
                    text: 'Por favor selecciona una valoración',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Preparar datos del formulario
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('comercial_id', '{{ $user->id }}');
            
            if (tipoCliente === 'nuevo') {
                formData.append('nombre_cliente', $('#nombre_cliente').val());
                formData.append('telefono_cliente', $('#telefono_cliente').val());
                formData.append('email_cliente', $('#email_cliente').val());
            } else {
                formData.append('cliente_id', $('#clienteSelect').val());
            }
            
            formData.append('tipo_visita', tipoVisita);
            formData.append('valoracion', valoracion);
            formData.append('comentarios', $('#comentarios').val());
            formData.append('requiere_seguimiento', $('input[name="requiere_seguimiento"]:checked').val());
            
            if ($('#fecha_seguimiento').val()) {
                formData.append('fecha_seguimiento', $('#fecha_seguimiento').val());
            }
            
            // Agregar datos del plan interesado
            if ($('#plan_interesado').val()) {
                formData.append('plan_interesado', $('#plan_interesado').val());
            }
            if ($('#precio_plan').val()) {
                formData.append('precio_plan', $('#precio_plan').val());
            }
            if ($('#estado').val()) {
                formData.append('estado', $('#estado').val());
            }
            if ($('#observaciones_plan').val()) {
                formData.append('observaciones_plan', $('#observaciones_plan').val());
            }
            
            // Agregar audio si existe
            if (audioBlob) {
                formData.append('audio', audioBlob, 'visita_audio.wav');
                formData.append('audio_duration', $('#audioDuration').val());
            }
            
            // Mostrar loading
            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('{{ route("visitas.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#modalNuevaVisita').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al guardar la visita',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });

        // Funciones para el manejo de pasos - ELIMINADAS (duplicadas)

        // Variables para grabación de audio
        let mediaRecorder;
        let audioChunks = [];
        let audioBlob = null;
        let recordingStartTime = null;
        let recordingInterval = null;

        // Función para iniciar grabación
        function startRecording() {
            // Verificar si el navegador soporta MediaRecorder
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                Swal.fire({
                    title: 'Error',
                    text: 'Tu navegador no soporta grabación de audio. Usa Chrome, Firefox o Safari.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Verificar permisos existentes primero
            if (navigator.permissions) {
                navigator.permissions.query({ name: 'microphone' }).then(function(permissionStatus) {
                    if (permissionStatus.state === 'granted') {
                        // Ya tenemos permisos, iniciar grabación directamente
                        iniciarGrabacion();
                    } else if (permissionStatus.state === 'denied') {
                        // Permisos denegados, pero intentar solicitar de nuevo
                        Swal.fire({
                            title: 'Permisos de Micrófono',
                            text: 'Los permisos de micrófono fueron denegados anteriormente. ¿Quieres intentar concederlos de nuevo?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, intentar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Limpiar localStorage y intentar de nuevo
                                localStorage.removeItem('microphonePermissionGranted');
                                iniciarGrabacion();
                            }
                        });
                    } else {
                        // Estado 'prompt' - solicitar permisos
                        solicitarPermisos();
                    }
                }).catch(() => {
                    // Fallback si no soporta permissions API
                    solicitarPermisos();
                });
            } else {
                // Fallback para navegadores que no soportan permissions API
                solicitarPermisos();
            }
        }

        function solicitarPermisos() {
            Swal.fire({
                title: 'Permisos de Micrófono',
                text: 'Se solicitará acceso al micrófono para grabar la conversación.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Permitir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    iniciarGrabacion();
                }
            });
        }

        function forzarSolicitudPermisos() {
            // Limpiar localStorage para forzar nueva solicitud
            localStorage.removeItem('microphonePermissionGranted');
            
            Swal.fire({
                title: 'Solicitar Permisos de Micrófono',
                text: 'Se solicitará acceso al micrófono nuevamente. Si anteriormente los denegaste, el navegador te permitirá cambiarlos.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Solicitar Permisos',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    iniciarGrabacion();
                }
            });
        }

        function iniciarGrabacion() {
            // Solicitar permisos con configuración específica
            navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                    sampleRate: 44100
                } 
            })
                        .then(stream => {
                            console.log('Micrófono accedido correctamente');
                            
                            // Recordar que los permisos fueron concedidos
                            localStorage.setItem('microphonePermissionGranted', 'true');
                            
                            // Crear MediaRecorder con configuración específica
                        const options = {
                            mimeType: 'audio/webm;codecs=opus' // Mejor compatibilidad
                        };
                        
                        // Verificar si el navegador soporta el tipo MIME
                        if (MediaRecorder.isTypeSupported(options.mimeType)) {
                            mediaRecorder = new MediaRecorder(stream, options);
                        } else {
                            // Fallback a configuración por defecto
                            mediaRecorder = new MediaRecorder(stream);
                        }
                        
                        audioChunks = [];
                        
                        mediaRecorder.ondataavailable = event => {
                            if (event.data.size > 0) {
                                audioChunks.push(event.data);
                            }
                        };
                        
                        mediaRecorder.onstop = () => {
                            // Crear blob con el tipo correcto
                            const mimeType = mediaRecorder.mimeType || 'audio/webm';
                            audioBlob = new Blob(audioChunks, { type: mimeType });
                            
                            // Crear URL para reproducción
                            const audioUrl = URL.createObjectURL(audioBlob);
                            document.getElementById('audioPlayback').src = audioUrl;
                            
                            // Mostrar controles de reproducción
                            document.getElementById('playRecording2').style.display = 'inline-block';
                            document.getElementById('deleteRecording2').style.display = 'inline-block';
                            document.getElementById('audioPlayer').style.display = 'block';
                            
                            // Calcular duración
                            const duration = Math.floor((Date.now() - recordingStartTime) / 1000);
                            document.getElementById('audioDuration').textContent = formatTime(duration);
                            document.getElementById('audioDuration').value = duration;
                            
                            // Guardar blob en input hidden
                            const reader = new FileReader();
                            reader.onload = function() {
                                document.getElementById('audioBlob').value = reader.result;
                            };
                            reader.readAsDataURL(audioBlob);
                            
                            // Detener stream
                            stream.getTracks().forEach(track => track.stop());
                        };
                        
                        mediaRecorder.onerror = (event) => {
                            console.error('Error en MediaRecorder:', event.error);
                            Swal.fire({
                                title: 'Error de Grabación',
                                text: 'Ocurrió un error durante la grabación.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        };
                        
                        // Iniciar grabación
                        mediaRecorder.start(1000); // Recopilar datos cada segundo
                        recordingStartTime = Date.now();
                        
                        // Actualizar UI
                        document.getElementById('startRecording2').style.display = 'none';
                        document.getElementById('stopRecording2').style.display = 'inline-block';
                        document.getElementById('recordingStatus').style.display = 'block';
                        
                        // Iniciar contador
                        recordingInterval = setInterval(() => {
                            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                            document.getElementById('recordingTime').textContent = formatTime(elapsed);
                        }, 1000);
                        
                        console.log('Grabación iniciada');
                        
                    })
                    .catch(error => {
                        console.error('Error accessing microphone:', error);
                        
                        // Limpiar localStorage si hay error de permisos
                        if (error.name === 'NotAllowedError') {
                            localStorage.removeItem('microphonePermissionGranted');
                        }
                        
                        let errorMessage = 'No se pudo acceder al micrófono.';
                        
                        if (error.name === 'NotAllowedError') {
                            errorMessage = 'Permisos de micrófono denegados. Por favor, permite el acceso al micrófono en la configuración del navegador.';
                        } else if (error.name === 'NotFoundError') {
                            errorMessage = 'No se encontró ningún micrófono. Verifica que tengas un micrófono conectado.';
                        } else if (error.name === 'NotSupportedError') {
                            errorMessage = 'Tu navegador no soporta grabación de audio. Usa Chrome, Firefox o Safari.';
                        } else if (error.name === 'NotReadableError') {
                            errorMessage = 'El micrófono está siendo usado por otra aplicación.';
                        }
                        
                        Swal.fire({
                            title: 'Error de Micrófono',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
        }

        // Función para detener grabación
        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
                
                // Actualizar UI
                document.getElementById('stopRecording2').style.display = 'none';
                document.getElementById('startRecording2').style.display = 'inline-block';
                document.getElementById('recordingStatus').style.display = 'none';
                
                // Limpiar contador
                if (recordingInterval) {
                    clearInterval(recordingInterval);
                    recordingInterval = null;
                }
                
                console.log('Grabación detenida');
            }
        }

        // Función para reproducir grabación
        function playRecording() {
            const audio = document.getElementById('audioPlayback');
            if (audio.src) {
                audio.play();
            }
        }

        // Función para eliminar grabación
        function deleteRecording() {
            audioBlob = null;
            document.getElementById('audioBlob').value = '';
            document.getElementById('audioDuration').value = '';
            document.getElementById('audioDuration').textContent = '00:00';
            document.getElementById('playRecording2').style.display = 'none';
            document.getElementById('deleteRecording2').style.display = 'none';
            document.getElementById('audioPlayer').style.display = 'none';
            document.getElementById('audioPlayback').src = '';
        }

        // Función para formatear tiempo
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        // Función para verificar permisos de micrófono
        function checkMicrophonePermissions() {
            if (navigator.permissions) {
                navigator.permissions.query({ name: 'microphone' }).then(function(permissionStatus) {
                    console.log('Estado de permisos de micrófono:', permissionStatus.state);
                    
                    // Actualizar el estado del botón según los permisos
                    const startBtn = document.getElementById('startRecording2');
                    const retryBtn = document.querySelector('button[onclick="forzarSolicitudPermisos()"]');
                    
                    if (startBtn) {
                        if (permissionStatus.state === 'granted') {
                            startBtn.innerHTML = '<i class="fas fa-microphone me-2"></i>Iniciar Grabación';
                            startBtn.className = 'btn btn-outline-danger btn-lg mb-3';
                            startBtn.disabled = false;
                            if (retryBtn) retryBtn.style.display = 'none';
                        } else if (permissionStatus.state === 'denied') {
                            startBtn.innerHTML = '<i class="fas fa-microphone-slash me-2"></i>Permisos Denegados';
                            startBtn.className = 'btn btn-outline-secondary btn-lg mb-3';
                            startBtn.disabled = true;
                            if (retryBtn) retryBtn.style.display = 'inline-block';
                        } else {
                            startBtn.innerHTML = '<i class="fas fa-microphone me-2"></i>Iniciar Grabación';
                            startBtn.className = 'btn btn-outline-danger btn-lg mb-3';
                            startBtn.disabled = false;
                            if (retryBtn) retryBtn.style.display = 'none';
                        }
                    }
                }).catch(function(error) {
                    console.log('Error verificando permisos:', error);
                });
            } else {
                // Fallback para navegadores que no soportan permissions API
                console.log('Permissions API no soportada, usando verificación directa');
            }
        }

        // Función para mostrar información sobre permisos
        function showMicrophoneInfo() {
            Swal.fire({
                title: 'Grabación de Audio',
                html: `
                    <div class="text-start">
                        <p><strong>Para grabar audio necesitas:</strong></p>
                        <ul>
                            <li>✅ Un micrófono conectado</li>
                            <li>✅ Permisos de micrófono en el navegador</li>
                            <li>✅ Navegador compatible (Chrome, Firefox, Safari)</li>
                        </ul>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            El audio se graba en formato WebM para mejor compatibilidad.
                        </p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        }

        // Función para reproducir audio de una visita
        function playVisitaAudio(visitaId) {
            fetch(`/visitas/${visitaId}/audio`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Crear modal para reproducir audio
                        const modalHtml = `
                            <div class="modal fade" id="audioModal" tabindex="-1" aria-labelledby="audioModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title" id="audioModalLabel">
                                                <i class="fas fa-microphone me-2"></i>Audio de la Visita
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <audio controls class="w-100 mb-3">
                                                <source src="${data.audio.url}" type="audio/wav">
                                                Tu navegador no soporta el elemento de audio.
                                            </audio>
                                            <div class="text-muted">
                                                <small>
                                                    <i class="fas fa-clock me-1"></i>
                                                    Duración: ${data.audio.duration ? formatTime(data.audio.duration) : 'Desconocida'}
                                                </small>
                                                <br>
                                                <small>
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Grabado: ${data.audio.recorded_at ? new Date(data.audio.recorded_at).toLocaleString('es-ES') : 'Desconocido'}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remover modal anterior si existe
                        const existingModal = document.getElementById('audioModal');
                        if (existingModal) {
                            existingModal.remove();
                        }
                        
                        // Agregar nuevo modal
                        document.body.insertAdjacentHTML('beforeend', modalHtml);
                        
                        // Mostrar modal
                        const audioModal = new bootstrap.Modal(document.getElementById('audioModal'));
                        audioModal.show();
                        
                        // Limpiar modal al cerrar
                        document.getElementById('audioModal').addEventListener('hidden.bs.modal', function() {
                            this.remove();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo cargar el audio',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        // Limpiar modal al cerrar
        $('#modalNuevaVisita').on('hidden.bs.modal', function() {
            // Resetear formulario
            $('#formNuevaVisita')[0].reset();
            $('.visita-paso').hide();
            $('#paso1').show();
            pasoActual = 1;
            tipoCliente = null;
            tipoVisita = null;
            
            // Destruir Select2 si existe
            if ($('#clienteSelect').hasClass('select2-hidden-accessible')) {
                $('#clienteSelect').select2('destroy');
            }
        });

        function verVisita(id) {
            // Buscar la visita en los datos actuales
            const visitas = @json($visitas);
            const visita = visitas.find(v => v.id === id);
            
            if (!visita) {
                Swal.fire({
                    title: 'Error',
                    text: 'No se encontró la visita',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            // Formatear fecha de creación
            const fechaCreacion = new Date(visita.created_at).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Formatear fecha de seguimiento si existe
            let fechaSeguimiento = '';
            if (visita.fecha_seguimiento) {
                fechaSeguimiento = new Date(visita.fecha_seguimiento).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
            
            // Generar estrellas para la valoración
            let estrellas = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= (visita.valoracion / 2)) {
                    estrellas += '<i class="fas fa-star text-warning"></i>';
                } else {
                    estrellas += '<i class="fas fa-star-o text-muted"></i>';
                }
            }
            
            // Crear el HTML del modal
            const modalContent = `
                <div class="text-start">
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong><i class="fas fa-calendar text-primary me-2"></i>Fecha:</strong>
                            <p class="mb-0">${fechaCreacion}</p>
                        </div>
                        <div class="col-6">
                            <strong><i class="fas fa-user text-success me-2"></i>Cliente:</strong>
                            <p class="mb-0">${visita.cliente ? visita.cliente.name : visita.nombre_cliente}</p>
                            ${!visita.cliente ? '<span class="badge bg-warning ms-2">Lead</span>' : ''}
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <strong><i class="fas fa-tag text-info me-2"></i>Tipo:</strong>
                            <p class="mb-0">
                                <span class="badge bg-${visita.tipo_visita === 'presencial' ? 'primary' : 'info'}">
                                    <i class="fas fa-${visita.tipo_visita === 'presencial' ? 'handshake' : 'phone'} me-1"></i>
                                    ${visita.tipo_visita.charAt(0).toUpperCase() + visita.tipo_visita.slice(1)}
                                </span>
                            </p>
                        </div>
                        <div class="col-6">
                            <strong><i class="fas fa-star text-warning me-2"></i>Valoración:</strong>
                            <p class="mb-0">
                                ${estrellas} 
                                <span class="badge bg-warning ms-2">${visita.valoracion}/10</span>
                            </p>
                        </div>
                    </div>
                    
                    ${visita.comentarios ? `
                    <div class="mb-3">
                        <strong><i class="fas fa-comment text-secondary me-2"></i>Comentarios:</strong>
                        <p class="mb-0 bg-light p-2 rounded">${visita.comentarios}</p>
                    </div>
                    ` : ''}
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-bell text-primary me-2"></i>Seguimiento:</strong>
                        <p class="mb-0">
                            ${visita.requiere_seguimiento ? 
                                `<span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Sí
                                </span>` : 
                                `<span class="badge bg-secondary">
                                    <i class="fas fa-times me-1"></i>No
                                </span>`
                            }
                        </p>
                        ${fechaSeguimiento ? `<small class="text-muted d-block mt-1">Fecha: ${fechaSeguimiento}</small>` : ''}
                    </div>
                    
                    <div class="mb-3">
                        <strong><i class="fas fa-user-tie text-info me-2"></i>Comercial:</strong>
                        <p class="mb-0">${visita.comercial ? visita.comercial.name : 'N/A'}</p>
                    </div>
                </div>
            `;
            
            Swal.fire({
                title: '<i class="fas fa-eye text-primary me-2"></i>Detalles de la Visita',
                html: modalContent,
                width: '600px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'swal2-popup-custom'
                }
            });
        }

        function logout() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres cerrar sesión?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Control de jornada
        let timerState = '{{ $jornadaActiva ? "running" : "stopped" }}';
        let timerTime = {{ $timeWorkedToday }}; // En segundos
        let timerInterval;
        let lastSavedTime = {{ $timeWorkedToday }}; // Tiempo guardado en servidor

        function getTime() {
            fetch('/dashboard/timeworked', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    timerTime = data.time;
                    updateTime();
                }
            });
        }

        function updateTime() {
            let hours = Math.floor(timerTime / 3600);
            let minutes = Math.floor((timerTime % 3600) / 60);
            let seconds = timerTime % 60;

            hours = hours < 10 ? '0' + hours : hours;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;

            const timeString = `${hours}:${minutes}:${seconds}`;
            
            // Actualizar timer desktop
            const timerDesktop = document.getElementById('timer');
            if (timerDesktop) {
                timerDesktop.textContent = timeString;
            }
            
            // Guardar tiempo cada 30 segundos si hay diferencia
            if (timerState === 'running' && Math.abs(timerTime - lastSavedTime) >= 30) {
                saveCurrentTime();
            }
            
            // Actualizar timer móvil
            const timerMobile = document.getElementById('timer-mobile');
            if (timerMobile) {
                timerMobile.textContent = timeString;
            }
            
            // También actualizar el timer en el header móvil si existe
            const timerMobileHeader = document.getElementById('timer');
            if (timerMobileHeader) {
                timerMobileHeader.textContent = timeString;
            }
        }

        function startTimer() {
            timerState = 'running';
            timerInterval = setInterval(() => {
                timerTime++;
                updateTime();
            }, 1000);
        }

        function stopTimer() {
            clearInterval(timerInterval);
            timerState = 'stopped';
            // Guardar tiempo al pausar
            saveCurrentTime();
        }

        function saveCurrentTime() {
            fetch('/dashboard/save-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    time: timerTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    lastSavedTime = timerTime;
                    console.log('Tiempo guardado:', timerTime);
                }
            })
            .catch(error => {
                console.error('Error guardando tiempo:', error);
            });
        }

        function startJornada() {
            fetch('/start-jornada', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer();
                    // Desktop buttons
                    document.getElementById('startJornadaBtn').style.display = 'none';
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endJornadaBtn').style.display = 'block';
                    // Mobile buttons
                    document.getElementById('startJornadaBtn-mobile').style.display = 'none';
                    document.getElementById('startPauseBtn-mobile').style.display = 'block';
                    document.getElementById('endJornadaBtn-mobile').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'Error al iniciar la jornada',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al iniciar la jornada',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        function endJornada() {
            // Obtener el tiempo actualizado
            getTime();

            let now = new Date();
            let currentHour = now.getHours();
            let workedHours = timerTime / 3600;

            // Verificar si es antes de las 18:00 o si ha trabajado menos de 8 horas
            if (currentHour < 18 || workedHours < 8) {
                let title = '';
                let message = '';

                if (currentHour < 18) {
                    title = '¿Finalizar jornada antes de las 18:00?';
                    message = `Son las ${currentHour}:${now.getMinutes().toString().padStart(2, '0')} y has trabajado ${workedHours.toFixed(1)} horas. ¿Estás seguro de que quieres finalizar la jornada?`;
                } else {
                    title = '¿Finalizar jornada?';
                    message = `Has trabajado ${workedHours.toFixed(1)} horas. ¿Estás seguro de que quieres finalizar la jornada?`;
                }

                Swal.fire({
                    title: title,
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, finalizar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        finalizarJornada();
                    }
                });
            } else {
                finalizarJornada();
            }
        }

        function finalizarJornada() {
            // Guardar tiempo antes de finalizar
            saveCurrentTime();
            
            fetch('/end-jornada', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    stopTimer();
                    // Desktop buttons
                    document.getElementById('startJornadaBtn').style.display = 'block';
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'none';
                    document.getElementById('endJornadaBtn').style.display = 'none';
                    // Mobile buttons
                    document.getElementById('startJornadaBtn-mobile').style.display = 'block';
                    document.getElementById('startPauseBtn-mobile').style.display = 'none';
                    document.getElementById('endPauseBtn-mobile').style.display = 'none';
                    document.getElementById('endJornadaBtn-mobile').style.display = 'none';
                    
                    Swal.fire({
                        title: 'Jornada finalizada',
                        text: `Has trabajado ${(timerTime / 3600).toFixed(1)} horas hoy`,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'Error al finalizar la jornada',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Error al finalizar la jornada',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        function startPause() {
            fetch('/start-pause', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    stopTimer();
                    // Desktop buttons
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'block';
                    // Mobile buttons
                    document.getElementById('startPauseBtn-mobile').style.display = 'none';
                    document.getElementById('endPauseBtn-mobile').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'Error al iniciar la pausa',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function endPause() {
            fetch('/end-pause', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    startTimer();
                    // Desktop buttons
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endPauseBtn').style.display = 'none';
                    // Mobile buttons
                    document.getElementById('startPauseBtn-mobile').style.display = 'block';
                    document.getElementById('endPauseBtn-mobile').style.display = 'none';
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.mensaje || 'Error al finalizar la pausa',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function loadCurrentTime() {
            return fetch('/dashboard/get-current-time', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    timerTime = data.time;
                    lastSavedTime = data.time;
                    console.log('Tiempo cargado del servidor:', data.time);
                    return data.time;
                }
                return 0;
            })
            .catch(error => {
                console.error('Error cargando tiempo:', error);
                return 0;
            });
        }

        // Inicializar el timer al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando timer...');
            // Cargar tiempo actualizado del servidor y luego inicializar
            loadCurrentTime().then((savedTime) => {
                console.log('Tiempo cargado:', savedTime);
                timerTime = savedTime || 0;
                updateTime();
                
                // Configurar botones según el estado inicial
                if (timerState === 'running') {
                    console.log('Timer estaba corriendo, reiniciando...');
                    startTimer();
                    // Los botones ya están configurados desde el servidor
                } else {
                    console.log('Timer no estaba corriendo');
                    // Asegurar que solo se muestre el botón de inicio
                    document.getElementById('startJornadaBtn').style.display = 'block';
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'none';
                    document.getElementById('endJornadaBtn').style.display = 'none';
                }
            }).catch(error => {
                console.error('Error cargando tiempo:', error);
                // Fallback: usar tiempo calculado
                getTime();
            });

        // Verificar permisos de micrófono al cargar
        checkMicrophonePermissions();
        
        // Event listener para el botón Siguiente del paso 5
        const btnSiguiente = document.getElementById('btnSiguientePaso5');
        if (btnSiguiente) {
            btnSiguiente.addEventListener('click', function(e) {
                console.log('BOTÓN SIGUIENTE CLICKEADO - EVENT LISTENER');
                e.preventDefault();
                e.stopPropagation();
                avanzarPaso5();
            });
        } else {
            console.error('No se encontró el botón btnSiguientePaso5');
        }
        
        // Event listener alternativo con delegación de eventos
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'btnSiguientePaso5') {
                console.log('BOTÓN SIGUIENTE CLICKEADO - DELEGACIÓN DE EVENTOS');
                e.preventDefault();
                e.stopPropagation();
                avanzarPaso5();
            }
        });
    });

        // Inicializar modal cuando se abre
        document.getElementById('modalNuevaVisita').addEventListener('shown.bs.modal', function () {
            console.log('Modal de nueva visita abierto');
            // Resetear el modal
            pasoActual = 1;
            tipoCliente = null;
            tipoVisita = null;
            
            // Mostrar solo el paso 1
            $('.visita-paso').hide();
            $('#paso1').show();
            
            // Resetear formularios
            $('#clienteNuevo').hide();
            $('#clienteExistente').hide();
        });
    </script>
</body>
</html>
