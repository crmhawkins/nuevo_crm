@extends('layouts.appPortal')
@section('content')

<div class="content">
  <div class="row">
    <div class="col-md-4 col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="title">
            VENTAS RECIBIDAS
          </div>
          <div class="body-section row pt-4">
            <div class="col-2 icon-title">
              <i class="fa-solid fa-arrow-up"></i>
            </div>
            <div class="col-10 title-section ">
              <span>{{$cliente->facturasPorEstado(3)->sum('total')}}€</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="title">
            PENDIENTE DE PAGO
          </div>
          <div class="body-section row pt-4">
            <div class="col-2 icon-title">
              <i class="fa-solid fa-stopwatch"></i>
            </div>
            <div class="col-10 title-section ">
              <span>{{$cliente->facturasPorEstado(1)->sum('total')}}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="title">
            <div class="row align-items-center">
              <div class="col-auto">
                <span class="panel-title--emoji">👋</span>
              </div>
              <div class="col">
                <div class="panel-title">Hola {{$cliente->name}},</div>
                <div class="panel-subtitle">Has venido al lugar correcto</div>
              </div>
            </div>
          </div>
          <span class="random-tip--text">
            Supera tus objetivos. </span>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-md-8 col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="title">
              RESUMEN
              {{NOW()->format('Y')}}
          </div>
          <div class="row">
            <div class="col-12">
              <div id="chart">

              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 col-sm-12">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <div class="title">
                    PPRESUPUESTOS PENDIENTES
                  </div>
                </div>
              </div>
              <div class="body-section row pt-4">
                <div class="col-2 icon-title">
                  <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div class="col-10 title-section ">
                  <span>{{$cliente->presupuestosPorEstado(2)->count()}}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 mt-3">
          <div class="card">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <div class="title">
                    RATIOS DE FACTURAS
                  </div>
                </div>
              </div>
              <div class="body-section row pt-4">
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{ $cliente->facturasPorEstado(3)->count() > 0 ? $cliente->facturasPorEstado(3)->sum('total') / $cliente->facturasPorEstado(3)->count() : '0' }}€</div>
                    <div class="panel-avg--label">Importe medio de facturas</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{$cliente->facturasPorEstado(3)->sum('invoice_concepts_count') > 0 ? $cliente->facturasPorEstado(3)->sum('invoice_concepts_count') / $cliente->facturasPorEstado(3)->count() : '0' }}</div>
                    <div class="panel-avg--label">Productos por factura</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{$cliente->averagePaidTime(3)}}</div>
                    <div class="panel-avg--label">Días de plazo medio de pago</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{$cliente->facturasPorEstado(3)->count()}}</div>
                    <div class="panel-avg--label">Total de facturas</div>
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

@endsection
@section('scripts')
<script>

var options = {
  series: [
    {
    name: 'Facturas',
    data: @json(array_values($cliente->totalFacturasPorMes())).map(item => item ? item.total : 0)
    },
    {
    name: 'Presupuestos',
    data: @json(array_values($cliente->totalPresupuestosPorMes())).map(item => item ? item.total : 0)
    }
  ],
  chart: {
    type: 'bar',
    height: 350,
    toolbar: {
      show: false // Esto oculta la barra de herramientas
    }
  },
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      endingShape: 'rounded'
    },
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  xaxis: {
    categories: ['Ene','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dic'],
  },
  yaxis: {
    // title: {
    //   text: '$ (thousands)'
    // }
  },
  fill: {
    opacity: 1
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return  val + " €"
      }
    }
  }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>
@endsection
