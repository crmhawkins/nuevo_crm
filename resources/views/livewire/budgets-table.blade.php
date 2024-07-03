<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
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
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-end">
                <div class="mr-3">
                    <label for="">Gestores</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Gestor --</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="">Estados</label>
                    <select wire:model="selectedEstados" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    {{-- {{dd($users)}} --}}
    @if ( $budgets )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table">
                <thead class="header-table">
                    <th class="px-3" style="font-size:0.75rem">REFERENCIA</th>
                    <th class="" style="font-size:0.75rem">CLIENTE</th>
                    <th class="" style="font-size:0.75rem">CAMPAÑA</th>
                    <th class="" style="font-size:0.75rem">FECHA CREACION</th>
                    <th class="" style="font-size:0.75rem">ESTADO</th>
                    <th class="" style="font-size:0.75rem">TOTAL</th>
                    <th class="" style="font-size:0.75rem">GESTOR</th>
                    <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ( $budgets as $budget )

                        <tr>
                            <td>{{$budget->reference}}</td>
                            <td>{{$budget->cliente->name}}</td>
                            <td>{{$budget->proyecto->name}}</td>
                            <td>{{$budget->creation_date}}</td>
                            <td>{{$budget->estadoPresupuesto->name}}</td>
                            <td>{{$budget->total}} €</td>
                            <td>{{$budget->usuario->name}}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('presupuesto.show', $budget->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar usuario"></a>
                                <a class="" href="{{route('presupuesto.edit', $budget->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Mostrar usuario"></a>
                                <a class="delete" data-id="{{$budget->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Mostrar usuario"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $budgets->links() }}
            @endif
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
            const url = '{{route("presupuesto.delete")}}'
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
