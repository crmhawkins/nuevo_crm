@extends('layouts.app')

@section('titulo', 'Estadísticas')

@section('css')
    <link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.css" />
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
                        <div class="col-xl-12">
                            <div class="form-group mx-3 mb-3" style="display: flex;flex-direction: row;align-items: baseline;">
                                <label for="anio" style="margin-right: 1rem"><strong>Año</strong></label>
                                <select name="anio" id="anio" class="form-control select2">
                                    @foreach($arrayAnios as $anio)
                                        <option value="{{$anio}}" {{ $anio == $anioActual ? 'selected' : '' }}>{{$anio}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $index => $mes)
                            <div class="col-md-1">
                                <button id="meses" data-mes="{{str_pad($index + 1, 2, '0', STR_PAD_LEFT)}}" class="buttons_meses d-inline-block font-11 font-weight-500 text-dark text-uppercase mb-10 mx-3">{{ $mes }}</button>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mb-3">
                        <div class="col">
                            <div class="card card-sm" data-toggle="modal" data-target="#exampleModalCenter" style="cursor:pointer;">
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

                        <div class="col">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Presupuestos</span>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div>
                                            <span class="d-block display-6 font-weight-400 text-dark"><span class="counter-anim">{{$countTotalBudgets}}</span> €</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Facturación Anual Modal -->
                        <div class="col">
                            <div class="card card-sm" data-toggle="modal" data-target="#ModalFacturacionanual" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Facturación Anual</span>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div>
                                            <span class="d-block display-6 font-weight-400 text-dark">€ {{$dataFacturacionAnno['total']}}</span>
                                        </div>
                                        <div>
                                            <span class="text-success font-12 font-weight-600">+0%</span>
                                        </div>
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
                                            <span class="d-block display-6 font-weight-400 text-dark">{{$totalBeneficioAnual}}</span>
                                        </div>
                                        <div>
                                            <span class="text-danger font-12 font-weight-600">0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="card card-sm" data-toggle="modal" data-target="#ModalGastosAsociados" style="cursor:pointer;">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-dark text-uppercase mb-10">Gastos Comunes Anual</span>
                                    <div class="d-flex align-items-end justify-content-between">
                                        <div>
                                            <span class="d-block display-6 font-weight-400 text-dark">{{$dataGastosComunesAnual['total']}}</span>
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
                                <h3>Productividad</h3>
                                <canvas id="productividad-all"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h2>Trimestral</h2>
                            <ul class="nav nav-tabs custom-item">
                                <li class="active"><a data-toggle="tab" href="#primero">Primer Trimestre</a></li>
                                <li><a data-toggle="tab" href="#segundo">Segundo Trimestre</a></li>
                                <li><a data-toggle="tab" href="#tercero">Tercer Trimestre</a></li>
                                <li><a data-toggle="tab" href="#cuarto">Cuarto Trimestre</a></li>
                            </ul>

                            <div class="tab-content">
                                @foreach($iva as $itemIva)
                                    <div id="{{$itemIva['Trimestre']}}" class="tab-pane fade in active">
                                        <h3>{{$itemIva['Trimestre']}}</h3>
                                        <p>Total Facturado: <strong>{{$itemIva['totalFacturasIva']}} €</strong></p>
                                        <p>Total Gastos Asociados: <strong>{{$itemIva['gastosAsociadosTotalIva']}} €</strong></p>
                                        <h4>Diferencial = {{$itemIva['totalFacturasIva'] - $itemIva['gastosAsociadosTotalIva']}} €</h4>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Proyectos Activos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Contenido del modal...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalFacturacionanual" tabindex="-1" role="dialog" aria-labelledby="ModalFacturacionanualTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalFacturacionanualTitle">Facturación Anual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Contenido del modal...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ModalGastosAsociados" tabindex="-1" role="dialog" aria-labelledby="ModalGastosAsociadosTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalGastosAsociadosTitle">Gastos Comunes Anual</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Contenido del modal...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
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

    <script>
        $(document).ready(function () {
            $('#tableFacMen').DataTable({
                responsive: true,
            });

            $('.select2').select2();
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
                    borderWidth: 1
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

        console.log(Object.keys(@json($allArray)));
        // Facturación Anual
        var ctx2 = document.getElementById("facturacion-all-monthly").getContext("2d");
        var myChart2 = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                datasets: Object.keys(@json($allArray)).map(function (item, index) {
                    return {
                        label: 'Facturación ' + index,
                        data: item,
                        backgroundColor: "rgba(0,0,255,0.1)",
                        borderColor: "rgba(0,0,255,1)",
                        borderWidth: 1,
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
                    borderWidth: 1
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
        var ctx4 = document.getElementById("productividad-all").getContext("2d");
        var myChart4 = new Chart(ctx4, {
            type: 'bar',
            data: {
                labels: @json($nameUsers),
                datasets: [{
                    label: 'Productividad',
                    data: @json($userProductivity),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
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
