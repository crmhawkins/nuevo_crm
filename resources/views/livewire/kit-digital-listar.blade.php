@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-end">
                <div class="mr-3">
                    <label for="">Estados</label>
                    <select wire:model="selectedEstado" name="" id="" class="form-select">
                        <option value="">-- Seleccione un estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Clientes</label>
                    <select wire:model="selectedCliente" name="" id="" class="form-select choices">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Gesto</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select choices">
                        <option value="">-- Gestor --</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Comercial</label>
                    <select wire:model="selectedComerciales" name="" id="" class="form-select choices">
                        <option value="">-- Comercial --</option>
                        @foreach ($comerciales as $comercial)
                            <option value="{{$comercial->id}}">{{$comercial->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Servicios</label>
                    <select wire:model="selectedServicio" name="" id="" class="form-select choices">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($servicios as $servicio)
                            <option value="{{$servicio->id}}">{{$servicio->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Estado de la Factura</label>
                    <select wire:model="selectedEstadoFactura" name="" id="" class="form-select choices">
                        <option value="">-- Estado --</option>
                        @foreach ($estados_facturas as $estadofactura)
                            <option value="{{$estadofactura['id']}}">{{$estadofactura['nombre']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Segmento</label>
                    <select wire:model="selectedSegmento" name="" id="" class="form-select choices">
                        <option value="">-- Segmento --</option>
                        @foreach ($segmentos as $segmento)
                            <option value="{{$segmento['id']}}">{{$segmento['nombre']}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="col-md-12 col-sm-12">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nª por paginas</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="15">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>

    </div>
    {{-- {{dd($users)}} --}}
    @if ( $kitDigitals )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table">
                <thead class="header-table">
                    @foreach ([
                            'empresa' => 'EMPRESA',
                            'segmento' => 'SEGMENTO',
                            'cliente_id' => 'CLIENTE ASOCIADO',
                            'cliente' => 'CLIENTE KIT',
                            'mensaje_interpretado' => 'INTERESADO',
                            'mensaje' => 'CONVERSACION IA',
                            'contacto' => 'CONTACTO',
                            'telefono' => 'TELEFONO',
                            'expediente' => 'EXPEDIENTE',
                            'contratos' => 'CONTRATOS',
                            'servicio_id' => 'SERVICIOS',
                            'estado' => 'ESTADO',
                            'fecha_actualizacion' => 'FECHA ACTUALIZACION',
                            'importe' => 'IMPORTE',
                            'estado_factura' => 'ESTADO FACTURA',
                            'banco' => 'EN BANCO',
                            'fecha_acuerdo' => 'FECHA DEL ACUERDO',
                            'plazo_maximo_entrega' => 'PLAZO MÁXIMO DE ENTREGA',
                            'gestor' => 'GESTOR',
                            'comercial_id' => 'COMERCIAL',
                            'comentario' => 'COMENTARIO',
                            'nuevo_comentario' => 'NUEVO COMENTARIO',
                            ] as $field => $label)
                            <th class="px-3" style="font-size:0.75rem">
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
                    @foreach ( $kitDigitals as $item )
                        <tr>
                          <td>{{$item->empresa}}</td>
                          <td>{{$item->segmento}}</td>
                          <td style="width:250px;">{{$item->Client->name ?? 'Sin cliente'}}</td>
                          <td style="width:250px;">{{$item->cliente}}</td>
                          <td >{{$item->mensaje_interpretado}}</td>
                          <td style="width:300px;">{{$item->mensaje}}</td>
                          <td>{{$item->contacto}}</td>
                          <td>{{$item->telefono}}</td>
                          <td>{{$item->expediente}}</td>
                          <td>{{$item->contratos}}</td>
                          <td>{{$item->servicios->name ?? $item->servicio_id}}</td>
                          <td>{{$item->estados->nombre ?? $item->estado}}</td>
                          <td>{{$item->fecha_actualizacion}}</td>
                          <td>{{$item->importe}}</td>
                          <td>{{$item->estado_factura}}</td>
                          <td>{{$item->banco}}</td>
                          <td>{{$item->fecha_acuerdo}}</td>
                          <td>{{$item->plazo_maximo_entrega}}</td>
                          <td>{{$item->gestor}}</td>
                          <td>{{$item->comercial_id}}</td>
                          <td style="width:300px;" >{{$item->comentario}}</td>
                          <td style="width:300px;">{{$item->nuevo_comentario}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $kitDigitals->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>DOMINIOS</strong></h3>
        </div>
    @endif
</div>
@section('scripts')


    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>
        $(document).ready(() => {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
                botonAceptar(id);

            });
        });

        function botonAceptar(id){
            // Salta la alerta para confirmar la eliminacion
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar este dominio?",
                html: "<p>Esta acción es irreversible.</p>", // Corrige aquí
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
                // denyButtonText: `No Borrar`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Llamamos a la funcion para borrar el usuario
                    $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                        console.log(data)
                        if (!data.status) {
                            // Si recibimos algun error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            // Todo a ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            })
                            .then(() => {
                                location.reload()
                            })
                        }
                    });
                }
            });
        }
        function getDelete(id) {
            // Ruta de la peticion
            const url = '{{route("dominios.delete")}}'
            // Peticion
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                },
                dataType: "json"
            });
        }
    </script>
@endsection
