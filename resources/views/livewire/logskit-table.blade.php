<div>
    <div class="filtros row mb-4">
        <div class="col-md-3 col-sm-12">
            <div class="d-flex flex-row justify-start gap-3">
                <div>
                    <label for="">Nº</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="flex-fill">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
        <div class="col-md-9 col-sm-12">
            <div class="d-flex flex-row justify-end gap-3">
                <div class="w-25">
                    <label for="">Año</label>
                    <select wire:model="selectedYear" class="form-select">
                        <option value=""> Año </option>
                        @for ($year = date('Y'); $year >= 2000; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                <div class="w-50">
                    <label for="">Usuario</label>
                    <select wire:model="usuario" class="form-select">
                        <option value="">-- Seleccione un Usuario --</option>
                        @foreach ($usuarios as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-data="{ open: false, columnas: @entangle('columnasOcultas').defer }" class="dropdown mb-4 position-relative">
                    <button @click="open = !open" type="button" class="btn btn-outline-secondary dropdown-toggle">
                        Columnas ({{ count($columnasEstados) - count($columnasOcultas) }})
                    </button>

                    <div
                        x-show="open"
                        @click.away="open = false"
                        class="dropdown-menu show p-3 shadow position-absolute"
                        style="display: block; max-height: 300px; overflow-y: auto; z-index: 999;"
                    >
                        <ul class="list-unstyled">
                            <li class="fw-bold mb-2">Ocultar/Mostrar columnas</li>
                            @foreach($columnasEstados as $estado)
                                <li>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            :checked="!columnas.includes('{{ $estado }}')"
                                            @change="columnas.includes('{{ $estado }}') ? columnas.splice(columnas.indexOf('{{ $estado }}'), 1) : columnas.push('{{ $estado }}')"
                                            id="col_{{ $loop->index }}"
                                        >
                                        <label class="form-check-label" for="col_{{ $loop->index }}">
                                            {{ $estado }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-3 text-end">
                            <button class="btn btn-sm btn-primary" @click="$wire.aplicarColumnas(columnas); open = false">Aplicar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Tabla --}}
    @if ($logsPivotados && count($logsPivotados))
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Cliente</th>
                        <th>Servicio</th>
                        @foreach($columnasEstados as $estado)
                            @if(!in_array($estado, $columnasOcultas))
                                <th>{{ $estado }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($logsPivotados as $row)
                        <tr>
                            <td>{{ $row['cliente'] }}</td>
                            <td>{{ $row['servicio'] }}</td>
                            @foreach($columnasEstados as $estado)
                                @if(!in_array($estado, $columnasOcultas))
                                    <td style="min-height: 100px;">{{ $row[$estado] ?? '' }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($perPage !== 'all')
                <div class="mt-3">
                    {{ $logsPivotados->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>LOGS</strong></h3>
        </div>
    @endif
</div>
