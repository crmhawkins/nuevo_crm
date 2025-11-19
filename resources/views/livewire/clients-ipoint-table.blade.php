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
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 180px">
                                <a class="" href="{{ route('clientes.show', $client->id) }}"><img src="{{ asset('assets/icons/eye.svg') }}" alt="Mostrar usuario"></a>
                                <a class="" href="{{ route('clientes.edit', $client->id) }}"><img src="{{ asset('assets/icons/edit.svg') }}" alt="Mostrar usuario"></a>
                                <a class="trasladar-ipoint" data-id="{{ $client->id }}" href="javascript:void(0)" title="Trasladar a clients"><i class="fas fa-arrow-right text-primary"></i></a>
                                <a class="delete-ipoint" data-id="{{ $client->id }}" href=""><img src="{{ asset('assets/icons/trash.svg') }}" alt="Mostrar usuario"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if (count($clients) == 0)
                <div class="text-center py-4">
                    <h3 class="text-center fs-3">No se encontraron registros de <strong>CLIENTES IPOINT</strong></h3>
                    <p class="mt-2">Pulse el botón superior para crear algún cliente.</p>
                </div>
            @endif

            @if ($perPage !== 'all')
                {{ $clients->links('vendor.pagination.bootstrap-5') }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>CLIENTES IPOINT</strong></h3>
            <p class="mt-2">Pulse el botón superior para crear algún cliente.</p>
        </div>
    @endif
</div>

@section('scripts')
    @include('partials.toast')

    <script>
      document.addEventListener('livewire:load', () => {
    attachDeleteEventIpoint();
    attachTrasladarEventIpoint();
});

document.addEventListener('livewire:update', () => {
    attachDeleteEventIpoint();
    attachTrasladarEventIpoint();
});

function attachDeleteEventIpoint() {
            $('.delete-ipoint').on('click', function(e) {
            e.preventDefault();
                let id = $(this).data('id');
                botonAceptarIpoint(id);
            });
}

function attachTrasladarEventIpoint() {
            // Usar delegación de eventos para que funcione con Livewire
            $(document).off('click', '.trasladar-ipoint').on('click', '.trasladar-ipoint', function(e) {
                console.log('Click en trasladar detectado', $(this).data('id'));
                e.preventDefault();
                e.stopPropagation();
                let id = $(this).data('id');
                console.log('ID obtenido:', id);
                if (id) {
                    console.log('Llamando a botonTrasladarIpoint con ID:', id);
                    botonTrasladarIpoint(id);
                } else {
                    console.error('No se encontró el ID del cliente');
                }
            });
            console.log('Evento trasladar adjuntado');
}

        function botonAceptarIpoint(id) {
            Swal.fire({
                title: "¿Estás seguro que quieres eliminar este cliente iPoint?",
                html: "<p>Esta acción es irreversible.</p>",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.when(getDeleteIpoint(id)).then(function(data) {
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

        function getDeleteIpoint(id) {
            const url = '{{ route("clientes.delete") }}';
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                    'table': 'clients_ipoint'
                },
                dataType: "json"
            });
        }

        function botonTrasladarIpoint(id) {
            console.log('botonTrasladarIpoint llamado con ID:', id);
            if (!id) {
                console.error('ID no válido para trasladar');
                Toast.fire({
                    icon: "error",
                    title: "Error: ID de cliente no válido"
                });
                return;
            }

            console.log('Mostrando SweetAlert para confirmar traslado');
            Swal.fire({
                title: "¿Trasladar cliente a clients?",
                html: "<p>Este cliente será copiado a la tabla clients. El cliente original permanecerá en clients_ipoint.</p>",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Trasladar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                console.log('Resultado de SweetAlert:', result);
                if (result.isConfirmed) {
                    console.log('Usuario confirmó, iniciando traslado...');
                    // Mostrar loading
                    Swal.fire({
                        title: 'Trasladando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    console.log('Llamando a getTrasladarIpoint con ID:', id);
                    $.when(getTrasladarIpoint(id)).then(function(data) {
                        console.log('Respuesta recibida:', data);
                        Swal.close();
                        if (data && data.error) {
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje || "Error al trasladar el cliente"
                            });
                        } else if (data) {
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje || "Cliente trasladado correctamente"
                            });
                        } else {
                            Toast.fire({
                                icon: "error",
                                title: "No se recibió respuesta del servidor"
                            });
                        }
                    }).fail(function(xhr) {
                        Swal.close();
                        console.error('Error en trasladar:', xhr);
                        Toast.fire({
                            icon: "error",
                            title: "Error al comunicarse con el servidor"
                        });
                    });
                }
            });
        }

        function getTrasladarIpoint(id) {
            const url = '{{ route("clientes.trasladar") }}';
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            console.log('getTrasladarIpoint - URL:', url);
            console.log('getTrasladarIpoint - ID:', id);
            console.log('getTrasladarIpoint - CSRF Token:', csrfToken ? 'Presente' : 'FALTANTE');

            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                data: {
                    'id': id
                },
                dataType: "json",
                beforeSend: function() {
                    console.log('AJAX: Enviando petición...');
                }
            }).fail(function(xhr, status, error) {
                console.error('AJAX FAIL - Error en AJAX trasladar:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                Toast.fire({
                    icon: "error",
                    title: "Error al trasladar el cliente. Revisa la consola para más detalles."
                });
            });
        }
    </script>
@endsection

