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
    </div>

    @if ($gastos->count())
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'company_name' => 'EMPRESA',
                            'amount' => 'CANTIDAD',
                            'created_at' => 'FECHA DE RECEPCION',
                            'message' => 'DETALLE',
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
                        <th class="text-center" style="font-size:0.75rem">DOCUMENTO</th>
                        <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ($gastos as $gasto)
                        <tr class="clickable-row" data-href="{{route('gasto-sin-clasificar.edit', $gasto->id)}}">
                            <td>{{$gasto->company_name}}</td>
                            <td>{{ number_format($gasto->amount, 2) }}€</td>
                            <td>{{ \Carbon\Carbon::parse($gasto->created_at)->format('d/m/Y') }}</td>
                            <td>{{$gasto->message}}</td>
                            <td>
                                @if (isset($gasto->documents))
                                <a href="{{ asset('storage/' . $gasto->documents) }}" target="_blank">Ver Documento</a>
                                @endif
                            </td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('gasto-sin-clasificar.edit', $gasto->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar gasto"></a>
                                <a class="delete" data-id="{{$gasto->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar gasto"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $gastos->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>Gastos Sin Clasificar</strong></h3>
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
            const url = '{{route("gastos-sin-clasificar.delete")}}';
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
