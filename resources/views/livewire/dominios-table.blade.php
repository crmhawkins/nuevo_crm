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
                    <label for="">Estados</label>
                    <select wire:model="selectedEstado" name="" id="" class="form-select">
                        <option value="">-- Seleccione un estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Clientes</label>
                    <select wire:model="selectedCliente" name="" id="" class="form-select choices">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Filtros de Fecha --}}
    <div class="filtros-fecha row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">📅 Filtros de Fecha de Vencimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="fechaInicio">Fecha Inicio:</label>
                            <input wire:model="fechaInicio" type="date" class="form-control" id="fechaInicio">
                        </div>
                        <div class="col-md-4">
                            <label for="fechaFin">Fecha Fin:</label>
                            <input wire:model="fechaFin" type="date" class="form-control" id="fechaFin">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button wire:click="limpiarFiltrosFecha" class="btn btn-outline-secondary me-2">
                                🗑️ Limpiar Fechas
                            </button>
                            @if($fechaInicio || $fechaFin)
                                <span class="badge bg-info">
                                    @if($fechaInicio && $fechaFin)
                                        {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                    @elseif($fechaInicio)
                                        Desde: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                                    @else
                                        Hasta: {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                    @endif
                                </span>
                                <span class="badge bg-secondary ms-1">
                                    {{ $dominios->count() }} resultados
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Botones de filtros rápidos --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label">Filtros Rápidos:</label>
                            <div class="btn-group" role="group">
                                <button wire:click="filtroRango30Dias" class="btn btn-outline-primary btn-sm">
                                    📅 Próximos 30 días
                                </button>
                                <button wire:click="filtroRango90Dias" class="btn btn-outline-primary btn-sm">
                                    📅 Próximos 90 días
                                </button>
                                <button wire:click="filtroVencidos" class="btn btn-outline-danger btn-sm">
                                    ⚠️ Vencidos
                                </button>
                                <button wire:click="filtroEsteMes" class="btn btn-outline-info btn-sm">
                                    📆 Este mes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- {{dd($users)}} --}}
    @if ( $dominios )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'dominio' => 'DOMINIO',
                            'client_id' => 'CLIENTE',
                            'date_start' => 'FECHA CONTRATACION',
                            'date_end' => 'FECHA VENCIMIENTO',
                            'estado_id' => 'ESTADO',
                            'precio_compra' => 'PRECIO COMPRA',
                            'precio_venta' => 'PRECIO VENTA',
                            'iban' => 'IBAN',

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
                    @foreach ( $dominios as $dominio )
                        <tr class="clickable-row" data-href="{{route('dominios.edit', $dominio->id)}}">
                            <td>{{$dominio->dominio}}</td>
                            <td>{{$dominio->cliente->name ?? 'Cliente no asociado'}}</td>
                            <td>{{ \Carbon\Carbon::parse($dominio->date_start)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($dominio->date_end)->format('d/m/Y') }}</td>
                            @if ($dominio->estado_id == 2)
                                <td><span class="badge bg-warning text-dark">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 3)
                                <td><span class="badge bg-success">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 1)
                                <td><span class="badge bg-danger">{{$dominio->estadoName->name}}</span></td>
                            @else
                                <td></td>
                            @endif
                            <td>
                                @if($dominio->precio_compra)
                                    <span class="text-success">€{{ number_format($dominio->precio_compra, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($dominio->precio_venta)
                                    <span class="text-primary">€{{ number_format($dominio->precio_venta, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($dominio->iban)
                                    <span class="text-info" title="{{ $dominio->iban }}">{{ Str::limit($dominio->iban, 20) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 150px">
                                <a class="" href="{{route('dominios.show', $dominio->id)}}" title="Ver detalles"><img src="{{asset('assets/icons/eye.svg')}}" alt="Ver dominio"></a>
                                <a class="" href="{{route('dominios.edit', $dominio->id)}}" title="Editar"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar dominio"></a>
                                @if($dominio->estado_id != 2)
                                <button class="btn btn-sm btn-outline-danger cancelar-dominio" data-id="{{$dominio->id}}" title="Cancelar dominio">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info test-js" data-id="{{$dominio->id}}" title="Test JS">
                                    <i class="bi bi-bug"></i>
                                </button>
                                @endif
                                <a class="delete" data-id="{{$dominio->id}}" href="" title="Eliminar"><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar dominio"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $dominios->links() }}
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
                title: "¿Estas seguro que quieres eliminar este dominio?",
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
            const url = '{{route("dominios.delete")}}'
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

        // Función para cancelar dominio
        $(document).on('click', '.cancelar-dominio', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('Botón cancelar clickeado en index, ID:', id);
            
            // Verificar que SweetAlert2 esté disponible
            if (typeof Swal === 'undefined') {
                alert('SweetAlert2 no está cargado en index. ID del dominio: ' + id);
                return;
            }
            
            Swal.fire({
                title: '¿Cancelar Dominio?',
                text: '¿Estás seguro de que deseas cancelar este dominio? Esta acción cambiará el estado a "Cancelado".',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Cancelando dominio',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Hacer petición AJAX
                    fetch(`/dominios/cancelar/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Cancelado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Recargar la página para mostrar el nuevo estado
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error de conexión. Inténtalo de nuevo.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });

        // Función para test de JavaScript
        $(document).on('click', '.test-js', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('Botón test clickeado, ID:', id);
            alert('JavaScript funciona en index! ID: ' + id);
        });
    </script>
@endsection
