<div>
    <div class="filtros row mb-4">
        <div class="col-md-6">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nº</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="15">50 por página</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="flex flex-row justify-end">
                <div class="mr-3">
                    <label for="">Departamentos</label>
                    <select wire:model="selectedDepartamento" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Gestor --</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{$departamento->id}}">{{$departamento->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="">Nivel de Accesos</label>
                    <select wire:model="selectedNivel" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Nivel --</option>
                        @foreach ($niveles as $nivele)
                            <option value="{{$nivele->id}}">{{$nivele->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    {{-- {{dd($users)}} --}}
    @if ( $users )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        <th class="text-center" style="font-size:0.75rem">AVATAR</th>
                        @foreach ([
                            'name' => 'NOMBRE',
                            'acceso' => 'NIVEL DE ACCESO',
                            'departamento' => 'DEPARTAMENTO',
                            'cargo' => 'CARGO',
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
                    @foreach ( $users as $user )
                        <tr class="clickable-row" data-href="{{route('users.edit', $user->id)}}">
                            <td>

                            </td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->acceso}}</td>
                            <td>{{$user->departamento}}</td>
                            <td>{{$user->cargo}}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('users.show', $user->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar usuario"></a>
                                <a class="" href="{{route('users.edit', $user->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Mostrar usuario"></a>
                                <a class="delete" data-id="{{$user->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Mostrar usuario"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>USUARIOS</strong></h3>
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
                title: "¿Estas seguro que quieres eliminar este usuario?",
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
                        if (data.error) {
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
            const url = '{{route("users.delete")}}'
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
