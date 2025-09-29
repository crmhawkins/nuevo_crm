<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Gestión de Citas - CRM Hawkins</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--dark-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            margin: 20px;
            overflow: hidden;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .header-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0.5rem 0 0 0;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            padding: 2rem;
            background: white;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card.primary::before { background: var(--primary-color); }
        .stat-card.success::before { background: var(--success-color); }
        .stat-card.warning::before { background: var(--warning-color); }
        .stat-card.danger::before { background: var(--danger-color); }
        .stat-card.info::before { background: var(--info-color); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--secondary-color);
            margin: 0.5rem 0 0 0;
            font-weight: 500;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 3rem;
            height: 3rem;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            opacity: 0.1;
        }

        .filters-section {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin: 0 2rem 2rem 2rem;
            border: 1px solid var(--border-color);
        }

        .filters-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .filters-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filters-content {
            padding: 2rem;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn {
            border-radius: var(--radius-md);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--secondary-color);
            background: white;
        }

        .btn-outline-secondary:hover {
            background: var(--light-color);
            border-color: var(--secondary-color);
        }

        .calendar-section {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            margin: 0 2rem 2rem 2rem;
            border: 1px solid var(--border-color);
            overflow: hidden;
        }

        .calendar-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .calendar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .calendar-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .calendar-content {
            padding: 2rem;
        }

        /* FullCalendar Customization */
        .fc {
            font-family: inherit;
        }

        .fc-toolbar {
            margin-bottom: 2rem !important;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .fc-toolbar-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: var(--dark-color) !important;
        }

        .fc-button {
            border-radius: var(--radius-md) !important;
            border: 2px solid var(--border-color) !important;
            background: white !important;
            color: var(--dark-color) !important;
            font-weight: 600 !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.2s ease !important;
        }

        .fc-button:hover {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
            transform: translateY(-1px);
        }

        .fc-button:focus {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }

        .fc-button-primary {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }

        .fc-event {
            border-radius: var(--radius-sm) !important;
            border: none !important;
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
            font-weight: 500 !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .fc-event:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-md);
        }

        .fc-daygrid-event {
            margin: 1px 0 !important;
        }

        .fc-daygrid-day-number {
            font-weight: 600 !important;
            color: var(--dark-color) !important;
        }

        .fc-daygrid-day.fc-day-today {
            background: rgba(37, 99, 235, 0.05) !important;
        }

        /* Select2 Customization */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 48px !important;
            border: 2px solid var(--border-color) !important;
            border-radius: var(--radius-md) !important;
            background: white !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 44px !important;
            padding-left: 1rem !important;
            color: var(--dark-color) !important;
            font-weight: 500 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
            right: 1rem !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }

        /* Modal Customization */
        .modal-content {
            border-radius: var(--radius-lg) !important;
            border: none !important;
            box-shadow: var(--shadow-lg) !important;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            color: white !important;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
            border: none !important;
            padding: 1.5rem 2rem !important;
        }

        .modal-title {
            font-weight: 700 !important;
            font-size: 1.25rem !important;
        }

        .btn-close-white {
            filter: brightness(0) invert(1) !important;
        }

        .modal-body {
            padding: 2rem !important;
        }

        .modal-footer {
            border: none !important;
            padding: 1.5rem 2rem !important;
            background: #f8fafc !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
            }
            
            .header-section {
                padding: 1.5rem;
            }
            
            .header-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
            
            .filters-section, .calendar-section {
                margin: 0 1rem 1rem 1rem;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .calendar-actions {
                flex-direction: column;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Main Container -->
    <div class="main-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1 class="header-title">
                <i class="fas fa-calendar-days me-3"></i>
                Gestión de Citas
            </h1>
            <p class="header-subtitle">Calendario avanzado para gestión de citas y reuniones</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="stat-value" id="total-citas">3</h3>
                <p class="stat-label">Total Citas</p>
            </div>
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3 class="stat-value" id="citas-hoy">1</h3>
                <p class="stat-label">Hoy</p>
            </div>
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <h3 class="stat-value" id="citas-semana">3</h3>
                <p class="stat-label">Esta Semana</p>
            </div>
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="stat-value" id="citas-vencidas">0</h3>
                <p class="stat-label">Vencidas</p>
            </div>
            <div class="stat-card danger">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="stat-value" id="citas-seguimiento">1</h3>
                <p class="stat-label">Seguimiento</p>
            </div>
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3 class="stat-value" id="estados-diferentes">2</h3>
                <p class="stat-label">Estados</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section">
            <div class="filters-header">
                <h5 class="filters-title">
                    <i class="fas fa-filter"></i>
                    Filtros
                </h5>
            </div>
            <div class="filters-content">
                <div class="filters-grid">
                    <div class="form-group">
                        <label for="filtro-gestor" class="form-label">Gestor</label>
                        <select class="form-select select2" id="filtro-gestor">
                            <option value="">Todos los gestores</option>
                            @if(isset($gestores) && $gestores->count() > 0)
                                @foreach($gestores as $gestor)
                                    <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                @endforeach
                            @else
                                <option value="1">Diego Hawkins</option>
                                <option value="2">Gestor Principal</option>
                                <option value="3">Gestor Senior</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filtro-cliente" class="form-label">Cliente</label>
                        <select class="form-select select2" id="filtro-cliente">
                            <option value="">Todos los clientes</option>
                            @if(isset($clientes) && $clientes->count() > 0)
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                @endforeach
                            @else
                                <option value="1">Cliente Corporativo</option>
                                <option value="2">Cliente Importante</option>
                                <option value="3">Cliente de Prueba</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filtro-estado" class="form-label">Estado</label>
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="programada">Programada</option>
                            <option value="confirmada">Confirmada</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="aplicar-filtros">
                        <i class="fas fa-filter"></i> Aplicar
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="limpiar-filtros">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="calendar-section">
            <div class="calendar-header">
                <h5 class="calendar-title">
                    <i class="fas fa-calendar-alt"></i>
                    Calendario de Citas
                </h5>
                <div class="calendar-actions">
                    <button type="button" class="btn btn-primary" id="nueva-cita">
                        <i class="fas fa-plus"></i> Nueva Cita
                    </button>
                    <button type="button" class="btn btn-info" id="ver-proximas">
                        <i class="fas fa-clock"></i> Próximas
                    </button>
                    <button type="button" class="btn btn-warning" id="ver-vencidas">
                        <i class="fas fa-exclamation-triangle"></i> Vencidas
                    </button>
                </div>
            </div>
            <div class="calendar-content">
                <div id="calendar" style="min-height: 600px;"></div>
            </div>
        </div>
    </div>

    <!-- Modal para crear/editar cita -->
    <div class="modal fade" id="modal-cita" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="titulo-modal-cita">
                        <i class="fas fa-plus me-2"></i>Nueva Cita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-cita">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tipo" class="form-label">Tipo *</label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="reunion">Reunión</option>
                                        <option value="llamada">Llamada</option>
                                        <option value="visita">Visita</option>
                                        <option value="presentacion">Presentación</option>
                                        <option value="seguimiento">Seguimiento</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_inicio" class="form-label">Fecha y Hora de Inicio *</label>
                                    <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_fin" class="form-label">Fecha y Hora de Fin *</label>
                                    <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cliente_id" class="form-label">Cliente</label>
                                <select class="form-select select2" id="cliente_id" name="cliente_id">
                                    <option value="">Sin cliente</option>
                                    @if(isset($clientes) && $clientes->count() > 0)
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="1">Cliente Corporativo</option>
                                        <option value="2">Cliente Importante</option>
                                        <option value="3">Cliente de Prueba</option>
                                    @endif
                                </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gestor_id" class="form-label">Gestor Asignado</label>
                                <select class="form-select select2" id="gestor_id" name="gestor_id">
                                    <option value="">Sin asignar</option>
                                    @if(isset($gestores) && $gestores->count() > 0)
                                        @foreach($gestores as $gestor)
                                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="1">Diego Hawkins</option>
                                        <option value="2">Gestor Principal</option>
                                        <option value="3">Gestor Senior</option>
                                    @endif
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" name="ubicacion">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" class="form-control form-control-color" id="color" name="color" value="#3b82f6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-select" id="estado" name="estado">
                                        <option value="programada">Programada</option>
                                        <option value="confirmada">Confirmada</option>
                                        <option value="en_progreso">En Progreso</option>
                                        <option value="completada">Completada</option>
                                        <option value="cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="guardar-cita">
                            <i class="fas fa-save me-1"></i> Guardar Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
    // Variables globales
    let calendar;
    let citasData = [];
    let citaActual = null;
    
    document.addEventListener('DOMContentLoaded', function() {

        console.log('🚀 Iniciando sistema de citas...');
        console.log('👥 Gestores disponibles:', @json($gestores));
        console.log('👤 Clientes disponibles:', @json($clientes));

        // Inicializar Select2 con delay para asegurar que los elementos existan
        setTimeout(function() {
            console.log('🔧 Inicializando Select2...');
            console.log('📋 Elementos .select2 encontrados:', $('.select2').length);
            
            if ($('.select2').length > 0) {
                $('.select2').select2({
                    placeholder: 'Seleccionar...',
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "No se encontraron resultados";
                        },
                        searching: function() {
                            return "Buscando...";
                        }
                    }
                });
                console.log('✅ Select2 inicializado correctamente');
            } else {
                console.error('❌ No se encontraron elementos .select2');
            }
        }, 100);

        // Inicializar calendario
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            console.error('❌ No se encontró el elemento calendar');
            return;
        }

        try {
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                    list: 'Lista'
                },
                height: 'auto',
                events: function(info, successCallback, failureCallback) {
                    console.log('📅 Cargando eventos...');
                    cargarCitas(info.start, info.end, successCallback);
                },
                eventClick: function(info) {
                    console.log('🖱️ Clic en evento:', info.event.title);
                    mostrarDetalleCita(info.event);
                },
                dateClick: function(info) {
                    console.log('📅 Clic en fecha:', info.dateStr);
                    // Verificar que el modal existe antes de intentar crear cita
                    const modalCita = document.getElementById('modal-cita');
                    if (modalCita) {
                        crearNuevaCita(info.dateStr);
                    } else {
                        console.error('❌ Modal de cita no encontrado');
                        mostrarNotificacion('error', 'Error', 'No se puede abrir el modal de nueva cita');
                    }
                },
                eventDrop: function(info) {
                    console.log('🔄 Evento movido:', info.event.title);
                    actualizarFechaCita(info.event);
                },
                eventResize: function(info) {
                    console.log('📏 Evento redimensionado:', info.event.title);
                    actualizarDuracionCita(info.event);
                }
            });

            calendar.render();
            console.log('✅ Calendario renderizado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar calendario:', error);
        }

        // Event listeners con verificación de elementos
        const nuevaCitaBtn = document.getElementById('nueva-cita');
        if (nuevaCitaBtn) {
            nuevaCitaBtn.addEventListener('click', function() {
                console.log('➕ Botón Nueva Cita clickeado');
                mostrarModalCita();
            });
        } else {
            console.warn('⚠️ Botón nueva-cita no encontrado');
        }

        const nuevaCitaHeaderBtn = document.getElementById('nueva-cita-btn');
        if (nuevaCitaHeaderBtn) {
            nuevaCitaHeaderBtn.addEventListener('click', function() {
                console.log('➕ Botón Nueva Cita (header) clickeado');
                mostrarModalCita();
            });
        } else {
            console.warn('⚠️ Botón nueva-cita-btn no encontrado');
        }

        const verProximasBtn = document.getElementById('ver-proximas');
        if (verProximasBtn) {
            verProximasBtn.addEventListener('click', function() {
                console.log('⏰ Botón Próximas clickeado');
                mostrarProximasCitas();
            });
        } else {
            console.warn('⚠️ Botón ver-proximas no encontrado');
        }

        const verVencidasBtn = document.getElementById('ver-vencidas');
        if (verVencidasBtn) {
            verVencidasBtn.addEventListener('click', function() {
                console.log('⚠️ Botón Vencidas clickeado');
                mostrarVencidasCitas();
            });
        } else {
            console.warn('⚠️ Botón ver-vencidas no encontrado');
        }

        const aplicarFiltrosBtn = document.getElementById('aplicar-filtros');
        if (aplicarFiltrosBtn) {
            aplicarFiltrosBtn.addEventListener('click', function() {
                console.log('🔍 Aplicando filtros...');
                aplicarFiltros();
            });
        } else {
            console.warn('⚠️ Botón aplicar-filtros no encontrado');
        }

        const limpiarFiltrosBtn = document.getElementById('limpiar-filtros');
        if (limpiarFiltrosBtn) {
            limpiarFiltrosBtn.addEventListener('click', function() {
                console.log('🧹 Limpiando filtros...');
                limpiarFiltros();
            });
        } else {
            console.warn('⚠️ Botón limpiar-filtros no encontrado');
        }

        const formCita = document.getElementById('form-cita');
        if (formCita) {
            formCita.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('💾 Guardando cita...');
                guardarCita();
            });
        } else {
            console.warn('⚠️ Formulario form-cita no encontrado');
        }

        // Funciones
        function cargarCitas(start, end, callback) {
            console.log('📅 Cargando citas desde', start, 'hasta', end);
            
            // Usar la API real para cargar citas
            fetch('/api/eleven-labs/citas?' + new URLSearchParams({
                start: start.toISOString().split('T')[0],
                end: end.toISOString().split('T')[0],
                v: Date.now() // Forzar recarga del cache
            }), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar citas: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('📅 Citas cargadas desde API ElevenLabs:', data);
                console.log('📅 Total de citas recibidas:', data.length);
                console.log('📅 Primera cita completa:', JSON.stringify(data[0], null, 2));
                console.log('📅 Todas las citas:', JSON.stringify(data, null, 2));
                callback(data);
            })
            .catch(error => {
                console.error('❌ Error cargando citas:', error);
                console.log('⚠️ No se pudieron cargar las citas. Mostrando calendario vacío.');
                callback([]);
            });
        }

        function mostrarDetalleCita(event) {
            console.log('👁️ Mostrando detalles de:', event.title);
            
            // Crear modal de detalles
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'modal-detalle-cita';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-calendar-check me-2"></i>
                                Detalles de la Cita
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-tag me-2"></i>Título</h6>
                                    <p>${event.title}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-clock me-2"></i>Horario</h6>
                                    <p>${event.start.toLocaleString()} - ${event.end.toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-user me-2"></i>Cliente</h6>
                                    <p>${event.extendedProps.cliente || 'Sin cliente'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-user-tie me-2"></i>Gestor</h6>
                                    <p>${event.extendedProps.gestor || 'Sin gestor'}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <h6><i class="fas fa-info-circle me-2"></i>Descripción</h6>
                                    <p>${event.extendedProps.descripcion || 'Sin descripción'}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" onclick="editarCita('${event.id}')">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            new bootstrap.Modal(modal).show();
            
            // Limpiar modal después de cerrar
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        function mostrarModalCita() {
            console.log('📝 Abriendo modal de nueva cita');
            
            // Verificar que todos los elementos del modal existan
            if (!verificarElementosModal()) {
                mostrarNotificacion('error', 'Error', 'Algunos elementos del modal no están disponibles');
                return;
            }
            
            try {
                // Verificar que los elementos existan antes de acceder a ellos
                const tituloModal = document.getElementById('titulo-modal-cita');
                const formCita = document.getElementById('form-cita');
                const colorInput = document.getElementById('color');
                const modalCita = document.getElementById('modal-cita');
                
                if (tituloModal) {
                    tituloModal.innerHTML = '<i class="fas fa-plus me-2"></i>Nueva Cita';
                }
                
                if (formCita) {
                    formCita.reset();
                }
                
                if (colorInput) {
                    colorInput.value = '#3b82f6';
                }
                
                citaActual = null;
                
                // Reinicializar Select2 en el modal después de que se abra
                setTimeout(() => {
                    console.log('🔧 Inicializando Select2 en el modal...');
                    
                    // Destruir Select2 existente si existe
                    if ($('#cliente_id').hasClass('select2-hidden-accessible')) {
                        $('#cliente_id').select2('destroy');
                    }
                    if ($('#gestor_id').hasClass('select2-hidden-accessible')) {
                        $('#gestor_id').select2('destroy');
                    }
                    
                    // Inicializar Select2 en el modal
                    if ($('#cliente_id').length > 0) {
                        $('#cliente_id').select2({
                            placeholder: 'Sin cliente',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#modal-cita')
                        });
                        console.log('✅ Select2 cliente inicializado');
                    }
                    
                    if ($('#gestor_id').length > 0) {
                        $('#gestor_id').select2({
                            placeholder: 'Sin asignar',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#modal-cita')
                        });
                        console.log('✅ Select2 gestor inicializado');
                    }
                }, 300);
                
                if (modalCita) {
                    const modal = new bootstrap.Modal(modalCita);
                    modal.show();
                    console.log('✅ Modal de cita abierto correctamente');
                    
                    // Event listener para cuando el modal se muestre completamente
                    modalCita.addEventListener('shown.bs.modal', function() {
                        console.log('📋 Modal completamente abierto, inicializando Select2...');
                        
                        // Destruir Select2 existente si existe
                        if ($('#cliente_id').hasClass('select2-hidden-accessible')) {
                            $('#cliente_id').select2('destroy');
                        }
                        if ($('#gestor_id').hasClass('select2-hidden-accessible')) {
                            $('#gestor_id').select2('destroy');
                        }
                        
                        // Inicializar Select2 en el modal
                        if ($('#cliente_id').length > 0) {
                            $('#cliente_id').select2({
                                placeholder: 'Sin cliente',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#modal-cita')
                            });
                            console.log('✅ Select2 cliente inicializado en modal');
                        }
                        
                        if ($('#gestor_id').length > 0) {
                            $('#gestor_id').select2({
                                placeholder: 'Sin asignar',
                                allowClear: true,
                                width: '100%',
                                dropdownParent: $('#modal-cita')
                            });
                            console.log('✅ Select2 gestor inicializado en modal');
                        }
                    });
                } else {
                    console.error('❌ No se encontró el modal de cita');
                }
                
            } catch (error) {
                console.error('❌ Error al abrir modal:', error);
                mostrarNotificacion('error', 'Error', 'No se pudo abrir el modal de nueva cita');
            }
        }

        function crearNuevaCita(fecha) {
            console.log('📅 Creando nueva cita para:', fecha);
            
            try {
                // Primero mostrar el modal
                mostrarModalCita();
                
                // Esperar un poco para que el modal se abra completamente
                setTimeout(() => {
                    // Verificar que los elementos existan antes de acceder a ellos
                    const fechaInicio = document.getElementById('fecha_inicio');
                    const fechaFin = document.getElementById('fecha_fin');
                    
                    if (fechaInicio) {
                        fechaInicio.value = fecha + 'T09:00';
                        console.log('✅ Fecha de inicio establecida:', fecha + 'T09:00');
                    } else {
                        console.error('❌ Campo fecha_inicio no encontrado');
                    }
                    
                    if (fechaFin) {
                        fechaFin.value = fecha + 'T10:00';
                        console.log('✅ Fecha de fin establecida:', fecha + 'T10:00');
                    } else {
                        console.error('❌ Campo fecha_fin no encontrado');
                    }
                }, 200);
                
            } catch (error) {
                console.error('❌ Error al crear nueva cita:', error);
                mostrarNotificacion('error', 'Error', 'No se pudo crear la nueva cita');
            }
        }

        function guardarCita() {
            const form = document.getElementById('form-cita');
            
            if (!form) {
                console.error('❌ No se encontró el formulario de cita');
                return;
            }
            
            const formData = new FormData(form);
            const titulo = formData.get('titulo');
            const descripcion = formData.get('descripcion');
            const fechaInicio = formData.get('fecha_inicio');
            const fechaFin = formData.get('fecha_fin');
            const tipo = formData.get('tipo');
            const ubicacion = formData.get('ubicacion');
            const color = formData.get('color');
            const clienteId = formData.get('cliente_id');
            const gestorId = formData.get('gestor_id');
            
            console.log('💾 Guardando cita:', titulo);
            console.log('📝 Datos del formulario:', {
                titulo, descripcion, fechaInicio, fechaFin, tipo, ubicacion, color, clienteId, gestorId
            });
            
            // Determinar si es edición o creación
            const esEdicion = citaActual !== null;
            
            if (esEdicion) {
                console.log('✏️ Editando cita existente:', citaActual);
                
                // Buscar la cita en el calendario
                const cita = calendar.getEventById(citaActual);
                if (cita) {
                    // Actualizar los datos de la cita
                    cita.setProp('title', titulo);
                    cita.setStart(fechaInicio);
                    cita.setEnd(fechaFin);
                    cita.setProp('color', color);
                    cita.setExtendedProp('descripcion', descripcion);
                    cita.setExtendedProp('tipo', tipo);
                    cita.setExtendedProp('ubicacion', ubicacion);
                    cita.setExtendedProp('cliente', clienteId);
                    cita.setExtendedProp('gestor', gestorId);
                    
                    console.log('✅ Cita actualizada en el calendario');
                }
                
                mostrarNotificacion('success', 'Cita actualizada exitosamente', `La cita "${titulo}" ha sido actualizada correctamente.`);
            } else {
                console.log('➕ Creando nueva cita');
                
                // Crear nuevo evento en el calendario
                const nuevoEvento = {
                    id: Date.now(), // ID temporal
                    title: titulo,
                    start: fechaInicio,
                    end: fechaFin,
                    color: color,
                    extendedProps: {
                        descripcion: descripcion,
                        tipo: tipo,
                        ubicacion: ubicacion,
                        cliente: clienteId,
                        gestor: gestorId
                    }
                };
                
                calendar.addEvent(nuevoEvento);
                console.log('✅ Nueva cita agregada al calendario');
                
                mostrarNotificacion('success', 'Cita creada exitosamente', `La cita "${titulo}" ha sido creada correctamente.`);
            }
            
            // Limpiar variables
            citaActual = null;
            document.getElementById('titulo-modal-cita').textContent = 'Nueva Cita';
            
            // Cerrar modal
            const modalCita = document.getElementById('modal-cita');
            if (modalCita) {
                bootstrap.Modal.getInstance(modalCita).hide();
            }
        }

        function mostrarProximasCitas() {
            console.log('⏰ Mostrando próximas citas');
            mostrarNotificacion('info', 'Próximas Citas', '• Reunión de Prueba (Hoy)<br>• Llamada Cliente (Mañana)<br>• Presentación Proyecto (Pasado mañana)');
        }

        function mostrarVencidasCitas() {
            console.log('⚠️ Mostrando citas vencidas');
            mostrarNotificacion('warning', 'Citas Vencidas', 'No hay citas vencidas en este momento.');
        }

        function aplicarFiltros() {
            const gestor = document.getElementById('filtro-gestor').value;
            const cliente = document.getElementById('filtro-cliente').value;
            const estado = document.getElementById('filtro-estado').value;
            
            console.log('🔍 Filtros aplicados:', { gestor, cliente, estado });
            mostrarNotificacion('success', 'Filtros Aplicados', `Gestor: ${gestor || 'Todos'}<br>Cliente: ${cliente || 'Todos'}<br>Estado: ${estado || 'Todos'}`);
            
            if (calendar) {
                calendar.refetchEvents();
            }
        }

        function limpiarFiltros() {
            $('#filtro-gestor').val(null).trigger('change');
            $('#filtro-cliente').val(null).trigger('change');
            document.getElementById('filtro-estado').value = '';
            
            console.log('🧹 Filtros limpiados');
            mostrarNotificacion('info', 'Filtros Limpiados', 'Todos los filtros han sido restablecidos.');
            
            if (calendar) {
                calendar.refetchEvents();
            }
        }

        function actualizarFechaCita(event) {
            console.log('🔄 Actualizando fecha de:', event.title);
        }

        function actualizarDuracionCita(event) {
            console.log('📏 Actualizando duración de:', event.title);
        }

        // Función para mostrar notificaciones elegantes
        function mostrarNotificacion(tipo, titulo, mensaje) {
            const notificacion = document.createElement('div');
            notificacion.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`;
            notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: var(--shadow-lg);';
            
            const iconos = {
                'success': 'fas fa-check-circle',
                'info': 'fas fa-info-circle',
                'warning': 'fas fa-exclamation-triangle',
                'danger': 'fas fa-times-circle'
            };
            
            notificacion.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="${iconos[tipo]} me-2"></i>
                    <div>
                        <strong>${titulo}</strong><br>
                        <small>${mensaje}</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(notificacion);
            
            // Auto-remove después de 5 segundos
            setTimeout(() => {
                if (notificacion.parentNode) {
                    notificacion.remove();
                }
            }, 5000);
        }

        // Función para verificar que todos los elementos del modal existan
        function verificarElementosModal() {
            const elementos = [
                'modal-cita',
                'titulo-modal-cita', 
                'form-cita',
                'fecha_inicio',
                'fecha_fin',
                'cliente_id',
                'gestor_id',
                'color'
            ];
            
            const elementosFaltantes = [];
            
            elementos.forEach(id => {
                if (!document.getElementById(id)) {
                    elementosFaltantes.push(id);
                }
            });
            
            if (elementosFaltantes.length > 0) {
                console.error('❌ Elementos del modal faltantes:', elementosFaltantes);
                return false;
            }
            
            console.log('✅ Todos los elementos del modal están presentes');
            return true;
        }

        console.log('✅ Sistema de citas inicializado correctamente');
    });

    // Función global para editar cita
    function editarCita(citaId) {
        console.log('✏️ Editando cita:', citaId);
        
        // Eliminar completamente el modal de detalles del DOM
        const modalDetalles = document.getElementById('modal-detalle-cita');
        if (modalDetalles) {
            modalDetalles.remove();
            console.log('🗑️ Modal de detalles eliminado del DOM');
        }
        
        // Limpiar cualquier backdrop restante
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        
        // Limpiar estilos del body
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        
        // Esperar un momento y luego abrir el modal de edición
        setTimeout(() => {
            abrirModalEdicion(citaId);
        }, 100);
    }
    
    function abrirModalEdicion(citaId) {
        // Buscar la cita en el calendario
        const cita = calendar.getEventById(citaId);
        if (!cita) {
            console.error('❌ No se encontró la cita con ID:', citaId);
            alert('No se pudo encontrar la cita para editar');
            return;
        }
        
        // Configurar el modal para edición
        document.getElementById('titulo-modal-cita').textContent = 'Editar Cita';
        document.getElementById('form-cita').reset();
        
        // Llenar el formulario con los datos de la cita
        document.getElementById('titulo').value = cita.title;
        document.getElementById('descripcion').value = cita.extendedProps.descripcion || '';
        document.getElementById('fecha_inicio').value = cita.start.toISOString().slice(0, 16);
        document.getElementById('fecha_fin').value = cita.end.toISOString().slice(0, 16);
        document.getElementById('tipo').value = cita.extendedProps.tipo || 'reunion';
        document.getElementById('ubicacion').value = cita.extendedProps.ubicacion || '';
        document.getElementById('color').value = cita.color || '#3b82f6';
        
        // Guardar el ID de la cita para la actualización
        citaActual = citaId;
        
        // Mostrar el modal
        const modalEdicion = new bootstrap.Modal(document.getElementById('modal-cita'));
        modalEdicion.show();
        
        // Configurar Select2 de forma simple
        setTimeout(() => {
            console.log('🔧 Configurando Select2...');
            
            // Destruir Select2 existente
            try {
                $('#cliente_id').select2('destroy');
                $('#gestor_id').select2('destroy');
            } catch(e) {
                console.log('Select2 no estaba inicializado');
            }
            
            // Limpiar contenedores
            $('.select2-container').remove();
            $('.select2-dropdown').remove();
            
            // Reinicializar Select2
            $('#cliente_id').select2({
                placeholder: 'Buscar cliente...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-cita')
            });
            
            $('#gestor_id').select2({
                placeholder: 'Buscar gestor...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modal-cita')
            });
            
            console.log('✅ Select2 inicializado');
            
            // Configurar valores
            if (cita.extendedProps.cliente) {
                console.log('🔧 Configurando cliente:', cita.extendedProps.cliente);
                const clienteSelect = document.getElementById('cliente_id');
                const clienteOptions = clienteSelect.options;
                for (let i = 0; i < clienteOptions.length; i++) {
                    if (clienteOptions[i].text === cita.extendedProps.cliente) {
                        $('#cliente_id').val(clienteOptions[i].value).trigger('change');
                        console.log('✅ Cliente seleccionado:', clienteOptions[i].value);
                        break;
                    }
                }
            }
            
            if (cita.extendedProps.gestor) {
                console.log('🔧 Configurando gestor:', cita.extendedProps.gestor);
                const gestorSelect = document.getElementById('gestor_id');
                const gestorOptions = gestorSelect.options;
                for (let i = 0; i < gestorOptions.length; i++) {
                    if (gestorOptions[i].text === cita.extendedProps.gestor) {
                        $('#gestor_id').val(gestorOptions[i].value).trigger('change');
                        console.log('✅ Gestor seleccionado:', gestorOptions[i].value);
                        break;
                    }
                }
            }
            
        }, 300);
    }
    </script>
</body>
</html>
