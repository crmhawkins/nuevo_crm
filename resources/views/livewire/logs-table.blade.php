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
                <div class="mr-3">
                    <label for="">Tipo</label>
                    <select wire:model="tipo" name="" id="" class="form-select ">
                        <option value="">-- Seleccione un Tipo --</option>
                         @foreach ($tipos as $tip)
                            <option value="{{$tip->id}}">{{$tip->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    @if ( $logs )
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        @foreach ([
                            'usuario' => 'USUARIO',
                            'ayudas.cliente' => 'CLIENTE',
                            'action' => 'ACCION',
                            'description' => 'DESCRIPCION',
                            'reference_id' => 'REFERENCIA',
                            'created_at' => 'FECHA CREACION',

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
                    @foreach ( $logs as $log )
                        <tr>
                            <td>{{$log->usuario}}</td>
                            <td>{{$log->ayudas->cliente}}</td>
                            <td>{{$log->action}}</td>
                            <td>{{$log->description}}</td>
                            <td>{{$log->reference_id}}</td>
                            <td>{{$log->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all')
                {{ $logs->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>LOGS</strong></h3>
        </div>
    @endif
    {{-- {{$users}} --}}
</div>
