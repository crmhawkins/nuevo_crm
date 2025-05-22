@extends('layouts.app')

@section('titulo', 'Detalle de Factura')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/toastify/toastify.css') }}">
@endsection

@section('content')

<div class="page-heading card">
    <div class="page-title card-body mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3>Detalle de la Factura - {{ $invoice->reference }}</h3>
                <p class="text-subtitle text-muted">Vista detallada de la factura</p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('facturas.index') }}">Facturas</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vista de factura</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row invoice layout-top-spacing layout-spacing">
        <div class="col-lg-9">
            <div class="doc-container">
                <div class="invoice-container">
                    <div class="invoice-inbox">
                        <div class="content-section">
                            <!-- Encabezado -->
                            <div class="inv--head-section inv--detail-section">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <img class="company-logo mb-3" src="{{ asset('assets/images/logo/logo.png') }}" alt="company" style="width: auto;">
                                        <p class="inv-street-addr">{{ $empresa->address }}</p>
                                        <p class="inv-email-address">{{ $empresa->email }}</p>
                                        <p class="inv-email-address">{{ $empresa->telephone }}</p>
                                    </div>
                                    <div class="col-sm-6 text-sm-end">
                                        <p class="inv-list-number"><span class="inv-title">Factura:</span> <span class="inv-number">{{ $invoice->reference }}</span></p>
                                        <p class="inv-created-date"><span class="inv-title">Fecha de Creación:</span> <span class="inv-date">{{ Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Cliente -->
                            <div class="inv--detail-section inv--customer-detail-section mt-4">
                                <div class="row">
                                    <div class="col">
                                        <p class="inv-to">Cliente</p>
                                        <p class="inv-customer-name">{{ $invoice->cliente->name }}</p>
                                        <p class="inv-street-addr">{{ $invoice->cliente->address }}, {{ $invoice->cliente->city }}, {{ $invoice->cliente->province }} - {{ $invoice->cliente->zipcode }}</p>
                                        <p class="inv-email-address">{{ $invoice->cliente->email }}</p>
                                        <p class="inv-email-address">{{ $invoice->cliente->phone }}</p>
                                        <p><strong>Forma de Pago:</strong> {{ $invoice->payment_method_id == 9 ? 'Transferencia' : $invoice->paymentMethod->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Conceptos de la Factura -->
                            <div class="inv--product-table-section mt-4">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-end">Unidades</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Descuento</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoiceConcepts as $concepto)
                                            <tr>
                                                <td>{{$concepto->title}}</td>
                                                <td class="text-end">{{$concepto->units}}</td>
                                                <td class="text-end">{{$concepto->sale_price}}</td>
                                                <td class="text-end">{{$concepto->discount}}</td>
                                                <td class="text-end">{{$concepto->total}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Resumen de Totales -->
                            <div class="inv--total-amounts mt-4">
                                <div class="row">
                                    <div class="col-sm-5"></div>
                                    <div class="col-sm-7">
                                        <div class="text-sm-end">
                                            <div class="row">
                                                <div class="col-sm-8 col-7">
                                                    <p>Sub Total :</p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p>{{ $invoice->base }} €</p>
                                                </div>
                                                <div class="col-sm-8 col-7">
                                                    <p>IVA ({{ $invoice->iva_percentage }}%) :</p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p>{{ $invoice->iva }} €</p>
                                                </div>
                                                <div class="col-sm-8 col-7 mt-3">
                                                    <h4>Total : </h4>
                                                </div>
                                                <div class="col-sm-4 col-5 mt-3">
                                                    <h4>{{ $invoice->total }} €</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="col-lg-3">
            <div class="invoice-actions-btn">
                <div class="invoice-action-btn">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <a href="" id="generatePdf" class="btn btn-success w-100 btn-download">Descargar</a>
                        </div>
                        <div class="col-xl-12 col-md-3 col-sm-6">
                            <a href="{{route('factura.edit', $invoice->id)}}" class="btn btn-dark btn-edit">Editar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ asset('assets/vendors/toastify/toastify.js') }}"></script>
    @include('partials.toast')

<script>
    $(document).ready(function() {
        $('#generatePdf').click(function(e) {
            e.preventDefault();
            const idFactura = @json($invoice->id);
            $.ajax({
                url: '{{ route("factura.generarPDF") }}',
                type: 'POST',
                data: { id: idFactura },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(response) {
                    const blob = new Blob([response], { type: 'application/pdf' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'factura_' + idFactura + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    Toastify({
                        text: "PDF creado correctamente.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#4CAF50"
                    }).showToast();
                },
                error: function(xhr) {
                    Toastify({
                        text: "Error al generar el PDF.",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "#F44336"
                    }).showToast();
                }
            });
        });
    });
</script>
@endsection
