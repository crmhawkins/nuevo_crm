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
                        <tr>
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
                                                  ->orWhere('invoice_concepts.title', 'like', '%anual%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%dominio%')
                                                  ->orWhere('invoice_concepts.concept', 'like', '%Dominio%');
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
    </script>
@endsection
