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
            </div>
        </div>
    </div>

    @if(count($columnasEstados))
        <div class="dropdown mb-4">
            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownColumnas" data-bs-toggle="dropdown" aria-expanded="false">
                Columnas ({{ count($columnasEstados) - count($columnasOcultas) }})
            </button>
            <ul class="dropdown-menu p-3" aria-labelledby="dropdownColumnas" data-bs-auto-close="false" style="max-height: 300px; overflow-y: auto;">
                <li class="fw-bold mb-2">Ocultar/Mostrar columnas</li>
                <li class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" wire:click="invertirColumnas">
                        Invertir selección
                    </button>
                </li>
                @foreach($columnasEstados as $estado)
                    <li>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:click="toggleColumna('{{ $estado }}')" id="col_{{ $loop->index }}" {{ in_array($estado, $columnasOcultas) ? '' : 'checked' }}>
                            <label class="form-check-label" for="col_{{ $loop->index }}">
                                {{ $estado }}
                            </label>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tabla --}}
    @if ($logsPivotados && count($logsPivotados))
    {{dd($logsPivotados)}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>CLIENTE</th>
                        <th>KD</th>
                        <th>SERVICIO</th>
                        @foreach($columnasEstados as $estado)
                            @if(!in_array($estado, $columnasOcultas))
                                <th style="cursor: pointer;" wire:click="ordenarPorEstado('{{ $estado }}')">
                                    {{ $estado }}
                                    @if($ordenEstado === $estado)
                                        @if($ordenDireccion === 'asc')
                                            ↑
                                        @else
                                            ↓
                                        @endif
                                    @endif
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($logsPivotados as $row)
                        <tr>
                            <td style="min-width: 200px;" >{{ $row['cliente'] }}</td>
                            <td>{{ $row['KD'] }}</td>
                            <td>{{ $row['servicio'] }}</td>
                            @foreach($columnasEstados as $estado)
                                @if(!in_array($estado, $columnasOcultas))
                                    <td style="min-width: 140px; white-space: nowrap;">
                                        {{ $row[$estado] ?? '' }}
                                    </td>
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
