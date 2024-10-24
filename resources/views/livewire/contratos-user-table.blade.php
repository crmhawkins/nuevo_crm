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
                    <label for="">Año</label>
                    <select wire:model="selectedAnio" id="selectedAnio" class="form-select">
                        <option value="">-- Año -- </option>
                        @for ($year = now()->year; $year >= 2020; $year--)
                            <option value="{{$year}}">{{$year}}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="">Mes</label>
                    <select wire:model="selectedMes" id="selectedMes" class="form-select">
                        <option value="">-- Mes --</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{$month}}">{{DateTime::createFromFormat('!m', $month)->format('F')}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    {{-- {{dd($users)}} --}}
    @if ( $contratos )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <th class="px-3">
                        <a href="#" wire:click.prevent="sortBy('admin_user_id')">
                            USUARIO
                            @if ($sortColumn == 'admin_user_id')
                                <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="#" wire:click.prevent="sortBy('fecha')">
                            FECHA
                            @if ($sortColumn == 'fecha')
                                <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ( $contratos as $contrato )
                        <tr class="clickable-row" data-href="{{route('contratos.show', $contrato->id)}}">
                            <td>{{$contrato->usuario ? ($contrato->usuario->name.' '.$contrato->usuario->surname) : 'Usuario no encontrado'}}</td>
                            <td>{{ \Carbon\Carbon::parse($contrato->fecha)->format('d/m/Y') }}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('contratos.show', $contrato->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar contrato"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $contratos->links() }}
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
                title: "¿Estas seguro que quieres eliminar este contrato?",
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
            const url = '{{route("contratos.delete")}}'
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
