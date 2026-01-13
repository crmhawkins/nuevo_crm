@extends('layouts.app')

@section('titulo', 'Detalle del Dominio')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<style>
.card-header {
    background-color: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
}

.card-header .card-title {
    color: #495057 !important;
    font-weight: 600 !important;
    margin: 0 !important;
}
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="bi bi-globe-americas"></i> {{ $dominio->dominio }}</h3>
                <p class="text-subtitle text-muted">Información completa del dominio</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('dominios.index')}}">Dominios</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detalle</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="row">
            <!-- Información General -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">📋 Información General</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Dominio:</strong></div>
                            <div class="col-sm-8">{{ $dominio->dominio }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Cliente:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->cliente)
                                    <a href="{{ route('clientes.show', $dominio->cliente->id) }}" class="text-primary">
                                        {{ $dominio->cliente->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Sin cliente asociado</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Estado:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->estadoName)
                                    @if ($dominio->estado_id == 2)
                                        <span class="badge bg-warning text-dark">{{ $dominio->estadoName->name }}</span>
                                    @elseif($dominio->estado_id == 3)
                                        <span class="badge bg-success">{{ $dominio->estadoName->name }}</span>
                                    @elseif($dominio->estado_id == 1)
                                        <span class="badge bg-danger">{{ $dominio->estadoName->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $dominio->estadoName->name }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Sin estado</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Fecha Contratación:</strong></div>
                            <div class="col-sm-8">{{ \Carbon\Carbon::parse($dominio->date_start)->format('d/m/Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Fecha Vencimiento:</strong></div>
                            <div class="col-sm-8">
                                @if(\Carbon\Carbon::parse($dominio->date_end)->isPast())
                                    <span class="text-danger">{{ \Carbon\Carbon::parse($dominio->date_end)->format('d/m/Y') }} (Vencido)</span>
                                @else
                                    {{ \Carbon\Carbon::parse($dominio->date_end)->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                        @if($dominio->comentario)
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Comentario:</strong></div>
                            <div class="col-sm-8">{{ $dominio->comentario }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información Financiera -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">💰 Información Financiera</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Precio Compra:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->precio_compra)
                                    <span class="text-success">€{{ number_format($dominio->precio_compra, 2) }}</span>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Precio Venta:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->precio_venta)
                                    <span class="text-primary">€{{ number_format($dominio->precio_venta, 2) }}</span>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </div>
                        </div>
                        @if($dominio->precio_compra && $dominio->precio_venta)
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Margen Beneficio:</strong></div>
                            <div class="col-sm-8">
                                <span class="text-info">€{{ number_format($dominio->margen_beneficio, 2) }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>% Margen:</strong></div>
                            <div class="col-sm-8">
                                <span class="text-info">{{ number_format($dominio->porcentaje_margen, 2) }}%</span>
                            </div>
                        </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>IBAN:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->iban)
                                    <code>{{ $dominio->iban }}</code>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4"><strong>Sincronización:</strong></div>
                            <div class="col-sm-8">
                                @if($dominio->sincronizado)
                                    <span class="badge bg-success">Sincronizado</span>
                                    <br><small class="text-muted">Última: {{ $dominio->ultima_sincronizacion_formateada }}</small>
                                @else
                                    <span class="badge bg-warning">No sincronizado</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Stripe (Solo en modo test) -->
        @php
            $stripeKey = config('services.stripe.key');
            $isTestMode = strpos($stripeKey, 'pk_test_') !== false;
        @endphp
        @if($isTestMode && ($dominio->stripe_subscription_id || $dominio->stripe_plan_id))
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h4 class="card-title">🧪 Stripe - Modo Prueba</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Modo de Prueba:</strong> Puedes cancelar suscripciones y eliminar planes de prueba.
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-sm-5"><strong>Subscription ID:</strong></div>
                                    <div class="col-sm-7">
                                        @if($dominio->stripe_subscription_id)
                                            <code>{{ $dominio->stripe_subscription_id }}</code>
                                        @else
                                            <span class="text-muted">No configurado</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-5"><strong>Plan ID:</strong></div>
                                    <div class="col-sm-7">
                                        @if($dominio->stripe_plan_id)
                                            <code>{{ $dominio->stripe_plan_id }}</code>
                                        @else
                                            <span class="text-muted">No configurado</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-5"><strong>Método de Pago:</strong></div>
                                    <div class="col-sm-7">
                                        @if($dominio->stripe_payment_method_id)
                                            <code>{{ $dominio->stripe_payment_method_id }}</code>
                                        @else
                                            <span class="text-muted">No configurado</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-5"><strong>Método Preferido:</strong></div>
                                    <div class="col-sm-7">
                                        @if($dominio->metodo_pago_preferido)
                                            <span class="badge bg-info">{{ strtoupper($dominio->metodo_pago_preferido) }}</span>
                                        @else
                                            <span class="text-muted">No configurado</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-danger" onclick="cancelarSuscripcionStripe({{ $dominio->id }})" id="btn-cancelar-stripe">
                                        <i class="bi bi-x-circle"></i> Cancelar Suscripción y Eliminar Plan
                                    </button>
                                    <small class="text-muted text-center">
                                        Esto cancelará la suscripción y eliminará el plan en Stripe (solo modo test)
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Información de IONOS -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">🏢 Información de IONOS</h4>
                        <div>
                            <button class="btn btn-sm btn-outline-primary me-2" onclick="sincronizarIonos({{ $dominio->id }})" id="btn-sincronizar-ionos">
                                <i class="bi bi-arrow-clockwise"></i> Sincronizar con IONOS
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="probarConexionIonos()" id="btn-probar-ionos">
                                <i class="bi bi-wifi"></i> Probar Conexión
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Fecha Activación IONOS:</strong></div>
                                    <div class="col-sm-8">
                                        @if($dominio->fecha_activacion_ionos)
                                            <span class="text-success">{{ $dominio->fecha_activacion_ionos_formateada }}</span>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </div>
                                </div>
                               <div class="row mb-3">
                                   <div class="col-sm-4"><strong>Fecha Renovación IONOS:</strong></div>
                                   <div class="col-sm-8">
                                       @if($dominio->fecha_renovacion_ionos)
                                           <span class="text-primary">{{ $dominio->fecha_renovacion_ionos_formateada }}</span>
                                       @else
                                           <span class="text-muted">No disponible</span>
                                       @endif
                                   </div>
                               </div>
                               <div class="row mb-3">
                                   <div class="col-sm-4"><strong>Fecha Registro Calculada:</strong></div>
                                   <div class="col-sm-8">
                                       @if($dominio->fecha_registro_calculada)
                                           <span class="text-success">{{ $dominio->fecha_registro_calculada_formateada }}</span>
                                           <br><small class="text-muted">Calculada basándose en fecha de renovación IONOS - 1 año</small>
                                       @else
                                           <span class="text-muted">No calculada</span>
                                       @endif
                                   </div>
                               </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <div class="col-sm-4"><strong>Estado IONOS:</strong></div>
                                    <div class="col-sm-8">
                                        @if($dominio->isSincronizadoIonos())
                                            <span class="badge bg-success">Sincronizado con IONOS</span>
                                            <br><small class="text-muted">Última: {{ $dominio->ultima_sincronizacion_ionos->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="badge bg-warning">No sincronizado con IONOS</span>
                                        @endif
                                    </div>
                                </div>
                               <div class="row mb-3">
                                   <div class="col-sm-4"><strong>Acciones:</strong></div>
                                   <div class="col-sm-8">
                                       <button class="btn btn-sm btn-outline-primary me-2" onclick="obtenerInfoIonos({{ $dominio->id }})">
                                           <i class="bi bi-info-circle"></i> Ver Info IONOS
                                       </button>
                                       @if($dominio->fecha_renovacion_ionos && !$dominio->fecha_registro_calculada)
                                       <button class="btn btn-sm btn-outline-success" onclick="calcularFechaRegistro({{ $dominio->id }})" id="btn-calcular-registro">
                                           <i class="bi bi-calculator"></i> Calcular Fecha Registro
                                       </button>
                                       @elseif($dominio->fecha_registro_calculada)
                                       <span class="badge bg-success">Fecha calculada: {{ $dominio->fecha_registro_calculada_formateada }}</span>
                                       @endif
                                   </div>
                               </div>
                            </div>
                        </div>
                        
                        @if(!$dominio->isSincronizadoIonos())
                        <div class="alert alert-info">
                            <h6><i class="bi bi-info-circle"></i> Información de IONOS</h6>
                            <p class="mb-0">Este dominio no ha sido sincronizado con IONOS. Haz clic en "Sincronizar con IONOS" para obtener las fechas de activación y renovación.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview del Dominio -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">🌐 Estado del Sitio Web</h4>
                        <button class="btn btn-sm btn-outline-primary" onclick="verificarEstado()" id="btn-verificar">
                            <i class="bi bi-arrow-clockwise"></i> Verificar Estado
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="preview-container">
                            <div class="text-center py-4" id="preview-loading" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Verificando...</span>
                                </div>
                                <p class="mt-2">Verificando estado del dominio...</p>
                            </div>
                            
                            <div id="preview-content" style="display: none;">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <strong>URL:</strong> 
                                            <a href="#" id="preview-url" target="_blank" class="text-primary">
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Estado:</strong> 
                                            <span id="preview-estado" class="badge"></span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Descripción:</strong> 
                                            <span id="preview-descripcion"></span>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Tiempo de respuesta:</strong> 
                                            <span id="preview-tiempo"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">
                                            <button class="btn btn-primary btn-lg" onclick="abrirPreview()" id="btn-preview">
                                                <i class="bi bi-eye"></i> Ver Preview
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="preview-inicial" class="text-center py-4">
                                <i class="bi bi-globe display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">Estado del sitio web</h5>
                                <p class="text-muted">Haz clic en "Verificar Estado" para comprobar si el sitio web está funcionando correctamente.</p>
                                <button class="btn btn-primary" onclick="verificarEstado()">
                                    <i class="bi bi-search"></i> Verificar Estado
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notificaciones de Dominio -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">📧 Notificaciones de Caducidad</h4>
                        <span class="badge bg-info">{{ $notificaciones->count() }} notificaciones</span>
                    </div>
                    <div class="card-body">
                        @if($notificaciones->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Fecha de Envío</th>
                                            <th>Estado</th>
                                            <th>Fecha Caducidad</th>
                                            <th>Método Pago Solicitado</th>
                                            <th>Cliente</th>
                                            <th>Error</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notificaciones as $notificacion)
                                        <tr>
                                            <td>
                                                @if($notificacion->tipo_notificacion == 'email')
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-envelope"></i> Email
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-whatsapp"></i> WhatsApp
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($notificacion->fecha_envio)->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                @if($notificacion->estado == 'enviado')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Enviado
                                                    </span>
                                                @elseif($notificacion->estado == 'fallido')
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle"></i> Fallido
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-clock"></i> Pendiente
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($notificacion->fecha_caducidad)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @php
                                                    $metodoPago = $notificacion->metodo_pago_solicitado ?? 'ambos';
                                                    $badgeClass = match($metodoPago) {
                                                        'iban' => 'bg-info',
                                                        'stripe' => 'bg-primary',
                                                        'ambos' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">
                                                    {{ strtoupper($metodoPago) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($notificacion->cliente)
                                                    <a href="{{ route('clientes.show', $notificacion->cliente->id) }}" class="text-primary">
                                                        {{ $notificacion->cliente->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($notificacion->error_mensaje)
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            onclick="mostrarError('{{ addslashes($notificacion->error_mensaje) }}')"
                                                            title="Ver error">
                                                        <i class="bi bi-exclamation-triangle"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-bell-slash display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No hay notificaciones registradas</h5>
                                <p class="text-muted">Las notificaciones de caducidad aparecerán aquí cuando se envíen mensajes de email o WhatsApp.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Facturas Asociadas -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">🧾 Facturas Asociadas</h4>
                        <span class="badge bg-primary">{{ $facturasAsociadas->count() }} facturas encontradas</span>
                    </div>
                    <div class="card-body">
                        @if($facturasAsociadas->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Referencia</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($facturasAsociadas as $factura)
                                        <tr>
                                            <td>
                                                <a href="{{ route('factura.show', $factura->id) }}" class="text-primary">
                                                    {{ $factura->reference }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($factura->budget && $factura->budget->cliente)
                                                    {{ $factura->budget->cliente->name }}
                                                @else
                                                    <span class="text-muted">Sin cliente</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($factura->created_at)->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="text-success">€{{ number_format($factura->total, 2) }}</span>
                                            </td>
                                            <td>
                                                @if($factura->invoiceStatus)
                                                    @php
                                                        $estado = $factura->invoiceStatus;
                                                        $badgeClass = match($estado->id) {
                                                            1 => 'bg-warning',      // Pendiente
                                                            2 => 'bg-secondary',   // No cobrada
                                                            3 => 'bg-success',     // Cobrada
                                                            4 => 'bg-info',       // Cobrada parcialmente
                                                            5 => 'bg-danger',     // Cancelada
                                                            default => 'bg-secondary'
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $estado->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Sin estado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('factura.show', $factura->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-receipt display-1 text-muted"></i>
                                <h5 class="text-muted mt-3">No se encontraron facturas asociadas</h5>
                                <p class="text-muted">Este dominio no aparece en ningún concepto de factura.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center">
                        <a href="{{ route('dominios.edit', $dominio->id) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Editar Dominio
                        </a>
                        @if($dominio->estado_id != 2)
                        <button class="btn btn-danger me-2" onclick="cancelarDominio({{ $dominio->id }})" id="btn-cancelar">
                            <i class="bi bi-x-circle"></i> Cancelar Dominio
                        </button>
                        <button class="btn btn-info me-2" onclick="testFunction()" id="btn-test">
                            <i class="bi bi-bug"></i> Test
                        </button>
                        @endif
                        <a href="{{ route('dominios.index') }}" class="btn btn-secondary me-2">
                            <i class="bi bi-arrow-left"></i> Volver a Lista
                        </a>
                        @if($facturasAsociadas->count() > 0)
                        <button class="btn btn-info" onclick="sincronizarDominio()">
                            <i class="bi bi-arrow-clockwise"></i> Sincronizar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
@include('partials.toast')
<script>
    function sincronizarDominio() {
        Swal.fire({
            title: 'Sincronizar Dominio',
            text: '¿Deseas sincronizar este dominio con la base externa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, sincronizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Aquí podrías hacer una llamada AJAX para sincronizar
                Swal.fire({
                    title: 'Sincronización',
                    text: 'Funcionalidad de sincronización en desarrollo',
                    icon: 'info'
                });
            }
        });
    }

    function testFunction() {
        alert('JavaScript está funcionando!');
        console.log('Test function ejecutada');
    }

    function cancelarDominio(dominioId) {
        console.log('Función cancelarDominio llamada con ID:', dominioId);
        
        // Verificar que SweetAlert2 esté disponible
        if (typeof Swal === 'undefined') {
            alert('SweetAlert2 no está cargado. ID del dominio: ' + dominioId);
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
                fetch(`/dominios/cancelar/${dominioId}`, {
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
                            // Recargar la página para mostrar el nuevo estado
                            location.reload();
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
    }

    // Variables globales para el preview
    let estadoActual = null;
    let urlActual = null;

    // Función para verificar el estado del dominio
    function verificarEstado() {
        const loading = document.getElementById('preview-loading');
        const content = document.getElementById('preview-content');
        const inicial = document.getElementById('preview-inicial');
        
        // Mostrar loading
        loading.style.display = 'block';
        content.style.display = 'none';
        inicial.style.display = 'none';
        
        // Hacer petición AJAX
        fetch(`/dominios/verificar/{{ $dominio->id }}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            loading.style.display = 'none';
            
            if (data.success) {
                estadoActual = data;
                urlActual = data.url;
                
                // Actualizar contenido
                document.getElementById('preview-url').href = data.url;
                document.getElementById('preview-url').textContent = data.url;
                
                const estadoBadge = document.getElementById('preview-estado');
                estadoBadge.textContent = data.estado;
                estadoBadge.className = `badge bg-${data.clase}`;
                
                document.getElementById('preview-descripcion').textContent = data.descripcion;
                
                if (data.tiempo_respuesta) {
                    document.getElementById('preview-tiempo').textContent = `${data.tiempo_respuesta}ms`;
                } else {
                    document.getElementById('preview-tiempo').textContent = 'N/A';
                }
                
                content.style.display = 'block';
            } else {
                // Mostrar error
                Swal.fire({
                    title: 'Error',
                    text: data.message || 'Error al verificar el dominio',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                inicial.style.display = 'block';
            }
        })
        .catch(error => {
            loading.style.display = 'none';
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión al verificar el dominio',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            inicial.style.display = 'block';
        });
    }

    // Función para abrir el preview del dominio
    function abrirPreview() {
        if (!urlActual) {
            Swal.fire({
                title: 'Error',
                text: 'Primero debes verificar el estado del dominio',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Crear modal con iframe
        Swal.fire({
            title: `Preview de ${urlActual}`,
            html: `
                <div style="text-align: center; margin-bottom: 15px;">
                    <span class="badge bg-${estadoActual.clase}">${estadoActual.estado}</span>
                    <span class="ms-2">${estadoActual.descripcion}</span>
                </div>
                <iframe 
                    src="${urlActual}" 
                    style="width: 100%; height: 500px; border: 1px solid #ddd; border-radius: 5px;"
                    frameborder="0"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                ></iframe>
                <div style="display: none; text-align: center; padding: 50px; background: #f8f9fa; border-radius: 5px;">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #dc3545;"></i>
                    <p class="mt-2">No se pudo cargar el preview del sitio web</p>
                    <a href="${urlActual}" target="_blank" class="btn btn-primary">
                        <i class="bi bi-box-arrow-up-right"></i> Abrir en nueva pestaña
                    </a>
                </div>
            `,
            width: '90%',
            showConfirmButton: true,
            confirmButtonText: 'Cerrar',
            showCancelButton: true,
            cancelButtonText: 'Abrir en nueva pestaña',
            cancelButtonColor: '#3085d6',
            didOpen: () => {
                // Agregar estilos adicionales si es necesario
                const iframe = document.querySelector('iframe');
                if (iframe) {
                    iframe.onload = function() {
                        console.log('Preview cargado correctamente');
                    };
                    iframe.onerror = function() {
                        console.log('Error al cargar el preview');
                    };
                }
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // Abrir en nueva pestaña
                window.open(urlActual, '_blank');
            }
        });
    }

    // Funciones para IONOS
    function sincronizarIonos(dominioId) {
        console.log('Sincronizando con IONOS para dominio:', dominioId);
        
        Swal.fire({
            title: 'Sincronizando con IONOS',
            text: 'Obteniendo información del dominio...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/dominios/sincronizar-ionos/${dominioId}`, {
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
                    title: '¡Sincronizado!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
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
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión al sincronizar con IONOS',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    function obtenerInfoIonos(dominioId) {
        console.log('Obteniendo información de IONOS para dominio:', dominioId);
        
        Swal.fire({
            title: 'Obteniendo información',
            text: 'Consultando API de IONOS...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(`/dominios/info-ionos/${dominioId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = `
                    <div class="text-start">
                        <h6>Información de IONOS:</h6>
                        <p><strong>Dominio:</strong> ${data.domain_name || 'N/A'}</p>
                        <p><strong>Estado:</strong> ${data.status || 'N/A'}</p>
                        <p><strong>Registrar:</strong> ${data.registrar || 'N/A'}</p>
                        <p><strong>Auto Renovación:</strong> ${data.auto_renew ? 'Sí' : 'No'}</p>
                `;
                
                if (data.fecha_activacion_ionos) {
                    html += `<p><strong>Fecha Activación:</strong> ${data.fecha_activacion_ionos}</p>`;
                }
                
                if (data.fecha_renovacion_ionos) {
                    html += `<p><strong>Fecha Renovación:</strong> ${data.fecha_renovacion_ionos}</p>`;
                }
                
                html += '</div>';
                
                Swal.fire({
                    title: 'Información de IONOS',
                    html: html,
                    icon: 'info',
                    confirmButtonText: 'OK'
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
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión al obtener información de IONOS',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

    function probarConexionIonos() {
        console.log('Probando conexión con IONOS');
        
        Swal.fire({
            title: 'Probando conexión',
            text: 'Verificando conectividad con IONOS...',
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
                    title: '¡Conexión exitosa!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Error de conexión',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Error de conexión al probar IONOS',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    }

       // Función para calcular fecha de registro
       function calcularFechaRegistro(dominioId) {
           console.log('Calculando fecha de registro para dominio:', dominioId);
           
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
           fetch(`/dominios/calcular-fecha-registro/${dominioId}`, {
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
                       // Recargar la página para mostrar la nueva fecha
                       location.reload();
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

       // Event listener adicional para el botón
       document.addEventListener('DOMContentLoaded', function() {
           const btnCancelar = document.getElementById('btn-cancelar');
           if (btnCancelar) {
               btnCancelar.addEventListener('click', function(e) {
                   e.preventDefault();
                   const dominioId = {{ $dominio->id }};
                   console.log('Event listener activado para dominio:', dominioId);
                   cancelarDominio(dominioId);
               });
           }
       });

       // Función para mostrar error de notificación
       function mostrarError(errorMensaje) {
           Swal.fire({
               title: 'Error en la Notificación',
               html: `<div class="text-start"><pre style="white-space: pre-wrap; word-wrap: break-word;">${errorMensaje}</pre></div>`,
               icon: 'error',
               confirmButtonText: 'Cerrar',
               width: '600px'
           });
       }

       // Función para cancelar suscripción de Stripe (solo modo test)
       function cancelarSuscripcionStripe(dominioId) {
           Swal.fire({
               title: '¿Cancelar suscripción de Stripe?',
               html: '<p>Esta acción cancelará la suscripción y eliminará el plan en Stripe.</p><p class="text-warning"><strong>Esta acción solo está disponible en modo de prueba.</strong></p>',
               icon: 'warning',
               showCancelButton: true,
               confirmButtonColor: '#d33',
               cancelButtonColor: '#3085d6',
               confirmButtonText: 'Sí, cancelar',
               cancelButtonText: 'No, mantener',
               showLoaderOnConfirm: true,
               preConfirm: () => {
                   return fetch(`/dominios/cancelar-suscripcion-stripe/${dominioId}`, {
                       method: 'POST',
                       headers: {
                           'Content-Type': 'application/json',
                           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                       }
                   })
                   .then(response => response.json())
                   .then(data => {
                       if (!data.success) {
                           throw new Error(data.message || 'Error al cancelar suscripción');
                       }
                       return data;
                   })
                   .catch(error => {
                       Swal.showValidationMessage(`Error: ${error.message}`);
                   });
               },
               allowOutsideClick: () => !Swal.isLoading()
           })
           .then((result) => {
               if (result.isConfirmed) {
                   Swal.fire({
                       title: '¡Cancelado!',
                       html: result.value.message || 'Suscripción cancelada y plan eliminado correctamente',
                       icon: 'success',
                       confirmButtonText: 'OK'
                   }).then(() => {
                       // Recargar la página para actualizar la información
                       window.location.reload();
                   });
               }
           });
       }
</script>
@endsection
