@extends('layouts.app')

@section('titulo', 'Dashboard Personal')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
    <style>
        /* Ocultar la barra superior para el dashboard del departamento 9 */
        #topbar,
        .topBar,
        .navbar {
            display: none !important;
        }
        
        /* Ajustar el contenedor principal */
        .contenedor {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        
        /* Asegurar que el body no tenga scroll horizontal */
        body {
            overflow-x: hidden !important;
        }
        
        /* Estilos móviles modernos */
        .mobile-dashboard {
            padding: 0;
            margin: 0;
            min-height: 100vh;
            background: #f0f0f0;
        }
        
        /* Eliminar espacios en blanco y franjas negras */
        body {
            margin: 0 !important;
            padding: 0 !important;
            background: #f0f0f0 !important;
        }
        
        #app {
            margin: 0 !important;
            padding: 0 !important;
            background: #f0f0f0 !important;
        }
        
        main#main {
            margin: 0 !important;
            padding: 0 !important;
            background: #f0f0f0 !important;
        }
        
        .contenedor {
            margin: 0 !important;
            padding: 0 !important;
            background: #f0f0f0 !important;
        }
        
        .mobile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 0 0 20px 20px;
            margin: 0 0 1rem 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }
        
        .mobile-timer-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .timer-display {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            text-align: center;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
        }
        
        .jornada-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .jornada-btn {
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .jornada-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #a0aec0, #718096);
            color: white;
        }
        
        .btn-dark {
            background: linear-gradient(135deg, #4a5568, #2d3748);
            color: white;
        }
        
        .mobile-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .mobile-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid #e2e8f0;
        }
        
        .mobile-card h5 {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
            margin-top: 1rem;
        }
        
        .quick-action-btn {
            padding: 1rem;
            border-radius: 12px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            color: #4a5568;
            border: 1px solid #e2e8f0;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            color: #2d3748;
            text-decoration: none;
        }
        
        .user-info {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .user-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }
        
        .user-department {
            color: #718096;
            font-size: 0.9rem;
        }
        
        
        @media (max-width: 768px) {
            .mobile-dashboard {
                padding: 0.5rem;
            }
            
            .timer-display {
                font-size: 2rem;
            }
            
            .jornada-buttons {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .todo-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .todo-actions {
                margin-left: 0;
                align-self: flex-end;
            }
        }
    </style>
@endsection

@section('content')
    <div class="mobile-dashboard">
        <!-- Header móvil -->
        <div class="mobile-header">
            <div class="user-info">
                @if ($user->image == null)
                    <img alt="avatar" class="user-avatar" src="{{ asset('assets/images/guest.webp') }}" />
                @else
                    <img alt="avatar" class="user-avatar" src="{{ asset('/storage/avatars/' . $user->image) }}" />
                @endif
                <div class="user-name">{{ $user->name }} {{ $user->surname }}</div>
                <div class="user-department">{{ $user->departamento ? $user->departamento->name : 'Sin departamento' }}</div>
            </div>
        </div>

        <!-- Sección de fichaje -->
        <div class="mobile-timer-section">
            <h5 class="text-center mb-3">Control de Jornada</h5>
            <div class="timer-display" id="timer">00:00:00</div>
            
            <div class="jornada-buttons">
                <button id="startJornadaBtn" class="jornada-btn btn-primary" onclick="startJornada()">
                    <i class="fas fa-play me-2"></i>Iniciar Jornada
                </button>
                <button id="startPauseBtn" class="jornada-btn btn-secondary" onclick="startPause()" style="display:none;">
                    <i class="fas fa-pause me-2"></i>Iniciar Pausa
                </button>
                <button id="endPauseBtn" class="jornada-btn btn-dark" onclick="endPause()" style="display:none;">
                    <i class="fas fa-play me-2"></i>Finalizar Pausa
                </button>
                <button id="endJornadaBtn" class="jornada-btn btn-danger" onclick="endJornada()" style="display:none;">
                    <i class="fas fa-stop me-2"></i>Fin de Jornada
                </button>
            </div>
        </div>

        <!-- Acciones principales -->
        <div class="mobile-card">
            <h5>Gestión Personal</h5>
            <div class="quick-actions">
                <a class="quick-action-btn" href="{{ route('contratos.index_user', $user->id) }}">
                    <i class="fas fa-file-contract me-2"></i>Contrato
                </a>
                <a class="quick-action-btn" href="{{ route('nominas.index_user', $user->id) }}">
                    <i class="fas fa-money-bill-wave me-2"></i>Nómina
                </a>
                <a class="quick-action-btn" href="{{ route('holiday.index') }}">
                    <i class="fas fa-calendar-alt me-2"></i>Vacaciones
                </a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('partials.toast')
    <script src="{{ asset('assets/vendors/choices.js/choices.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Script para ocultar la barra superior inmediatamente -->
    <script>
        // Ejecutar inmediatamente, sin esperar al DOM
        (function() {
            // Función para ocultar la barra superior
            function hideTopBar() {
                const topBar = document.querySelector('#topbar');
                if (topBar) {
                    topBar.style.display = 'none';
                    topBar.style.visibility = 'hidden';
                    topBar.style.height = '0';
                    topBar.style.overflow = 'hidden';
                }
                
                const navbar = document.querySelector('.navbar');
                if (navbar) {
                    navbar.style.display = 'none';
                }
            }
            
            // Ejecutar inmediatamente
            hideTopBar();
            
            // Ejecutar cuando el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', hideTopBar);
            } else {
                hideTopBar();
            }
            
            // Ejecutar después de un pequeño delay para asegurar
            setTimeout(hideTopBar, 100);
            
            // Eliminar cualquier franja negra o espacio vacío
            function removeBlackBars() {
                // Buscar y eliminar elementos que puedan causar franjas negras
                const elementsToRemove = [
                    '.css-96uzu9',
                    '[style*="background-color: black"]',
                    '[style*="background: black"]',
                    '.loadingOverlay'
                ];
                
                elementsToRemove.forEach(selector => {
                    const elements = document.querySelectorAll(selector);
                    elements.forEach(el => {
                        if (el) {
                            el.style.display = 'none';
                            el.style.height = '0';
                            el.style.overflow = 'hidden';
                        }
                    });
                });
                
                // Asegurar que el body y app tengan el fondo correcto
                document.body.style.backgroundColor = '#f0f0f0';
                const app = document.getElementById('app');
                if (app) {
                    app.style.backgroundColor = '#f0f0f0';
                }
            }
            
            // Ejecutar la función de limpieza
            removeBlackBars();
            setTimeout(removeBlackBars, 200);
        })();
    </script>

    <script>
        // Sistema de fichaje - extraído del dashboard_gestor
        let timerState = '{{ $jornadaActiva ? 'running' : 'stopped' }}'
        let timerTime = {{ $timeWorkedToday }}; // In seconds, initialized with the time worked today
        
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
                        timerTime = data.time
                        updateTime()
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

            document.getElementById('timer').textContent = `${hours}:${minutes}:${seconds}`;
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
                        document.getElementById('startJornadaBtn').style.display = 'none';
                        document.getElementById('startPauseBtn').style.display = 'block';
                        document.getElementById('endJornadaBtn').style.display = 'block';
                        
                        // Notificación de éxito
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Jornada iniciada correctamente',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al iniciar la jornada',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
        }

        function endJornada() {
            // Obtener el tiempo actualizado
            getTime();

            let now = new Date();
            let currentHour = now.getHours();
            let currentMinute = now.getMinutes();

            // Convertir los segundos trabajados a horas
            let workedHours = timerTime / 3600;

            // Verificar si es antes de las 18:00 o si ha trabajado menos de 8 horas
            if (currentHour < 18 || workedHours < 8) {
                let title = '';
                let text = '';

                if (currentHour < 18) {
                    title = 'Horario de Salida Prematuro';
                    text = 'Es menos de las 18:00.  ';
                } else {
                    if (workedHours < 8) {
                        title = ('Jornada Incompleta');
                        text = 'Has trabajado menos de 8 horas. Si no compensas el tiempo faltante,';
                    }
                }

                text += 'Se te descontará de tus vacaciones al final del mes.';

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Finalizar Jornada',
                    cancelButtonText: 'Continuar Jornada',
                    confirmButtonColor: '#e53e3e',
                    cancelButtonColor: '#4299e1'
                }).then((result) => {
                    if (result.isConfirmed) {
                        finalizarJornada();
                    }
                });
            } else {
                // Si el tiempo es mayor o igual a 8 horas y es después de las 18:00, finalizamos directamente la jornada
                finalizarJornada();
            }
        }

        function finalizarJornada() {
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
                        document.getElementById('startJornadaBtn').style.display = 'block';
                        document.getElementById('startPauseBtn').style.display = 'none';
                        document.getElementById('endJornadaBtn').style.display = 'none';
                        document.getElementById('endPauseBtn').style.display = 'none';
                        
                        // Notificación de éxito
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Jornada finalizada correctamente',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error al finalizar la jornada',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
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
                        document.getElementById('startPauseBtn').style.display = 'none';
                        document.getElementById('endPauseBtn').style.display = 'block';
                        
                        // Notificación de éxito
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Pausa iniciada',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
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
                        document.getElementById('startPauseBtn').style.display = 'block';
                        document.getElementById('endPauseBtn').style.display = 'none';
                        
                        // Notificación de éxito
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Pausa finalizada',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Inicialización del dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Ocultar la barra superior
            const topBar = document.querySelector('#topbar');
            if (topBar) {
                topBar.style.display = 'none';
                topBar.style.visibility = 'hidden';
                topBar.style.height = '0';
                topBar.style.overflow = 'hidden';
            }
            
            // También ocultar por clase
            const navbar = document.querySelector('.navbar');
            if (navbar) {
                navbar.style.display = 'none';
            }
            
            // Ajustar el contenedor principal
            const contenedor = document.querySelector('.contenedor');
            if (contenedor) {
                contenedor.style.marginTop = '0';
                contenedor.style.paddingTop = '0';
            }
            
            updateTime(); // Initialize the timer display

            setInterval(function() {
                getTime();
            }, 120000);

            // Initialize button states based on jornada and pause
            if ('{{ $jornadaActiva }}') {
                document.getElementById('startJornadaBtn').style.display = 'none';
                document.getElementById('endJornadaBtn').style.display = 'block';
                if ('{{ $pausaActiva }}') {
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'block';
                } else {
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endPauseBtn').style.display = 'none';
                    startTimer(); // Start timer if not in pause
                }
            } else {
                document.getElementById('startJornadaBtn').style.display = 'block';
                document.getElementById('endJornadaBtn').style.display = 'none';
                document.getElementById('startPauseBtn').style.display = 'none';
                document.getElementById('endPauseBtn').style.display = 'none';
            }

        });
    </script>

    @include('components.justificaciones.modal')
    @include('components.justificaciones.script')
@endsection
