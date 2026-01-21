@extends('layouts.app')

@section('titulo', 'Generador de Contraseñas')

@section('css')
<style>
    .password-result {
        font-family: 'Courier New', monospace;
        font-size: 1.2rem;
        font-weight: bold;
        background: #ffffff;
        border: 2px solid #e9ecef;
        border-left: 4px solid #0d6efd;
        color: #212529;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
        margin-top: 1rem;
        word-break: break-all;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .domain-info {
        color: #6c757d;
        font-size: 0.9rem;
        margin-top: 0.5rem;
    }
    .password-strength {
        margin-top: 1rem;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
    }
    .strength-label {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    .strength-bar-container {
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }
    .strength-bar {
        height: 100%;
        transition: width 0.3s ease, background-color 0.3s ease;
        border-radius: 4px;
    }
    .strength-text {
        font-size: 0.8rem;
        font-weight: 500;
    }
    .strength-weak {
        background-color: #dc3545;
    }
    .strength-fair {
        background-color: #ffc107;
    }
    .strength-good {
        background-color: #0dcaf0;
    }
    .strength-strong {
        background-color: #198754;
    }
    .strength-very-strong {
        background-color: #0d6efd;
    }
    .password-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: #495057;
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
                                    <div class="password-header">
                                        <i class="bi bi-shield-lock-fill"></i>
                                        <span>Contraseña Generada</span>
                                    </div>
                                    <div id="password-value" style="font-size: 1.5rem; letter-spacing: 2px; color: #212529; margin: 1rem 0;"></div>

                                    <div class="password-strength">
                                        <div class="strength-label">
                                            <i class="bi bi-shield-check"></i> Nivel de Seguridad
                                        </div>
                                        <div class="strength-bar-container">
                                            <div class="strength-bar" id="strength-bar" style="width: 0%;"></div>
                                        </div>
                                        <div class="strength-text" id="strength-text"></div>
                                    </div>

                                    <div class="domain-info" id="domain-info" style="margin-top: 1rem;"></div>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary mt-3"
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
                domainInfo.textContent = `Dominio procesado: ${data.dominio_limpio}`;
                resultadoContainer.style.display = 'block';

                // Animar la generación de la contraseña
                animarGeneracionPassword(data.password);

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

    // Función para calcular la seguridad de la contraseña (sin animación)
    function calcularSeguridad(password) {
        let score = 0;
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        // Criterios de seguridad
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;
        if (password.length >= 16) score += 1;
        if (/[a-z]/.test(password)) score += 1;
        if (/[A-Z]/.test(password)) score += 1;
        if (/[0-9]/.test(password)) score += 1;
        if (/[^a-zA-Z0-9]/.test(password)) score += 1;
        if (password.length >= 20) score += 1;

        // Determinar nivel y porcentaje
        let nivel = '';
        let porcentaje = 0;
        let colorClass = '';

        if (score <= 2) {
            nivel = 'Débil';
            porcentaje = 25;
            colorClass = 'strength-weak';
        } else if (score <= 4) {
            nivel = 'Regular';
            porcentaje = 50;
            colorClass = 'strength-fair';
        } else if (score <= 6) {
            nivel = 'Buena';
            porcentaje = 75;
            colorClass = 'strength-good';
        } else if (score <= 7) {
            nivel = 'Fuerte';
            porcentaje = 90;
            colorClass = 'strength-strong';
        } else {
            nivel = 'Muy Fuerte';
            porcentaje = 100;
            colorClass = 'strength-very-strong';
        }

        return {
            nivel: nivel,
            porcentaje: porcentaje,
            colorClass: colorClass
        };
    }

    // Función para animar la generación de la contraseña
    function animarGeneracionPassword(password) {
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');

        // Calcular seguridad final
        const seguridad = calcularSeguridad(password);

        // Mostrar contraseña completa instantáneamente
        passwordValue.textContent = password;

        // Resetear barra
        strengthBar.style.width = '0%';
        strengthBar.className = 'strength-bar';
        strengthText.textContent = '';

        const duracionTotal = 1000; // 1 segundo
        const inicioTiempo = Date.now();

        // Mapa de colores para el texto de seguridad
        const colorMap = {
            'strength-weak': '#dc3545',
            'strength-fair': '#ffc107',
            'strength-good': '#0dcaf0',
            'strength-strong': '#198754',
            'strength-very-strong': '#0d6efd'
        };

        function animarFrame() {
            const tiempoTranscurrido = Date.now() - inicioTiempo;
            const progreso = Math.min(tiempoTranscurrido / duracionTotal, 1);

            // Calcular progreso exponencial de la barra (ease-out exponencial)
            // Función exponencial: 1 - Math.pow(1 - progreso, 3) para efecto ease-out
            const progresoExponencial = 1 - Math.pow(1 - progreso, 3);
            const porcentajeActual = progresoExponencial * seguridad.porcentaje;

            // Actualizar barra de seguridad
            strengthBar.style.width = porcentajeActual + '%';

            // Actualizar color y texto cuando llegue al 30% del progreso exponencial
            if (progresoExponencial >= 0.3 && strengthBar.className === 'strength-bar') {
                strengthBar.className = 'strength-bar ' + seguridad.colorClass;
                strengthText.textContent = seguridad.nivel;
                strengthText.style.color = colorMap[seguridad.colorClass] || '#495057';
            }

            if (progreso < 1) {
                // Continuar animación
                requestAnimationFrame(animarFrame);
            } else {
                // Asegurar que todo esté al 100%
                strengthBar.style.width = seguridad.porcentaje + '%';
                strengthBar.className = 'strength-bar ' + seguridad.colorClass;
                strengthText.textContent = seguridad.nivel;
                strengthText.style.color = colorMap[seguridad.colorClass] || '#495057';
            }
        }

        // Iniciar animación de la barra
        requestAnimationFrame(animarFrame);
    }

    // Función para copiar al portapapeles
    btnCopiar.addEventListener('click', function() {
        const password = passwordValue.textContent;

        navigator.clipboard.writeText(password).then(function() {
            // Feedback visual
            const originalText = btnCopiar.innerHTML;
            btnCopiar.innerHTML = '<i class="bi bi-check"></i> ¡Copiado!';
            btnCopiar.classList.remove('btn-outline-primary');
            btnCopiar.classList.add('btn-success');

            setTimeout(function() {
                btnCopiar.innerHTML = originalText;
                btnCopiar.classList.remove('btn-success');
                btnCopiar.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(function(err) {
            console.error('Error al copiar:', err);
            alert('Error al copiar al portapapeles. Por favor, copia manualmente.');
        });
    });
});
</script>
@endsection
