<div>
    <div class="filtros row mb-4">
        <div class="col-md-7">
            <div class="d-flex flex-row justify-start">
                <div class="w-25">
                    <label for="">Nº</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="ms-5 w-100">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="flex flex-row justify-end">
                <div class="mr-0 w-75">
                    <label for="">Gestores</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Gestor --</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{ $gestor->id }}">{{ $gestor->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($clients)
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('company')">
                                CLIENTE
                                @if ($sortColumn == 'company')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th class="px-3">
                            <a href="#" wire:click.prevent="sortBy('name')">
                                NOMBRE
                                @if ($sortColumn == 'name')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('cif')">
                                CIF
                                @if ($sortColumn == 'cif')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('identifier')">
                                MARCA
                                @if ($sortColumn == 'identifier')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('activity')">
                                ACTIVIDAD
                                @if ($sortColumn == 'activity')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="#" wire:click.prevent="sortBy('admin_user_id')">
                                GESTOR
                                @if ($sortColumn == 'admin_user_id')
                                    <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                @endif
                            </a>
                        </th>
                        <th class="text-center">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                    <tr class="clickable-row" data-href="{{route('clientes.edit', $client->id)}}">
                            <td class="px-3">{{ $client->company }}</td>
                            <td >{{ $client->name }}</td>
                            <td>{{ $client->cif }}</td>
                            <td>{{ $client->identifier }}</td>
                            <td>{{ $client->activity }}</td>
                            <td>{{ $client->gestor->name ?? 'Gestor Borrado' }}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{ route('clientes.show', $client->id) }}"><img src="{{ asset('assets/icons/eye.svg') }}" alt="Mostrar usuario"></a>
                                <a class="" href="{{ route('clientes.edit', $client->id) }}"><img src="{{ asset('assets/icons/edit.svg') }}" alt="Mostrar usuario"></a>
                                <a class="delete" data-id="{{ $client->id }}" href=""><img src="{{ asset('assets/icons/trash.svg') }}" alt="Mostrar usuario"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (count($clients) == 0)
                <div class="text-center py-4">
                    <h3 class="text-center fs-3">No se encontraron registros de <strong>CLIENTES</strong></h3>
                    <p class="mt-2">Pulse el botón superior para crear algún cliente.</p>
                </div>
            @endif

            @if ($perPage !== 'all')
                {{ $clients->links('vendor.pagination.bootstrap-5') }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>CLIENTES</strong></h3>
            <p class="mt-2">Pulse el botón superior para crear algún cliente.</p>
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

        function botonAceptar(id) {
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar este cliente?",
                html: "<p>Esta acción es irreversible.</p>",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.when(getDelete(id)).then(function(data) {
                        if (data.error) {
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            }).then(() => {
                                location.reload()
                            })
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            const url = '{{ route("clientes.delete") }}';
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
