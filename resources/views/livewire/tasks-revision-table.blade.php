<div>
    {{-- Filtros --}}
    <div class="filtros row mb-4">
        <div class="col-md-5">
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
        <div class="col-md-7">
            <div class="flex flex-row justify-end">
                <div class="mr-3">
                    <label for="">Categorías</label>
                    <select wire:model="selectedCategoria" name="" id="" class="form-select ">
                        <option value="">-- Categorías --</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Clientes</label>
                    <select wire:model="selectedCliente" name="" id="" class="form-select ">
                        <option value="">-- Clientes --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Empleado</label>
                    <select wire:model="selectedEmpleado" name="" id="" class="form-select ">
                        <option value="">-- Empleados --</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{$empleado->id}}">{{$empleado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Gestor</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select ">
                        <option value="">-- Gestores --</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ( $tareas )

        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'id' => 'REF',
                            'title' => 'TITULO',
                            'categoria_nombre' => 'CATEGORIA',
                            'concept' => 'CONCEPTO',
                            'cliente' => 'CLIENTE',
                            'empleado' => 'EMPLEADO ASIGNADO',
                            'gestor' => 'GESTOR',
                            'created_at' => 'FECHA DE CREACION',
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
                    {{-- Recorremos los servicios --}}
                    @foreach ( $tareas as $tarea )
                        <tr class="clickable-row" data-href="{{route('tarea.edit', $tarea->id)}}">
                            <td class="px-3">{{$tarea->id}}</td>
                            <td class="">{{$tarea->title}}</td>
                            <td class="">{{$tarea->presupuestoConcepto->servicioCategoria->name ?? 'No definido' }}</td>
                            <td class="">{{$tarea->presupuestoConcepto->title ?? 'No definido'}}</td>
                            <td class="">{{$tarea->presupuesto->cliente->company ?? $tarea->presupuesto->cliente->name ?? 'Cliente Borrado'}}</td>
                            <td class="">{{$tarea->split_master_task_id ? ($tarea->usuario->name ?? 'No definido') : 'Tarea Maestra'}}</td>
                            <td class="">{{$tarea->gestor ?? 'No definido'}}</td>
                            <td class="">{{Carbon\Carbon::parse($tarea->created_at)->format('d/m/Y')}}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('tarea.edit', $tarea->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar servicio"></a>
                                <a class="delete" data-id="{{$tarea->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar servicio"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- Si los servicios vienen vacio --}}
            @if( count($tareas) == 0 )
                <div class="text-center py-4">
                    <h3 class="text-center fs-3">No se encontraron registros de <strong>TAREAS</strong> en revisión</h3>
                </div>
            @endif

            {{-- Paginacion --}}
            @if($perPage !== 'all')
                {{ $tareas->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>TAREAS</strong> en revisión</h3>
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
                title: "¿Estas seguro que quieres eliminar este servicio?",
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
            const url = '{{route("servicios.delete")}}'
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
