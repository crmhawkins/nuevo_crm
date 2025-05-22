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

    @if ($holidays->count())
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'from' => 'DÍA/S PEDIDOS',
                            'half_day' => 'MEDIO DÍA',
                            'total_days' => 'DÍAS EN TOTAL',
                            'holidays_status_id' => 'ESTADO',
                            'created_at' => 'FECHA DE PETICIÓN',

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
                </thead>
                <tbody>
                    @foreach ($holidays as $holiday)
                        @if($holiday->holidays_status_id == 3)
                            <tr data-href="{{ route('holiday.edit', $holiday->id) }}" class="table-warning" style="background-color:#FFDD9E">
                        @endif
                        @if($holiday->holidays_status_id == 1)
                            <tr data-href="{{ route('holiday.edit', $holiday->id) }}" class="table-success" style="background-color:#C3EBC4">
                        @endif
                        @if($holiday->holidays_status_id == 2)
                            <tr data-href="{{ route('holiday.edit', $holiday->id) }}" class="table-danger" style="background-color:#FBC4C4">
                        @endif
                                <td>{{ Carbon\Carbon::parse($holiday->from)->format('d/m/Y') . ' - ' .  Carbon\Carbon::parse($holiday->to)->format('d/m/Y') }}</td>
                                @if($holiday->half_day)
                                    <td><i class="fas fa-check"></i></td>
                                @else
                                    <td><i class="fas fa-times"></i></td>
                                @endif
                                <td>{{ $holiday->total_days }}</td>

                                @if($holiday->holidays_status_id == 1)
                                    <td>Aceptada</td>
                                @elseif($holiday->holidays_status_id == 2)
                                    <td>Denegada</td>
                                @elseif($holiday->holidays_status_id == 3)
                                    <td>Pendiente</td>
                                @endif
                                <td>{{ Carbon\Carbon::parse($holiday->created_at)->format('d/m/Y H:i:s') }}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $holidays->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>Vacaciones</strong></h3>
        </div>
    @endif
</div>


@section('scripts')

@endsection
