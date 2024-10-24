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
                    <select wire:model="selectedEstados" name="" id="" class="form-select ">
                        <option value="">Seleccione un Estado</option>
                            <option value="{{0}}">Pendiente</option>
                            <option value="{{1}}">Realizada</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    @if ( $petitions )
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'client_id' => 'CLIENTE',
                            'admin_user_id' => 'GESTOR',
                            'created_at' => 'FECHA CREACION',
                            'note' => 'NOTA',
                            'finished' => 'ESTADO'
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ( $petitions as $petition )
                        <tr class="clickable-row" data-href="{{route('peticion.edit', $petition->id)}}">
                            <td>@if (isset($petition->cliente->name))
                                {{$petition->cliente->name}}
                                @else
                                Cliente no encontrado
                            @endif</td>
                            <td>{{$petition->usuario->name}}</td>
                            <td>{{$petition->created_at->format('Y-m-d')}}</td>
                            <td style="max-width: 25rem">{{$petition->note}}</td>
                            @if ($petition->finished == 0)
                                <td><span class="badge bg-warning text-dark">{{$petition->getEstado()}}</span></td>
                            @elseif($petition->finished == 1)
                                <td><span class="badge bg-success">{{$petition->getEstado()}}</span></td>
                            @endif
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('presupuesto.createFromPetition', $petition->id)}}"><img src="{{asset('assets/icons/file-text.svg')}}" alt="Mostrar usuario"></a>
                                <a class="" href="{{route('peticion.edit', $petition->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Mostrar usuario"></a>
                                <a class="delete" data-id="{{$petition->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Mostrar usuario"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $petitions->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>PETICIONES</strong></h3>
            <p class="mt-2">Pulse el boton superior para crear algun usuario.</p>
        </div>
    @endif
    {{-- {{$users}} --}}
</div>
@section('scripts')


    @include('partials.toast')

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
                title: "¿Estas seguro que quieres eliminar esta petición?",
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
            const url = '{{route("peticion.delete")}}'
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
