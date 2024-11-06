@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection
<div>
    <div class="filtros row mb-4">
        <div class="col-md-12 col-sm-12">
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
    </div>
    @if ( $orders )
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'reference' => 'PRESUPUESTO',
                            'proveedorNombre' => 'PROVEEDOR',
                            'title' => 'CONCEPTO',
                            'clienteNombre' => 'CLIENTE',
                            'date_aceptado' => 'FECHA ACEPTADA',
                            'quantity' => 'IMPORTE',
                            'gestorNombre' => 'GESTOR',
                            'state' => 'ESTADO',
                            'aceptado_gestor' => 'ACEPTADA',
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
                    @foreach ( $orders as $order )
                        <tr class="clickable-row" data-href="{{ route('gasto-asociado.edit', $order->id) }}">
                            <td>{{$order->reference}}</td>
                            <td>{{$order->OrdenCompra->Proveedor->name ?? 'Proveedor no definido' }}</td>
                            <td>{{$order->title }}</td>
                            <td>{{$order->OrdenCompra->cliente->name ?? 'Cliente no definido' }}</td>
                            <td>{{$order->date_aceptado ? Carbon\Carbon::parse($order->date_aceptado)->format('d/m/Y') : $order->updated_at->format('d/m/Y')}}</td>
                            <td>{{$order->quantity }}</td>
                            <td>{{$order->gestorNombre }}</td>
                            <td>{{$order->state }}</td>
                            <td style="text-align: center;">@if($order->aceptado_gestor == 1) SI @else NO @endif</td>
                            <td class="d-flex flex-row justify-evenly align-middle">
                                <a class="m-2" href="{{route('gasto-asociado.edit', $order->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar Gasto"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $orders->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>ACTAS</strong></h3>
        </div>
    @endif
</div>
@section('scripts')


    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>
        Livewire.on('toast', data => {
            Toast.fire({
                icon: data.icon,
                title: data.mensaje
            });
        });
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
                title: "¿Estas seguro que quieres eliminar este acta?",
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


    </script>
@endsection
