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
</script>
@endsection
