@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<style>
    .dominio-row:hover {
        background-color: #f8f9fa !important;
        transition: background-color 0.2s ease;
    }

    .dominio-row {
        transition: background-color 0.2s ease;
    }

    .dominio-checkbox {
        cursor: pointer;
        transform: scale(1.2);
    }
</style>
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
                <div class="mr-3">
                    <label for="">Estados</label>
                    <select wire:model="selectedEstado" name="" id="" class="form-select">
                        <option value="">-- Seleccione un estado --</option>
                        @foreach ($estados as $estado)
                            <option value="{{$estado->id}}">{{$estado->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Clientes</label>
                    <select wire:model="selectedCliente" name="" id="" class="form-select choices">
                        <option value="">-- Seleccione un cliente --</option>
                        @foreach ($clientes as $cliente)
                            <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mr-3">
                    <label for="">Facturación {{ date('Y') }}</label>
                    <select wire:model="filtroFacturacion" name="" id="" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="facturado">✅ Facturado</option>
                        <option value="pendiente">⏳ Pendiente</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros de Fecha --}}
    <div class="filtros-fecha row mb-4">
        <div class="col-12">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">📅 Filtros de Fecha de Vencimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="fechaInicio" class="form-label fw-bold">Fecha Inicio:</label>
                            <input wire:model="fechaInicio" type="date" class="form-control" id="fechaInicio">
                        </div>
                        <div class="col-md-4">
                            <label for="fechaFin" class="form-label fw-bold">Fecha Fin:</label>
                            <input wire:model="fechaFin" type="date" class="form-control" id="fechaFin">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button wire:click="limpiarFiltrosFecha" class="btn btn-outline-secondary me-2">
                                🗑️ Limpiar Fechas
                            </button>
                            <button wire:click="testFiltros" class="btn btn-outline-info me-2">
                                🔍 Test Filtros
                            </button>
                            @if($fechaInicio || $fechaFin)
                                <span class="badge bg-info">
                                    @if($fechaInicio && $fechaFin)
                                        {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                    @elseif($fechaInicio)
                                        Desde: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                                    @else
                                        Hasta: {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                    @endif
                                </span>
                                <span class="badge bg-secondary ms-1">
                                    {{ $dominios->count() }} resultados
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Botones de filtros rápidos --}}
                    <div class="row mt-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">Filtros Rápidos:</label>
                            <div class="btn-group" role="group">
                                <button wire:click="filtroRango30Dias" class="btn btn-outline-primary btn-sm">
                                    📅 Próximos 30 días
                                </button>
                                <button wire:click="filtroRango90Dias" class="btn btn-outline-primary btn-sm">
                                    📅 Próximos 90 días
                                </button>
                                <button wire:click="filtroVencidos" class="btn btn-outline-danger btn-sm">
                                    ⚠️ Vencidos
                                </button>
                                <button wire:click="filtroEsteMes" class="btn btn-outline-info btn-sm">
                                    📆 Este mes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtro de Dominios Sin Facturas --}}
    <div class="filtros-sin-facturas row mb-4">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">📄 Filtro de Dominios Sin Facturas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="añoSinFacturas" class="form-label fw-bold">Año para filtrar:</label>
                            <input wire:model="añoSinFacturas" type="number" class="form-control" id="añoSinFacturas"
                                   placeholder="Ej: 2024" min="2020" max="{{ now()->year + 1 }}">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            @if($cargandoFiltro)
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <span class="text-muted">Procesando filtro...</span>
                                </div>
                            @elseif($filtroSinFacturas)
                                <button wire:click="desactivarFiltroSinFacturas" class="btn btn-outline-danger me-2">
                                    ❌ Desactivar Filtro
                                </button>
                                <span class="badge bg-warning text-dark">
                                    Sin facturas en {{ $añoSinFacturas }}
                                </span>
                                <span class="badge bg-secondary ms-1">
                                    {{ $dominios->count() }} resultados
                                </span>
                            @else
                                <button wire:click="activarFiltroSinFacturas" class="btn btn-outline-success">
                                    ✅ Filtrar Dominios Sin Facturas
                                </button>
                            @endif
                        </div>
                    </div>
                    @if($filtroSinFacturas)
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <strong>ℹ️ Información:</strong> Se muestran solo los dominios que NO tienen facturas asociadas con la palabra "dominio" en el año {{ $añoSinFacturas }}.
                                    <br><strong>⚠️ Nota:</strong> Para optimizar el rendimiento, se muestran máximo 50 resultados.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Botón de Batch Calls --}}
    @if($dominios->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-success">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">📞 Llamadas Automáticas (Batch Calls)</h5>
                    <span class="badge bg-light text-dark" id="totalDominiosConTelefono">0 con teléfono</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-warning me-2" onclick="seleccionarSoloPendientesDominios()">
                                <i class="bi bi-exclamation-triangle"></i> Solo Pendientes
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="seleccionarTodosDominios()">
                                <i class="bi bi-check-all"></i> Todos
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="deseleccionarTodosDominios()">
                                <i class="bi bi-x-lg"></i> Ninguno
                            </button>
                            <span class="badge bg-info" id="totalSeleccionadosDominios">0 seleccionados</span>
                        </div>
                        <button type="button" class="btn btn-success" onclick="abrirModalBatchCallDominios()">
                            <i class="bi bi-telephone-outbound"></i> Enviar Batch Call a Seleccionados
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Botones de sincronización IONOS --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">🔄 Sincronización IONOS</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-outline-primary w-100" onclick="actualizarTodasLasFechasIonos()" id="btn-actualizar-todas-fechas">
                                <i class="bi bi-arrow-clockwise"></i> Actualizar TODAS las Fechas IONOS
                            </button>
                            <small class="text-muted d-block mt-1">Actualiza fechas de TODOS los dominios sin fechas IONOS</small>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-success w-100" onclick="sincronizarDominiosFaltantes()" id="btn-sincronizar-faltantes">
                                <i class="bi bi-plus-circle"></i> Sincronizar Dominios Faltantes
                            </button>
                            <small class="text-muted d-block mt-1">Añade dominios nuevos de IONOS</small>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-info w-100" onclick="probarConexionIonos()" id="btn-probar-ionos">
                                <i class="bi bi-wifi"></i> Probar Conexión IONOS
                            </button>
                            <small class="text-muted d-block mt-1">Verifica la conexión con IONOS</small>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-warning w-100" onclick="analizarDominiosFaltantes()" id="btn-analizar-faltantes">
                                <i class="bi bi-search"></i> Analizar Dominios Faltantes
                            </button>
                            <small class="text-muted d-block mt-1">Analiza qué dominios faltan</small>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100" onclick="probarComandoWeb()" id="btn-probar-comando">
                                <i class="bi bi-bug"></i> Probar Comando Web
                            </button>
                            <small class="text-muted d-block mt-1">Prueba la ejecución de comandos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- {{dd($users)}} --}}
    @if ( $dominios )
        {{-- Filtros --}}
        {{-- Tabla --}}
        <div class="table-responsive">
             <table class="table table-hover">
                <thead class="header-table">
                    <tr>
                        <th width="40" class="px-3">
                            <input type="checkbox" class="form-check-input" id="checkboxSelectAllDominios" onclick="toggleSelectAllDominios(this)" checked>
                        </th>
                        @foreach ([
                            'dominio' => 'DOMINIO',
                            'client_id' => 'CLIENTE',
                            'created_at' => 'FECHA INSERCIÓN',
       'fecha_activacion_ionos' => 'FECHA ACTIVACION IONOS',
       'fecha_renovacion_ionos' => 'FECHA RENOVACION IONOS',
       'fecha_registro_calculada' => 'FECHA REGISTRO CALCULADA',
                            'estado_id' => 'ESTADO',
                            'precio_compra' => 'PRECIO COMPRA',
                            'precio_venta' => 'PRECIO VENTA',
                            'iban' => 'IBAN',

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
                        <th class="text-center" style="font-size:0.75rem">FACTURA {{ date('Y') }}</th>
                        <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                </thead>
                <tbody>
                    @foreach ( $dominios as $dominio )
                        @php
                            $añoActualCheck = date('Y');
                            // Verificar si tiene factura del año actual (igual que el badge)
                            $tieneFacturaCheck = \DB::table('invoices')
                                ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
                                ->where('invoices.client_id', $dominio->client_id)
                                ->where(function($query) use ($añoActualCheck) {
                                    $query->where('invoice_concepts.title', 'like', '%' . $añoActualCheck . '%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%' . $añoActualCheck . '%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.title', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovación%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%renovacion%')
                                          ->orWhere('invoice_concepts.title', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.title', 'like', '%DOMINIO%')
                                          ->orWhere('invoice_concepts.title', 'like', '%anual%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%Dominio%')
                                          ->orWhere('invoice_concepts.concept', 'like', '%DOMINIO%');
                                })
                                ->exists();

                            // Solo marcar por defecto si NO tiene factura (PENDIENTE)
                            $checkedPorDefecto = !$tieneFacturaCheck;
                        @endphp
                        <tr class="dominio-row">
                            <td onclick="event.stopPropagation()">
                                @if($dominio->cliente && $dominio->cliente->phone)
                                    <input type="checkbox" class="form-check-input dominio-checkbox"
                                           data-cliente-id="{{ $dominio->cliente->id }}"
                                           data-cliente-nombre="{{ $dominio->cliente->name }} {{ $dominio->cliente->primerApellido ?? '' }} {{ $dominio->cliente->segundoApellido ?? '' }}"
                                           data-cliente-telefono="{{ $dominio->cliente->phone }}"
                                           data-dominio="{{ $dominio->dominio }}"
                                           onchange="actualizarContadorDominios()"
                                           onclick="event.stopPropagation()"
                                           {{ $checkedPorDefecto ? 'checked' : '' }}>
                                @endif
                            </td>
                            <td>{{$dominio->dominio}}</td>
                            <td>{{$dominio->cliente->name ?? 'Cliente no asociado'}}</td>
                            <td>{{ \Carbon\Carbon::parse($dominio->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($dominio->fecha_activacion_ionos)
                                    <span class="text-success">{{ $dominio->fecha_activacion_ionos_formateada }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
       <td>
           @if($dominio->fecha_renovacion_ionos)
               <span class="text-primary">{{ $dominio->fecha_renovacion_ionos_formateada }}</span>
           @else
               <span class="text-muted">-</span>
           @endif
       </td>
       <td>
           @if($dominio->fecha_registro_calculada)
               <span class="text-success">{{ $dominio->fecha_registro_calculada_formateada }}</span>
           @else
               <span class="text-muted">-</span>
           @endif
       </td>
                            @if ($dominio->estado_id == 2)
                                <td><span class="badge bg-warning text-dark">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 3)
                                <td><span class="badge bg-success">{{$dominio->estadoName->name}}</span></td>
                            @elseif($dominio->estado_id == 1)
                                <td><span class="badge bg-danger">{{$dominio->estadoName->name}}</span></td>
                            @else
                                <td></td>
                            @endif
                            <td>
                                @if($dominio->precio_compra)
                                    <span class="text-success">€{{ number_format($dominio->precio_compra, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($dominio->precio_venta)
                                    <span class="text-primary">€{{ number_format($dominio->precio_venta, 2) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($dominio->iban)
                                    <span class="text-info" title="{{ $dominio->iban }}">{{ Str::limit($dominio->iban, 20) }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $añoActual = date('Y');
                                    // Buscar facturas del cliente que contengan el año actual en conceptos o títulos
                                    $tieneFactura = \DB::table('invoices')
                                        ->join('invoice_concepts', 'invoices.id', '=', 'invoice_concepts.invoice_id')
                                        ->where('invoices.client_id', $dominio->client_id)
                                        ->where(function($query) use ($añoActual) {
                                            $query->where('invoice_concepts.title', 'like', '%' . $añoActual . '%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%' . $añoActual . '%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%renovación%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%renovacion%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%renovación%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%renovacion%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%dominio%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%Dominio%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%DOMINIO%')
                                                  ->orWhere('invoice_concepts.title', 'like', '%anual%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%Dominio%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%DOMINIO%');
                                        })
                                        ->exists();
                                @endphp
                                @if($tieneFactura)
                                    <span class="badge bg-success" title="Tiene factura del año {{ $añoActual }}">
                                        <i class="bi bi-check-circle me-1"></i>FACTURADO
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark" title="Sin factura del año {{ $añoActual }}">
                                        <i class="bi bi-exclamation-triangle me-1"></i>PENDIENTE
                                    </span>
                                @endif
                            </td>
                            <td class="text-center" style="min-width: 200px">
                                <div class="btn-group" role="group">
                                    <a class="btn btn-sm btn-outline-primary" href="{{route('dominios.show', $dominio->id)}}" title="Ver detalles">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-secondary" href="{{route('dominios.edit', $dominio->id)}}" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($dominio->estado_id != 2)
                                    <button class="btn btn-sm btn-outline-danger cancelar-dominio" data-id="{{$dominio->id}}" title="Cancelar dominio">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    @endif
                                    @if($dominio->fecha_renovacion_ionos && !$dominio->fecha_registro_calculada)
                                    <button class="btn btn-sm btn-success" title="Calcular fecha de registro" onclick="calcularFechaRegistro({{$dominio->id}})">
                                        <i class="bi bi-calculator"></i>
                                    </button>
                                    @elseif($dominio->fecha_registro_calculada)
                                    <span class="btn btn-sm btn-success disabled" title="Fecha calculada: {{ $dominio->fecha_registro_calculada_formateada }}">
                                        <i class="bi bi-check-circle"></i>
                                    </span>
                                    @endif
                                    <a class="btn btn-sm btn-outline-danger delete" data-id="{{$dominio->id}}" href="" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($perPage !== 'all' && method_exists($dominios, 'links'))
                {{ $dominios->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>DOMINIOS</strong></h3>
        </div>
    @endif
</div>

<!-- Modal de Batch Call para Dominios -->
<div class="modal fade" id="batchCallDominiosModal" tabindex="-1" aria-labelledby="batchCallDominiosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchCallDominiosModalLabel">
                    <i class="bi bi-telephone-outbound"></i> Configurar Batch Call a ElevenLabs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBatchCallDominios">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> Se enviarán llamadas automáticas a <span id="totalLlamadasDominios" class="fw-bold">0</span> clientes con teléfono válido.
                        Los números serán procesados y validados automáticamente con IA.
                    </div>

                    <div class="mb-3">
                        <label for="callNameDominios" class="form-label">Nombre de la Campaña <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="callNameDominios" name="call_name" required
                               placeholder="Ej: Renovación Dominios 2025">
                        <small class="text-muted">Identificador de esta campaña de llamadas</small>
                    </div>

                    <div class="mb-3">
                        <label for="agentIdDominios" class="form-label">Agente <span class="text-danger">*</span></label>
                        <select class="form-select" id="agentIdDominios" name="agent_id" required disabled>
                            <option value="">Cargando agente...</option>
                        </select>
                        <small class="text-muted">Agente predeterminado: Hera Dominios (bloqueado)</small>
                    </div>

                    <div class="mb-3">
                        <label for="agentPhoneNumberIdDominios" class="form-label">Número de Teléfono <span class="text-danger">*</span></label>
                        <select class="form-select" id="agentPhoneNumberIdDominios" name="agent_phone_number_id" required disabled>
                            <option value="">Primero selecciona un agente...</option>
                        </select>
                        <small class="text-muted">Número de teléfono desde el cual se realizarán las llamadas</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-megaphone"></i> Mensaje Inicial (Configuración Fija)
                        </label>
                        <div class="alert alert-info mb-0">
                            <strong><i class="bi bi-chat-quote"></i> Mensaje que se enviará:</strong><br><br>
                            <div class="p-2 bg-white rounded">
                                <em>"Hola, soy Carolina de la Agencia Hawkins, te llamo porque próximamente caduca tu dominio <span class="text-primary fw-bold">{dominio}</span> y, antes de nada, te recuerdo que esta llamada está siendo grabada para fines de calidad y gestión administrativa. ¿Deseas renovar el dominio y mantener la web activa, o prefieres cancelarlo?"</em>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle-fill text-primary"></i> <strong>Variable automática:</strong><br>
                            - <code class="bg-light p-1 rounded">{dominio}</code> → se reemplazará con el dominio pendiente que caduca este mes (del cliente seleccionado)
                        </small>
                        <input type="hidden" id="firstMessageDominios" name="first_message" value="Hola, soy Carolina de la Agencia Hawkins, te llamo porque próximamente caduca tu dominio {dominio} y, antes de nada, te recuerdo que esta llamada está siendo grabada para fines de calidad y gestión administrativa. ¿Deseas renovar el dominio y mantener la web activa, o prefieres cancelarlo?">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Clientes a Llamar</label>
                        <div id="listaClientesBatchCallDominios" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-center text-muted">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 mb-0">Cargando clientes...</p>
                            </div>
                        </div>
                    </div>

                    <div id="alertaBatchCallDominios"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnEnviarBatchCallDominios" onclick="enviarBatchCallDominios()">
                    <i class="bi bi-send"></i> Enviar Batch Call
                </button>
            </div>
        </div>
    </div>
</div>

@section('scripts')


    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

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
                title: "¿Estas seguro que quieres eliminar este dominio?",
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
            const url = '{{route("dominios.delete")}}'
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

        // Función para cancelar dominio
        $(document).on('click', '.cancelar-dominio', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('Botón cancelar clickeado en index, ID:', id);

            // Verificar que SweetAlert2 esté disponible
            if (typeof Swal === 'undefined') {
                alert('SweetAlert2 no está cargado en index. ID del dominio: ' + id);
                return;
            }

            Swal.fire({
                title: '¿Cancelar Dominio?',
                text: '¿Estás seguro de que deseas cancelar este dominio? Esta acción cambiará el estado a "Cancelado".',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Procesando...',
                        text: 'Cancelando dominio',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Hacer petición AJAX
                    fetch(`/dominios/cancelar/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Cancelado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                           }).then(() => {
                               // Actualizar solo el componente Livewire sin recargar la página
                               @this.call('actualizarDominios');
                           });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error de conexión. Inténtalo de nuevo.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        });



       // Función global para calcular fecha de registro
       window.calcularFechaRegistro = function(id) {
           console.log('Función calcularFechaRegistro llamada con ID:', id);

           // Verificar que SweetAlert2 esté disponible
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado. ID del dominio: ' + id);
               return;
           }

           Swal.fire({
               title: '¿Calcular Fecha de Registro?',
               text: 'Se calculará la fecha de registro basándose en la fecha de renovación IONOS menos 1 año.',
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#28a745',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, calcular',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   // Mostrar loading
                   Swal.fire({
                       title: 'Calculando...',
                       text: 'Calculando fecha de registro basada en IONOS',
                       allowOutsideClick: false,
                       showConfirmButton: false,
                       willOpen: () => {
                           Swal.showLoading();
                       }
                   });

                   // Hacer petición AJAX
                   fetch(`/dominios/calcular-fecha-registro/${id}`, {
                       method: 'POST',
                       headers: {
                           'Content-Type': 'application/json',
                           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                       }
                   })
                   .then(response => response.json())
                   .then(data => {
                       if (data.success) {
                           Swal.fire({
                               title: '¡Calculado!',
                               text: data.message,
                               icon: 'success',
                               confirmButtonText: 'OK'
                   }).then(() => {
                       // Actualizar solo el componente Livewire sin recargar la página
                       @this.call('actualizarDominios');
                   });
                       } else {
                           Swal.fire({
                               title: 'Error',
                               text: data.message,
                               icon: 'error',
                               confirmButtonText: 'OK'
                           });
                       }
                   })
                   .catch(error => {
                       Swal.fire({
                           title: 'Error',
                           text: 'Error de conexión. Inténtalo de nuevo.',
                           icon: 'error',
                           confirmButtonText: 'OK'
                       });
                   });
               }
           });
       };

       // Funciones para sincronización IONOS
       function actualizarTodasLasFechasIonos() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: '¿Actualizar TODAS las Fechas IONOS?',
               text: 'Esto actualizará las fechas de IONOS para TODOS los dominios que no las tengan. Puede tomar varios minutos. ¿Continuar?',
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#0d6efd',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, actualizar TODAS',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   ejecutarComandoIonos('ionos:update-all-dates', 'Actualizando TODAS las fechas IONOS...');
               }
           });
       }

       function sincronizarIonos() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: '¿Sincronizar Fechas IONOS?',
               text: 'Esto sincronizará las fechas de IONOS para dominios existentes. ¿Continuar?',
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#0d6efd',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, sincronizar',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   ejecutarComandoIonos('ionos:sync-missing-dates', 'Sincronizando fechas IONOS...');
               }
           });
       }

       function sincronizarDominiosFaltantes() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: '¿Sincronizar Dominios Faltantes?',
               text: 'Esto añadirá dominios nuevos de IONOS a la base de datos. ¿Continuar?',
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#198754',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, sincronizar',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   ejecutarComandoIonos('ionos:sync-all-missing', 'Sincronizando dominios faltantes...');
               }
           });
       }

       function probarConexionIonos() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: 'Probando conexión...',
               text: 'Verificando conexión con IONOS',
               allowOutsideClick: false,
               showConfirmButton: false,
               willOpen: () => {
                   Swal.showLoading();
               }
           });

           fetch('/dominios/probar-ionos', {
               method: 'GET',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
               }
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   Swal.fire({
                       title: '¡Conexión Exitosa!',
                       text: data.message,
                       icon: 'success',
                       confirmButtonText: 'OK'
                   });
               } else {
                   Swal.fire({
                       title: 'Error de Conexión',
                       text: data.message,
                       icon: 'error',
                       confirmButtonText: 'OK'
                   });
               }
           })
           .catch(error => {
               Swal.fire({
                   title: 'Error',
                   text: 'Error de conexión. Inténtalo de nuevo.',
                   icon: 'error',
                   confirmButtonText: 'OK'
               });
           });
       }

       function probarComandoWeb() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: '¿Probar Comando Web?',
               text: 'Esto ejecutará un comando de prueba para verificar que la ejecución desde la web funciona correctamente.',
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#6c757d',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, probar',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   ejecutarComandoIonos('test:web-command', 'Probando comando web...');
               }
           });
       }

       function analizarDominiosFaltantes() {
           if (typeof Swal === 'undefined') {
               alert('SweetAlert2 no está cargado');
               return;
           }

           Swal.fire({
               title: '¿Analizar Dominios Faltantes?',
               text: 'Esto analizará qué dominios de IONOS no existen en la base de datos. ¿Continuar?',
               icon: 'question',
               showCancelButton: true,
               confirmButtonColor: '#fd7e14',
               cancelButtonColor: '#6c757d',
               confirmButtonText: 'Sí, analizar',
               cancelButtonText: 'Cancelar'
           }).then((result) => {
               if (result.isConfirmed) {
                   ejecutarComandoIonos('ionos:analyze-missing', 'Analizando dominios faltantes...');
               }
           });
       }

       function ejecutarComandoIonos(comando, mensaje) {
           Swal.fire({
               title: mensaje,
               text: 'Esto puede tomar varios minutos...',
               allowOutsideClick: false,
               showConfirmButton: false,
               willOpen: () => {
                   Swal.showLoading();
               }
           });

           fetch('/dominios/ejecutar-comando-ionos', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
               },
               body: JSON.stringify({
                   comando: comando
               })
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   Swal.fire({
                       title: '¡Comando Ejecutado!',
                       text: data.message,
                       icon: 'success',
                       confirmButtonText: 'OK'
                   }).then(() => {
                       // Actualizar solo el componente Livewire sin recargar la página
                       @this.call('actualizarDominios');
                   });
               } else {
                   Swal.fire({
                       title: 'Error',
                       text: data.message,
                       icon: 'error',
                       confirmButtonText: 'OK'
                   });
               }
           })
           .catch(error => {
               Swal.fire({
                   title: 'Error',
                   text: 'Error de conexión. Inténtalo de nuevo.',
                   icon: 'error',
                   confirmButtonText: 'OK'
               });
           });
       }

       // ==================== BATCH CALL FUNCTIONALITY PARA DOMINIOS ====================

       let clientesParaBatchCallDominios = [];

       // Función para abrir el modal y cargar los clientes seleccionados con checkboxes
       window.abrirModalBatchCallDominios = function() {
           // Obtener clientes seleccionados con checkbox
           const checkboxesSeleccionados = document.querySelectorAll('.dominio-checkbox:checked');

           if (checkboxesSeleccionados.length === 0) {
               Swal.fire({
                   icon: 'warning',
                   title: 'Sin Clientes',
                   text: 'Debes seleccionar al menos un cliente con teléfono para enviar batch calls.'
               });
               return;
           }

           // Preparar array de clientes únicos desde los checkboxes
           const clientesMap = {};
           const dominiosPorCliente = {}; // Para agrupar dominios por cliente

           checkboxesSeleccionados.forEach(checkbox => {
               const clienteId = checkbox.dataset.clienteId;
               const dominio = checkbox.dataset.dominio;

               // Agrupar dominios por cliente
               if (!dominiosPorCliente[clienteId]) {
                   dominiosPorCliente[clienteId] = [];
               }
               dominiosPorCliente[clienteId].push(dominio);

               // Evitar duplicados (mismo cliente puede tener múltiples dominios)
               if (!clientesMap[clienteId]) {
                   clientesMap[clienteId] = {
                       id: parseInt(clienteId),
                       nombre: checkbox.dataset.clienteNombre.trim(),
                       telefono: checkbox.dataset.clienteTelefono,
                       dominio: dominio, // Primer dominio encontrado
                       dominios: []
                   };
               }
           });

           // Asignar todos los dominios a cada cliente
           Object.keys(clientesMap).forEach(clienteId => {
               clientesMap[clienteId].dominios = dominiosPorCliente[clienteId];
               clientesMap[clienteId].total_dominios = dominiosPorCliente[clienteId].length;
           });

           clientesParaBatchCallDominios = Object.values(clientesMap);

           console.log('Clientes únicos seleccionados para batch call:', clientesParaBatchCallDominios);

           // Mostrar el modal
           const modal = new bootstrap.Modal(document.getElementById('batchCallDominiosModal'));
           modal.show();

           // Cargar los agentes (automáticamente seleccionará Hera Saliente)
           cargarAgentesDominios();

           // Mostrar clientes en el modal
           mostrarClientesBatchCallDominios(clientesParaBatchCallDominios);
       }

       // ==================== FUNCIONES DE SELECCIÓN DE CHECKBOXES DOMINIOS ====================

       // Actualizar contador de seleccionados en dominios
       window.actualizarContadorDominios = function() {
           const checkboxes = document.querySelectorAll('.dominio-checkbox:checked');

           // Contar clientes únicos (mismo cliente puede aparecer en múltiples dominios)
           const clientesUnicos = new Set();
           checkboxes.forEach(cb => {
               clientesUnicos.add(cb.dataset.clienteId);
           });

           const total = clientesUnicos.size;
           const totalSeleccionadosEl = document.getElementById('totalSeleccionadosDominios');

           if (totalSeleccionadosEl) {
               totalSeleccionadosEl.textContent = `${total} seleccionados`;
           }

           // Actualizar checkbox del header
           const checkboxAll = document.getElementById('checkboxSelectAllDominios');
           const todosLosCheckboxes = document.querySelectorAll('.dominio-checkbox');
           if (checkboxAll) {
               // Marcar el header solo si todos los checkboxes están marcados
               checkboxAll.checked = (checkboxes.length === todosLosCheckboxes.length && todosLosCheckboxes.length > 0);
               // Indeterminate si algunos están marcados pero no todos
               checkboxAll.indeterminate = (checkboxes.length > 0 && checkboxes.length < todosLosCheckboxes.length);
           }
       }

       // Toggle seleccionar/deseleccionar todos en dominios
       window.toggleSelectAllDominios = function(checkbox) {
           const checkboxes = document.querySelectorAll('.dominio-checkbox');
           checkboxes.forEach(cb => {
               cb.checked = checkbox.checked;
           });
           actualizarContadorDominios();
       }

       // Seleccionar todos en dominios
       window.seleccionarTodosDominios = function() {
           const checkboxes = document.querySelectorAll('.dominio-checkbox');
           checkboxes.forEach(cb => {
               cb.checked = true;
           });
           const checkboxAll = document.getElementById('checkboxSelectAllDominios');
           if (checkboxAll) checkboxAll.checked = true;
           actualizarContadorDominios();
       }

       // Deseleccionar todos en dominios
       window.deseleccionarTodosDominios = function() {
           const checkboxes = document.querySelectorAll('.dominio-checkbox');
           checkboxes.forEach(cb => {
               cb.checked = false;
           });
           const checkboxAll = document.getElementById('checkboxSelectAllDominios');
           if (checkboxAll) {
               checkboxAll.checked = false;
               checkboxAll.indeterminate = false;
           }
           actualizarContadorDominios();
       }

       // Seleccionar solo pendientes en dominios
       window.seleccionarSoloPendientesDominios = function() {
           // Primero deseleccionar todos
           deseleccionarTodosDominios();

           // Luego recorrer la tabla y seleccionar solo los que tienen badge PENDIENTE
           const filas = document.querySelectorAll('.dominio-row');
           filas.forEach(fila => {
               // Buscar el badge de facturación en la fila
               const badgePendiente = fila.querySelector('.badge.bg-warning.text-dark');
               const checkbox = fila.querySelector('.dominio-checkbox');

               // Si tiene badge PENDIENTE y tiene checkbox, marcarlo
               if (badgePendiente && checkbox && badgePendiente.textContent.includes('PENDIENTE')) {
                   checkbox.checked = true;
               }
           });

           actualizarContadorDominios();
       }

       // Función para cargar el agente "Hera Dominios" automáticamente
       function cargarAgentesDominios() {
           const selectAgente = document.getElementById('agentIdDominios');

           // Agent ID fijo para Hera Dominios
           const HERA_DOMINIOS_AGENT_ID = 'agent_2101k6g86xpmf9vvcshs353mc7ft';

           // Obtener el nombre del agente desde la API
           fetch('/api/elevenlabs-monitoring/batch-calls/agentes')
               .then(response => response.json())
               .then(data => {
                   if (data.success && data.data) {
                       // Buscar el agente Hera Dominios por su agent_id específico
                       const heraDominios = data.data.find(agente =>
                           agente.agent_id === HERA_DOMINIOS_AGENT_ID
                       );

                       if (heraDominios) {
                           // Seleccionar automáticamente Hera Dominios
                           selectAgente.innerHTML = `<option value="${heraDominios.agent_id}" selected>${heraDominios.name}</option>`;
                           selectAgente.disabled = true; // Mantener bloqueado

                           console.log('Agente Hera Dominios seleccionado automáticamente:', heraDominios);

                           // Cargar números de teléfono automáticamente
                           cargarPhoneNumbersDominios();
                       } else {
                           // Si no se encuentra en la lista de agentes activos, usar el ID directamente
                           selectAgente.innerHTML = `<option value="${HERA_DOMINIOS_AGENT_ID}" selected>Hera Dominios</option>`;
                           selectAgente.disabled = true;

                           console.warn('Agente Hera Dominios no encontrado en lista, usando ID directo');

                           // Intentar cargar números de teléfono de todas formas
                           cargarPhoneNumbersDominios();
                       }
                   } else {
                       console.error('Error al cargar agentes:', data.message);
                       // Usar el ID fijo de todas formas
                       selectAgente.innerHTML = `<option value="${HERA_DOMINIOS_AGENT_ID}" selected>Hera Dominios</option>`;
                       selectAgente.disabled = true;
                       cargarPhoneNumbersDominios();
                   }
               })
               .catch(error => {
                   console.error('Error:', error);
                   // Usar el ID fijo como fallback
                   selectAgente.innerHTML = `<option value="${HERA_DOMINIOS_AGENT_ID}" selected>Hera Dominios</option>`;
                   selectAgente.disabled = true;
                   cargarPhoneNumbersDominios();
               });
       }

       // Función para cargar phone numbers del agente Hera Saliente
       function cargarPhoneNumbersDominios() {
           const agentId = document.getElementById('agentIdDominios').value;
           const selectPhoneNumber = document.getElementById('agentPhoneNumberIdDominios');

           if (!agentId) {
               selectPhoneNumber.innerHTML = '<option value="">Agente no seleccionado</option>';
               return;
           }

           // Mostrar loading
           selectPhoneNumber.disabled = true;
           selectPhoneNumber.innerHTML = '<option value="">Cargando números...</option>';

           // Obtener números del agente seleccionado (Hera Saliente)
           fetch(`/api/elevenlabs-monitoring/batch-calls/agentes/${agentId}/phone-numbers`)
               .then(response => response.json())
               .then(data => {
                   if (data.success && data.data) {
                       selectPhoneNumber.innerHTML = '<option value="">Selecciona un número...</option>';

                       if (Array.isArray(data.data) && data.data.length > 0) {
                           data.data.forEach(phoneNumber => {
                               const option = document.createElement('option');
                               option.value = phoneNumber.phone_number_id;

                               // Mostrar: label + agente asignado + provider
                               let displayText = phoneNumber.label || phoneNumber.phone_number;

                               // Añadir nombre del agente asignado
                               if (phoneNumber.assigned_agent_name) {
                                   displayText += ` → ${phoneNumber.assigned_agent_name}`;
                               }

                               // Añadir provider
                               if (phoneNumber.provider) {
                                   displayText += ` (${phoneNumber.provider})`;
                               }

                               // Añadir indicador de outbound
                               if (phoneNumber.supports_outbound) {
                                   displayText += ' ✓';
                               }

                               option.textContent = displayText;
                               selectPhoneNumber.appendChild(option);
                           });
                           selectPhoneNumber.disabled = false;

                           console.log('Phone numbers cargados (todos):', data.data.length);
                       } else {
                           selectPhoneNumber.innerHTML = '<option value="">No hay números de teléfono disponibles</option>';
                           console.warn('No hay números de teléfono en la cuenta');
                       }
                   } else {
                       selectPhoneNumber.innerHTML = '<option value="">Error al cargar números</option>';
                       console.error('Error al cargar phone numbers:', data.message);
                       mostrarAlertaBatchCallDominios('warning', 'No se pudieron cargar los números de teléfono del agente.');
                   }
               })
               .catch(error => {
                   console.error('Error:', error);
                   selectPhoneNumber.innerHTML = '<option value="">Error al cargar números</option>';
                   mostrarAlertaBatchCallDominios('warning', 'Error al cargar los números de teléfono.');
               });
       }


       // Función para mostrar la lista de clientes en el modal
       function mostrarClientesBatchCallDominios(clientes) {
           const lista = document.getElementById('listaClientesBatchCallDominios');
           const totalLlamadas = document.getElementById('totalLlamadasDominios');

           if (clientes.length === 0) {
               lista.innerHTML = `
                   <div class="alert alert-warning mb-0">
                       <i class="bi bi-exclamation-triangle"></i>
                       No hay clientes con teléfono válido en los dominios filtrados.
                   </div>
               `;
               totalLlamadas.textContent = '0';
               document.getElementById('btnEnviarBatchCallDominios').disabled = true;
               return;
           }

           document.getElementById('btnEnviarBatchCallDominios').disabled = false;
           totalLlamadas.textContent = clientes.length;

           let html = '<div class="list-group">';
           clientes.forEach((cliente, index) => {
               // Mostrar el primer dominio y total si tiene más
               let dominioTexto = cliente.dominio;
               if (cliente.total_dominios > 1) {
                   dominioTexto += ` <span class="badge bg-info">+${cliente.total_dominios - 1} más</span>`;
               }

               html += `
                   <div class="list-group-item d-flex justify-content-between align-items-start">
                       <div class="flex-grow-1">
                           <strong>${cliente.nombre}</strong>
                           <br>
                           <small class="text-muted">
                               <i class="bi bi-telephone"></i> ${cliente.telefono}
                           </small>
                           <br>
                           <small class="text-primary">
                               <i class="bi bi-globe"></i> ${dominioTexto}
                           </small>
                       </div>
                       <span class="badge bg-primary rounded-pill">${index + 1}</span>
                   </div>
               `;
           });
           html += '</div>';

           lista.innerHTML = html;
       }

       // Función para enviar el batch call de dominios
       window.enviarBatchCallDominios = function() {
           // Validar formulario
           const callName = document.getElementById('callNameDominios').value.trim();
           const agentId = document.getElementById('agentIdDominios').value.trim();
           const agentPhoneNumberId = document.getElementById('agentPhoneNumberIdDominios').value.trim();
           const firstMessage = document.getElementById('firstMessageDominios').value.trim();

           if (!callName || !agentId || !agentPhoneNumberId) {
               mostrarAlertaBatchCallDominios('danger', 'Por favor, completa todos los campos obligatorios.');
               return;
           }

           if (clientesParaBatchCallDominios.length === 0) {
               mostrarAlertaBatchCallDominios('danger', 'No hay clientes para enviar el batch call.');
               return;
           }

           // Deshabilitar botón y mostrar loading
           const btnEnviar = document.getElementById('btnEnviarBatchCallDominios');
           btnEnviar.disabled = true;
           btnEnviar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando llamadas...';

           // Preparar datos
           const datos = {
               call_name: callName,
               agent_id: agentId,
               agent_phone_number_id: agentPhoneNumberId,
               clientes: clientesParaBatchCallDominios
           };

           // Agregar first_message si está presente
           if (firstMessage) {
               datos.first_message = firstMessage;
           }

           // Enviar petición
           fetch('/api/elevenlabs-monitoring/batch-calls/submit-clientes-filtrados', {
               method: 'POST',
               headers: {
                   'Content-Type': 'application/json',
                   'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
               },
               body: JSON.stringify(datos)
           })
           .then(response => response.json())
           .then(data => {
               if (data.success) {
                   let mensaje = `¡Batch call enviado exitosamente! <br>
                       <strong>Estadísticas:</strong><br>
                       - Total clientes: ${data.estadisticas.total_clientes}<br>
                       - Llamadas programadas: ${data.estadisticas.llamadas_programadas}<br>`;

                   if (data.estadisticas.con_mensaje_personalizado > 0) {
                       mensaje += `- Con mensaje personalizado: ${data.estadisticas.con_mensaje_personalizado}<br>`;
                   }

                   mensaje += `- Errores: ${data.estadisticas.errores}`;

                   mostrarAlertaBatchCallDominios('success', mensaje);

                   // Cerrar modal después de 3 segundos
                   setTimeout(() => {
                       bootstrap.Modal.getInstance(document.getElementById('batchCallDominiosModal')).hide();
                       // Limpiar solo el nombre de la campaña (el mensaje es fijo)
                       document.getElementById('callNameDominios').value = '';
                       // Resetear selects
                       document.getElementById('agentPhoneNumberIdDominios').disabled = true;
                       document.getElementById('agentPhoneNumberIdDominios').innerHTML = '<option value="">Primero selecciona un agente...</option>';
                       // NO limpiar firstMessageDominios porque es estático
                       document.getElementById('alertaBatchCallDominios').innerHTML = '';
                   }, 3000);
               } else {
                   mostrarAlertaBatchCallDominios('danger', 'Error al enviar batch call: ' + data.message);
               }
           })
           .catch(error => {
               console.error('Error:', error);
               mostrarAlertaBatchCallDominios('danger', 'Error al enviar batch call. Por favor, inténtalo de nuevo.');
           })
           .finally(() => {
               // Rehabilitar botón
               btnEnviar.disabled = false;
               btnEnviar.innerHTML = '<i class="bi bi-send"></i> Enviar Batch Call';
           });
       }

       // Función para mostrar alertas en el modal de dominios
       function mostrarAlertaBatchCallDominios(tipo, mensaje) {
           const alerta = document.getElementById('alertaBatchCallDominios');
           alerta.innerHTML = `
               <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                   ${mensaje}
                   <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
               </div>
           `;
       }

       // Función para mostrar errores al cargar clientes de dominios
       function mostrarErrorBatchCallDominios(mensaje) {
           const lista = document.getElementById('listaClientesBatchCallDominios');
           lista.innerHTML = `
               <div class="alert alert-danger mb-0">
                   <i class="bi bi-exclamation-triangle"></i> ${mensaje}
               </div>
           `;
           document.getElementById('btnEnviarBatchCallDominios').disabled = true;
       }

       // Actualizar el badge del botón cuando cambian los filtros de Livewire
       Livewire.hook('message.processed', (message, component) => {
           // Recargar el badge y los checkboxes después de que Livewire actualice
           setTimeout(() => {
               actualizarBadgeTotalDominios();
               actualizarContadorDominios(); // Actualizar contador de checkboxes
           }, 100);
       });

       // Función para actualizar el badge con el total de clientes con teléfono
       async function actualizarBadgeTotalDominios() {
           try {
               // Obtener los filtros actuales de Livewire
               const filtros = await @this.call('getFiltrosActuales');

               const params = new URLSearchParams({
                   buscar: filtros.buscar || '',
                   selectedCliente: filtros.selectedCliente || '',
                   selectedEstado: filtros.selectedEstado || '',
                   fechaInicio: filtros.fechaInicio || '',
                   fechaFin: filtros.fechaFin || '',
                   filtroSinFacturas: filtros.filtroSinFacturas ? '1' : '0',
                   añoSinFacturas: filtros.añoSinFacturas || '',
                   filtroFacturacion: filtros.filtroFacturacion || ''
               });

               fetch(`/api/telefonos-clientes-dominios?${params.toString()}`)
                   .then(response => response.json())
                   .then(data => {
                       if (data.success) {
                           const badge = document.getElementById('totalDominiosConTelefono');
                           if (badge) {
                               badge.textContent = data.total + ' con teléfono';
                           }
                       }
                   })
                   .catch(error => {
                       console.error('Error al cargar total de clientes:', error);
                   });
           } catch (error) {
               console.error('Error en actualizarBadgeTotalDominios:', error);
           }
       }

       // Cargar contadores al iniciar
       document.addEventListener('DOMContentLoaded', function() {
           setTimeout(() => {
               actualizarBadgeTotalDominios();
               actualizarContadorDominios(); // Actualiza el contador de seleccionados
           }, 500);
       });

       // También actualizar al renderizar Livewire
       document.addEventListener('livewire:load', function() {
           setTimeout(() => {
               actualizarContadorDominios();
           }, 200);
       });
    </script>
@endsection
