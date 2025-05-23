@extends('layouts.appWhatsapp')

@section('titulo', 'Plantillas')

<style>
    #botonesPreview button {
        border-radius: 18px;
        padding: 6px 12px;
        font-size: 13px;
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .sortable {
        cursor: pointer;
    }

    .sort-icon {
        font-size: 12px;
        margin-left: 5px;
    }

    .css-96uzu9 {
        z-index: -1 !important;
    }

    .whatsapp-preview {
        background-color: #e5ddd5;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        min-height: 160px;
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .chat-bubble {
        background-color: #dcf8c6;
        border-radius: 7.5px;
        padding: 10px 14px;
        max-width: 100%;
        font-size: 14px;
        line-height: 1.4;
        white-space: normal;
        color: #222;
        word-break: break-word;
        display: inline-block;
    }

    .chat-bubble img {
        display: block;
        margin-top: 10px;
        max-width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .whatsapp-button {
        border-top: 1px solid #d0d0d0;
        font-size: 14px;
        cursor: default;
        transition: background 0.2s ease;
    }

    .whatsapp-button:hover {
        background-color: #f0f0f0;
    }

    #botonesPreview {
        margin-top: 10px;
        border-top: 1px solid #ccc;
    }
</style>

@section('content')
    <!-- Contenido principal -->
    <div class="col-md-10 p-4 bg-white rounded">
        <div class="d-flex justify-content-between mb-4 ">
            <h2 class="mb-0">Plantillas</h2>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTemplateModal">
                Nueva plantilla
            </button>
        </div>

        <div class="table-responsive">
            <table id="templatesTable" class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">ID <span class="sort-icon">↕</span></th>
                        <th class="sortable" data-column="name">Nombre<span class="sort-icon">↕</span></th>
                        <th class="sortable" data-column="mensaje">Mensaje<span class="sort-icon">↕</span></th>
                        <th>Botones</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $template)
                        <tr id="template-{{ $template->id }}">
                            <td>{{ $template->id }}</td>
                            <td>{{ $template->name }}</td>
                            <td>{{ $template->mensaje }}</td>
                            <td>
                                @if($template->botones)
                                    <button class="btn btn-sm btn-info view-buttons" data-bs-toggle="modal" data-bs-target="#buttonsModal" data-buttons='@json($template->botones)'>
                                        <i class="fas fa-list"></i> Ver botones
                                    </button>
                                @else
                                    <span class="text-muted">Sin botones</span>
                                @endif
                            </td>
                            @php
                                switch ($template->status) {
                                    case 0:
                                        $estado = '<span class="badge bg-danger">Pendiente</span>';
                                        break;
                                    case 1:
                                        $estado = '<span class="badge bg-success">Aceptado</span>';
                                        break;
                                    case 2:
                                        $estado = '<span class="badge bg-warning">Rechazado</span>';
                                        break;
                                    case 3:
                                        $estado = '<span class="badge bg-info">Desconocido</span>';
                                }
                            @endphp
                            <td>{!! $estado !!}</td>
                            <td>
                                <button onclick="deleteTemplate({{ $template->id }})" class="btn btn-sm btn-danger delete-template" data-id="{{ $template->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para mostrar botones -->
    <div class="modal fade" id="buttonsModal" tabindex="-1" aria-labelledby="buttonsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buttonsModalLabel">Botones de la plantilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="buttonsList" class="list-group">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Texto</th>
                                <th>URL</th>
                            </tr>
                        </thead>
                        <tbody id="buttonsTableBody">
                            @foreach ($template->botones as $boton)
                                <tr>
                                    <td>{{ $boton['tipo'] }}</td>
                                    <td>{{ $boton['texto'] }}</td>
                                    <td>{{ $boton['url'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear plantilla -->
    <div class="modal fade" id="newTemplateModal" tabindex="-1" aria-labelledby="newTemplateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newTemplateModalLabel">Nueva Plantilla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="templateForm">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la plantilla</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="incluirContenido">
                                <label class="form-check-label" for="incluirContenido">
                                    Añadir contenido
                                </label>
                            </div>
                            <div id="contenidoContainer" style="display: none;">
                                <label for="tipoContenidoLabel" class="form-label">Tipo de contenido</label>
                                <select class="form-select mb-2" id="tipoContenido">
                                    <option value="imagen">Imagen</option>
                                    <option value="video">Video</option>
                                    <option value="documento">Documento</option>
                                    <option value="ubicacion">Ubicación</option>
                                </select>

                                <input type="file" class="form-control" id="archivoContenido" style="display: none;"
                                    accept="image/*,video/*,.pdf,.doc,.docx,.xls,.xlsx">
                                <div id="previewContenido" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 200px;"
                                        alt="Preview">
                                    <video id="previewVideo" class="w-100 rounded" controls
                                        style="max-height: 200px; display: none;"></video>
                                    <div id="previewDoc" class="alert alert-info py-2 px-3 d-none">Documento listo para
                                        enviar</div>
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="incluirBotones">
                                <label class="form-check-label" for="incluirBotones">
                                    Incluir botones
                                </label>
                            </div>
                            <div id="botonesContainer" style="display: none;">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-primary" id="addButton">
                                        <i class="fas fa-plus"></i> Añadir botón
                                    </button>
                                </div>
                                <div id="botonesList">
                                    <!-- Aquí se añadirán los inputs de botones dinámicamente -->
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mensaje</label>
                            <div class="row">
                                <!-- Campo de edición -->
                                <div class="col-md-6">
                                    <textarea class="form-control" id="mensaje" name="mensaje" rows="8" placeholder="Escribe el mensaje..."
                                        required></textarea>
                                </div>

                                <!-- Previsualización tipo WhatsApp -->
                                <div class="col-md-6">
                                    <div class="whatsapp-preview">
                                        <div class="chat-bubble" id="mensajePreview">
                                            <div id="imagenPreview" class="imagen-preview mb-2" style="display: none;">
                                                <img id="previewImg" src="" alt="Preview">
                                            </div>
                                            <div id="mensajeContenido">Tu mensaje aparecerá aquí...</div>
                                            <div id="botonesPreview" class="mt-2" style="display: none;">
                                                <!-- Aquí se mostrarán los botones en la previsualización -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveTemplate">Guardar plantilla</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css"
        rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js">
    </script>

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        function actualizarPreviewBotones() {
            let botonesHTML = '';
            $('#botonesList .input-group').each(function() {
                const texto = $(this).find('.boton-input').val().trim();
                const tipo = $(this).find('.boton-tipo').val();

                if (texto !== '') {
                    let icono = '';
                    switch (tipo) {
                        case 'link':
                            icono = '<i class="fas fa-link me-2 text-primary"></i>';
                            break;
                        case 'call':
                            icono = '<i class="fas fa-phone-alt me-2 text-success"></i>';
                            break;
                        default:
                            icono = '<i class="fas fa-ellipsis-h me-2 text-secondary"></i>';
                            break;
                    }

                    botonesHTML += `
                <div class="whatsapp-button d-flex align-items-center py-2 px-2">
                    ${icono}
                    <span class="fw-semibold text-primary">${texto}</span>
                </div>
            `;
                }
            });

            if (botonesHTML !== '') {
                $('#botonesPreview').html(botonesHTML).show();
            } else {
                $('#botonesPreview').empty().hide();
            }
        }

        $(document).ready(function() {
            $('#mensajeContenido').html('Tu mensaje aparecerá aquí...');
            // Inicializar DataTables
            $('#templatesTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });

            $('#newTemplateModal').on('shown.bs.modal', function() {
                $('#mensaje').summernote({
                    placeholder: 'Escribe tu mensaje aquí...',
                    height: 200,
                    toolbar: [
                        ['style', ['bold', 'italic', 'underline']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link']],
                        ['view', ['codeview']]
                    ],
                    callbacks: {
                        onChange: function(contents, $editable) {
                            let finalContent = contents || 'Tu mensaje aparecerá aquí...';
                            if ($('#tipoContenido').val() === 'ubicacion') {
                                if (!finalContent.includes('\{\{ ubicacion \}\}')) {
                                    finalContent = '<div class="mb-2 text-muted">\{\{ ubicacion \}\}</div>' + finalContent;
                                }
                            }
                            $('#mensajeContenido').html(finalContent);
                        }
                    }
                });
            });

            $('#incluirImagen').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#imagenContainer').slideDown();
                } else {
                    $('#imagenContainer').slideUp();
                    $('#imagenPreview').hide();
                    $('#previewImg').attr('src', '');
                    $('#imagen').val('');
                }
            });

            $('#imagen').on('change', function(e) {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewImg').attr('src', e.target.result);
                        $('#imagenPreview').show();
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagenPreview').hide();
                    $('#previewImg').attr('src', '');
                }
            });




            // Limpiar el editor cuando el modal se cierra
            $('#newTemplateModal').on('hidden.bs.modal', function() {
                $('#mensaje').summernote('destroy');
                $('#templateForm')[0].reset();
            });

            // Guardar plantilla
            $('#saveTemplate').click(function() {
                var nombre = $('#nombre').val();
                var mensaje = $('#mensaje').summernote('code');
                var tipoContenido = $('#tipoContenido').val();
                var contenido = $('#archivoContenido')[0].files[0];
                var botones = [];

                // Recopilar datos de los botones
                $('#botonesList .input-group').each(function() {
                    var boton = {
                        texto: $(this).find('.boton-input').val(),
                        url: $(this).find('.boton-input').val(),
                        tipo: $(this).find('.boton-tipo').val()
                    };
                    if (boton.texto && boton.tipo) {
                        botones.push(boton);
                    }
                });

                // Crear FormData para enviar archivos
                var formData = new FormData();
                formData.append('nombre', nombre);
                formData.append('mensaje', mensaje);
                formData.append('tipoContenido', tipoContenido);
                formData.append('botones', JSON.stringify(botones));
                formData.append('_token', '{{ csrf_token() }}');

                if (contenido) {
                    formData.append('contenido', contenido);
                }

                $.ajax({
                    url: '{{ route('plataforma.createTemplate') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Plantilla guardada exitosamente');
                        } else {
                            toastr.error('Error al guardar la plantilla: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        alert('Error al guardar la plantilla');
                    }
                });
            });

            // Editar plantilla
            $('.edit-template').click(function() {
                var templateId = $(this).data('id');
                // Aquí puedes implementar la lógica para cargar y editar la plantilla
            });

            // Eliminar plantilla
            $('.delete-template').click(function() {
                var templateId = $(this).data('id');
                if (confirm('¿Estás seguro de que deseas eliminar esta plantilla?')) {
                    // Aquí puedes implementar la lógica para eliminar la plantilla
                }
            });
        });

        $(document).ready(function() {

            // Mostrar/ocultar contenedor de botones
            $('#incluirBotones').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#botonesContainer').slideDown();
                    $('#botonesPreview').show();
                } else {
                    $('#botonesContainer').slideUp();
                    $('#botonesList').empty();
                    $('#botonesPreview').empty().hide();
                }
            });

            $('#addButton').on('click', function() {
                const id = Date.now();
                const botonInput = `
        <div class="input-group mb-2 align-items-center" data-id="${id}">
            <select class="form-select boton-tipo me-2" style="max-width: 110px;">
                <option value="link">Link</option>
                <option value="call">Llamar</option>
                <option value="default">Otro</option>
            </select>
            <input type="text" class="form-control boton-input me-2" placeholder="Texto del botón">
            <button class="btn btn-danger remove-button" type="button"><i class="fas fa-trash"></i></button>
        </div>
    `;
                $('#botonesList').append(botonInput);
                actualizarPreviewBotones(); // 👈 actualiza la vista previa
            });


            // Eliminar botón
            $('#botonesList').on('click', '.remove-button', function() {
                $(this).closest('.input-group').remove();
                actualizarPreviewBotones();
            });


            $('#mensaje').on('input', function() {
                const content = $(this).val().trim();
                $('#mensajeContenido').html(contents || 'Tu mensaje aparecerá aquí...');
            });
        });

        // Mostrar u ocultar el input de imagen según el checkbox
        $('#incluirImagen').on('change', function() {
            if ($(this).is(':checked')) {
                $('#imagenContainer').slideDown();
            } else {
                $('#imagenContainer').slideUp();
                $('#imagenPreview').hide();
                $('#previewImg').attr('src', '');
                $('#imagen').val(''); // Limpia el input
            }
        });

        // Mostrar imagen en vista previa
        $('#imagen').on('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagenPreview').show();

                }
                reader.readAsDataURL(file);
            } else {
                $('#imagenPreview').hide();
                $('#previewImg').attr('src', '');
            }
        });

        $('#botonesList').on('input', '.boton-input', actualizarPreviewBotones);
        $('#botonesList').on('change', '.boton-tipo', actualizarPreviewBotones);

        // Mostrar/ocultar contenido según checkbox
        $('#incluirContenido').on('change', function() {
            if ($(this).is(':checked')) {
                $('#contenidoContainer').slideDown();
            } else {
                $('#contenidoContainer').slideUp();
                $('#archivoContenido').val('');
                $('#previewContenido').hide();
                $('#previewImg').hide().attr('src', '');
                $('#previewVideo').hide().attr('src', '');
                $('#previewDoc').addClass('d-none');
                $('#mensajeContenido').find('#ubicacionTexto').remove();
            }
        });

        // Cambiar tipo de input file según el tipo de contenido
        $('#tipoContenido').on('change', function() {
            const tipo = $(this).val();
            $('#archivoContenido').hide().val('');
            $('#previewContenido').hide();
            $('#previewImg').hide().attr('src', '');
            $('#previewVideo').hide().attr('src', '');
            $('#previewDoc').addClass('d-none');
            $('#mensajeContenido').find('#ubicacionTexto').remove();

            if (tipo === 'ubicacion') {
                $('#archivoContenido').hide();
                if (!$('#mensajeContenido').find('#ubicacionTexto').length) {
                    $('#mensajeContenido').prepend(
                        '<div id="ubicacionTexto" class="mb-2 text-muted">\{\{ ubicacion \}\}</div>');
                }
            } else {
                $('#archivoContenido').show();

                let accept = '';
                if (tipo === 'imagen') accept = 'image/*';
                if (tipo === 'video') accept = 'video/*';
                if (tipo === 'documento') accept = '.pdf,.doc,.docx,.xls,.xlsx';
                $('#archivoContenido').attr('accept', accept);
            }
        });

        // Preview de archivo subido
        $('#archivoContenido').on('change', function(e) {
            const file = e.target.files[0];
            const tipo = $('#tipoContenido').val();
            const reader = new FileReader();

            $('#previewContenido').show();
            $('#previewImg').hide();
            $('#previewVideo').hide();
            $('#previewDoc').addClass('d-none');

            if (!file) return;

            reader.onload = function(e) {
                if (tipo === 'imagen') {
                    $('#previewImg').attr('src', e.target.result).show();
                } else if (tipo === 'video') {
                    $('#previewVideo').attr('src', e.target.result).show();
                } else if (tipo === 'documento') {
                    $('#previewDoc').removeClass('d-none');
                }
            };

            if (tipo === 'imagen' || tipo === 'video') {
                reader.readAsDataURL(file);
            } else if (tipo === 'documento') {
                reader.readAsArrayBuffer(file); // no se necesita preview, solo señal
            }
        });

        function deleteTemplate(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta plantilla?')) {
                $.ajax({
                    url: '{{ route('plataforma.deleteTemplate') }}',
                    type: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#template-' + id).remove();
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: 'Plantilla eliminada exitosamente',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al eliminar la plantilla',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            location.reload();
                        }
                    }
                });
            }
        }
    </script>
@endsection
