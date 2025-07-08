<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal SEO - Hawkins</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Estilos personalizados */
        .glass-morphism {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .custom-gradient {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 50%, #818cf8 100%);
        }

        .pattern-bg {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366f1' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6366f1;
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>
</head>
<body class="min-h-screen pattern-bg">
    <!-- Main Content -->
    <main class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Portal de Informes</span>
                    <span class="block text-primary-600">SEO Analytics</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    Accede a tus informes detallados de posicionamiento y rendimiento SEO. Analiza el progreso de tu sitio web.
                </p>
            </div>

            <!-- Main Card -->
            <div class="max-w-md mx-auto">
                <div class="glass-morphism rounded-2xl shadow-xl p-8">
                    <!-- PIN Form -->
                    <div class="space-y-8" id="pinSection">
                        <div class="flex justify-center">
                            <div class="animate-float">
                                <svg class="w-24 h-24 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label for="pin" class="block text-sm font-medium text-gray-700">
                                Código de Acceso Seguro
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <input type="password"
                                       id="pin"
                                       class="block w-full pr-10 pl-4 py-3 text-gray-900 placeholder-gray-400 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200"
                                       placeholder="Introduce tu PIN"
                                       autocomplete="off">
                                <button type="button"
                                        onclick="togglePinVisibility()"
                                        class="absolute inset-y-0 right-0 px-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-primary-600" id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="button"
                                id="checkPinBtn"
                                class="w-full flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <span>Acceder a mis informes</span>
                            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </button>

                        <!-- Status Messages -->
                        <div id="pinResult"></div>
                    </div>

                    <!-- Reports Section -->
                    <div id="reportsSection" class="hidden space-y-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Informes Disponibles
                            </h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                SEO Analytics
                            </span>
                        </div>
                        <div id="reportButtons" class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar pr-2"></div>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        ¿Necesitas ayuda?
                        <a href="mailto:soporte@hawkins.es" class="font-medium text-primary-600 hover:text-primary-500">
                            Contacta con nuestro equipo
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>
    <script>
        function togglePinVisibility() {
            const pinInput = document.getElementById('pin');
            const eyeIcon = document.getElementById('eyeIcon');

            if (pinInput.type === 'password') {
                pinInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                pinInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        function formatDate(dateString) {
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleString('es-ES', options);
        }

        function showLoadingState() {
            const resultDiv = document.getElementById('pinResult');
            resultDiv.innerHTML = `
                <div class="flex items-center justify-center py-4 space-x-3">
                    <svg class="animate-spin h-5 w-5 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Verificando acceso...</span>
                </div>
            `;
        }

        function showError(message) {
            return `
                <div class="rounded-lg bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">${message}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function showSuccess(message) {
            return `
                <div class="rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">${message}</p>
                        </div>
                    </div>
                </div>
            `;
        }

        function createReportCard(report) {
            return `
                <a href="/autoseo/reports/${report.autoseo_id}/${report.id}"
                   class="block p-4 rounded-xl border border-gray-200 hover:border-primary-500 bg-white transition-all duration-200 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="p-2 bg-primary-100 rounded-lg">
                                    <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Informe SEO Detallado</h4>
                                <p class="text-xs text-gray-500">${formatDate(report.created_at)}</p>
                            </div>
                        </div>
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            `;
        }

        function checkPin() {
            const pin = document.getElementById('pin').value;
            const resultDiv = document.getElementById('pinResult');
            const reportsSection = document.getElementById('reportsSection');
            const reportButtons = document.getElementById('reportButtons');

            if (!pin) {
                resultDiv.innerHTML = showError('Por favor, introduce tu PIN de acceso');
                return;
            }

            showLoadingState();

            fetch('{{ route('autoseo.reports.login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ pin })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultDiv.innerHTML = showSuccess('Acceso verificado correctamente');
                    reportsSection.classList.remove('hidden');

                    if (data.reports.length > 0) {
                        reportButtons.innerHTML = data.reports
                            .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
                            .map(report => createReportCard(report))
                            .join('');
                    } else {
                        reportButtons.innerHTML = `
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay informes disponibles</h3>
                                <p class="mt-1 text-sm text-gray-500">Los nuevos informes aparecerán aquí cuando estén listos.</p>
                            </div>
                        `;
                    }
                } else {
                    resultDiv.innerHTML = showError(data.message || 'PIN incorrecto');
                    reportsSection.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error(error);
                resultDiv.innerHTML = showError('Error al verificar el acceso');
                reportsSection.classList.add('hidden');
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const pinInput = document.getElementById('pin');
            const checkPinBtn = document.getElementById('checkPinBtn');

            checkPinBtn.addEventListener('click', checkPin);

            pinInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    checkPin();
                }
            });
        });
    </script>
</body>
</html>
