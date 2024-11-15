<div>
    {{-- Filtros --}}
    <div class="filtros row mb-4">
        <div class="col-md-3">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nª</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10 </option>
                        <option value="25">25 </option>
                        <option value="15">50 </option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="flex flex-row justify-end">

                <div wire:ignore  class="mb-3 px-2 flex-fill" style="width: 200px">
                    <label for="">Clientes</label>
                    <select  wire:ignore  wire:model="selectedCliente" id="clientesChoices" class="form-select choices">
                        <option value="">Clientes</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->company ?? $cliente->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 150px">
                    <label for="">Estados</label>
                    <select wire:model="selectedEstado" name="" id="" class="form-select ">
                        <option value="">Estados</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 150px">
                    <label for="">Categorías</label>
                    <select wire:model="selectedCategoria" name="" id="" class="form-select ">
                        <option value="">Categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{$categoria->id}}">{{$categoria->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 150px">
                    <label for="">Departamento</label>
                    <select wire:model="selectedDepartamento" name="" id="" class="form-select ">
                        <option value="">Departamento</option>
                        @foreach ($departamentos as $departamento)
                            <option value="{{$departamento->id}}">{{$departamento->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 150px">
                    <label for="">Empleado</label>
                    <select wire:model="selectedEmpleado" name="" id="" class="form-select ">
                        <option value="">Empleados</option>
                        @foreach ($empleados as $empleado)
                            <option value="{{$empleado->id}}">{{$empleado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 150px">
                    <label for="">Gestor</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select ">
                        <option value="">Gestores</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 px-2 flex-fill" style="width: 100px">
                    <label for="">Año</label>
                    <select wire:model="selectedYear" class="form-select">
                        <option value="">Año --</option>
                        @foreach (range(date('Y'), date('Y') - 5, -1) as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
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
                            'title' => 'TITULO',
                            'prioridad' => 'PRIORIDAD',
                            'task_status_id' => 'ESTADO',
                            'cliente' => 'CLIENTE',
                            'departamento' => 'DEPARTAMENTO',
                            'empleado' => 'EMPLEADO ASIGNADO',
                            'gestor' => 'GESTOR',
                            'estimated_time' => 'TIEMPO ESTIMADO',
                            'real_time' => 'TIEMPO REAL',
                            'created_at' => 'FECHA DE CREACION',
                            // 'created_at' => 'FECHA DE ENTREGA',
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
                        <th class="text-center" style="font-size:0.75rem">FECHA DE ENTREGA</th>
                        <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    {{-- Recorremos los servicios --}}
                    @foreach ( $tareas as $tarea )
                        <tr class="clickable-row" data-href="{{route('tarea.edit', $tarea->id)}}" >
                            <td class="px-3">{{$tarea->title}}</td>
                            <td class="">{{$tarea->prioridad ? $tarea->prioridad : 'Prioridad no asignada'}}</td>
                            <td class="">{{$tarea->estado ? $tarea->estado->name  : 'Estado no asignado'}}</td>
                            <td class="">{{$tarea->presupuesto->cliente->company ?? $tarea->presupuesto->cliente->name ?? 'Cliente Borrado'}}</td>
                            <td class="">{{$tarea->split_master_task_id ? ($tarea->usuario ? ($tarea->departamento ?? 'Usuario sin departamento'  ) : 'Usuario no asignado') : ''}}</td>
                            <td class="">{{$tarea->split_master_task_id ? ($tarea->empleado ?? 'No definido') : 'Tarea Maestra'}}</td>
                            <td class="">{{$tarea->gestor ?? 'No definido'}}</td>
                            <td class="">{{$tarea->split_master_task_id ? $tarea->estimated_time : $tarea->total_time_budget}}</td>
                            <td class="">{{$tarea->split_master_task_id ? $tarea->real_time : $tarea->real_time_maestra()}}</td>
                            <td class="">{{Carbon\Carbon::parse($tarea->created_at)->format('d/m/Y')}}</td>
                            <td>
                                @if (isset($fechasEstimadas[$tarea->id]))
                                    {{ $fechasEstimadas[$tarea->id]['fecha_estimada'] }}
                                @else
                                    No calculada
                                @endif
                            </td>                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('tarea.edit', $tarea->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar servicio"></a>
                                <a class="delete" data-id="{{$tarea->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar servicio"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if( count($tareas) == 0 )
                <div class="text-center py-4">
                    <h3 class="text-center fs-3">No se encontraron registros de <strong>TAREAS</strong></h3>
                </div>
            @endif
            @if($perPage !== 'all')
                {{ $tareas->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>TAREAS</strong></h3>
        </div>
    @endif
    {{-- {{$users}} --}}
</div>
@section('scripts')
<!-- Choices.js CSS -->
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>



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
