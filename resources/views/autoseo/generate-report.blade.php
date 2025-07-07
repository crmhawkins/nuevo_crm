@extends('layouts.app')

@section('title', 'Generar Informe SEO')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">🔍 Generar Informe SEO Comparativo</h4>
                    <p class="card-subtitle text-muted">Genera informes SEO comparativos con datos históricos</p>
                </div>
                <div class="card-body">
                    <form id="generateReportForm" method="POST" action="{{ route('autoseo.generate.report') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="report_id">ID del Reporte</label>
                                    <input type="number"
                                           class="form-control @error('report_id') is-invalid @enderror"
                                           id="report_id"
                                           name="report_id"
                                           value="{{ old('report_id', 15) }}"
                                           placeholder="ID del reporte (por defecto 15)">
                                    @error('report_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">ID del reporte SEO a procesar</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_notification">Email de Notificación (Opcional)</label>
                                    <input type="email"
                                           class="form-control @error('email_notification') is-invalid @enderror"
                                           id="email_notification"
                                           name="email_notification"
                                           value="{{ old('email_notification') }}"
                                           placeholder="tu@email.com">
                                    @error('email_notification')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Recibirás una notificación cuando el informe esté listo</small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6>📋 Proceso de Generación:</h6>
                                    <ul class="mb-0">
                                        <li>Descarga de datos JSON desde el servidor</li>
                                        <li>Procesamiento de keywords y métricas SEO</li>
                                        <li>Generación de gráficos comparativos</li>
                                        <li>Creación de informe HTML</li>
                                        <li>Envío automático al servidor</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary" id="generateBtn">
                                    <i class="fas fa-chart-line"></i> Generar Informe
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="history.back()">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Progress Modal -->
                    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="progressModalLabel">Generando Informe SEO</h5>
                                </div>
                                <div class="modal-body">
                                    <div class="progress mb-3">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                             role="progressbar"
                                             style="width: 0%"
                                             id="progressBar">0%</div>
                                    </div>
                                    <div id="progressText" class="text-center">
                                        Iniciando proceso de generación...
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('generateReportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const generateBtn = document.getElementById('generateBtn');
    const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));

    // Mostrar modal de progreso
    progressModal.show();

    // Deshabilitar botón
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    // Simular progreso
    let progress = 0;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');

    const progressInterval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;

        progressBar.style.width = progress + '%';
        progressBar.textContent = Math.round(progress) + '%';

        if (progress < 30) {
            progressText.textContent = 'Descargando datos JSON...';
        } else if (progress < 60) {
            progressText.textContent = 'Procesando keywords y métricas...';
        } else if (progress < 90) {
            progressText.textContent = 'Generando gráficos y tablas...';
        }
    }, 500);

    // Enviar formulario
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);

        if (data.success) {
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';
            progressText.innerHTML = `
                <div class="alert alert-success">
                    <h6>✅ Informe Generado Correctamente</h6>
                    <p><strong>Archivo:</strong> ${data.filename}</p>
                    <p><strong>Mensaje:</strong> ${data.message}</p>
                </div>
            `;
        } else {
            progressText.innerHTML = `
                <div class="alert alert-danger">
                    <h6>❌ Error al Generar Informe</h6>
                    <p>${data.error}</p>
                </div>
            `;
        }
    })
    .catch(error => {
        clearInterval(progressInterval);
        progressText.innerHTML = `
            <div class="alert alert-danger">
                <h6>❌ Error de Conexión</h6>
                <p>${error.message}</p>
            </div>
        `;
    })
    .finally(() => {
        // Habilitar botón
        generateBtn.disabled = false;
        generateBtn.innerHTML = '<i class="fas fa-chart-line"></i> Generar Informe';
    });
});
</script>
@endsection
