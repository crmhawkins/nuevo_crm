<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('titulo') - {{ config('app.name', 'Laravel') }}</title>

    <style>
            /* Estilos específicos para la tabla */
        .table-responsive {
            overflow-x: auto; /* Asegura un desplazamiento suave en pantallas pequeñas */
            overflow-y: hidden; /* Asegura un desplazamiento suave en pantallas pequeñas */
        }

        .header-table th {
            vertical-align: bottom; /* Alinea el texto de los encabezados en la parte inferior */
            white-space: nowrap; /* Evita que los encabezados se rompan en líneas */
            font-size: 0.85rem; /* Ajusta el tamaño del texto para los encabezados */
        }



        .long-text {
            max-width: 250px; /* Máximo ancho para el texto largo */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        th {
        white-space: nowrap !important;
        }
        .titulo_filtros {
        white-space: nowrap !important;
        }
        /* Cambia el estilo del select */
        .cliente .choices {
            width: 50px !important;
            margin-bottom: 0 !important;
            font-size: 0.75rem;
            height: fit-content;
        }
        .cliente .choices__inner {
            padding-bottom: 0 !important;
            display: block !important;
            vertical-align: top !important;
            width: 100% !important;
            background-color: transparent !important;
            padding: 0.1rem 0.1rem 0.1rem 0.2rem !important;
            border: 1px solid rgb(175, 175, 175) !important;
            border-radius: 2.5px !important;
            font-size: 0.75rem !important;
            min-height: 0px !important;
            overflow: hidden !important;
            box-shadow: none !important;
        }

        /* Estilo del dropdown */
        .cliente .choices__list {
            width: 200px; /* Cambia el ancho del dropdown */
            max-width: 400px; /* Ajusta el ancho máximo como desees */
        }
        .choices__list.choices__list--single {
            padding: 0.1rem 0.1rem 0.1rem 0.2rem !important;
        }
        .cliente .choices__item.choices__item--choice.choices__item--selectable {
            color: black !important;
            /* Puedes agregar más estilos aquí */
        }

    </style>
    <!-- Fonts y estilos -->
    <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
    <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/iconly/bold.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">

    <!-- CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('css')
    <link rel="stylesheet" href="{{ asset('build/assets/app-d2e38ed8.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="{{ asset('build/assets/app-bf7e6802.js') }}"></script>
    @laravelViewsStyles
</head>
<body class="" style="overflow-x: hidden">
    <div id="app">
        <div id="loadingOverlay" style="display: block; position: fixed; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.5); z-index: 50000; cursor: pointer;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-black" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <div class="css-96uzu9"></div>

        @include('layouts.sidebar')

        <main id="main">
            @include('layouts.topBar')
            <div class="contenedor p-4">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>


    @yield('scripts')
    @laravelViewsScripts
    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
    <script>

        document.addEventListener("DOMContentLoaded", function() {
            var loader = document.getElementById('loadingOverlay');
            if (loader) {
                    loader.style.display = 'none';
            }
        });

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

        function seleccionarCliente(itemId) {
            // Guardar el ID del elemento seleccionado
            $('#clienteIdSeleccionado').val(itemId);
        }

        function guardarCliente() {
            $('#clienteModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            let itemId = $('#clienteIdSeleccionado').val();
            let clienteId = $('#clienteSelect').val();
            let clienteNombre = $('#clienteSelect option:selected').text();
            handleDataUpdate(itemId, clienteId, 'cliente_id');
            $('#cliente-nombre-' + itemId).val(clienteNombre);

        }
    </script>
</body>
</html>
