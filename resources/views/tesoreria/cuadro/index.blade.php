@extends('layouts.app')

@section('titulo', 'Tesoreria')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta class="monthActual" name="mes" content="{{ $month }}">
<meta class="diasTotales" name="dias" content="{{ $diasDelMes }}">
<meta class="diaActual" name="dia" content="{{ $day }}">
<meta class="yearActual" name="anio" content="{{ $year }}">

<div class="page-heading card" style="box-shadow: none !important" >

    <div class="page-title card-body p-3">
        <div class="row justify-content-between">
            <div class="col-12 col-md-4 order-md-1 order-first">
                <h3><i class="bi bi-people"></i> Tesoreria</h3>
                <p class="text-subtitle text-muted">Cuadro de Tesoreria</p>
            </div>

            <div class="col-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="section mt-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="row justify-content-between">
                    <div class="col-1 text-center">
                        <a href="{{ route('admin.treasury.indexYear', $year - 1) }}" class="year-nav">◀</a>
                    </div>
                    <div class="col-10 text-center">
                        <button class="year btn btn-outline-secondary" value="{{ $year }}">{{ $year }}</button>
                    </div>
                    <div class="col-1 text-center">
                        <a href="{{ route('admin.treasury.indexYear', $year + 1) }}" class="year-nav">▶</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-12 text-center">
                        <div class="months">
                            <ul class="list-inline d-flex flex-wrap justify-content-center">
                                <li class="january list-inline-item">
                                    <button value="1" class="btn-month btn-january">ENERO</button>
                                </li>
                                <li class="february list-inline-item">
                                    <button value="2" class="btn-month btn-february">FEBRERO</button>
                                </li>
                                <li class="march list-inline-item">
                                    <button value="3" class="btn-month btn-march">MARZO</button>
                                </li>
                                <li class="april list-inline-item">
                                    <button value="4" class="btn-month btn-april">ABRIL</button>
                                </li>
                                <li class="may list-inline-item">
                                    <button value="5" class="btn-month btn-may">MAYO</button>
                                </li>
                                <li class="june list-inline-item">
                                    <button value="6" class="btn-month btn-june">JUNIO</button>
                                </li>
                                <li class="july list-inline-item">
                                    <button value="7" class="btn-month btn-july">JULIO</button>
                                </li>
                                <li class="august list-inline-item">
                                    <button value="8" class="btn-month btn-august">AGOSTO</button>
                                </li>
                                <li class="september list-inline-item">
                                    <button value="9" class="btn-month btn-september">SEPTIEMBRE</button>
                                </li>
                                <li class="october list-inline-item">
                                    <button value="10" class="btn-month btn-october">OCTUBRE</button>
                                </li>
                                <li class="november list-inline-item">
                                    <button value="11" class="btn-month btn-november">NOVIEMBRE</button>
                                </li>
                                <li class="december list-inline-item">
                                    <button value="12" class="btn-month btn-december">DICIEMBRE</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin" style="display:none; font-size:48px; margin-top: 50px;"></i>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="ocultar card-body">
                <div class="table-responsive table-banks">
                    <h3 class="text-center this-month" value-year="{{ $year }}">{{ $nameMonth }}</h3>
                    <table class="table table-month table-striped table-hover">
                        <thead class="thead-dark">
                            <tr class='col-month'>
                                <th></th>
                                @for ($i = 1; $i <= $diasDelMes; $i++)
                                    <th>{{ $i }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody class='tbody-month'>
                            @foreach($banksAccounts as $bankAccount)
                            <tr>
                                <td class="text-center">{{ $bankAccount->name }}</td>
                                @foreach ($bigArray['meses'][$month]['bancos'][$bankAccount->id]['Balance'] as $balance_dia)
                                    <td>{{ number_format($balance_dia, 2, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                            <tr class="table-secondary">
                                <td class="text-center">Total</td>
                                @foreach($arrayTotal['meses'][$month]['TOTAL'] as $totalDia)
                                    <td>{{ number_format($totalDia, 2, ',', '.') }}</td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="text-center">Total Previsto</td>
                                @foreach($arrayTotalPrevisto['meses'][$month]['TOTAL'] as $totalDia)
                                    <td>{{ number_format($totalDia, 2, ',', '.') }}</td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="ocultar card-body">
                <div class="prevision">
                    <h3 class="text-center title-Prevision">Previsión</h3>
                    <div class="table-forecast mt-4">
                        <h4 class="title-Facturas" style="text-align:center;">FACTURAS</h4>
                        <div class="table-responsive">
                            <table class="table table-facturas table-striped table-hover">
                                <thead class="caption">
                                    <tr>
                                        <th></th>
                                        @for ($i = 1; $i <= $diasDelMes; $i++)
                                            <th style="text-align:center;">{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody class="cuerpo">
                                @foreach($globalArrayFactura['Facturas'] as $factura)
                                <tr>
                                    <td style="text-align:center;" data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ?? 'Cliente Borrado '}}">{{ $factura->reference }}</td>
                                    <?php
                                        $dia = Carbon\Carbon::parse($factura->paid_date)->format('d');
                                        $pintada = false;
                                    ?>
                                    @for ($i = 1; $i <= $diasDelMes; $i++)
                                        @if($i<=$dia)
                                            @switch($factura->invoice_status_id)
                                                @case(1)
                                                    @if($i==$dia)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" data-status="{{$factura->invoice_status_id}}" class="clickActual" style="background-color:#B3E3FF;">{{ number_format($factura->total-$factura->paid_amount, 2, ',', '.') }}</td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" class="click" style="background-color:#B3E3FF;"></td>
                                                    @endif
                                                    @break
                                                @case(2)
                                                    @if($i==$dia)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" data-status="{{$factura->invoice_status_id}}" class="clickActual" style="background-color:#E07B7B;">{{ number_format($factura->total-$factura->paid_amount, 2, ',', '.') }}</td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" class="click" style="background-color:#E07B7B;"></td>
                                                    @endif
                                                    @break
                                                @case(3)
                                                    @if($i==$dia)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" class='clickActual' data-status="{{$factura->invoice_status_id}}" style="background-color:#9DFFA5;">{{number_format($factura->total, 2, ',', '.') }}</td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" class="click" style="background-color:#9DFFA5;"></td>
                                                    @endif
                                                    @break
                                                @case(4)
                                                    @if($i==$dia)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" data-status="{{$factura->invoice_status_id}}" class="clickActual" style="background-color:#ffc498;">{{ number_format($factura->total-$factura->paid_amount, 2, ',', '.') }}</td>
                                                        <?php $pintada = true; ?>
                                                    @else
                                                        @foreach($globalArrayFactura['PartialStatus'] as $pendiente)
                                                            @if($pendiente['id_factura']==$factura->id)
                                                                <?php
                                                                    $diaPendiente = Carbon\Carbon::parse($pendiente['date'])->format('d');
                                                                ?>
                                                                @if($i==$diaPendiente)
                                                                    <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" style="background-color:#9DFFA5;">{{ number_format($pendiente['quantity'], 2, ',', '.') }}</td>
                                                                    <?php $pintada = true; ?>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                        @if($pintada==false)
                                                            <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}" style="background-color:#ffc498;"></td>
                                                        @else
                                                            <?php $pintada = false; ?>
                                                        @endif
                                                    @endif
                                                    @break
                                                @default
                                            @endswitch
                                        @else
                                            @switch($factura->invoice_status_id)
                                                @case(3)
                                                    @if($i<=$day)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" class="click" data-value="{{$factura->id}}"> </td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$factura->id}}"> </td>
                                                    @endif
                                                @break
                                                @default
                                                <td data-toggle="tooltip" data-placement="top" title="{{$factura->cliente->company ?? $factura->cliente->name ??  'Cliente Borrado '}}" value="{{$i}}" class="click" data-value="{{$factura->id}}"> </td>
                                            @endswitch
                                        @endif
                                    @endfor
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="table-forecast mt-4">
                        <h4 class="title-Ingresos" style="text-align:center;">INGRESOS</h4>
                        <div class="table-responsive">
                            <table class="table table-ingresos table-striped table-hover">
                                <thead class="caption">
                                    <tr>
                                        <th></th>
                                        @for ($i = 1; $i <= $diasDelMes; $i++)
                                            <th style="text-align:center;">{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody class="cuerpo">
                                        @foreach($ingresos as $ingreso)
                                        <tr>
                                            @if($ingreso->getInvoice)
                                                <td style="text-align:center;" data-toggle="tooltip" data-placement="top" title="{{$ingreso->getInvoice->cliente->company ?? $ingreso->getInvoice->cliente->name ?? 'Cliente Borrado '}}">{{ $ingreso->title }}</td>
                                                <?php $dia = Carbon\Carbon::parse($ingreso->date)->format('d'); ?>
                                            @else
                                                <?php $dia = Carbon\Carbon::parse($ingreso->date)->format('d'); ?>
                                                <td style="text-align:center;" data-toggle="tooltip" data-placement="top" title="Sin cliente">{{ $ingreso->title }}</td>
                                            @endif
                                            @for ($i = 1; $i <= $diasDelMes; $i++)
                                                @if($i<=$dia)
                                                    @if($i==$dia)
                                                        @if($ingreso->getInvoice)
                                                            <td data-toggle="tooltip" data-placement="top" title="{{$ingreso->getInvoice->cliente->company ?? $ingreso->getInvoice->cliente->name  ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$ingreso->id}}" style="background-color:#76F380;">{{ number_format($ingreso->quantity, 2, ',', '.') }}</td>
                                                        @else
                                                            <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" data-value="{{$ingreso->id}}" style="background-color:#76F380;">{{ number_format($ingreso->quantity, 2, ',', '.') }}</td>
                                                        @endif
                                                    @else
                                                        @if($ingreso->getInvoice)
                                                            <td data-toggle="tooltip" data-placement="top" title="{{$ingreso->getInvoice->cliente->company ?? $ingreso->getInvoice->cliente->name  ??  'Cliente Borrado '}}" value="{{$i}}" data-value="{{$ingreso->id}}" data-type="ingreso" class="clickIngresos" style="background-color:#76F380;"></td>
                                                        @else
                                                            <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" data-value="{{$ingreso->id}}" data-type="ingreso" class="clickIngresos" style="background-color:#76F380;"></td>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($ingreso->getInvoice)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$ingreso->getInvoice->cliente->company ?? $ingreso->getInvoice->cliente->name  ??  'Cliente Borrado '}}" value="{{$i}}" class="clickIngresos" data-type="ingreso" data-value="{{$ingreso->id}}"> </td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" class="clickIngresos" data-type="ingreso" data-value="{{$ingreso->id}}"> </td>
                                                    @endif
                                                @endif
                                            @endfor
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-forecast mt-4">
                        <h4 class="title-Ingresos" style="text-align:center;">GASTOS</h4>
                        <div class="table-responsive">
                            <table class="table table-gastos table-striped table-hover">
                                <thead class="caption">
                                    <tr>
                                        <th></th>
                                        @for ($i = 1; $i <= $diasDelMes; $i++)
                                            <th style="text-align:center;">{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody class="cuerpo">
                                        @foreach($gastos as $gasto)
                                        <tr>
                                            <td style="text-align:center;">{{ $gasto->title }}</td>
                                            @php
                                            $dia = Carbon\Carbon::parse($gasto->date)->format('d');
                                            $mes = Carbon\Carbon::parse($gasto->date)->format('m');
                                            @endphp
                                            @for ($i = 1; $i <= $diasDelMes; $i++)
                                                @if ($mes == $month)
                                                    @if($i<=$dia)
                                                        @if($i==$dia)
                                                            <td value="{{$i}}" data-value="{{$gasto->id}}" style="background-color:#E07B7B;">{{ number_format(($gasto->quantity * (1 + ($gasto->iva/100))), 2, ',', '.') }}</td>
                                                        @else
                                                            <td value="{{$i}}" data-value="{{$gasto->id}}" data-type="gasto" class="clickGastos" style="background-color:#E07B7B;"></td>
                                                        @endif
                                                    @else
                                                        <td value="{{$i}}" class="clickGastos" data-type="gasto" data-value="{{$gasto->id}}"> </td>
                                                    @endif
                                                @else
                                                    <td value="{{$i}}" data-value="{{$gasto->id}}" data-type="gasto" class="clickGastos" style="background-color:#E07B7B;"></td>
                                                @endif
                                            @endfor
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="table-forecast mt-4">
                        <h4 class="title-gastosAsociados" style="text-align:center;">GASTOS ASOCIADOS</h4>
                        <div class="table-responsive">
                            <table class="table table-gastos-asociados table-striped table-hover">
                                <thead class="caption">
                                    <tr>
                                        <th></th>
                                        @for ($i = 1; $i <= $diasDelMes; $i++)
                                            <th style="text-align:center;">{{ $i }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                                <tbody class="cuerpo">
                                        @foreach($gastosAsociados as $gasto)

                                        <tr>
                                            @if($gasto->budget)
                                                <td data-toggle="tooltip" data-placement="top" title="{{$gasto->budget->cliente->company ??  $gasto->budget->cliente->name}}" style="width: 150px;text-align:center;">{{ $gasto->title }}</td>
                                                <?php $dia = Carbon\Carbon::parse($gasto->date)->format('d'); ?>
                                            @else
                                                <td data-toggle="tooltip" data-placement="top" title="Sin cliente" style="width: 150px;text-align:center;">{{ $gasto->title }}</td>
                                                <?php $dia = Carbon\Carbon::parse($gasto->date)->format('d'); ?>
                                            @endif
                                            @for ($i = 1; $i <= $diasDelMes; $i++)
                                                @if($i<=$dia)
                                                    @if($i==$dia)
                                                        @if($gasto->budget)
                                                            <td data-toggle="tooltip" data-placement="top" title="{{$gasto->budget->cliente->company ??  $gasto->budget->cliente->name}}" value="{{$i}}" data-value="{{$gasto->id}}" style="background-color:#E07B7B;">{{ number_format(($gasto->quantity * (1 + ($gasto->iva/100))), 2, ',', '.') }}</td>
                                                        @else
                                                            <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" data-value="{{$gasto->id}}" style="background-color:#E07B7B;">{{ number_format(($gasto->quantity * (1 + ($gasto->iva/100))), 2, ',', '.') }}</td>
                                                        @endif
                                                    @else
                                                        @if($gasto->budget)
                                                            <td data-toggle="tooltip" data-placement="top" title="{{$gasto->budget->cliente->company ??  $gasto->budget->cliente->name}}" value="{{$i}}" data-value="{{$gasto->id}}" data-type="gastoAsociado" class="clickGastosAsociados" style="background-color:#E07B7B;"></td>
                                                        @else
                                                            <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" data-value="{{$gasto->id}}" data-type="gastoAsociado" class="clickGastosAsociados" style="background-color:#E07B7B;"></td>
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($gasto->budget)
                                                        <td data-toggle="tooltip" data-placement="top" title="{{$gasto->budget->cliente->company ??  $gasto->budget->cliente->name}}" value="{{$i}}" class="clickGastosAsociados" data-type="gastoAsociado" data-value="{{$gasto->id}}"> </td>
                                                    @else
                                                        <td data-toggle="tooltip" data-placement="top" title="Sin cliente" value="{{$i}}" class="clickGastosAsociados" data-type="gastoAsociado" data-value="{{$gasto->id}}"> </td>
                                                    @endif
                                                @endif
                                            @endfor
                                        </tr>
                                        @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<style>
    .btn-month {
        width: 80px; /* Ajusta el ancho de los botones de mes */
        margin: 3px;
        font-size: 12px; /* Reduce el tamaño de la fuente */
    }

    .table-responsive {
        font-size: 10px; /* Reduce el tamaño de la fuente en las tablas */
    }

    th, td {
        padding: 3px; /* Reduce el espacio dentro de las celdas */
        text-align: center;
        white-space: nowrap; /* Evita que el texto se divida en múltiples líneas */
        border: 1px solid black !important;

    }

    .table-month th, .table-month td {
        width: 10px;
        padding-left: 5px !important;
        padding-right: 5px !important;
    }

    .table-facturas th, .table-facturas td,
    .table-ingresos th, .table-ingresos td,
    .table-gastos th, .table-gastos td,
    .table-gastos-asociados th, .table-gastos-asociados td {
        overflow:auto;
        max-width: 60px !important; /* Ajusta el ancho de las celdas en tablas específicas */
        max-height: 20px !important; /* Ajusta el ancho de las celdas en tablas específicas */
        padding: 15px 5px 15px 5px !important;
    }

    td::-webkit-scrollbar ,th::-webkit-scrollbar{
        height: 5px !important;
    }
    td::-webkit-scrollbar-track,th::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.1); /* Fondo semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    td::-webkit-scrollbar-thumb,th::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.5); /* Pulgar negro semi-translúcido */
        border-radius: 10px; /* Bordes redondeados */
    }

    td::-webkit-scrollbar-thumb:hover,th::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.7); /* Pulgar más oscuro cuando se desplaza */
    }
    .year-nav {
        font-size: 20px;
    }

    @media (max-width: 900px) {
        .btn-month {
            width: 70px;
            font-size: 10px;
        }

        .year-nav {
            font-size: 18px;
        }

        th, td {
            font-size: 10px; /* Aún más compacto en pantallas pequeñas */
            padding: 2px;
        }
    }
</style>
@endsection

@section('scripts')
<!-- JS -->
<script type="text/javascript">
    $(document).ready(function() {
        function forceNumeric(){
            var $input = $(this);
            $input.val($input.val().replace(/[^\d,.]+/g,''));
        }

        $(function () {
          $('[data-toggle="tooltip"]').tooltip({ boundary: 'viewport' });
        })

        $(document).on('propertychange input', 'input[type="number"]', forceNumeric);

        $("#sidebar").remove();
        $(".navbar").remove();

        $("#main").css("margin-left", "0px");

        $("html").css("background", "#f4f4f4");

        $(document).on( "click", ".click", function(){
            var id = $(this).attr("data-value");
            var dia = $(this).attr("value");
            var mes = $(".monthActual").attr('content');
            var anio = $(".year").val();
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Moverás la factura "+id+ " a la fecha "+anio+"-"+mes+"-"+dia,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, mover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    $.when( saveInvoiceAjax(id, dia, mes, anio) ).then(function( data, textStatus, jqXHR ) {
                        if(jqXHR.responseText!=503){
                            refreshTableTesoreria();
                            refreshTableInvoices();
                            Swal.fire(
                                'Éxito',
                                'La factura ha sido movida.',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error',
                                'Error al mover la factura',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on( "click", ".clickIngresos", function(){
            var id = $(this).attr("data-value");
            var dia = $(this).attr("value");
            var mes = $(".monthActual").attr('content');
            var anio = $(".year").val();
            var type = $(this).attr("data-type");

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Moverás el ingreso "+id+ " a la fecha "+anio+"-"+mes+"-"+dia,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, mover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    $.when( saveDateContabilidad(id, dia, mes, anio, type) ).then(function( data, textStatus, jqXHR ) {
                        if(jqXHR.responseText!=503){
                            refreshTableTesoreria();
                            refreshTableInvoices();
                            refreshTableIngresos();
                            Swal.fire(
                                'Éxito',
                                'El ingreso ha sido movido.',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error',
                                'Error al mover el ingreso.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on( "click", ".clickGastos", function(){
            var id = $(this).attr("data-value");
            var dia = $(this).attr("value");
            var mes = $(".monthActual").attr('content');
            var anio = $(".year").val();
            var type = $(this).attr("data-type");
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Moverás el gasto "+id+ " a la fecha "+anio+"-"+mes+"-"+dia,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, mover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    $.when( saveDateContabilidad(id, dia, mes, anio, type) ).then(function( data, textStatus, jqXHR ) {
                        if(jqXHR.responseText!=503){
                            refreshTableTesoreria();
                            refreshTableInvoices();
                            refreshTableGastos();
                            Swal.fire(
                                'Éxito',
                                'El gasto sido movido.',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error',
                                'Error al mover el gasto.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on( "click", ".clickGastosAsociados", function(){
            var id = $(this).attr("data-value");
            var dia = $(this).attr("value");
            var mes = $(".monthActual").attr('content');
            var anio = $(".year").val();
            var type = $(this).attr("data-type");
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Moverás el gasto asociado "+id+ " a la fecha "+anio+"-"+mes+"-"+dia,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, mover',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.value) {
                    $.when( saveDateContabilidad(id, dia, mes, anio, type) ).then(function( data, textStatus, jqXHR ) {
                        if(jqXHR.responseText!=503){
                            refreshTableTesoreria();
                            refreshTableInvoices();
                            refreshTableGastosAsociados();
                            Swal.fire(
                                'Éxito',
                                'El gasto asociado ha sido movido.',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error',
                                'Error al mover el gasto asociado.',
                                'error'
                            );
                        }
                    });
                }
            });
        });

        $(document).on( "click", ".clickActual", function(){
            var id = $(this).attr("data-value");
            var importeFactura = $(this).html();
            var estadoSeleccionado = $(this).attr("data-status");
            var dia = $(this).attr("value");
            var mes = $(".monthActual").attr('content');
            var anio = $(".year").val();
            var impEditable = '';
            var bancoEditable = '';

            if(estadoSeleccionado==4){
                var bancoEditable = '<select id="swal-input2" class="swal2-input m-1"><option value="0" selected="selected">Seleccionar banco</option>@foreach($banksAccounts as $bankAccount)<option value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>@endforeach</select>';
                var impEditable = '<input id="swal-input3" class="swal2-input m-1" type="number" placeholder="Importe">';
            }else if(estadoSeleccionado==3){
                var bancoEditable = '<select id="swal-input2" class="swal2-input m-1"><option value="0" selected="selected">Seleccionar banco</option>@foreach($banksAccounts as $bankAccount)<option value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>@endforeach</select>';
            }
            Swal.fire({
                title: 'Modificar',
                icon: 'question',
                html:
                    '<p>¿Quieres cambiar el estado de la factura '+id+'?' +
                    '<select id="swal-input1" class="swal2-input m-1 estadoSelect"><option value="0" selected="selected">Cambiar estado</option><option value="1">Pendiente</option><option value="2">No cobrada</option><option value="3">Cobrada</option><option value="4">Cobrada parcialmente</option></select>'+
                    bancoEditable +
                    impEditable,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancelar',
                preConfirm: function (value) {
                    return new Promise(function (resolve) {
                        var estado = $("#swal-input1").val();
                        var banco = $("#swal-input2").val();
                        var importe = $("#swal-input3").val();
                        $(".swal2-messages").remove();
                        $("#swal2-html-container").append("<div class='swal2-messages'>");
                        flagImporte = false;
                        flagBanco = false;
                        if (value){
                            $(".swal2-messages").empty();
                            if (estado!=0){
                                if(estado==3){
                                    if(banco!=0){
                                        importeFactura = importeFactura.replace(".", "");
                                        importeFactura = importeFactura.replace(",", ".");
                                        $.when( saveInvoiceDataAjax(estado, banco, importeFactura, id, dia, mes, anio) ).then(function( data, textStatus, jqXHR ) {
                                            if (jqXHR.responseJSON.estado == "OK"){
                                                refreshTableTesoreria();
                                                refreshTableInvoices();
                                                refreshTableIngresos();
                                                Swal.fire(
                                                    'Éxito',
                                                    'La factura ha sido modificada.',
                                                    'success'
                                                );
                                            }else{
                                                Swal.fire(
                                                    'Error',
                                                    'Error al modificar la factura.',
                                                    'error'
                                                );
                                            }
                                        });
                                    }else{
                                        swal.enableButtons();
                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>Necesitas seleccionar un banco</span>");
                                    }
                                }else if(estado == 4){
                                    //Si ha introducido un importe
                                    if(importe != 0){
                                        //Quitar los punto al importe y cambiar los comas por puntos.
                                        importeFactura = importeFactura.replace(".", "");
                                        importeFactura = importeFactura.replace(",", ".");
                                        if(parseFloat(importe) >= parseFloat(importeFactura)){
                                            flagImporte = false;
                                            swal.enableButtons();
                                            $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El importe no puede ser mayor o igual que el total en una factura cobrada parcialmente</span>");
                                        }else{
                                            flagImporte = true;
                                        }
                                    }else{
                                        flagImporte = false;
                                        swal.enableButtons();
                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>Necesitas introducir un importe<br></span>");
                                    }
                                    //Si ha introducido un banco
                                    if(banco!=0){
                                       flagBanco = true;
                                    }else{
                                        flagBanco = false;
                                        swal.enableButtons();
                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>Necesitas seleccionar un banco</span>");
                                    }
                                    //Comprobamos que todos los campos estan rellenos, para poder hacer el ingreso
                                    if(flagBanco && flagImporte){
                                        $.when( saveInvoiceDataAjax(estado, banco, importe, id, dia, mes, anio) ).then(function( data, textStatus, jqXHR ) {
                                            if (jqXHR.responseJSON.estado == "OK"){
                                                refreshTableTesoreria();
                                                refreshTableInvoices();
                                                Swal.fire(
                                                    'Éxito',
                                                    'La factura ha sido modificada.',
                                                    'success'
                                                );
                                            }else{
                                                Swal.fire(
                                                    'Error',
                                                    'Error al modificar la factura.',
                                                    'error'
                                                );
                                            }
                                        });
                                    }
                                }
                                if(estado == 2 || estado == 1){
                                    $.when( saveInvoiceDataAjax(estado, banco, importe, id, dia, mes, anio) ).then(function( data, textStatus, jqXHR ) {
                                        if (jqXHR.responseJSON.estado == "OK"){
                                            refreshTableTesoreria();
                                            refreshTableInvoices();
                                            Swal.fire(
                                                'Éxito',
                                                'La factura ha sido modificada.',
                                                'success'
                                            );
                                        }else{
                                            Swal.fire(
                                                'Error',
                                                'Error al modificar la factura.',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            }else{
                                swal.enableButtons();
                                $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>Necesitas seleccionar un estado</span>");
                            }
                        }
                    });
                },
            });
        });

        $(document).on("change", ".estadoSelect", function(){
            var estado = this.value;
            $(".swal2-messages").empty();
            if(estado==4){
                $("#swal-input2").remove();
                $("#swal-input3").remove();
                $("#swal2-html-container p").append('<select id="swal-input2" class="swal2-input m-1"><option value="0" selected="selected">Seleccionar banco</option>@foreach($banksAccounts as $bankAccount)<option value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>@endforeach</select>');
                $("#swal2-html-container p").append('<input id="swal-input3" class="swal2-input m-1" type="number" style="max-width:100%;" placeholder="Importe">');
            }else if(estado == 3){
                $("#swal-input2").remove();
                $("#swal-input3").remove();
                $("#swal2-html-container p").append('<select id="swal-input2" class="swal2-input m-1"><option value="0" selected="selected">Seleccionar banco</option>@foreach($banksAccounts as $bankAccount)<option value="{{$bankAccount->id}}">{{$bankAccount->name}}</option>@endforeach</select>');
            }else{
                $("#swal-input2").remove();
                $("#swal-input3").remove();
            }
        });

        $(".btn-month").click(function() {
            var mes = $(this).val();
            var anio = $(".year").val();
            $(".table-facturas").empty();
            loadSpinner();
            $.ajax({
                type: "GET",
                url: '/treasury/'+anio+'/'+mes+'/getMonthYear',
                dataType: "json",
                success: function (data){
                    constructTable(data.result);
                    refreshTableInvoices();
                    constructTableIngresos(data.ingresos);
                    constructTableGastos(data.gastos);
                    constructTableGastosAsociados(data.gastosAsociados);
                    stopSpinner();
                    $(".title-Prevision").show();
                    $(".title-Facturas").show();
                },
            });
        });

        function refreshTableInvoices(){
            var mes = $(".monthActual").attr('content');
            var anio = $(".yearActual").attr('content');
            $.ajax({
                type: "POST",
                url: '/treasury/getInvoices',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    "month": mes,
                    "year": anio
                },
                dataType: "json",
                success: function (data){
                    constructTableFacturas(data);
                },
            });
        }

        function refreshTableIngresos(){
            var mes = $(".monthActual").attr('content');
            var anio = $(".yearActual").attr('content');
            loadSpinner();
            $.ajax({
                type: "GET",
                url: '/treasury/'+anio+'/'+mes+'/getMonthYear',
                dataType: "json",
                success: function (data){
                    constructTableIngresos(data.ingresos);
                    stopSpinner();
                },
            });
        }

        function refreshTableGastos(){
            var mes = $(".monthActual").attr('content');
            var anio = $(".yearActual").attr('content');
            loadSpinner();
            $.ajax({
                type: "GET",
                url: '/treasury/'+anio+'/'+mes+'/getMonthYear',
                dataType: "json",
                success: function (data){
                    constructTableGastos(data.gastos);
                    stopSpinner();
                },
            });
        }

        function refreshTableGastosAsociados(){
            var mes = $(".monthActual").attr('content');
            var anio = $(".yearActual").attr('content');
            $.ajax({
                type: "POST",
                url: '/treasury/getGastosAsociados',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    "month": mes,
                    "year": anio
                },
                dataType: "json",
                success: function (data){
                    constructTableGastosAsociados(data);
                },
            });
        }

        function saveInvoiceAjax(id, dia, mes, anio){
            return  $.ajax({
                type: "POST",
                url: '/treasury/SaveInvoice',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                    'dia': dia,
                    'mes': mes,
                    'anio': anio
                },
                dataType: "json"
            });
        }

        function changeInvoiceStatus(estado, id){
            return  $.ajax({
                type: "POST",
                url: '/treasury/ChangeInvoiceStatus',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'estado': estado,
                    'id': id
                },
                dataType: "json"
            });
        }

        function saveInvoiceDataAjax(estado, banco, importe, id, dia, mes, anio){
            return  $.ajax({
                type: "POST",
                url: '/treasury/SaveInvoiceData',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'estado': estado,
                    'banco': banco,
                    'importe': importe,
                    'id': id,
                    'dia': dia,
                    'mes': mes,
                    'anio': anio
                },
                dataType: "json"
            });
        }

        function saveDateContabilidad(id, dia, mes, anio, type){
            return  $.ajax({
                type: "POST",
                url: '/treasury/saveDateContabilidad',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                    'dia': dia,
                    'mes': mes,
                    'anio': anio,
                    'type': type
                },
                dataType: "json"
            });
        }

        function constructTableFacturas(data){
            var dias = $(".diasTotales").attr('content');
            var diaActual = $(".diaActual").attr('content');
            var pintada;
            $(".table-facturas").empty();
            $(".table-facturas").append("<thead class='caption'>");
            $(".table-facturas .caption").append("<tr class='col-dias-month'>");
            $(".col-dias-month").append("<th></th>");
            for(var i = 1 ; i <= dias; i++){
                $(".col-dias-month").append("<th>"+i+"</th>");
            }
            $(".table-facturas").append("<tbody class='tbody-facturas'>");
            var table = "<tr>";
            $.each( data.Facturas, function( key, value ) {
                table += "<td style='text-align:center;' data-toggle='tooltip' data-placement='top' >"+value.reference+"</td>";
                pintada = false;
                var date = new Date(value.paid_date);
                var day = date.getUTCDate();
                var total;
                for (var i=1;i<=dias;i++){
                    if (i<=day){
                        switch(value.invoice_status_id){
                            case 1:
                                if (i==day){
                                    total = (value.total-value.paid_amount);
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickActual' data-status='"+value.invoice_status_id+"' style='background-color:#B3E3FF;'>"+number_format(total, 2, ',', '.');+"</td>";
                                }else{
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='click' style='background-color:#B3E3FF;'></td>";
                                }
                            break;
                            case 2:
                                if (i==day){
                                    total = (value.total-value.paid_amount);
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickActual' data-status='"+value.invoice_status_id+"' style='background-color:#E07B7B;'>"+number_format(total, 2, ',', '.');+"</td>";
                                }else{
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='click' style='background-color:#E07B7B;'></td>";
                                }
                            break;
                            case 3:
                                if (i==day){
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickActual' data-status='"+value.invoice_status_id+"' style='background-color:#9DFFA5;'>Cobrada<br>("+number_format(value.total, 2, ',', '.');+" )</td>";
                                }else{
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='click' style='background-color:#9DFFA5;'></td>";
                                }
                            break;
                            case 4:
                                if (i==day){
                                    total = (value.total-value.paid_amount);
                                    table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickActual' data-status='"+value.invoice_status_id+"' style='background-color:#ffc498;'>"+number_format(total, 2, ',', '.');+"</td>";
                                    pintada = true;
                                }else{
                                    $.each(data.PartialStatus, function(key1, value2){
                                        if (value2.id_factura==value.id){
                                            var datePendiente = new Date(value2.date);
                                            var diaPendiente = datePendiente.getUTCDate();
                                            if(i==diaPendiente){
                                                table += "<td value="+i+" data-value="+value.id+" style='background-color:#9DFFA5;'>"+number_format(value2.quantity, 2, ',', '.');+"</td>"
                                                pintada = true;
                                            }
                                        }
                                    });
                                    if (pintada==false){
                                        table +=  "<td value='"+i+"' data-value='"+value.id+"' style='background-color:#ffc498;'></td>";
                                    }else{
                                        pintada = false;
                                    }
                                }
                            break;
                        }
                    }else{
                        switch(value.invoice_status_id){
                            case 3:
                                if(i<=diaActual){
                                    table += "<td value='"+i+"' data-value='"+value.id+"' class='click' style=''> </td>";
                                }else{
                                    table += "<td value='"+i+"' data-value='"+value.id+"' style=''> </td>";
                                }
                            break;
                            default:
                                table += "<td value='"+i+"' data-value='"+value.id+"' class='click' style=''> </td>";
                        }
                    }
                }
                table += "</tr>";
            });
            $(".tbody-facturas").append(table);
        }

        function constructTableIngresos(data){
            var dias = $(".diasTotales").attr('content');
            var diaActual = $(".diaActual").attr('content');
            $(".table-ingresos").empty();
            $(".table-ingresos").append("<thead class='caption'>");
            $(".table-ingresos .caption").append("<tr class='col-dias-month-ingresos'>");
            $(".col-dias-month-ingresos").append("<td></td>");
            for(var i = 1 ; i <= dias; i++){
                $(".col-dias-month-ingresos").append("<th>"+i+"</th>");
            }
            $(".table-ingresos").append("<tbody class='tbody-ingresos'>");
            var table = "<tr>";
            $.each( data, function( key, value ) {
                table += "<td>"+value.title+"</td>";
                var date = new Date(value.date);
                var day = date.getUTCDate();
                var total;
                for (var i=1;i<=dias;i++){
                    if (i<=day){
                        if (i==day){
                            total = value.quantity;
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' style='background-color:#76F380;'>"+number_format(total, 2, ',', '.');+"</td>";
                        }else{
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickIngresos' data-type='ingreso' style='background-color:#76F380;'></td>";
                        }
                    }else{
                      table +=  "<td value='"+i+"' style='' class='clickIngresos' data-type='ingreso' data-value='"+value.id+"'> </td>";
                    }
                }
                table += "</tr>";
            });
            $(".tbody-ingresos").append(table);
        }

        function constructTableGastos(data){
            var dias = $(".diasTotales").attr('content');
            var diaActual = $(".diaActual").attr('content');
            $(".table-gastos").empty();
            $(".table-gastos").append("<thead class='caption'>");
            $(".table-gastos .caption").append("<tr class='col-dias-month-gastos'>");
            $(".col-dias-month-gastos").append("<td></td>");
            for(var i = 1 ; i <= dias; i++){
                $(".col-dias-month-gastos").append("<th>"+i+"</th>");
            }
            $(".table-gastos").append("<tbody class='tbody-gastos'>");
            var table = "<tr>";
            $.each( data, function( key, value ) {
                table += "<td>"+value.title+"</td>";
                var date = new Date(value.date);
                var day = date.getUTCDate();
                var total;
                for (var i=1;i<=dias;i++){
                    if (i<=day){
                        if (i==day){
                            total = value.quantity;
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' style='background-color:#E07B7B;'>"+number_format(total, 2, ',', '.');+"</td>";
                        }else{
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickGastos' data-type='gasto' style='background-color:#E07B7B;'></td>";
                        }
                    }else{
                      table +=  "<td value='"+i+"' style='' class='clickGastos' data-type='gasto' data-value='"+value.id+"'> </td>";
                    }
                }
                table += "</tr>";
            });
            $(".tbody-gastos").append(table);
        }

        function constructTableGastosAsociados(data){
            var dias = $(".diasTotales").attr('content');
            var diaActual = $(".diaActual").attr('content');
            $(".table-gastos-asociados").empty();
            $(".table-gastos-asociados").append("<thead class='caption'>");
            $(".table-gastos-asociados .caption").append("<tr class='col-dias-month-gastos-asociados'>");
            $(".col-dias-month-gastos-asociados").append("<td></td>");
            for(var i = 1 ; i <= dias; i++){
                $(".col-dias-month-gastos-asociados").append("<th>"+i+"</th>");
            }
            $(".table-gastos-asociados").append("<tbody class='tbody-gastos-asociados'>");
            var table = "<tr>";
            $.each( data, function( key, value ) {
                table += "<td>"+value.title+"</td>";
                var date = new Date(value.date);
                var day = date.getUTCDate();
                var total;
                for (var i=1;i<=dias;i++){
                    if (i<=day){
                        if (i==day){
                            total = value.quantity;
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' style='background-color:#E07B7B;'>"+number_format(total, 2, ',', '.');+"</td>";
                        }else{
                            table +=  "<td value='"+i+"' data-value='"+value.id+"' class='clickGastosAsociados' data-type='gastoAsociado' style='background-color:#E07B7B;'></td>";
                        }
                    }else{
                      table +=  "<td value='"+i+"' style='' class='clickGastosAsociados' data-type='gastoAsociado' data-value='"+value.id+"'> </td>";
                    }
                }
                table += "</tr>";
            });
            $(".tbody-gastos-asociados").append(table);
        }
        /************* TESORERIA ******************/
        function number_format(number, decimals, dec_point, thousands_sep){
            let formattedNumber = parseFloat(number).toFixed(decimals);
            let nstr = formattedNumber.toString();
            let x = nstr.split('.');
            let x1 = x[0];
            let x2 = x.length > 1 ? dec_point + x[1] : '';
            let rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');
            }
            return x1 + x2;
        }

        function refreshTableTesoreria(){
            var mes = $(".monthActual").attr('content');
            var anio = $(".yearActual").attr('content');
            loadSpinner();
            $.ajax({
                type: "GET",
                url: '/treasury/'+anio+'/'+mes+'/getMonthYear',
                dataType: "json",
                success: function (data){
                    constructTable(data.result);
                    stopSpinner();
                },
            });
        }

        function loadSpinner(){
            $(".fa-spinner").css("display", "block");
            //$(".table-banks").css("display", "none");
            $(".ocultar").css("display", "none");
            $(".title-Prevision").hide();
            $(".title-Facturas").hide();
        }

        function stopSpinner(){
            $(".fa-spinner").css("display", "none");
            //$(".table-bankss").css("display", "block");
            $(".ocultar").css("display", "block");
            $(".title-Prevision").show();
            $(".title-Facturas").show();
        }

        function constructTable(data){
            $(".monthActual").attr('content', data.month);
            $(".diasTotales").attr('content', data.DiasDelMes);
            $(".yearActual").attr('content', data.year);
            $(".table-banks").empty();
            $(".table-banks").append("<h3 class='text-center this-month' value-year='"+data.year+"'>"+data.nameMonth+"</h3>");
            $(".table-banks").append("<table class='table table-month table-striped table-hover'>");
            $(".table-month").append("<thead class='thead-dark'>");
            $(".table-month .thead-dark").append("<tr class='col-month'>");
            $(".col-month").append("<th></th>");
            for(var i = 1 ; i <= data.DiasDelMes; i++){
                $(".col-month").append("<th>"+i+"</th>");
            }
            $(".table-month").append("<tbody class='tbody-month'>");
            $.each( data.bankAccounts, function( key, value ) {
                var tabla = "<tr>";
                tabla += "<td>"+value.name+"</td>";
                $.each(data.BigArray['meses'][data.month]['bancos'][value.id]['Balance'], function (key2, value2){
                    tabla += "<td>"+number_format(value2, 2, ',', '.');+"</td>";
                });
                tabla += "</tr>";
                $(".tbody-month").append(tabla);
            });
            var tablaTotal = "<tr class='table-secondary'>";
            tablaTotal += "<td>Total</td>";
            $.each(data.arrayTotal['meses'][data.month]['TOTAL'], function (key, value){
                tablaTotal += "<td>"+number_format(value, 2, ',', '.');+"</td>";
            });
            tablaTotal += "</tr>";
            $(".tbody-month").append(tablaTotal);
            var tablaTotalPrevisto = "<tr>";
            tablaTotalPrevisto += "<td>Total Previsto</td>";
            $.each(data.arrayTotalPrevisto ['meses'][data.month]['TOTAL'], function (key, value){
                tablaTotalPrevisto += "<td>"+number_format(value, 2, ',', '.');+"</td>";
            });
            tablaTotalPrevisto += "</tr>";
            $(".tbody-month").append(tablaTotalPrevisto);
        }
    });
</script>
@endsection
