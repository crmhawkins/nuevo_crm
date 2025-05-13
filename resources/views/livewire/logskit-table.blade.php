<div>
    <div wire:loading.delay>
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-white bg-opacity-75" style="z-index: 1050;">
            <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    </div>

    <div class="filtros row mb-4">
        <div class="col-md-8 col-sm-12">
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
                <div class="">
                    <label for="">Estados</label>
                    <select wire:model="estadoSeleccionado" class="form-select">
                        <option value="">Todo los estados</option> {{-- <-- Esta línea ya sirve como "Todos" --}}
                        @foreach ($estados as $estado)
                            <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                        @endforeach
                    </select>

                </div>
            </div>
        </div>
    </div>

    @if(count($columnasEstados))
        <div class="mb-4 d-flex align-items-start gap-2">
            {{-- Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="dropdownColumnas" data-bs-toggle="dropdown" aria-expanded="false">
                    Columnas ({{ count($columnasEstados) - count($columnasOcultas) }})
                </button>
                <div class="dropdown-menu p-3" aria-labelledby="dropdownColumnas" data-bs-auto-close="false" style="max-height: 300px; overflow-y: auto; min-width: 280px;">
                    <div class="d-flex justify-between align-items-center mb-2 flex-column">
                        <button class="btn btn-outline-secondary btn-sm w-100" wire:click="invertirColumnas" type="button">
                            Invertir selección
                        </button>
                        <span class="fw-bold ms-2">Selecciona las columnas</span>
                    </div>

                    @foreach($columnasEstados as $estado)
                        <div class="form-check mb-1">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                wire:model="columnasSeleccionadasTemp"
                                value="{{ $estado }}"
                                id="col_temp_{{ $loop->index }}"
                                @checked(in_array($estado, $columnasSeleccionadasTemp))
                            >


                            <label class="form-check-label" for="col_temp_{{ $loop->index }}">
                                {{ $estado }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Botón aplicar --}}
            <div>
                <button class="btn btn-sm btn-primary" wire:click="aplicarColumnasSeleccionadas">
                    Aplicar columnas
                </button>
            </div>
            <div class="form-check mb-2">
                <input
                    class="form-check-input"
                    type="checkbox"
                    wire:model="mostrarSoloConValor"
                    id="mostrarSoloConValor"
                >
                <label class="form-check-label" for="mostrarSoloConValor">
                    Mostrar solo si tiene valor
                </label>
            </div>
        </div>
    @endif




    @if($logsPivotados && $logsPivotados->count())
        <div class="alert alert-info mb-3 fs-4 text-center">
            <strong>Total Importe:</strong> {{$importeTotal}} €
        </div>
    @endif

    {{-- Tabla --}}
    @if ($logsPivotados && count($logsPivotados))
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 270px">ACCIÓN</th>
                        <th style="width: 270px">CLIENTE</th>
                        <th style="width: 100px">KD</th>
                        <th style="width: 100px">SERVICIO</th>

                        <th style="cursor: pointer;width: 100px" wire:click="sortBy('importe')">
                            IMPORTE
                            @if($sortColumn === 'importe')
                                @if($sortDirection === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            @endif
                    </th>

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
                        <th style="width: 100px">SASAK</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logsPivotados as $row)
                        <tr>
                            <td>
                                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#crearAlertaModal" data-reference-id="{{ $row['id'] }}">
                                    Crear alerta
                                </button>
                            </td>
                            <td style="min-width: 200px;" >{{ $row['cliente'] }}</td>
                            <td>{{ $row['KD'] }}</td>
                            <td>{{ $row['servicio'] }}</td>

                            <td>{{ number_format($row['importe'], 2, ',', '.') }} €</td>
                            @foreach($columnasEstados as $estado)
                                @if(!in_array($estado, $columnasOcultas))
                                @php
                                    // Asumiendo que cada fila tiene su 'reference_id' original
                                    $refId = array_search($row, $logsPivotados->all()); // no es seguro
                                    $fecha = null;

                                    foreach ($fechasEditables as $ref => $fechasPorEstado) {
                                        if (
                                            ($fechasPorEstado[$estado] ?? null) === ($row[$estado] ?? null)
                                            && ($row[$estado] ?? null) !== null
                                        ) {
                                            $refId = $ref;
                                            $fecha = $fechasPorEstado[$estado];
                                            break;
                                        }
                                    }
                                @endphp


                                <td style="width: 140px; white-space: nowrap; text-align: center;">
                                        <input type="date"
                                            class="form-control form-control-sm new-control"
                                            value="{{ $fecha ? \Carbon\Carbon::parse($row[$estado])->format('Y-m-d') : '' }}"
                                            wire:change="$emit('cambiarFecha', '{{ $refId }}', '{{ $estado }}', $event.target.value)">
                                </td>

                                @endif
                            @endforeach
                            <td style="width: 140px; white-space: nowrap; text-align: center;">
                                <input type="date"
                                       wire:model.lazy="fechasSasak.{{ $row['id'] }}.sasak"
                                       class="form-control form-control-sm new-control">
                            </td>

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
    <div class="modal fade" id="crearAlertaModal" tabindex="-1" aria-labelledby="crearAlertaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <form method="POST" action="{{ route('alerts.create') }}">
            @csrf
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="crearAlertaModalLabel">Crear alerta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="reference_id" id="referenceIdInput">

                <div class="mb-3">
                  <label for="activation_date" class="form-label">Fecha de activación</label>
                  <input type="date" name="activation_date" class="form-control" required>
                  <small class="form-text text-muted">Se activará a las 00:00 del día elegido.</small>
                </div>

                <div class="mb-3">
                  <label for="description" class="form-label">Descripción</label>
                  <textarea name="description" class="form-control" rows="3" required></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Crear alerta</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
    </div>
</div>
<style>
    .table-responsive>.table>:not(caption)>*>* {
        padding: 0 !important;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding-right: 10px !important;
        padding-left: 10px !important;
    }
    .new-control {
        max-width: 130px;
        margin: 0 auto;
        padding: 2px 4px;
        font-size: 0.875rem;
        text-align: center;
    }

</style>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const crearAlertaModal = document.getElementById('crearAlertaModal');
    crearAlertaModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const referenceId = button.getAttribute('data-reference-id');
      const inputReference = crearAlertaModal.querySelector('#referenceIdInput');
      inputReference.value = referenceId;
    });
  </script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('cambiarFecha', (referenceId, estado, nuevaFecha) => {
            Livewire.emit('llamarActualizarFecha', referenceId, estado, nuevaFecha);
        });

        window.addEventListener('notificacion', event => {
            const tipo = event.detail.tipo;
            const mensaje = event.detail.mensaje;

            Swal.fire({
                icon: tipo,
                title: tipo === 'success' ? '¡Éxito!' : '¡Error!',
                text: mensaje,
                timer: 2500,
                showConfirmButton: false,
            });
        });
    });
</script>
@endsection

