@extends('layouts.appPortal')

@section('titulo', 'Detalle de Presupuesto')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/toastify/toastify.css') }}">
@endsection

@section('content')

<div class="page-heading card">
    <div class="page-title card-body mb-3">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3>Detalle del Presupuesto - {{ $budget->reference }}</h3>
                <p class="text-subtitle text-muted">Vista detallada del presupuesto</p>
            </div>
            <div class="col-12 col-md-6">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('portal.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('portal.presupuestos') }}">Presupuestos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vista de presupuesto</li>
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
                                        <p class="inv-list-number"><span class="inv-title">Presupuesto:</span> <span class="inv-number">{{ $budget->reference }}</span></p>
                                        <p class="inv-created-date"><span class="inv-title">Fecha de Creación:</span> <span class="inv-date">{{ Carbon\Carbon::parse($budget->creation_date)->format('d/m/Y') }}</span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del Cliente -->
                            <div class="inv--detail-section inv--customer-detail-section mt-4">
                                <div class="row">
                                    <div class="col">
                                        <p class="inv-to">Cliente</p>
                                        <p class="inv-customer-name">{{ $budget->cliente->company }}</p>
                                        <p class="inv-street-addr">{{ $budget->cliente->address }}, {{ $budget->cliente->city }}, {{ $budget->cliente->province }} - {{ $budget->cliente->zipcode }}</p>
                                        <p class="inv-email-address">{{ $budget->cliente->email }}</p>
                                        <p class="inv-email-address">{{ $budget->cliente->phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Conceptos del Presupuesto -->
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
                                        @foreach($concepts as $concept)
                                            <tr>
                                                <td>{{ $concept['title'] }}</td>
                                                <td class="text-end">{{ $concept['units'] }}</td>
                                                <td class="text-end">{{ $concept['unit_price'] ?? 0 }} €</td>
                                                <td class="text-end">{{ $concept['discount'] ? $concept['discount'] . '%' : '-' }}</td>
                                                <td class="text-end">{{ $concept['total'] }} €</td>
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
                                                    <p>{{ $budget->base }} €</p>
                                                </div>
                                                <div class="col-sm-8 col-7">
                                                    <p>IVA ({{ $budget->iva_percentage }}%) :</p>
                                                </div>
                                                <div class="col-sm-4 col-5">
                                                    <p>{{ $budget->iva }} €</p>
                                                </div>
                                                <div class="col-sm-8 col-7 mt-3">
                                                    <h4>Total : </h4>
                                                </div>
                                                <div class="col-sm-4 col-5 mt-3">
                                                    <h4>{{ $budget->total }} €</h4>
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
            const idPresupuesto = @json($budget->id);
            $.ajax({
                url: '{{ route("presupuesto.generarPDF") }}',
                type: 'POST',
                data: { id: idPresupuesto },
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
                    link.download = 'presupuesto_' + idPresupuesto + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
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
