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
                        <button class="btn btn-danger me-2" onclick="cancelarDominio({{ $dominio->id }})">
                            <i class="bi bi-x-circle"></i> Cancelar Dominio
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

    function cancelarDominio(dominioId) {
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
</script>
@endsection
