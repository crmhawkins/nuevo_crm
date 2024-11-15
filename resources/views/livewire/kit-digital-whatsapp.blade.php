@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

<div>
    <div class="filtros row mb-4">
        <div class="col-md-12 col-sm-12">
            <div class="flex flex-row justify-center">
                <div class="mb-3 px-2" style="width: 85px">
                    <label class="titulo_filtros" for="" >Nª</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-20 mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
                <div wire:ignore  class="mb-3 px-2 flex-fill" style="width: 200px">
                    <label class="titulo_filtros" for="">Clientes</label>
                    <select wire:key="{{rand()}}" wire:model="selectedCliente" name="selectedCliente" id="selectedCliente" class="form-select choices">
                        <option value=""> Seleccione un cliente </option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Gestor</label>
                    <select wire:model="selectedGestor" name="selectedGestor" id="selectedGestor" class="form-select">
                        <option value=""> Gestor </option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros"for="">Comercial</label>
                    <select wire:model="selectedComerciales" name="selectedComerciales" id="selectedComerciales" class="form-select">
                        <option value=""> Comercial </option>
                        @foreach ($comerciales as $comercial)
                            <option value="{{$comercial->id}}">{{$comercial->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Estados</label>
                    <select wire:model="selectedEstado" name="selectedEstado" id="selectedEstado" class="form-select">
                        <option value=""> Estado </option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Servicios</label>
                    <select wire:model="selectedServicio" name="selectedServicio" id="selectedServicio" class="form-select">
                        <option value=""> Servicio </option>
                        @foreach ($servicios as $servicio)
                            <option value="{{$servicio->id}}">{{$servicio->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Estado de la Factura</label>
                    <select wire:model="selectedEstadoFactura" name="selectedEstadoFactura" id="selectedEstadoFactura" class="form-select">
                        <option value=""> Estado </option>
                        @foreach ($estados_facturas as $estadofactura)
                            <option value="{{$estadofactura['id']}}">{{$estadofactura['nombre']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="">Segmento</label>
                    <select wire:model="selectedSegmento" name="selectedSegmento" id="selectedSegmento" class="form-select">
                        <option value=""> Segmento </option>
                        @foreach ($segmentos as $segmento)
                            <option value="{{$segmento['id']}}">{{$segmento['nombre']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="filterByDate">Filtrar por Fecha</label>
                    <select wire:model="selectedDateField" name="selectedDateField" id="selectedDateField" class="form-select">
                        <option value="">Seleccione campo de fecha</option>
                        <option value="created_at">Fecha de Creación</option>
                        <option value="fecha_actualizacion">Fecha de Actualización</option>
                        <option value="fecha_acuerdo">Fecha de Acuerdo</option>
                        <option value="plazo_maximo_entrega">Plazo Máximo</option>
                        <option value="banco">En banco</option>
                        <!-- Puedes agregar más opciones según los campos de fecha disponibles -->
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="date_from">Desde</label>
                    <input wire:model="dateFrom" type="date" name="date_from" id="date_from" class="form-control">
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 140px">
                    <label class="titulo_filtros" for="date_to">Hasta</label>
                    <input wire:model="dateTo" type="date" name="date_to" id="date_to" class="form-control">
                </div>

            </div>
            <div class="col-md-12 col-sm-12 text-center">
                <span class="fs-3" >Sumatorio: <b>{{ number_format($Sumatorio, 2, ',', '.') .' €'}}</b></span>
            </div>
        </div>
    </div>
    @if ( $kitDigitals )
    <div class="table-responsive">
        <table class="table table-sm">
            <thead class="header-table">
                @foreach ([
                    'empresa' => 'EMP',
                    'segmento' => 'SEG',
                    'cliente_id' => 'CLI. A.',
                    'cliente' => 'CLIENTE',
                    'mensaje_interpretado' => 'INTERES.',
                    'mensaje' => 'IA',
                    'contacto' => 'CONTACTO',
                    'telefono' => 'TELEFONO',
                    'servicio_id' => 'SERVICIOS',
                    'estado' => 'ESTADO',
                    'importe' => 'IMPORTE',
                    'estado_factura' => 'ESTADO F.',
                    'gestor' => 'GESTOR',
                    'comercial_id' => 'COMERCIAL',
                    ] as $field => $label)
                    <th class="px-3">
                        <a href="#" wire:click.prevent="sortBy('{{ $field }}')">
                            {{ $label }}
                            @if ($sortColumn == $field)
                                <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                            @endif
                        </a>
                    </th>
                @endforeach
            </thead>
            <tbody>
                @foreach ($kitDigitals as $item)
                <tr  wire:key='{{rand()}}' style="--bs-table-bg: {{$item->estados->color}} !important; --bs-table-color: {{$item->estados->text_color}} !important">
                    <td class="exclude" style="max-width: 50px"> <input data-id="{{$item->id}}" type="text" name="empresa" id="empresa" value="{{ $item->empresa }}" style="max-width: 50px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none; margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                    <td class="exclude" style="max-width: 50px">
                        <select name="segmento" id="segmento" style="max-width: 50px;padding: 0.1rem 0.1rem 0.1rem 0.2rem; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;" data-id="{{$item->id}}">
                            @foreach ($segmentos as $segmento)
                                <option value="{{$segmento['id']}}" @if($item->segmento == $segmento['id']) selected  @endif>{{$segmento['nombre']}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width: 50px !important; ">
                        <select data-id="{{$item->id}}" name="cliente_id" id="cliente_id" style="width: 50px !important; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;">
                            <option value="">SC</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{$cliente->id}}" @if($item->cliente_id == $cliente->id) selected  @endif>{{$cliente->company ?? $cliente->name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="max-width: 70px !important"><input data-id="{{$item->id}}" type="text" name="cliente" id="cliente" value="{{ $item->cliente }}" style="max-width: 70px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem"></td>
                    <td style="">
                        <input disabled data-id="{{$item->id}}" type="text" name="mensaje_interpretado" id="mensaje_interpretado"
                        value="{{ $item->mensaje_interpretado == 1 ? 'Si' : ($item->mensaje_interpretado == 2 ? 'No se' : ($item->mensaje_interpretado === 0 ? 'No' : ($item->mensaje_interpretado === 3 ? 'Error' : ($item->mensaje_interpretado === 4 ? 'No respondio' : '')))) }}"
                        style="height: fit-content; background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border: none; margin-bottom: 0 !important; font-size: 0.75rem; text-align: center; width: 56px;">
                    </td>
                    <td class="row" style="">
                        <textarea disabled cols="30" rows="1"  style="margin-bottom: 0; width:80%;">{{ $item->mensaje }}</textarea>
                        <button type="button"  style="margin-bottom: 0; margin-left:5px; width:18%;" class="btn btn-sm btn-light" onclick="redirectToWhatsapp({{$item->id}})">Ver</button>

                    </td>
                    <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="contacto" id="contacto" value="{{ $item->contacto }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                    <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="telefono" id="telefono" value="{{ $item->telefono }}" style="max-width: 50px;height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem;"></td>
                    <td style="max-width: 50px">
                        <select name="servicio_id" id="servicio_id" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 60px">
                            @foreach($servicios as $servicio)
                                <option @if($item->servicio_id == $servicio->id) selected  @endif value="{{$servicio->id}}">{{$servicio->name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="estado" id="estado" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}};margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 60px">
                            <option value="">Seleccione un estado</option>
                            @foreach($estados as $estado)
                                <option @if($item->estado == $estado->id) selected  @endif value="{{$estado->id}}">{{$estado->nombre}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="max-width: 50px"><input data-id="{{$item->id}}" type="text" name="importe" id="importe" value="{{ $item->importe }}" style="height: fit-content;background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; border:none;margin-bottom: 0 !important;font-size: 0.75rem; text-align: center;width: 50px"></td>
                    <td style="max-width: 50px" @if($item->estado_factura == 0) style="background-color: #f25757; color: white;" @else style="background-color: #2cbc09; color: white;" @endif >
                        <select name="estado_factura" id="estado_factura" data-id="{{$item->id}}" style="background-color: {{$item->estado_factura == 1 ? '#2cbc09': '#f25757'}}; color: white;margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem; width: 66px;">
                            <option value="">Seleccione un estado</option>
                                <option @if($item->estado_factura == 1) selected style="height: fit-content;background-color: #2cbc09; color: white;" @endif value="1">Abonada</option>
                                <option @if($item->estado_factura == 0) selected style="height: fit-content;background-color: #f25757; color: white;" @endif value="0">No Abonada</option>
                        </select>
                    </td>
                    <td style="max-width: 80px" class="exclude">
                        <select name="gestor" id="gestor" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 80px">
                            <option value="">Seleccione un gestor</option>
                            @foreach($gestores as $gestor)
                                <option @if($item->gestor == $gestor->id) selected  @endif value="{{$gestor->id}}">{{$gestor->name}} {{$gestor->surname}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="max-width: 80px" class="exclude">
                        <select name="comercial_id" id="comercial_id" data-id="{{$item->id}}" style="background-color: {{$item->estados->color}}; color: {{$item->estados->text_color}}; margin-bottom: 0 !important;font-size: 0.75rem;height: fit-content;padding: 0.1rem 0.1rem 0.1rem 0.2rem;max-width: 80px">
                            <option value="">Seleccione un comercial</option>
                            @foreach($comerciales as $comercial)
                                <option @if($item->comercial_id == $comercial->id) selected  @endif value="{{$comercial->id}}">{{$comercial->name}} {{$comercial->surname}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Modal Structure -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea id="modal-textarea" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" id="saveChanges">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>



        @if($perPage !== 'all')
        {{ $kitDigitals->links() }}
        @endif
        <div class="col-md-12 col-sm-12 text-center" style="margin: 1rem 0">
            <span class="fs-3" >Sumatorio: <b>{{ number_format($Sumatorio, 2, ',', '.') .' €'}}</b></span>
        </div>
    </div>

    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>DOMINIOS</strong></h3>
        </div>
    @endif
    <style>
        /* Estilos específicos para la tabla */
    .table-responsive {
        overflow-x: auto; /* Asegura un desplazamiento suave en pantallas pequeñas */
    }

    .header-table th {
        vertical-align: bottom; /* Alinea el texto de los encabezados en la parte inferior */
        white-space: nowrap; /* Evita que los encabezados se rompan en líneas */
        font-size: 0.85rem; /* Ajusta el tamaño del texto para los encabezados */
    }

    .table td, .table th {
        padding: 0.5rem; /* Ajusta el padding para las celdas */
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
    </style>
</div>
@section('scripts')
<script>
$(document).ready(function() {
    // Usar delegación de eventos para asegurar que los nuevos elementos tengan el evento click
    $(document).on('click', '.edit-textarea', function() {
        var id = $(this).data('id');
        var field = $(this).data('field');
        var content = $(this).data('content');

        console.log(content, field, id);

        // Configurar el textarea y el título del modal
        $('#modal-textarea').val(content).data('id', id).data('field', field);
        $('#editModalLabel').text('Editar ' + field.replace('_', ' ').toUpperCase());
    });

    // Guardar los cambios cuando el botón "Guardar Cambios" es presionado
    $('#saveChanges').on('click', function() {
        var id = $('#modal-textarea').data('id');
        var field = $('#modal-textarea').data('field');
        var value = $('#modal-textarea').val();

        // Actualizar el contenido visual en el DOM (si es necesario)
        $('[data-id="' + id + '"][data-field="' + field + '"]').data('content', value);

        // Llamar a la función para guardar los datos actualizados en el servidor
        handleDataUpdate(id, value, field);
    });
});

// Función para manejar la actualización de datos
function handleDataUpdate(id, value, key) {
    $.ajax({
        type: "POST",
        url: "{{ route('kitDigital.updateData') }}", // Asegúrate de que esta es la ruta correcta
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            id: id,
            value: value,
            key: key
        },
        success: function(data) {
            // Cierra el modal explícitamente después de actualizar
            $('#editModal').modal('hide');
            $('.modal-backdrop').remove();
            // Opcionalmente muestra una notificación de éxito
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
        },
        error: function(xhr, status, error) {
            // Opcionalmente muestra una notificación de error
            $('#editModal').modal('hide'); // Cierra el modal también en caso de error si lo prefieres
        }
    });
}


</script>


    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>

        function redirectToWhatsapp(id) {
            window.open(`/kit-digital/whatsapp/${id}`, '_blank');
        }

        $(document).ready(function() {

        $("#sidebar").remove();
        $("#main").css("margin-left", "0px");
        // Función para manejar la actualización de datos
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
                },
                error: function(xhr, status, error) {
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
                        icon: 'error', // Se cambió a un ícono fijo 'error' porque 'data.icon' no estaría disponible aquí
                        title: 'Error de servidor' // Mensaje genérico de error, puedes personalizarlo
                    });
                }
            });
        }

        // Detectar cambios en inputs, selects y textareas dentro de la tabla
        $('.table').on('change', 'input, select, textarea', function() {
            var id = $(this).data('id');  // Asegúrate de que cada fila tenga un atributo data-id
            var key = $(this).attr('name');
            var value = $(this).val();
            handleDataUpdate(id, value, key);
        });
    });
    </script>
@endsection
