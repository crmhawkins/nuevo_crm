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
              <span>10.974,81€</span>
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
              <span>{{$cliente->invoice_status_id(1)->sum('total')}}</span>
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
                    <div class="panel-avg--value">609,71€</div>
                    <div class="panel-avg--label">Importe medio de facturas</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">3.06</div>
                    <div class="panel-avg--label">Productos por factura</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">66.97</div>
                    <div class="panel-avg--label">Días de plazo medio de pago</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">18</div>
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
    name: 'Net Profit',
    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
    },
    {
    name: 'Revenue',
    data: [76, 85, 101, 98, 87, 105, 91, 114, 94]
    },
    {
    name: 'Free Cash Flow',
    data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
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
    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
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
        return "$ " + val + " thousands"
      }
    }
  }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>
@endsection
