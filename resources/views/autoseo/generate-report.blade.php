@extends('layouts.app')

@section('title', 'Generar Informe SEO')

@section('content')
<style>
    .css-96uzu9 {
        opacity: 0;
    }
</style>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30 pattern-bg py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">
            <div class="bg-gradient-to-r from-indigo-500 via-primary-500 to-purple-500 p-8">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/10 backdrop-blur-xl rounded-2xl shadow-inner">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white tracking-tight">Generar Informe SEO</h2>
                        <p class="text-indigo-100 text-sm mt-1">Genera informes SEO comparativos con datos históricos</p>
                    </div>
                </div>
            </div>

            <div class="p-8">
                <form id="generateReportForm" method="POST" action="{{ route('autoseo.generate.report') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Cliente</label>
                            <div class="relative">
                                <select id="client_id"
                                        name="client_id"
                                        class="appearance-none block w-full px-6 py-3.5 text-base border border-gray-300 bg-white/80 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $selectedClientId == $client->id ? 'selected' : '' }}>
                                            {{ $client->client_name }} - {{ $client->url }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="email_notification" class="block text-sm font-medium text-gray-700 mb-2">Correo para notificación</label>
                            <div class="relative">
                                <input type="email"
                                       id="email_notification"
                                       name="email_notification"
                                       value="{{ old('email_notification') }}"
                                       class="block w-full px-6 py-3.5 text-base border border-gray-300 bg-white/80 backdrop-blur-sm rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('email_notification') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror"
                                       placeholder="ejemplo@dominio.com">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <svg class="h-6 w-6 text-gray-400 @error('email_notification') text-red-400 @enderror" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            @error('email_notification')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500 flex items-center">
                                <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Recibirás una notificación cuando el informe esté listo
                            </p>
                        </div>

                        <div class="bg-primary-50 rounded-xl p-6 border border-primary-100/50">
                            <h6 class="text-sm font-medium text-primary-900 mb-3">📋 Proceso de Generación:</h6>
                            <ul class="space-y-2 text-sm text-primary-700">
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Descarga de datos JSON desde el servidor
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Procesamiento de keywords y métricas SEO
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Generación de gráficos comparativos
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Creación de informe HTML
                                </li>
                                <li class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Envío automático al servidor
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="flex justify-end space-x-4">
                            <button type="submit" name="report_type" value="parallel" formaction="{{ route('autoseo.generate.report') }}?type=parallel"
                                class="inline-flex items-center px-6 py-3.5 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Generar Justificación
                            </button>
                            <button type="submit" name="report_type" value="standard"
                                class="inline-flex items-center px-6 py-3.5 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-indigo-500 via-primary-500 to-purple-500 hover:from-indigo-600 hover:via-primary-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Generar Informe
                            </button>
                        </div>
                        <button type="button" onclick="history.back()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Volver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="progressModal" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-primary-100">
                    <svg class="h-6 w-6 text-primary-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Generando Informe SEO
                    </h3>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%" id="progressBar"></div>
                        </div>
                        <p class="mt-4 text-sm text-gray-500" id="progressText">
                            Iniciando proceso de generación...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitButtons = form.querySelectorAll('button[type="submit"]');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const progressModal = document.getElementById('progressModal');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        submitButtons.forEach(button => {
            button.addEventListener('click', async function(e) {
                e.preventDefault();

                // Mostrar modal de progreso
                progressModal.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = 'Iniciando proceso...';

                // Simular progreso
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += Math.random() * 15;
                    if (progress > 90) progress = 90;
                    progressBar.style.width = progress + '%';

                    if (progress < 30) {
                        progressText.textContent = 'Descargando datos JSON...';
                    } else if (progress < 60) {
                        progressText.textContent = 'Procesando keywords y métricas...';
                    } else if (progress < 90) {
                        progressText.textContent = 'Generando informe...';
                    }
                }, 500);

                const formData = new FormData(form);
                const reportType = this.value; // 'standard' o 'parallel'
                const url = `${form.action}?type=${reportType}`;

                try {
                    console.log('Iniciando generación de informe:', {
                        type: reportType,
                        url: url,
                        client_id: formData.get('client_id'),
                        email: formData.get('email_notification')
                    });

                    const response = await fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();
                    console.log('Respuesta del servidor:', data);

                    clearInterval(progressInterval);

                    if (!response.ok) {
                        throw new Error(data.error || 'Error al generar el informe');
                    }

                    // Mostrar mensaje de éxito
                    progressBar.style.width = '100%';
                    progressText.innerHTML = `
                        <div class="mt-4">
                            <div class="rounded-md bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-green-800">Informe Generado Correctamente</h3>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p><strong>Archivo:</strong> ${data.filename}</p>
                                            <p><strong>Mensaje:</strong> ${data.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button type="button" onclick="document.getElementById('progressModal').classList.add('hidden')" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                                Cerrar
                            </button>
                        </div>
                    `;

                } catch (error) {
                    console.error('Error en la generación del informe:', error);
                    clearInterval(progressInterval);
                    progressText.innerHTML = `
                        <div class="mt-4">
                            <div class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Error al Generar Informe</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>${error.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button type="button" onclick="document.getElementById('progressModal').classList.add('hidden')" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                                Cerrar
                            </button>
                        </div>
                    `;
                }
            });
        });
    });
</script>

<style>
.pattern-bg {
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%234f46e5' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
</style>
@endsection
