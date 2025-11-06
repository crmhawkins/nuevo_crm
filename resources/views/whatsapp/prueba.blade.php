@extends('layouts.app')

@section('titulo', 'Prueba WhatsApp - Envío Simple')

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3><i class="fab fa-whatsapp text-success"></i> Prueba de Envío WhatsApp</h3>
                <p class="text-subtitle text-muted">Enviar mensaje de prueba sin template</p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Prueba WhatsApp</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fab fa-whatsapp"></i> Enviar Mensaje de Prueba
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>⚠️ IMPORTANTE - Ventana de Conversación:</strong>
                            <p class="mb-2">Solo puedes enviar mensajes de texto libre si:</p>
                            <ul class="mb-2">
                                <li><strong>El usuario te escribió primero</strong> en las últimas <strong>24 horas</strong></li>
                                <li>Estás dentro de una conversación activa</li>
                            </ul>
                            <p class="mb-0"><strong>Fuera de esas 24 horas → Solo puedes usar templates aprobados</strong></p>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Información:</strong>
                            <ul class="mb-0">
                                <li>Usa la API oficial de WhatsApp Business de Meta</li>
                                <li>El número debe estar registrado en WhatsApp</li>
                                <li>Acepta formatos: +34600123456, 600123456, 34600123456</li>
                            </ul>
                        </div>

                        <form id="formEnviarWhatsapp">
                            <div class="mb-3">
                                <label for="phoneInput" class="form-label">
                                    Número de Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg"
                                       id="phoneInput"
                                       name="phone"
                                       placeholder="+34 600 12 34 56"
                                       required>
                                <small class="text-muted">
                                    Formatos aceptados: +34600123456, 600123456, 34 600 12 34 56
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="mensajeInput" class="form-label">
                                    Mensaje <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control"
                                          id="mensajeInput"
                                          name="mensaje"
                                          rows="5"
                                          placeholder="Escribe tu mensaje aquí..."
                                          required
                                          maxlength="4096"></textarea>
                                <small class="text-muted">
                                    Máximo 4096 caracteres. <span id="charCount">0</span> / 4096
                                </small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Vista Previa del Mensaje:</label>
                                <div class="p-3 bg-light rounded border">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <i class="fab fa-whatsapp fa-2x text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <strong>WhatsApp Business</strong>
                                            <div class="mt-2 p-2 bg-white rounded shadow-sm" id="previsualizacion">
                                                <em class="text-muted">El mensaje aparecerá aquí...</em>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg" id="btnEnviar">
                                    <i class="fab fa-whatsapp"></i> Enviar Mensaje de Prueba
                                </button>
                            </div>
                        </form>

                        <div id="resultado" class="mt-4"></div>
                    </div>
                </div>

                <!-- Template: Reparaciones -->
                <div class="card mt-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <h5 class="mb-0">
                            <i class="fas fa-tools"></i> Enviar Template "Reparaciones"
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>✅ Templates - Sin Restricción de 24 Horas:</strong>
                            <p class="mb-0">Los templates aprobados se pueden enviar <strong>en cualquier momento</strong>, sin necesidad de que el usuario te escriba primero.</p>
                        </div>

                        <div class="alert alert-info">
                            <strong><i class="fas fa-comment-dots"></i> Formato del Template "reparaciones":</strong>
                            <div class="p-2 mt-2 bg-white rounded">
                                <p class="mb-2">Hola <strong class="text-primary">{{1}}</strong>, hemos recibido una incidecia en el apartamento <strong class="text-primary">*{{2}}*</strong> del edificio <strong class="text-primary">*{{3}}*</strong>. El cliente nos ha comentado la siguiente información:</p>
                                <p class="mb-2 ms-3"><em class="text-primary">{{4}}</em></p>
                                <p class="mb-0">Para contactar con el cliente este es su numero: <strong class="text-primary">*{{5}}*</strong></p>
                            </div>
                        </div>

                        <form id="formEnviarTemplate">
                            <div class="mb-3">
                                <label for="phoneTemplateInput" class="form-label">
                                    Número de Teléfono <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg"
                                       id="phoneTemplateInput"
                                       name="phone"
                                       placeholder="+34 600 12 34 56"
                                       required>
                                <small class="text-muted">
                                    Número del operario/técnico que recibirá la notificación
                                </small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombreInput" class="form-label">
                                        {{1}} Nombre del Operario <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="nombreInput"
                                           name="nombre"
                                           placeholder="Helena"
                                           value="Helena"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="apartamentoInput" class="form-label">
                                        {{2}} Apartamento <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="apartamentoInput"
                                           name="apartamento"
                                           placeholder="Ej: A1, B3, etc."
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tipoIncidenciaInput" class="form-label">
                                        {{4}} Tipo de Incidencia <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="tipoIncidenciaInput" name="tipo_incidencia" required>
                                        <option value="">Selecciona...</option>
                                        <option value="Avería eléctrica">Avería eléctrica</option>
                                        <option value="Fuga de agua">Fuga de agua</option>
                                        <option value="Calefacción no funciona">Calefacción no funciona</option>
                                        <option value="Aire acondicionado averiado">Aire acondicionado averiado</option>
                                        <option value="Problemas con cerraduras">Problemas con cerraduras</option>
                                        <option value="Rotura de cristales">Rotura de cristales</option>
                                        <option value="Problemas de limpieza">Problemas de limpieza</option>
                                        <option value="Otros">Otros</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="numeroClienteInput" class="form-label">
                                        {{5}} Teléfono del Cliente <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="numeroClienteInput"
                                           name="numero_cliente"
                                           placeholder="+34 600 12 34 56"
                                           required>
                                </div>
                            </div>

                            <div class="alert alert-secondary">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> <strong>Nota:</strong> El parámetro {{3}} (Edificio) se enviará automáticamente como <strong>"no identificado"</strong>
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;" id="btnEnviarTemplate">
                                    <i class="fas fa-paper-plane"></i> Enviar Template "Reparaciones"
                                </button>
                            </div>
                        </form>

                        <div id="resultadoTemplate" class="mt-4"></div>
                    </div>
                </div>

                <!-- Configuración del Token -->
                <div class="card mt-4 border-primary">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-cog"></i> Configuración Requerida</h6>
                    </div>
                    <div class="card-body">
                        <h6><strong>Variables de entorno necesarias en tu archivo .env:</strong></h6>

                        <div class="mb-3">
                            <label class="fw-bold">1. TOKEN_WHATSAPP</label>
                            <pre class="bg-dark text-light p-2 rounded mt-1"><code>TOKEN_WHATSAPP=EAAKn6tggu1UBO3auxdovN59gPBGNDEkUfA22gHtjIbLKsTHqzjsGx10rpa1kuczhZC4IobJ6jx5pDfDupllYARDAsI3dhq1EeIVZC1vtnFtNzNbtarckpdsGucUvxYS91ERsFxXsrGLEHLOA8IDOFD9Bpipi9e3Prk0Ym5aP4ZCx5kVKWKnQdIOaITmNyTUZBwZDZD</code></pre>
                            <small class="text-muted">Token de acceso de Meta (debe empezar con <code>EAAx</code> o <code>EAAG</code>)</small>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold">2. BUSINESS_ID</label>
                            <pre class="bg-dark text-light p-2 rounded mt-1"><code>BUSINESS_ID=113437731696576</code></pre>
                            <small class="text-muted">ID de la cuenta de negocio de WhatsApp (WABA) - usado para gestionar templates</small>
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nota:</strong> El <strong>Phone Number ID</strong> (<code>102360642838173</code>) ya está configurado en el código. Si cambias de número de WhatsApp Business, deberás actualizar este ID en los controladores.
                        </div>

                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Importante:</strong> Después de actualizar el .env, ejecuta:
                            <pre class="bg-dark text-light p-2 rounded mt-2 mb-0"><code>php artisan config:clear</code></pre>
                        </div>
                    </div>
                </div>

                <!-- Información técnica -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-code"></i> Información Técnica</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Endpoint:</strong> <code>https://graph.facebook.com/v18.0/102360642838173/messages</code></p>
                        <p><strong>Variables de entorno requeridas:</strong></p>
                        <ul>
                            <li><code>TOKEN_WHATSAPP</code> - Token de acceso de Meta (empieza con EAAx...)</li>
                            <li><code>BUSINESS_ID</code> - ID de la cuenta de negocio (WABA) para templates</li>
                        </ul>
                        <p><strong>Phone Number ID (hardcodeado):</strong> <code>102360642838173</code></p>
                        <p><strong>Método:</strong> <code>POST</code></p>
                        <p><strong>Tipo de mensaje:</strong> <code>text</code> (sin template)</p>
                        <p class="mb-0"><strong>SSL:</strong> <code>VERIFYPEER=false, VERIFYHOST=false</code></p>
                    </div>
                </div>

                <!-- Debug y ayuda -->
                <div class="card mt-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="fas fa-bug"></i> Debug y Solución de Problemas</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Si hay errores, verifica:</strong></p>
                        <ol>
                            <li>La variable <code>TOKEN_WHATSAPP</code> en el archivo <code>.env</code></li>
                            <li>Que el token sea válido y no haya expirado (tokens temporales duran 24h)</li>
                            <li>Que hayas ejecutado <code>php artisan config:clear</code> después de modificar el .env</li>
                            <li>Que el número de destino esté registrado en WhatsApp</li>
                            <li>Los logs detallados en: <code>storage/logs/laravel.log</code></li>
                        </ol>

                        <div class="alert alert-danger mt-3">
                            <strong>⚠️ Mensajes que no llegan (sin error de API):</strong>
                            <p class="mb-2">Si la API responde "exitoso" pero el mensaje NO llega:</p>
                            <ul class="mb-3">
                                <li><strong>Ventana de 24h cerrada:</strong> El destinatario no te escribió recientemente → Usa templates</li>
                                <li><strong>Cuenta en modo desarrollo:</strong> Solo puedes enviar a números de prueba registrados → Agrega el número en Meta for Developers > WhatsApp > API Setup > Phone Numbers > Add</li>
                                <li><strong>Número no verificado:</strong> El destinatario debe tener WhatsApp activo en ese número</li>
                            </ul>

                            <strong>Errores comunes de la API:</strong>
                            <ul class="mb-0">
                                <li><strong>Error 190:</strong> Token inválido o expirado → Regenera en <a href="https://developers.facebook.com/apps/" target="_blank">Meta for Developers</a></li>
                                <li><strong>Error 100 (subcode 33):</strong> Phone Number ID incorrecto → Verifica que sea <code>102360642838173</code></li>
                                <li><strong>Error 131047:</strong> Número no verificado</li>
                                <li><strong>Error 131026:</strong> Fuera de ventana de 24h → Usa templates aprobados</li>
                            </ul>
                        </div>

                        <p class="mb-0">
                            <strong>Para ver logs detallados:</strong><br>
                            <code class="text-dark">tail -f storage/logs/laravel.log | grep "enviarMensajePrueba"</code>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Contador de caracteres
    const mensajeInput = document.getElementById('mensajeInput');
    const charCount = document.getElementById('charCount');
    const previsualizacion = document.getElementById('previsualizacion');

    mensajeInput.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;

        // Actualizar previsualización
        if (this.value.trim() === '') {
            previsualizacion.innerHTML = '<em class="text-muted">El mensaje aparecerá aquí...</em>';
        } else {
            previsualizacion.textContent = this.value;
        }

        // Cambiar color si se acerca al límite
        if (count > 3800) {
            charCount.className = 'text-danger fw-bold';
        } else if (count > 3500) {
            charCount.className = 'text-warning fw-bold';
        } else {
            charCount.className = '';
        }
    });

    // Enviar formulario
    document.getElementById('formEnviarWhatsapp').addEventListener('submit', function(e) {
        e.preventDefault();

        const phone = document.getElementById('phoneInput').value.trim();
        const mensaje = document.getElementById('mensajeInput').value.trim();

        if (!phone || !mensaje) {
            Swal.fire({
                icon: 'error',
                title: 'Campos vacíos',
                text: 'Por favor completa todos los campos'
            });
            return;
        }

        const btnEnviar = document.getElementById('btnEnviar');
        const originalHtml = btnEnviar.innerHTML;
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando mensaje...';

        // Enviar petición
        fetch('{{ route("whatsapp.enviarPrueba") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                mensaje: mensaje
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json().then(data => ({
                status: response.status,
                ok: response.ok,
                data: data
            }));
        })
        .then(({status, ok, data}) => {
            console.log('Respuesta completa:', data);
            console.log('Status:', status);
            console.log('OK:', ok);

            const resultadoDiv = document.getElementById('resultado');

            if (data.success) {
                const phoneUsado = data.phone_usado || phone;
                const messageId = data.data?.messages?.[0]?.id || 'N/A';

                resultadoDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-check-circle"></i> ¡Mensaje enviado exitosamente!
                        </h5>
                        <hr>
                        <p class="mb-0"><strong>Número usado:</strong> ${phoneUsado}</p>
                        <p class="mb-0"><strong>Estado:</strong> Enviado a WhatsApp API</p>
                        <p class="mb-0"><strong>Message ID:</strong> <code>${messageId}</code></p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <strong>Respuesta de la API:</strong>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"><code>${JSON.stringify(data.data, null, 2)}</code></pre>
                        </div>
                    </div>
                `;

                Swal.fire({
                    icon: 'success',
                    title: '¡Mensaje Enviado!',
                    text: 'El mensaje se envió correctamente a WhatsApp',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                // Extraer detalles del error
                let errorDetalles = '';
                if (typeof data.error === 'string') {
                    errorDetalles = data.error;
                } else if (typeof data.error === 'object') {
                    errorDetalles = JSON.stringify(data.error, null, 2);
                }

                // Verificar si hay error_details
                let errorExtendido = '';
                if (data.error_details) {
                    errorExtendido = JSON.stringify(data.error_details, null, 2);
                }

                resultadoDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Error al enviar mensaje
                        </h5>
                        <hr>
                        <p class="mb-0"><strong>Error:</strong> ${data.message || 'Error desconocido'}</p>
                        ${errorDetalles ? `<p class="mb-0"><strong>Detalles:</strong></p><pre class="mb-0">${errorDetalles}</pre>` : ''}
                        ${data.phone_usado ? `<p class="mb-0 mt-2"><strong>Número usado:</strong> ${data.phone_usado}</p>` : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    ${errorExtendido ? `
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <strong>Respuesta completa del servidor:</strong>
                            </div>
                            <div class="card-body">
                                <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"><code>${errorExtendido}</code></pre>
                            </div>
                        </div>
                    ` : ''}
                `;

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `<p>${data.message || 'No se pudo enviar el mensaje'}</p>
                           ${errorDetalles ? `<pre class="text-start small mt-2">${errorDetalles}</pre>` : ''}`,
                    width: 600
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);

            document.getElementById('resultado').innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Error de conexión
                    </h5>
                    <p class="mb-0">No se pudo conectar con el servidor: ${error.message}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                footer: error.message
            });
        })
        .finally(() => {
            btnEnviar.disabled = false;
            btnEnviar.innerHTML = originalHtml;
        });
    });

    // ==================== FORMULARIO DE TEMPLATE ====================
    document.getElementById('formEnviarTemplate').addEventListener('submit', function(e) {
        e.preventDefault();

        const phone = document.getElementById('phoneTemplateInput').value.trim();
        const nombre = document.getElementById('nombreInput').value.trim();
        const apartamento = document.getElementById('apartamentoInput').value.trim();
        const tipoIncidencia = document.getElementById('tipoIncidenciaInput').value.trim();
        const numeroCliente = document.getElementById('numeroClienteInput').value.trim();

        if (!phone || !nombre || !apartamento || !tipoIncidencia || !numeroCliente) {
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor completa todos los campos obligatorios'
            });
            return;
        }

        const btnEnviarTemplate = document.getElementById('btnEnviarTemplate');
        const originalHtml = btnEnviarTemplate.innerHTML;
        btnEnviarTemplate.disabled = true;
        btnEnviarTemplate.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando template...';

        // Enviar petición
        fetch('{{ route("whatsapp.enviarTemplate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                phone: phone,
                nombre: nombre,
                apartamento: apartamento,
                tipo_incidencia: tipoIncidencia,
                numero_cliente: numeroCliente
            })
        })
        .then(response => {
            return response.json().then(data => ({
                status: response.status,
                ok: response.ok,
                data: data
            }));
        })
        .then(({status, ok, data}) => {
            console.log('Respuesta completa:', data);

            const resultadoDiv = document.getElementById('resultadoTemplate');

            if (data.success) {
                const phoneUsado = data.phone_usado || phone;
                const messageId = data.data?.messages?.[0]?.id || 'N/A';

                resultadoDiv.innerHTML = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-check-circle"></i> ¡Template enviado exitosamente!
                        </h5>
                        <hr>
                        <p class="mb-0"><strong>Número usado:</strong> ${phoneUsado}</p>
                        <p class="mb-0"><strong>Operario:</strong> ${nombre}</p>
                        <p class="mb-0"><strong>Apartamento:</strong> ${apartamento}</p>
                        <p class="mb-0"><strong>Incidencia:</strong> ${tipoIncidencia}</p>
                        <p class="mb-0"><strong>Tel. Cliente:</strong> ${numeroCliente}</p>
                        <p class="mb-0"><strong>Message ID:</strong> <code>${messageId}</code></p>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <strong>Respuesta de la API:</strong>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0" style="max-height: 300px; overflow-y: auto;"><code>${JSON.stringify(data.data, null, 2)}</code></pre>
                        </div>
                    </div>
                `;

                Swal.fire({
                    icon: 'success',
                    title: '¡Template Enviado!',
                    html: `<p>El template "reparaciones" se envió correctamente a <strong>${nombre}</strong></p>`,
                    timer: 3000,
                    showConfirmButton: false
                });

                // Limpiar formulario
                document.getElementById('formEnviarTemplate').reset();
                document.getElementById('nombreInput').value = 'Helena';
            } else {
                let errorDetalles = '';
                if (typeof data.error === 'string') {
                    errorDetalles = data.error;
                } else if (typeof data.error === 'object') {
                    errorDetalles = JSON.stringify(data.error, null, 2);
                }

                resultadoDiv.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-exclamation-triangle"></i> Error al enviar template
                        </h5>
                        <hr>
                        <p class="mb-0"><strong>Error:</strong> ${data.message || 'Error desconocido'}</p>
                        ${errorDetalles ? `<p class="mb-0 mt-2"><strong>Detalles:</strong></p><pre class="mb-0 mt-1">${errorDetalles}</pre>` : ''}
                        ${data.phone_usado ? `<p class="mb-0 mt-2"><strong>Número usado:</strong> ${data.phone_usado}</p>` : ''}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `<p>${data.message || 'No se pudo enviar el template'}</p>
                           ${errorDetalles ? `<pre class="text-start small mt-2">${errorDetalles}</pre>` : ''}`,
                    width: 600
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);

            document.getElementById('resultadoTemplate').innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-exclamation-triangle"></i> Error de conexión
                    </h5>
                    <p class="mb-0">No se pudo conectar con el servidor: ${error.message}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor',
                footer: error.message
            });
        })
        .finally(() => {
            btnEnviarTemplate.disabled = false;
            btnEnviarTemplate.innerHTML = originalHtml;
        });
    });
</script>
@endsection

