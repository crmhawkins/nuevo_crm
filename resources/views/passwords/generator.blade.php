@extends('layouts.app')

@section('titulo', 'Generador de Contraseñas')

@section('css')
<style>
    .password-result {
        font-family: 'Courier New', monospace;
        font-size: 1.2rem;
        font-weight: bold;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 1rem;
        word-break: break-all;
    }
    .domain-info {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
</style>
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-key-fill"></i> Generador de Contraseñas Deterministas</h3>
                    <p class="text-subtitle text-muted">Herramienta para generar contraseñas basadas en dominios</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('passwords.index')}}">Contraseñas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Generador</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6">
                            <div class="text-center mb-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Herramienta de Generación</strong><br>
                                    Ingresa un dominio y obtén su contraseña determinista. Esta herramienta no guarda ninguna información.
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="text-uppercase form-label" style="font-weight: bold" for="dominio-input">
                                    <i class="bi bi-globe"></i> Dominio:
                                </label>
                                <div class="input-group">
                                    <input
                                        type="text"
                                        class="form-control form-control-lg"
                                        id="dominio-input"
                                        placeholder="ejemplo.com o https://www.ejemplo.com"
                                        autofocus
                                    >
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-lg"
                                        id="btn-generar"
                                    >
                                        <i class="bi bi-magic"></i> Generar
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Puedes ingresar el dominio con o sin protocolo (http/https) y con o sin www
                                </small>
                            </div>

                            <div id="resultado-container" style="display: none;">
                                <div class="password-result" id="password-result">
                                    <div class="mb-2">
                                        <i class="bi bi-shield-lock"></i> Contraseña Generada
                                    </div>
                                    <div id="password-value" style="font-size: 1.5rem; letter-spacing: 2px;"></div>
                                    <div class="domain-info" id="domain-info"></div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light mt-3"
                                        id="btn-copiar"
                                        title="Copiar al portapapeles"
                                    >
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGenerar = document.getElementById('btn-generar');
    const btnCopiar = document.getElementById('btn-copiar');
    const inputDominio = document.getElementById('dominio-input');
    const resultadoContainer = document.getElementById('resultado-container');
    const passwordValue = document.getElementById('password-value');
    const domainInfo = document.getElementById('domain-info');

    // Generar contraseña al presionar Enter
    inputDominio.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            btnGenerar.click();
        }
    });

    // Función para generar contraseña
    btnGenerar.addEventListener('click', function() {
        const dominio = inputDominio.value.trim();

        if (!dominio) {
            alert('Por favor, ingresa un dominio para generar la contraseña.');
            inputDominio.focus();
            return;
        }

        // Deshabilitar botón mientras se genera
        btnGenerar.disabled = true;
        btnGenerar.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando...';

        // Realizar petición AJAX
        fetch('{{ route("passwords.generar") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ dominio: dominio })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error === false) {
                passwordValue.textContent = data.password;
                domainInfo.textContent = `Dominio procesado: ${data.dominio_limpio}`;
                resultadoContainer.style.display = 'block';

                // Scroll suave al resultado
                resultadoContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                alert('Error al generar la contraseña: ' + (data.mensaje || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al generar la contraseña. Por favor, intenta nuevamente.');
        })
        .finally(() => {
            // Rehabilitar botón
            btnGenerar.disabled = false;
            btnGenerar.innerHTML = '<i class="bi bi-magic"></i> Generar';
        });
    });

    // Función para copiar al portapapeles
    btnCopiar.addEventListener('click', function() {
        const password = passwordValue.textContent;

        navigator.clipboard.writeText(password).then(function() {
            // Feedback visual
            const originalText = btnCopiar.innerHTML;
            btnCopiar.innerHTML = '<i class="bi bi-check"></i> ¡Copiado!';
            btnCopiar.classList.remove('btn-light');
            btnCopiar.classList.add('btn-success');

            setTimeout(function() {
                btnCopiar.innerHTML = originalText;
                btnCopiar.classList.remove('btn-success');
                btnCopiar.classList.add('btn-light');
            }, 2000);
        }).catch(function(err) {
            console.error('Error al copiar:', err);
            alert('Error al copiar al portapapeles. Por favor, copia manualmente.');
        });
    });
});
</script>
@endsection
