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
                    <label for="">Usuario</label>
                    <select wire:model="usuario" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Tipo --</option>
                         @foreach ($usuarios as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @if ( $logsPivotados )
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        <th class="border px-4 py-2">Cliente</th>
                        <th class="border px-4 py-2">Servicio</th>
                        @foreach($columnasEstados as $estado)
                            <th class="border px-4 py-2">{{ $estado }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($logsPivotados as $row)
                        <tr>
                            <td class="border px-4 py-2">{{ $row['cliente'] }}</td>
                            <td class="border px-4 py-2">{{ $row['servicio'] }}</td>
                            @foreach($columnasEstados as $estado)
                                <td class="border px-4 py-2">{{ $row[$estado] ?? '' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $logsPivotados->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>LOGS</strong></h3>
        </div>
    @endif
    {{-- {{$users}} --}}
</div>
