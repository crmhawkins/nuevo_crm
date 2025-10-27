<script>
    function showJustificacionesModal() {
        var justificacionesModal = new bootstrap.Modal(document.getElementById('justificacionesModal'));
        justificacionesModal.show();
    }

    // Manejo del modal de justificaciones
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo_justificacion');
        const camposDinamicos = document.getElementById('campos_dinamicos');
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
                nombreJustificacion.value = 'Segunda Justificacion Presencia Basica';
            } else {
                camposDinamicos.style.display = 'none';
                nombreJustificacion.value = '';
            }
        });

        // Enviar formulario
        enviarBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
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

