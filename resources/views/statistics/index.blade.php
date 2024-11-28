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

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <form action="{{ route('estadistica.index') }}" method="GET">
                            <div class="row">
                                <div class="col-xl-12">
                                    <div class="form-group mx-3 mb-3" style="display: flex; flex-direction: row; align-items: baseline;">
                                        <label for="mes" style="margin-right: 1rem"><strong>Mes</strong></label>
                                        <input type="month" name="mes" id="mes" class="form-control" style="margin-right: 1rem;" value="{{ request('mes', now()->format('Y-m')) }}">

                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalProyectos" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Proyectos Activos</span>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div>
                                            <span class="d-block display-6 font-weight-400 text-dark">{{$dataBudgets['total']}}+</span>
                                        </div>
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
                                                <tr>
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
                        <div class="col">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Presupuestos</span>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div>
                                            <span class="d-block display-6 font-weight-400 text-dark"><span class="counter-anim">{{number_format($countTotalBudgets, 2, ',', '.')}}</span> €</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facturación Anual Modal -->
                        <div class="col">
                            <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalFacturacionanual" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Facturación Anual</span>
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
                                            <tr>
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

                        <div class="col">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Beneficios Anual</span>
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
                    </div>
                        <div class="row mb-3">
                            <div class="col">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosComunesAnual" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Gastos Comunes Anual</span>
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
                                                <tr>
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

                            <div class="col">
                                <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosAsociadosAnual" style="cursor:pointer;">
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Gastos Asociados Anual</span>
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
                                                <tr>
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
                                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total: </td>
                                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataAsociadosAnual['total'], 2, ',', '.')}}</td>
                                                </tr>
                                            </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card card-sm" >
                                    <div class="card-body">
                                        <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Iva Anual</span>
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
                    <div class="row mb-3">

                        <div class="col">
                            <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalFacturacion" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Facturación</span>
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
                                            <tr>
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

                        <div class="col">
                            <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosComunes" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Gastos Comunes</span>
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
                                            @foreach($dataGastosComunes['gastos'] as $item)
                                            <tr>
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

                        <div class="col">
                            <div class="card card-sm" data-bs-toggle="modal" data-bs-target="#ModalGastosAsociados" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Gastos Asociados </span>
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
                                                    <th onclick="sortTable(0)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Orden</th>
                                                    <th onclick="sortTable(1)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Proveedor</th>
                                                    <th onclick="sortTable(2)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Concepto</th>
                                                    <th onclick="sortTable(3)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">F.Recepción</th>
                                                    <th onclick="sortTable(4)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Estado</th>
                                                    <th onclick="sortTable(5)" style="cursor: pointer; border: 2px solid lightsteelblue; padding: 0.3rem; color: white; background-color: dodgerblue; font-weight: bold;">Importe</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dataAsociados['array'] as $item)
                                                <tr>
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
                                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">Total:</td>
                                                    <td style="padding: 0.3rem; border: 1px solid lightgray;">{{number_format($dataAsociados['total'], 2, ',', '.')}}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Iva</span>
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
    </div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.8.0/dist/chart.min.js"></script>
    <script>
        function sortTable(columnIndex) {
            const table = document.getElementById("tablaGastosAsociados");
            const tbody = table.tBodies[0];
            const rows = Array.from(tbody.rows);

            // Toggle sorting direction
            const ascending = table.dataset.sortDirection !== 'asc';
            table.dataset.sortDirection = ascending ? 'asc' : 'desc';

            // Sort rows based on the selected column
            rows.sort((a, b) => {
                const aText = a.cells[columnIndex].innerText.trim();
                const bText = b.cells[columnIndex].innerText.trim();

                // Handle numeric and text sorting
                return ascending
                    ? aText.localeCompare(bText, undefined, { numeric: true })
                    : bText.localeCompare(aText, undefined, { numeric: true });
            });

            // Append sorted rows back to the tbody
            rows.forEach(row => tbody.appendChild(row));
        }
    </script>
    <script>
        function getColorByIndex(index, opacity = 1) {
            const r = (index * 137 + 83) % 256; // Números primos para rotación
            const g = (index * 197 + 67) % 256; // Números primos para rotación
            const b = (index * 229 + 47) % 256; // Números primos para rotación
            return `rgba(${r}, ${g}, ${b}, ${opacity})`;
        }

        $(document).ready(function () {


            $('.select2').select2();

            $('#tablaProyectosActivos').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaFacturacionAnual').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaGastosComunesAnual').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaGastosAsociadosAnual').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaGastosAsociados').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaFacturacion').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });

            $('#tablaGastosComunes').DataTable({
                responsive: true,
                paging: false // Desactiva la paginación

            });
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
@endsection
