    $(document).ready(function() {
        // Eliminar barra lateral
        $("#sidebar").remove();
        // Eliminar margen izquierdo
        $("#main").css("margin-left", "0px");
        // Sincronizar filtros con el formulario de exportación
        $('#formFiltros').on('change', 'input, select', function () {
            const exportForm = $('#exportToExcelForm');
            exportForm.find('[name="selectedCliente"]').val($('select[name="selectedCliente"]').val());
            exportForm.find('[name="selectedEstado"]').val($('select[name="selectedEstado"]').val());
            exportForm.find('[name="selectedGestor"]').val($('select[name="selectedGestor"]').val());
            exportForm.find('[name="selectedServicio"]').val($('select[name="selectedServicio"]').val());
            exportForm.find('[name="selectedEstadoFactura"]').val($('select[name="selectedEstadoFactura"]').val());
            exportForm.find('[name="selectedComerciales"]').val($('select[name="selectedComerciales"]').val());
            exportForm.find('[name="selectedSegmento"]').val($('select[name="selectedSegmento"]').val());
            exportForm.find('[name="selectedDateField"]').val($('select[name="selectedDateField"]').val());
            exportForm.find('[name="date_from"]').val($('input[name="date_from"]').val());
            exportForm.find('[name="date_to"]').val($('input[name="date_to"]').val());
            exportForm.find('[name="buscar"]').val($('input[name="buscar"]').val());
            exportForm.find('[name="sortColumn"]').val($('#sortColumn').val());
            exportForm.find('[name="sortDirection"]').val($('#sortDirection').val());
        });

        // Detectar cambios en inputs, selects y textareas dentro de la tabla
        $('.table').on('change', 'input, select, textarea', function() {
            var id = $(this).data('id');  // Asegúrate de que cada fila tenga un atributo data-id
            var key = $(this).attr('name');
            var value = $(this).is(':checkbox') ? ($(this).is(':checked') ? 1 : 0) : $(this).val();
            handleDataUpdate(id, value, key);
        });

        $('#formFiltros').on('change', 'input, select', function (e) {
            const selectedDateField = $('#selectedDateField').val();
            const dateFrom = $('#date_from').val();
            const dateTo = $('#date_to').val();

            // Verificar si el campo cambiado es uno de los tres del filtro por fecha
            if ($(this).is('#selectedDateField, #date_from, #date_to')) {
                // Comprobar si los tres campos tienen valores
                if (selectedDateField && dateFrom && dateTo) {
                    $('#formFiltros').submit(); // Enviar el formulario si están completos
                } else {
                    e.preventDefault(); // Prevenir el envío si falta alguno
                }
            } else {
                $('#formFiltros').submit(); // Enviar el formulario para otros campos
            }
        });

        $('.sort').on('click', function (e) {
            e.preventDefault();
            // Obtener la columna seleccionada del atributo data-column
            var column = $(this).data('column');
            console.log(column);
            // Obtener el valor actual del formulario
            var currentColumn = $('#sortColumn').val();
            var currentDirection = $('#sortDirection').val();
            // Si la columna seleccionada es la misma, cambiar la dirección
            if (column === currentColumn) {
                var newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                $('#sortDirection').val(newDirection);
            } else {
                // Si es una columna diferente, establecer 'asc' por defecto
                $('#sortDirection').val('desc');
            }

            // Actualizar el valor de la columna seleccionada
            $('#sortColumn').val(column);
            console.log(column);

            // Enviar el formulario
            $('#formFiltros').submit();
        });

        function redirectToWhatsapp(id) {
            window.open(`/kit-digital/whatsapp/${id}`, '_blank');
        }

        function handleDataUpdate(id, value, key) {
            $.ajax({
                type: "POST",
                url: "{{ route('kitDigital.updateData') }}", // Asegúrate que esta es la ruta correcta
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    id: id,
                    value: value,
                    key: key
                },
                success: function(data) {
                    if (data.icon === 'success') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer);
                                toast.addEventListener('mouseleave', Swal.resumeTimer);
                            }
                        });

                        Toast.fire({
                            icon: data.icon, // Corregido: Se agregó una coma al final
                            title: data.mensaje // Corregido: Se agregó una coma al final
                        });
                    }else{
                        Swal.fire({
                            icon: data.icon,
                            title: data.mensaje,
                            confirmButtonText: 'Ok',
                            backdrop: true // Agrega un fondo oscurecido
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'Ha ocurrido un error. Por favor, intenta de nuevo.',
                        confirmButtonText: 'Ok',
                        backdrop: true // Agrega un fondo oscurecido
                    });
                }
            });
        }
    });
