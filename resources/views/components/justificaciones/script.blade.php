<script>
    function showJustificacionesModal() {
        var justificacionesModal = new bootstrap.Modal(document.getElementById('justificacionesModal'));
        justificacionesModal.show();
    }

    // Manejo del modal de justificaciones
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_justificacion');
        const camposDinamicos = document.getElementById('campos_dinamicos');
        const camposPuestoSeguro = document.getElementById('campos_puesto_seguro');
        const camposPresenciaAvanzada = document.getElementById('campos_presencia_avanzada');
        const camposCrmErpFactura = document.getElementById('campos_crm_erp_factura');
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
                if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'none';
                if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'none';
                nombreJustificacion.value = 'Segunda Justificacion Presencia Basica';
            } else if (valor === 'puesto_trabajo_seguro') {
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'block';
                camposDinamicos.style.display = 'none';
                if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'none';
                if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'none';
                nombreJustificacion.value = 'Puesto de trabajo seguro';
            } else if (valor === 'presencia_avanzada_2') {
                if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'block';
                camposDinamicos.style.display = 'none';
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'none';
                nombreJustificacion.value = 'Presencia Avanzada (2ª)';
            } else if (valor === 'crm_erp_factura') {
                if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'block';
                camposDinamicos.style.display = 'none';
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'none';
                nombreJustificacion.value = 'CRM/ERP/Factura';
            } else {
                camposDinamicos.style.display = 'none';
                if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'none';
                if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'none';
                nombreJustificacion.value = '';
            }
        });

        // Enviar formulario
        enviarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const tipoJustificacion = tipoSelect.value;
            const formData = new FormData(justificacionesForm);
            
            // Validación según tipo
            if (tipoJustificacion === 'puesto_trabajo_seguro') {
                const nombre = document.getElementById('nombre_campo').value;
                const email = document.getElementById('email_campo').value;
                const empresa = document.getElementById('empresa_campo').value;
                
                if (!nombre || !email || !empresa) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe completar todos los campos'
                    });
                    return;
                }
            } else if (tipoJustificacion === 'segunda_justificacion_presencia_basica') {
                const urlCampo = document.getElementById('url_campo').value;
                
                if (!urlCampo) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe ingresar la URL'
                    });
                    return;
                }
            } else if (tipoJustificacion === 'presencia_avanzada_2') {
                const kd = document.getElementById('kd_campo').value;
                const fecha = document.getElementById('fecha_campo').value;
                const nombre = document.getElementById('nombre_presencia_campo').value;
                const url = document.getElementById('url_presencia_campo').value;
                const keyword = document.getElementById('keyword_campo').value;
                const phone = document.getElementById('phone_campo').value;
                const email = document.getElementById('email_presencia_campo').value;
                const address = document.getElementById('address_campo').value;
                const descripcion = document.getElementById('descripcion_campo').value;
                
                if (!kd || !fecha || !nombre || !url || !keyword || !phone || !email || !address || !descripcion) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe completar todos los campos obligatorios'
                    });
                    return;
                }
            } else if (tipoJustificacion === 'crm_erp_factura') {
                const tipoSistema = document.getElementById('tipo_sistema_campo').value;
                const urlCrm = document.getElementById('url_crm_campo').value;
                
                if (!tipoSistema || !urlCrm) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe seleccionar el tipo de sistema e ingresar la URL'
                    });
                    return;
                }
            }
            
            // Mostrar loader
            Swal.fire({
                title: 'Enviando solicitud...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Enviar formulario al servidor
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
                        html: `<p>${data.message}</p>
                               <p class="text-muted mt-2">Tu solicitud se está procesando en segundo plano.</p>`,
                        showConfirmButton: true,
                        confirmButtonText: 'Ver mis justificaciones'
                    }).then((result) => {
                        // Cerrar modal y limpiar formulario
                        const modal = bootstrap.Modal.getInstance(document.getElementById('justificacionesModal'));
                        modal.hide();
                        justificacionesForm.reset();
                        camposDinamicos.style.display = 'none';
                        if (camposPuestoSeguro) camposPuestoSeguro.style.display = 'none';
                        if (camposPresenciaAvanzada) camposPresenciaAvanzada.style.display = 'none';
                        if (camposCrmErpFactura) camposCrmErpFactura.style.display = 'none';
                        
                        // Redirigir a justificaciones
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("justificaciones.index") }}';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Hubo un error al enviar la solicitud',
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
