<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="d-flex">
                <div class="mr-3">
                    <label for="perPage">Nº</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="buscar">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control" placeholder="Buscar observación...">
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 d-flex justify-content-end">
            <div class="mr-3">
                <label for="selectedUser">Usuario</label>
                <select wire:model="selectedUser" class="form-select">
                    <option value="">-- Seleccione un usuario --</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mr-3">
                <label for="selectedAnio">Año</label>
                <select wire:model="selectedAnio" class="form-select">
                    <option value="">-- Año --</option>
                    @for ($year = now()->year; $year >= 2020; $year--)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="selectedMes">Mes</label>
                <select wire:model="selectedMes" class="form-select">
                    <option value="">-- Mes --</option>
                    @foreach (range(1, 12) as $month)
                        <option value="{{ $month }}">{{ DateTime::createFromFormat('!m', $month)->format('F') }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if($bajas->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('admin_user_id')">Usuario</a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('inicio')">Fecha de Inicio</a>
                        </th>
                        <th>
                            Observación
                        </th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bajas as $baja)
                        <tr>
                            <td>{{ $baja->usuario ? $baja->usuario->name : 'Sin usuario' }}</td>
                            <td>{{ \Carbon\Carbon::parse($baja->inicio)->format('d/m/Y') }}</td>
                            <td>{{ $baja->observacion }}</td>
                            <td class="flex flex-row justify-evenly align-middle" >
                                <a href="{{ route('bajas.edit', $baja->id) }}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Mostrar baja"></a>
                                <a class="delete" data-id="{{$baja->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar baja"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $bajas->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3>No se encontraron registros de Bajas</h3>
        </div>
    @endif
</div>

@section('scripts')
    @include('partials.toast')
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
                    title: "¿Estas seguro que quieres eliminar esta baja?",
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
                const url = '{{route("bajas.delete")}}';
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
