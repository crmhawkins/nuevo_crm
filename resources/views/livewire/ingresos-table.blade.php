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
                <div class="mr-3" style="width: 100px">
                    <label for="">Banco</label>
                    <select wire:model="selectedBanco" class="form-select">
                        <option value=""> Banco </option>
                        @foreach ($Bancos as $banco)
                            <option value="{{ $banco->id }}">{{ $banco->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3" style="width: 150px">
                    <label for="">Fecha inicio</label>
                    <input wire:model="startDate" type="date" class="form-control" placeholder="Fecha de inicio">
                </div>
                <div class="mr-3" style="width: 150px">
                    <label for="">Fecha fin</label>
                    <input wire:model="endDate" type="date" class="form-control" placeholder="Fecha de fin">
                </div>
                <div class="mr-3" style="width: 100px">
                    <label for="">Año</label>
                    <select wire:model="selectedYear" class="form-select">
                        <option value=""> Año </option>
                        @for ($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($ingresos->count())
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'bank_id' => 'BANCO',
                            'title' => 'TITULO',
                            'quantity' => 'CANTIDAD',
                            'invoice_id' => 'FACTURA ASOCIADA',
                            'date' => 'FECHA',

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
                    @foreach ($ingresos as $ingreso)
                    <tr class="clickable-row" data-href="{{route('ingreso.edit', $ingreso->id)}}">
                        <td>{{$ingreso->bankAccount->name ?? ($ingreso->bank_id ? 'Banco Eliminado' : 'Banco No Asignado')}}</td>
                            <td>{{$ingreso->title}}</td>
                            <td>{{ number_format($ingreso->quantity, 2) }}€</td>
                            @if (isset($ingreso->invoice_id))
                            <td><a href="{{route('factura.edit', $ingreso->invoice_id)}}">{{$ingreso->invoice_id}}</a> </td>
                            @else
                            <td></td>
                            @endif
                            <td>{{ \Carbon\Carbon::parse($ingreso->date)->format('d/m/Y') }}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('ingreso.edit', $ingreso->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar gasto"></a>
                                <a class="delete" data-id="{{$ingreso->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar gasto"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="1"></td>
                        <th>Sumatorio:</th>
                        <td>{{number_format((float)$ingresos->sum('quantity'), 2, '.', '') }}€</td>
                        <td colspan="3"></td>

                    </tr>
                </tfoot>
            </table>
            @if($perPage !== 'all')
                {{ $ingresos->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>Ingresos</strong></h3>
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
                title: "¿Estas seguro que quieres eliminar este gasto?",
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
            const url = '{{route("ingreso.delete")}}';
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
