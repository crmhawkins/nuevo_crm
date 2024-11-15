@extends('layouts.app')

@section('titulo', 'Ver Presupuesto')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/toastify/toastify.css')}}">
@endsection

@section('content')
<div class="page-heading card">
    <div class="page-title card-body mb-3">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Vista del Presupuesto - {{$presupuesto->reference}}</h3>
                <p class="text-subtitle text-muted">Previsulación un presupuesto</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Presupuestos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Vista del presupuesto</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="row invoice layout-top-spacing layout-spacing">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="doc-container">
                <div class="row">
                    <div class="col-xl-9">
                        <div class="invoice-container">
                            <div class="invoice-inbox">
                                <div id="ct" class="">
                                    <div class="invoice-00001">
                                        <div class="content-section">
                                            <div class="inv--head-section inv--detail-section">
                                                <div class="row">
                                                    <div class="col-sm-6 col-12 mr-auto">
                                                        <div class="d-flex">
                                                            <img class="company-logo" src={{ asset('assets/images/logo/logo.png') }} alt="company" style="width: auto">
                                                        </div>
                                                        <p class="inv-street-addr mt-3">{{$empresa->address}}</p>
                                                        <p class="inv-email-address">{{$empresa->email}}</p>
                                                        <p class="inv-email-address">{{$empresa->telephone}}</p>
                                                    </div>

                                                    <div class="col-sm-6 text-sm-end">
                                                        <p class="inv-list-number mt-sm-3 pb-sm-2 mt-4"><span class="inv-title">Presupuesto: </span> <span class="inv-number">{{$presupuesto->reference}}</span></p>
                                                        <p class="inv-created-date mt-sm-5 mt-3"><span class="inv-title">Fecha de Emisión : </span> <span class="inv-date">{{$presupuesto->creation_date}}</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="inv--detail-section inv--customer-detail-section">
                                                <div class="row">
                                                    <div class="col-xl-8 col-lg-7 col-md-6 col-sm-4 align-self-center">
                                                        <p class="inv-to">Cliente</p>
                                                    </div>
                                                    <div class="col-xl-8 col-lg-7 col-md-6 col-sm-4">
                                                        <p class="inv-customer-name">{{$presupuesto->cliente->company ?? $presupuesto->cliente->name }}</p>
                                                        <p class="inv-street-addr">{{$presupuesto->cliente->address}}</p>
                                                        <p class="inv-email-address">{{$presupuesto->cliente->email}}</p>
                                                        <p class="inv-email-address">{{$presupuesto->cliente->phone}}</p>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="inv--product-table-section">
                                                <div class="table-responsive">
                                                     <table class="table table-hover">
                                                        <thead class="">
                                                            <tr>
                                                                <th scope="col">Concepto</th>
                                                                <th class="text-end" scope="col">Unidades</th>
                                                                <th class="text-end" scope="col">Precio</th>
                                                                <th class="text-end" scope="col">Descuento</th>
                                                                <th class="text-end" scope="col">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($BudgetConcepts as $concepto)
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
                                            </div>

                                            <div class="inv--total-amounts">
                                                <div class="row mt-4">
                                                    <div class="col-sm-5 col-12 order-sm-0 order-1">
                                                    </div>
                                                    <div class="col-sm-7 col-12 order-sm-1 order-0">
                                                        <div class="text-sm-end">
                                                            <div class="row">
                                                                <div class="col-sm-8 col-7">
                                                                    <p class="">Sub Total :</p>
                                                                </div>
                                                                <div class="col-sm-4 col-5">
                                                                    <p class="">{{$presupuesto->base}} €</p>
                                                                </div>
                                                                <div class="col-sm-8 col-7">
                                                                    <p class="">IVA {{$presupuesto->iva_percentage}}% :</p>
                                                                </div>
                                                                <div class="col-sm-4 col-5">
                                                                    <p class="">{{$presupuesto->iva}} €</p>
                                                                </div>
                                                                <div class="col-sm-8 col-7 mt-3">
                                                                    <h4 class="">Total : </h4>
                                                                </div>
                                                                <div class="col-sm-4 col-5 mt-3">
                                                                    <h4 class="">{{$presupuesto->total}} €</h4>
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
                    </div>

                    <div class="col-xl-3">
                        <div class="invoice-actions-btn">
                            <div class="invoice-action-btn">
                                <div class="row">
                                    <div class="col-xl-12 col-md-3 col-sm-6">
                                        <a href="" id="generatePdf" class="btn btn-success btn-download">Descargar</a>
                                    </div>
                                    <div class="col-xl-12 col-md-3 col-sm-6">
                                        <a href="{{route('presupuesto.edit', $presupuesto->id)}}" class="btn btn-dark btn-edit">Editar</a>
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

    {{-- <!--  BEGIN CUSTOM SCRIPTS FILE  -->
    <x-slot:footerFiles>
         @vite(['resources/assets/js/apps/invoice-preview.js'])
    </x-slot> --}}
@endsection

@section('scripts')
    <!-- toastify -->
    <script src="{{asset('assets/vendors/toastify/toastify.js')}}"></script>

    @include('partials.toast')
<script>
    $(document).ready(function() {
        $('#generatePdf').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);
            $.ajax({
                url: '{{ route("presupuesto.generarPDF") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                xhrFields: {
                    responseType: 'blob' // Necesario para manejar la descarga del archivo
                },
                success: function(response) {
                    // Crear una URL para el blob y forzar la descarga
                    const blob = new Blob([response], { type: 'application/pdf' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'presupuesto_' + idPresupuesto + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Pdf creado correctamente.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                        didClose: () => {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar la tarea. Por favor, inténtalo de nuevo.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>

@endsection
