<script>
    function showJustificacionesModal() {
        var justificacionesModal = new bootstrap.Modal(document.getElementById('justificacionesModal'));
        justificacionesModal.show();
    }

    // Sistema de cola para peticiones de Puesto de Trabajo Seguro
    const puestoTrabajoQueue = {
        isProcessing: false,
        queue: [],
        
        // Agregar petición a la cola
        add: function(requestData) {
            this.queue.push(requestData);
            console.log(`Petición añadida a la cola. Total en cola: ${this.queue.length}`);
            
            // Mostrar notificación si hay más de una en cola
            if (this.queue.length > 1) {
                Swal.fire({
                    icon: 'info',
                    title: 'Petición en cola',
                    text: `Hay ${this.queue.length - 1} petición(es) en espera. Tu solicitud se procesará automáticamente.`,
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            // Si no está procesando, iniciar el procesamiento
            if (!this.isProcessing) {
                this.processNext();
            }
        },
        
        // Procesar siguiente petición de la cola
        processNext: function() {
            if (this.queue.length === 0) {
                this.isProcessing = false;
                console.log('Cola vacía. Esperando nuevas peticiones.');
                return;
            }
            
            this.isProcessing = true;
            const requestData = this.queue[0]; // Obtener el primero sin removerlo aún
            
            console.log(`Procesando petición: ${requestData.nombre} - ${requestData.email}`);
            
            // Mostrar loader
            Swal.fire({
                title: 'Generando puesto de trabajo seguro...',
                html: `<p>Procesando solicitud para: <strong>${requestData.nombre}</strong></p>
                       <p class="text-muted mt-2">Peticiones en cola: ${this.queue.length - 1}</p>`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Paso 1: Crear registro en base de datos
            const formData = new FormData();
            formData.append('tipo_justificacion', 'puesto_trabajo_seguro');
            formData.append('nombre_justificacion', 'Puesto de trabajo seguro - ' + requestData.nombre);
            formData.append('url_campo', 'https://puesto-trabajo-seguro.com'); // URL dummy requerida
            formData.append('tipo_analisis', 'web'); // Requerido por validación
            formData.append('nombre_campo', requestData.nombre);
            formData.append('email_campo', requestData.email);
            formData.append('empresa_campo', requestData.empresa);
            
            // Crear justificación en base de datos
            fetch('{{ route("justificaciones.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Error al crear justificación');
                }
                
                const justificacionId = data.id;
                console.log('Justificación creada con ID:', justificacionId);
                
                // Paso 2: Enviar a API externa con el ID de la justificación
                return fetch('https://aiapi.hawkins.es/sgpseg/generate-pdf', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nombre: requestData.nombre,
                        email: requestData.email,
                        nombre_empresa: requestData.empresa,
                        justificacion_id: justificacionId,
                        callback_url: window.location.origin + '/justificaciones/receive/' + justificacionId
                    })
                });
            })
            .then(response => response.json())
            .then(data => {
                // Remover de la cola solo si fue exitoso
                this.queue.shift();
                
                if (data.success) {
                    const redirectUrl = '{{ route("justificaciones.index") }}';
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        html: `<p>${data.message || 'Solicitud enviada correctamente'}</p>
                               <p class="text-muted mt-2">Los archivos se procesarán en segundo plano y estarán disponibles en breve.</p>`,
                        timer: this.queue.length > 0 ? 3000 : 5000,
                        showConfirmButton: true,
                        confirmButtonText: this.queue.length > 0 ? `Continuar (${this.queue.length} en cola)` : 'Ver mis justificaciones'
                    }).then((result) => {
                        // Cerrar modal solo si fue la última petición del usuario actual
                        if (requestData.shouldCloseModal) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('justificacionesModal'));
                            if (modal) modal.hide();
                            const form = document.getElementById('justificacionesForm');
                            if (form) form.reset();
                            const camposPuestoSeguro = document.getElementById('campos_puesto_seguro');
                            if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                        }
                        
                        // Si no hay más en cola y el usuario hace clic en el botón, redirigir
                        if (this.queue.length === 0 && result.isConfirmed) {
                            window.location.href = redirectUrl;
                        } else {
                            // Procesar siguiente en cola
                            this.processNext();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Hubo un error al generar el puesto de trabajo seguro',
                        confirmButtonText: this.queue.length > 0 ? 'Continuar con siguiente' : 'OK'
                    }).then(() => {
                        // Procesar siguiente en cola aunque haya fallado
                        this.processNext();
                    });
                }
            })
            .catch(error => {
                // Remover de la cola en caso de error
                this.queue.shift();
                
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Red',
                    text: 'No se pudo conectar con el servidor. Por favor revisa tu conexión.',
                    footer: error.message,
                    confirmButtonText: this.queue.length > 0 ? 'Continuar con siguiente' : 'OK'
                }).then(() => {
                    // Procesar siguiente en cola aunque haya fallado
                    this.processNext();
                });
            });
        },
        
        // Obtener estado de la cola
        getStatus: function() {
            return {
                isProcessing: this.isProcessing,
                queueLength: this.queue.length
            };
        }
    };

    // Manejo del modal de justificaciones
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_justificacion');
        const camposDinamicos = document.getElementById('campos_dinamicos');
        const camposPuestoSeguro = document.getElementById('campos_puesto_seguro');
        const nombreJustificacion = document.getElementById('nombre_justificacion');
        const enviarBtn = document.getElementById('enviarJustificacion');
        const justificacionesForm = document.getElementById('justificacionesForm');

        if (!tipoSelect || !enviarBtn || !justificacionesForm) {
            return; // Salir si no existen los elementos
        }

        // Mostrar/ocultar campos según el tipo seleccionado
        tipoSelect.addEventListener('change', function() {
            const valor = this.value;
            
            if (valor === 'segunda_justificacion_presencia_basica') {
                camposDinamicos.style.display = 'block';
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                nombreJustificacion.value = 'Segunda Justificacion Presencia Basica';
            } else if (valor === 'puesto_trabajo_seguro') {
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'block';
                camposDinamicos.style.display = 'none';
                nombreJustificacion.value = 'Puesto de trabajo seguro';
            } else {
                camposDinamicos.style.display = 'none';
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                nombreJustificacion.value = '';
            }
        });

        // Enviar formulario
        enviarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipoJustificacion = tipoSelect.value;
            
            // Si es Puesto de trabajo seguro, añadir a la cola
            if (tipoJustificacion === 'puesto_trabajo_seguro') {
                const nombre = document.getElementById('nombre_campo').value;
                const email = document.getElementById('email_campo').value;
                const empresa = document.getElementById('empresa_campo').value;
                
                // Validar campos
                if (!nombre || !email || !empresa) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe completar todos los campos'
                    });
                    return;
                }
                
                // Añadir a la cola
                puestoTrabajoQueue.add({
                    nombre: nombre,
                    email: email,
                    empresa: empresa,
                    shouldCloseModal: true
                });
                
                // Limpiar formulario inmediatamente
                justificacionesForm.reset();
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                
                return;
            }
            
            // Flujo normal para otros tipos de justificación
            const formData = new FormData(justificacionesForm);
            
            // Validar que se haya ingresado la URL
            const urlCampo = document.getElementById('url_campo').value;
            
            if (!urlCampo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe ingresar la URL'
                });
                return;
            }
            
            // Mostrar loader
            Swal.fire({
                title: 'Enviando justificación...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('{{ route("justificaciones.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: data.message,
                        timer: 3000
                    }).then(() => {
                        // Cerrar modal y limpiar formulario
                        const modal = bootstrap.Modal.getInstance(document.getElementById('justificacionesModal'));
                        modal.hide();
                        justificacionesForm.reset();
                        camposDinamicos.style.display = 'none';
                        
                        // Redirigir al panel de justificaciones
                        window.location.href = '{{ route("justificaciones.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Hubo un error al enviar la justificación',
                        html: data.errors ? '<pre>' + JSON.stringify(data.errors, null, 2) + '</pre>' : undefined
                    });
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Red',
                    text: 'No se pudo conectar con el servidor. Por favor revisa tu conexión.',
                    footer: error.message
                });
            });
        });
    });
</script>

