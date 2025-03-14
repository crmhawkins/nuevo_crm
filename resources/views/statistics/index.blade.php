@extends('layouts.app')

@section('titulo', 'Estadísticas')

@section('css')
    <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css" />
    <style>
        .modal-dialog.modal-lg-custom {
            max-width: 60%;
        }
        tr.clickable-row-sta{
            cursor: pointer;
        }
        /* Estilo personalizado para el buscador de DataTables */
        .dataTables_filter {
            float: right; /* Alinea el buscador a la derecha */
            text-align: right;
        }
        .dataTables_filter label {
            width: 100%;
            display: flex;
            align-items: center;
        }
        .dataTables_filter input {
            margin-left: .5em;
            padding: .5em;
            width: 100%; /* Asegura que el input toma el ancho necesario */
            box-shadow: 0 4px 6px rgba(0,0,0,.1); /* Sombra ligera para el input */
        }
        /* Ajustes para que la tabla sea más compacta y moderna */
        table.dataTable {
            width: 100%; /* Asegura que las tablas ocupen todo el espacio disponible */
            margin-top: 12px !important;
            margin-bottom: 12px !important;
        }
        table.dataTable thead th, table.dataTable tfoot th {
            padding: 10px 18px; /* Espaciado interno más generoso para cabeceras y pies */
            border-bottom: 2px solid #dee2e6; /* Línea más definida en el pie y la cabecera */
        }
        table.dataTable thead tr{
            cursor: pointer;

        }
        table.dataTable.no-footer {
            border-bottom: none; /* Evita bordes innecesarios en el pie cuando no hay pie de página */
        }
    </style>
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body p-3">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-first">
                    <h3><i class="bi bi-bar-chart"></i> Estadísticas</h3>
                    <p class="text-subtitle text-muted">Visión general de las estadísticas de la empresa</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Estadísticas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        {{-- {{var_dump($cashflow)}} --}}
        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('estadistica.index') }}" method="GET">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group mx-3 mb-3" style="display: flex; flex-direction: row; align-items: baseline;">
                                        <label for="mes" style="margin-right: 1rem"><strong>Rango</strong></label>
                                        <input type="text" class="form-control date-range p-1 rangofecha" id="dateRange" name="dateRange" value="{{ request('dateRange',$dateRange) }}">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="text-uppercase">Datos Anuales</h3>
                            <div class="row">
                                {{-- Proyectos Activos Anuales --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalProyectos" style="cursor:pointer;">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Proyectos Activos</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">{{$dataBudgets['total']}}+</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Presupuestos Activos Anuales --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Presupuestos</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark"><span class="counter-anim">{{number_format($countTotalBudgets, 2, ',', '.')}}</span> €</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Facturación Anual -->
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalFacturacionanual" style="cursor:pointer;">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Facturación Anual</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">€ {{number_format($dataFacturacionAnno['total'], 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-success font-12 font-weight-600">+0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Facturacion Anual Base Imponible --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalFacturacionanual" style="cursor:pointer;">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Facturación Anual Base Imponible</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">€ {{number_format($dataFacturacionAnnoBase['total'], 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-success font-12 font-weight-600">+0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Beneficio Anual --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Beneficios Anual</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">{{number_format($totalBeneficioAnual, 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-danger font-12 font-weight-600">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Gastos Comunes Anuales --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosComunesAnual" style="cursor:pointer;">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Comunes Anual</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataGastosComunesAnual['total'], 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-danger font-12 font-weight-600">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Gastos Asociados Anuales --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosAsociadosAnual" style="cursor:pointer;">
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Asociados Anual</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataAsociadosAnual['total'], 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-danger font-12 font-weight-600">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- Iva Anual --}}
                                <div class="col-12 mb-2">
                                    <div class="card card-sm" >
                                        <div class="card-body">
                                            <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Iva Anual</span>
                                            <div class="d-flex align-items-end justify-content-between">
                                                <div>
                                                    <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataIvaAnual, 2, ',', '.')}}</span>
                                                </div>
                                                <div>
                                                    <span class="text-danger font-12 font-weight-600">0%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <h3 class="text-uppercase">Datos Mensuales</h3>
                            {{-- Facturacion Mensual --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalFacturacion" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Facturación Mensual</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">€ {{number_format($dataFacturacion['total'], 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-success font-12 font-weight-600">+0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Gastos Comunes Totales --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosComunes" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Comunes Totales</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataGastosComunesTotales['total'], 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Gastos Comunes Deducibles --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosComunesDeducibles" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Comunes Deducibles</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataGastosComunes['total'], 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Gastos Asociados --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosAsociados" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Asociados </span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataAsociados['total'], 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Iva Mensual --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Iva</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataIva, 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Iva Emitido</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataFacturacion['ivas'], 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-2">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Iva a Pagar</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($dataFacturacion['ivas'] - $dataIva, 2, ',', '.')}}</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-uppercase">Cash Flow</h3>
                            {{-- Ingresos --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalIngresosCash" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Ingresos</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($cashflow['ingresos'], 2, ',', '.')}} €</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Gastos Asociados --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosAsociadosCash" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Asociados</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($cashflow['gastos_asociados'], 2, ',', '.')}} €</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Gastos Comunes --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastoComunesCash" style="cursor:pointer;" >
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Gastos Comunes</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($cashflow['gastos_comunes'], 2, ',', '.')}} €</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{-- Beneficio --}}
                            <div class="col-12 mb-2">
                                <div class="card card-sm" >
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-1">Diferencia</span>
                                        <div class="d-flex align-items-end justify-content-between">
                                            <div>
                                                <span class="d-block display-6 font-weight-400 text-dark">{{number_format($cashflow['ingresos'] - ($cashflow['gastos_comunes'] + $cashflow['gastos_asociados']), 2, ',', '.')}} €</span>
                                            </div>
                                            <div>
                                                <span class="text-danger font-12 font-weight-600">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="container">
                                <h3>Facturación Anual</h3>
                                <canvas id="facturacion-all-monthly"></canvas>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="container">
                                <h3>Facturación Mensual</h3>
                                <canvas id="facturacion-mensual"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="container">
                                <h3>Beneficio Mensual</h3>
                                <canvas id="beneficio-mensual"></canvas>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="container">
                                <h3>Facturacion Media</h3>
                                <canvas id="facturacion-media"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="ModalGastosAsociadosCash" tabindex="-1" role="dialog" aria-labelledby="ModalGastosAsociadosCash" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gastos Asociados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosAsociadosCash" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha de E.</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach($cashflow['gastos_asociados_array'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('gasto-asociado.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->note}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;color:blue"><a href="@if ($item->budget_concept_id) {{route('budgetConcepts.editTypeSupplier', $item->budget_concept_id )}} @endif" target="_blank" rel="noopener noreferrer">{{$item->budget_concept_id ?? 'N\A'}}</a></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->shipping_date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->status}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->amount}} €</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($cashflow['gastos_asociados'], 2, ',', '.')}} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastoComunesCash" tabindex="-1" role="dialog" aria-labelledby="ModalGastoComunesCash" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gastos Comunes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastoComunesCash" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Factura Asoc.</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha de E.</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach($cashflow['gastos_comunes_array'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('gasto.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray; color:blue"><a href="@if ($item->invoice_id) {{route('factura.edit', $item->invoice_id )}} @endif" target="_blank" rel="noopener noreferrer">{{$item->invoice_id ?? 'N\A'}}</a></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->quantity}} €</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($cashflow['gastos_comunes'], 2, ',', '.')}} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalIngresosCash" tabindex="-1" role="dialog" aria-labelledby="ModalIngresosCash" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Ingresos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaIngresosCash" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Factura</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach($cashflow['ingresos_array'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('ingreso.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"><a href="@if ($item->invoice_id) {{route('factura.edit', $item->invoice_id )}} @endif" target="_blank" rel="noopener noreferrer">{{$item->invoice_id ?? 'N\A'}}</a></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->quantity}} €</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($cashflow['ingresos'], 2, ',', '.')}} €</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastosAsociados" tabindex="-1" role="dialog" aria-labelledby="ModalGastosAsociados" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gatos Asociados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosAsociados" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Orden</th>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Proveedor</th>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th  style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataAsociados['array'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('gasto-asociado.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->purchase_order_id}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{optional(optional($item->OrdenCompra)->Proveedor)->name}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->received_date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state ?? 'N\A'}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->quantity, 2, ',', '.')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total:</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataAsociados['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastosComunesDeducibles" tabindex="-1" role="dialog" aria-labelledby="ModalGastosComunesDeducibles" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gatos Comunes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosComunesDeducibles" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataGastosComunes['gastos'] as $item)
                                    <tr class="clickable-row-sta" data-href="{{route('gasto.edit', $item->id)}}">
                                        <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                        <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state ?? 'N\A'}}</td>
                                        <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->received_date}}</td>
                                        <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->quantity, 2, ',', '.')}}</td>
                                    </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataGastosComunes['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastosComunes" tabindex="-1" role="dialog" aria-labelledby="ModalGastosComunes" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gatos Comunes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosComunes" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach($dataGastosComunesTotales['gastos'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('gasto.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state ?? 'N\A'}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->received_date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->quantity, 2, ',', '.')}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataGastosComunes['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalFacturacion" tabindex="-1" role="dialog" aria-labelledby="ModalFacturacion" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Facturacion</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaFacturacion" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Cliente </th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Campaña</th>
                                    <th class="w-full-th" style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha Creacion</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataFacturacion['facturas'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('factura.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->reference}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->cliente->company ?? $item->cliente->name ?? 'Cliente Borrado'}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->concept}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->invoiceStatus->name}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->project_id}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->created_at}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->total, 2, ',', '.')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataFacturacion['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastosAsociadosAnual" tabindex="-1" role="dialog" aria-labelledby="ModalGastosAsociadosAnual" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gatos Asociados Anual</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosAsociadosAnual" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Orden</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Proveedor </th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataAsociadosAnual['array'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('gasto-asociado.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->purchase_order_id}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{optional(optional($item->OrdenCompra)->Proveedor)->name}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->received_date}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state ?? 'N\A'}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->quantity, 2, ',', '.')}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataAsociadosAnual['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalGastosComunesAnual" tabindex="-1" role="dialog" aria-labelledby="ModalGastosComunesAnual" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Gatos Comunes Anual</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaGastosComunesAnual" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>

                                </tr>
                            </thead>
                            <tbody>
                            @foreach($dataGastosComunesAnual['gastos'] as $item)
                            <tr class="clickable-row-sta" data-href="{{route('gasto.edit', $item->id)}}">
                                <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->title}}</td>
                                <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->state ?? 'N\A'}}</td>
                                <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->received_date}}</td>
                                <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->quantity}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataGastosComunesAnual['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalFacturacionanual" tabindex="-1" role="dialog" aria-labelledby="ModalFacturacionanual" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Facturacion Anual</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaFacturacionAnual" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Cliente </th>
                                    <th style="max-width: 500px; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Campaña</th>
                                    <th class="w-full-th" style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha Creacion</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataFacturacionAnno['facturas'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('factura.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->reference}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->cliente->company ?? $item->cliente->name ?? 'Cliente Borrado'}}</td>
                                    <td style="max-width: 500px; padding: 0.3rem; border: 1px solid lightgray;">{{$item->concept}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->invoiceStatus->name}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->project_id}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->created_at}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($item->total, 2, ',', '.')}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataFacturacionAnno['total'], 2, ',', '.')}}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="ModalProyectos" tabindex="-1" role="dialog" aria-labelledby="ModalProyectos" aria-hidden="true">
            <div class="modal-dialog modal-lg-custom modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Proyectos Activos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table id="tablaProyectosActivos" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Referencia</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Cliente </th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Campaña</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>
                                    <th style="border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Fecha Creacion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataBudgets['ProjectsActive'] as $item)
                                <tr class="clickable-row-sta" data-href="{{route('presupuesto.edit', $item->id)}}">
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->reference}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->cliente->company ?? $item->cliente->name ?? 'Cliente Borrado'}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->estadoPresupuesto->name}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->reference}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->total}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{$item->created_at}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataBudgets['ProjectsActive']->sum('total'), 2, ',', '.')}}</td>
                                    <td style="padding: 0.3rem; border: 1px solid lightgray;"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <script>
        function getColorByIndex(index, opacity = 1) {
            const r = (index * 137 + 83) % 256; // Números primos para rotación
            const g = (index * 197 + 67) % 256; // Números primos para rotación
            const b = (index * 229 + 47) % 256; // Números primos para rotación
            return `rgba(${r}, ${g}, ${b}, ${opacity})`;
        }

        document.addEventListener('DOMContentLoaded', function () {


            $('.select2').select2();

            $('#tablaGastosComunes').DataTable({responsive: true, paging: false});
            $('#tablaIngresosCash').DataTable({responsive: true, paging: false});
            $('#tablaFacturacionAnual').DataTable({responsive: true, paging: false});
            $('#tablaGastosComunesAnual').DataTable({responsive: true, paging: false});
            $('#tablaFacturacion').DataTable({responsive: true, paging: false});
            $('#tablaProyectosActivos').DataTable({responsive: true, paging: false});
            $('#tablaGastosComunesDeducibles').DataTable({responsive: true, paging: false});
            $('#tablaGastoComunesCash').DataTable({responsive: true, paging: false});
            $('#tablaGastosAsociadosAnual').DataTable({responsive: true, paging: false});
            $('#tablaGastosAsociadosCash').DataTable({responsive: true, paging: false});
            $('#tablaGastosAsociados').DataTable({responsive: true, paging: false});


        });

        // Configuración de gráficos

        // Facturación Mensual
        var ctx1 = document.getElementById("facturacion-mensual").getContext("2d");
        var myChart1 = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: @json($monthsToActually),
                datasets: [{
                    label: 'Facturación Mensual',
                    data: @json($billingMonthly),
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Facturación Anual
        var data = @json($allArray);

        var ctx2 = document.getElementById("facturacion-all-monthly").getContext("2d");
        var myChart2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                datasets: Object.keys(data).map(function (year, index) {

                    return {
                        label: 'Facturación ' + year,
                        data: data[year],
                        backgroundColor: getColorByIndex(index, 0.2),
                        borderColor: getColorByIndex(index),
                        borderWidth: 3,
                        fill: false
                    };
                })
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Beneficio Mensual
        var ctx3 = document.getElementById("beneficio-mensual").getContext("2d");
        var myChart3 = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: @json($monthsToActually),
                datasets: [{
                    label: 'Beneficio Mensual',
                    data: @json($totalBeneficio),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Productividad
        var ctx4 = document.getElementById("facturacion-media").getContext("2d");
        var myChart4 = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                datasets: [{
                    label: 'Media',
                    data: @json($monthlyAveragesValues),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 3
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#dateRange", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "es",
            });
        });
    </script>
@endsection
