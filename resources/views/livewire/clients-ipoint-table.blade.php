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
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 180px" onclick="event.stopPropagation();">
                                <a class="" href="{{ route('clientes.show', $client->id) }}"><img src="{{ asset('assets/icons/eye.svg') }}" alt="Mostrar usuario"></a>
                                <a class="" href="{{ route('clientes.edit', $client->id) }}"><img src="{{ asset('assets/icons/edit.svg') }}" alt="Mostrar usuario"></a>
                                <form class="form-trasladar-ipoint" action="{{ route('clientes.trasladar') }}" method="POST" style="display: inline;" onclick="event.stopPropagation(); event.stopImmediatePropagation();" data-id="{{ $client->id }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $client->id }}">
                                    <button type="submit" class="btn btn-link p-0 form-trasladar-btn" title="Trasladar a clients" style="border: none; background: none; padding: 0;" onclick="event.stopPropagation(); event.stopImmediatePropagation();">
                                        <i class="fas fa-arrow-right text-primary"></i>
                                    </button>
                                </form>
                                <form class="form-delete-ipoint" action="{{ route('clientes.delete') }}" method="POST" style="display: inline;" onclick="event.stopPropagation(); event.stopImmediatePropagation();" data-id="{{ $client->id }}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $client->id }}">
                                    <input type="hidden" name="table" value="clients_ipoint">
                                    <button type="submit" class="btn btn-link p-0" title="Eliminar cliente" style="border: none; background: none; padding: 0;" onclick="event.stopPropagation(); event.stopImmediatePropagation();">
                                        <img src="{{ asset('assets/icons/trash.svg') }}" alt="Eliminar usuario">
                                    </button>
                                </form>
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
        // Manejar formularios de borrar con AJAX
        function attachDeleteFormIpoint() {
            $(document).off('submit', '.form-delete-ipoint').on('submit', '.form-delete-ipoint', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                const form = $(this);
                const id = form.data('id');

                if (!confirm('¿Estás seguro que quieres eliminar este cliente iPoint? Esta acción es irreversible.')) {
                    return false;
                }

                const url = form.attr('action');
                const formData = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response && response.error) {
                            Toast.fire({
                                icon: "error",
                                title: response.mensaje || "Error al eliminar el cliente"
                            });
                        } else if (response) {
                            Toast.fire({
                                icon: "success",
                                title: response.mensaje || "Cliente eliminado correctamente"
                            });
                            // Refrescar el componente Livewire sin recargar la página
                            setTimeout(function() {
                                // Buscar el componente Livewire dentro del div principal
                                const componentDiv = document.querySelector('div[wire\\:id]');
                                if (componentDiv && typeof Livewire !== 'undefined') {
                                    const wireId = componentDiv.getAttribute('wire:id');
                                    if (wireId) {
                                        try {
                                            const component = Livewire.find(wireId);
                                            if (component && component.call) {
                                                component.call('refresh');
                                            } else {
                                                // Si no funciona, recargar la página
                                                window.location.reload();
                                            }
                                        } catch(e) {
                                            // Si falla, recargar la página
                                            window.location.reload();
                                        }
                                    } else {
                                        window.location.reload();
                                    }
                                } else {
                                    // Fallback: recargar la página
                                    window.location.reload();
                                }
                            }, 500);
                        }
                    },
                    error: function(xhr) {
                        let mensaje = "Error al eliminar el cliente";
                        if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                            mensaje = xhr.responseJSON.mensaje;
                        } else if (xhr.responseText) {
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                if (errorData.mensaje) {
                                    mensaje = errorData.mensaje;
                                }
                            } catch(e) {}
                        }
                        Toast.fire({
                            icon: "error",
                            title: mensaje
                        });
                    }
                });

                return false;
            });
        }


        // Manejar formularios de trasladar con AJAX
        function attachTrasladarFormIpoint() {
            $(document).off('submit', '.form-trasladar-ipoint').on('submit', '.form-trasladar-ipoint', function(e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                const form = $(this);
                const id = form.data('id');

                if (!confirm('¿Trasladar cliente a clients? Este cliente será copiado a la tabla clients. El cliente original permanecerá en clients_ipoint.')) {
                    return false;
                }

                const url = form.attr('action');
                const formData = form.serialize();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: formData,
                    dataType: "json",
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function(response) {
                        if (response && response.error) {
                            Toast.fire({
                                icon: "error",
                                title: response.mensaje || "Error al trasladar el cliente"
                            });
                        } else if (response) {
                            Toast.fire({
                                icon: "success",
                                title: response.mensaje || "Cliente trasladado correctamente"
                            });
                        }
                    },
                    error: function(xhr) {
                        let mensaje = "Error al trasladar el cliente";
                        if (xhr.responseJSON && xhr.responseJSON.mensaje) {
                            mensaje = xhr.responseJSON.mensaje;
                        } else if (xhr.responseText) {
                            try {
                                const errorData = JSON.parse(xhr.responseText);
                                if (errorData.mensaje) {
                                    mensaje = errorData.mensaje;
                                }
                            } catch(e) {}
                        }
                        Toast.fire({
                            icon: "error",
                            title: mensaje
                        });
                    }
                });

                return false;
            });
        }

        // Ejecutar cuando el DOM esté listo
        $(document).ready(function() {
            attachDeleteFormIpoint();
            attachTrasladarFormIpoint();
        });

        // Ejecutar cuando Livewire carga/actualiza
        document.addEventListener('livewire:load', () => {
            attachDeleteFormIpoint();
            attachTrasladarFormIpoint();
        });

        document.addEventListener('livewire:update', () => {
            attachDeleteFormIpoint();
            attachTrasladarFormIpoint();
        });
    </script>
@endsection

