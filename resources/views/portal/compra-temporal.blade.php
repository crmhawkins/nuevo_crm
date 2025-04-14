@extends('layouts.appPortal')

@section('content')
<div class="content">
  @if (session('success_message'))
  <div class="alert alert-success">
      {!! session('success_message') !!}  
  </div>
  @endif

  @if (session('error_message'))
  <div class="alert alert-danger">
      {!! session('error_message') !!}  
  </div>
  @endif
  
<!-- Modal -->
<div class="modal fade" id="b2bModal" tabindex="-1" aria-labelledby="b2bModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 90%; width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="b2bModalLabel">Que quiere comprar </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="row justify-content-center">
            <div class="col">
              <div class="image-container">
                <h4 class="">B2B Sitio web</h4>
                <br>
                <a href="/portal/estructura/web">
                  <img src="/assets/images/plantillas/web1.png" alt="B2B Sitio web" class="img-fluid clickable-image">
                </a>
              </div>
            </div>
            <div class="col">
              <div class="image-container">
                <h4 class="">B2B Ecommerce</h4>
                <br>
                <a href="/portal/estructura/eccommerce">
                  <img src="/assets/images/plantillas/ecommerce2.png" alt="B2B Ecommerce" class="img-fluid clickable-image">
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-12 text-center">
      <div class="row justify-content-center">
        <div class="col-auto">
          <a href="/portal/estructura/web" class="btn btn-light btn-lg">B2B Sitio web</a>
        </div>
        <div class="col-auto">
          <a href="/portal/estructura/eccommerce" class="btn btn-light btn-lg">B2B Ecommerce</a>
        </div>
      </div>
    </div>
  </div>

  </div>
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
              <span>{{$cliente->facturasPorEstado(3)->sum('total') + $cliente->facturasPorEstado(4)->sum('total') }}€</span>
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
                <div class="panel-title">Hola {{$cliente->company ?? $cliente->name}},</div>
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
                    <div class="panel-avg--value">{{$cliente->facturasPorEstado(3)->count() + $cliente->facturasPorEstado(4)->count() > 0 ? number_format(($cliente->facturasPorEstado(3)->sum('total') + $cliente->facturasPorEstado(4)->sum('total')) / ($cliente->facturasPorEstado(3)->count() + $cliente->facturasPorEstado(4)->count()), 2, '.') : '' }}€</div>
                    <div class="panel-avg--label">Importe medio de facturas</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{ $cliente->facturasPorEstado(3)->sum('invoice_concepts_count') + $cliente->facturasPorEstado(4)->sum('invoice_concepts_count') > 0 ? number_format(($cliente->facturasPorEstado(3)->sum('invoice_concepts_count') + $cliente->facturasPorEstado(4)->sum('invoice_concepts_count')) / ($cliente->facturasPorEstado(3)->count() + $cliente->facturasPorEstado(4)->count()), 2, '.') : '0' }}</div>
                    <div class="panel-avg--label">Productos por factura</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{ number_format( $cliente->averagePaidTime(3) , 2, '.') }}</div>
                    <div class="panel-avg--label">Días de plazo medio de pago</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="panel-avg--content">
                    <div class="panel-avg--value">{{$cliente->facturasPorEstado(3)->count() + $cliente->facturasPorEstado(4)->count()}}</div>
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
@include('partials.toast')

<script>
// Mostrar el modal automáticamente cuando la página se carga
window.addEventListener('load', function () {
    var modal = new bootstrap.Modal(document.getElementById('b2bModal'));
    modal.show();
});

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
    labels: {
      formatter: function (value) {
        return value.toFixed(0); // Formatea los valores del eje Y sin decimales
      }
    }
  },
  fill: {
    opacity: 1
  },
  tooltip: {
    y: {
      formatter: function (val) {
        return  val.toFixed(2) + " €"; // Formatea los valores del tooltip con dos decimales
      }
    }
  }
};

var chart = new ApexCharts(document.querySelector("#chart"), options);
chart.render();
</script>

<style>/* Contenedor para las imágenes */
.image-container {
  position: relative;
  display: inline-block;
  margin: 10px;
}

/* Texto sobre la imagen */
.image-text {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  font-size: 18px;
  font-weight: bold;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
}

/* Estilo para las imágenes, para hacerlas más pequeñas y clicables */
.clickable-image {
  cursor: pointer;
  width: 100%; /* Asegura que la imagen ocupe el 100% del ancho del contenedor */
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

/* Efecto hover para las imágenes */
.clickable-image:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}
</style>
@endsection
