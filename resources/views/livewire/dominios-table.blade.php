@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nº</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-end">
                <div class="mr-3">
                    <label for="">Estados</label>
                    <select wire:model="selectedEstado" name="" id="" class="form-select">
                        <option value="">-- Seleccione un estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Clientes</label>
                    <select wire:model="selectedCliente" name="" id="" class="form-select choices">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div>
                    <label for="">Estados</label>
                    <select wire:model="selectedEstados" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div> --}}
            </div>
        </div>
    </div>
    {{-- {{dd($users)}} --}}
    @if ( $dominios )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'dominio' => 'DOMINIO',
                            'client_id' => 'CLIENTE',
                            'date_start' => 'FECHA CONTRATACION',
                            'date_end' => 'FECHA VENCIMIENTO',
                            'estado_id' => 'ESTADO',

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
                        <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ( $dominios as $dominio )
                        <tr class="clickable-row" data-href="{{route('dominios.edit', $dominio->id)}}">
                            <td>{{$dominio->dominio}}</td>
                            <td>{{$dominio->cliente->name ?? 'Cliente no asociado'}}</td>
                            <td>{{ \Carbon\Carbon::parse($dominio->date_start)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($dominio->date_end)->format('d/m/Y') }}</td>
                            @if ($dominio->estado_id == 2)
                                <td><span class="badge bg-warning text-dark">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 3)
                                <td><span class="badge bg-success">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 1)
                                <td><span class="badge bg-danger">{{$dominio->estadoName->name}}</span></td>
                            @else
                                <td></td>
                            @endif
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                {{-- <a class="" href="{{route('presupuesto.show', $dominio->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar dominio"></a> --}}
                                <a class="" href="{{route('dominios.edit', $dominio->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar dominio"></a>
                                <a class="delete" data-id="{{$dominio->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar dominio"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $dominios->links() }}
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
