<div>
    <div class="filtros row mb-4">
        <div class="col-md-3 col-sm-12">
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
                <div class="w-50">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-9 col-sm-12">
            <div class="flex flex-row justify-end">

                <div class="mr-3 w-50">
                    <label for="">Año</label>
                    <select wire:model="selectedYear" class="form-select">
                        <option value=""> Año </option>
                        @for ($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Importe min</label>
                    <input wire:model="minImporte" type="number" step="0.01" class="form-control" placeholder="Importe mínimo">
                </div>
                <div class="mr-3">
                    <label for="">Importe max</label>
                    <input wire:model="maxImporte" type="number" step="0.01" class="form-control" placeholder="Importe máximo">
                </div>
                <div class="mr-3">
                    <label for="">Fecha inicio</label>
                    <input wire:model="startDate" type="date" class="form-control" placeholder="Fecha de inicio">
                </div>
                <div class="mr-3">
                    <label for="">Fecha fin</label>
                    <input wire:model="endDate" type="date" class="form-control" placeholder="Fecha de fin">
                </div>
                <div class="mr-3">
                    <label for="">Gestores</label>
                    <select wire:model="selectedGestor" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Gestor --</option>
                        @foreach ($gestores as $gestor)
                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="">Estados</label>
                    <select wire:model="selectedEstados" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>
    <div class="flex flex-row justify-end">
            <button wire:click="exportToExcel" class="btn btn-success mx-2">
                Descargar Excel
            </button>
            <button wire:click="downloadFilteredInvoicesZip" class="btn btn-dark mx-2">
                Descargar facturas
            </button>
    </div>
    @if ( $invoices )
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'reference' => 'REFERENCIA',
                            'client_id' => 'CLIENTE',
                            'project_id' => 'CAMPAÑA',
                            'created_at' => 'FECHA CREACION',
                            'invoice_status_id' => 'ESTADO',
                            'total' => 'TOTAL',
                            'admin_user_id' => 'GESTOR',

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
                    @foreach ( $invoices as $invoice )
                    <tr class="clickable-row" data-href="{{route('factura.edit', $invoice->id)}}">
                        <td>{{$invoice->reference}}</td>
                            <td>{{$invoice->cliente->name ??  ($invoice->client_id ? 'Cliente borrado' : 'Sin cliente asignado')}}</td>
                            <td>{{$invoice->project->name ?? ($invoice->project_id ? 'Campaña borrada' : 'Sin campaña asignada')}}</td>
                            <td>{{Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y')}}</td>
                            <td>{{$invoice->invoiceStatus->name ?? ($invoice->invoice_status_id ? 'Estado borrado' : 'Sin estado asignado')}}</td>
                            <td>{{number_format((float)$invoice->total, 2, '.', '') }}€</td>
                            <td>{{$invoice->adminUser->name ?? ($invoice->admin_user_id ? 'Gestor borrado' : 'Sin gestor asignado')}}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('factura.show', $invoice->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Ver factura"></a>
                                <a class="" href="{{route('factura.edit', $invoice->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar factura"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"></td>
                        <th>Sumatorio:</th>
                        <td>{{number_format((float)$invoices->sum('total'), 2, '.', '') }}€</td>
                        <td colspan="2"></td>

                    </tr>
                </tfoot>
            </table>
            @if($perPage !== 'all')
                {{ $invoices->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>FACTURAS</strong></h3>
            <p class="mt-2">Pulse el boton superior para crear alguna factura.</p>
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
                title: "¿Estas seguro que quieres eliminar esta factura?",
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
            const url = '{{route("factura.delete")}}'
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
