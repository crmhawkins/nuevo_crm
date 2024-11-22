<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nº por página</label>
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
                <div class="mr-3" wire:ignore>
                    <label for="">Usuario</label>
                    <select wire:model="selectedUser" class="form-select">
                        <option value="">-- Seleccione un usuario --</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{$usuario->id}}">{{$usuario->name.' '.$usuario->surname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Año</label>
                    <select wire:model="selectedAnio" class="form-select">
                        <option value="">-- Año --</option>
                        @for ($year = now()->year; $year >= 2020; $year--)
                            <option value="{{$year}}">{{$year}}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="">Mes</label>
                    <select wire:model="selectedMes" class="form-select">
                        <option value="">-- Mes --</option>
                        @foreach (range(1, 12) as $month)
                            <option value="{{$month}}">{{DateTime::createFromFormat('!m', $month)->format('F')}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($incidencias->count())
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
                        <a href="#" wire:click.prevent="sortBy('created_at')">
                            FECHA
                            @if ($sortColumn == 'created_at')
                                <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ($incidencias as $incidencia)
                        <tr class="clickable-row" data-href="{{ route('incidencias.edit', $incidencia->id) }}">
                            <td>{{ $incidencia->adminUser ? $incidencia->adminUser->name . ' ' . $incidencia->adminUser->surname : 'Usuario no encontrado' }}</td>
                            <td>{{ \Carbon\Carbon::parse($incidencia->created_at)->format('d/m/Y') }}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a href="{{ route('incidencias.edit', $incidencia->id) }}"><img src="{{ asset('assets/icons/edit.svg') }}" alt="Editar incidencia"></a>
                                <a class="delete" data-id="{{ $incidencia->id }}" href="#"><img src="{{ asset('assets/icons/trash.svg') }}" alt="Eliminar incidencia"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $incidencias->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3>No se encontraron registros de <strong>INCIDENCIAS</strong></h3>
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
                let id = $(this).data('id');
                botonAceptar(id);
            });
        });

        function botonAceptar(id){
            Swal.fire({
                title: "¿Estás seguro de que quieres eliminar esta incidencia?",
                html: "<p>Esta acción es irreversible.</p>",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.when(getDelete(id)).then(function(data, textStatus, jqXHR) {
                        if (!data.status) {
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            const url = '{{ route("incidencias.delete") }}';
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: { 'id': id },
                dataType: "json"
            });
        }
    </script>
@endsection
