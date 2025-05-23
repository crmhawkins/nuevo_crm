@extends('layouts.tempappPortal')

@section('content')

@section('css')
@endsection

<style>
.input-control {
  font-size: 16px;
  border: 1px solid #ececec;
  padding: 0.2rem 1rem;
  width: 100%;
  box-sizing: border-box;
}

.table-clientportal tbody tr>td {
  background-color: rgba(237, 239, 243, .49);
  font-size: 14px;
  color: #424b5a;
  padding-top: 16px;
  padding-bottom: 16px;
}

.table-clientportal {
  border-collapse: separate;
  border-spacing: 0 10px;
  overflow-x: auto;
}

.table-responsive-mobile {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

.table-clientportal tbody tr td:first-of-type {
  border-radius: 12px 0 0 12px;
}

.table-clientportal thead tr>th {
  color: #9fa5ae;
  font-size: 12px;
  padding-bottom: 0;
}

.table-clientportal thead tr th, .table-clientportal tbody tr td {
  font-weight: 400;
  line-height: 24px;
  border: none;
  padding-left: 4px;
  padding-right: 20px !important;
}

.table-clientportal td {
  word-wrap: break-word;
}

.table-clientportal tbody tr td:last-of-type {
  border-radius: 0 12px 12px 0;
}

.menu_estado_dias {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 50px;
  gap: 10px;
}

@media (max-width: 768px) {
  .table-clientportal th, .table-clientportal td {
    font-size: 12px;
  }

  .table-clientportal thead th {
    padding: 8px;
  }

  .table-clientportal tbody td {
    padding-left: 8px;
    padding-right: 8px;
  }

  .input-control {
    margin-bottom: 1rem;
    font-size: 14px;
  }
}

.dataTables_wrapper .dataTables_paginate {
  text-align: center;
  padding: 15px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  margin: 0 5px;
  padding: 5px 10px;
  border-radius: 5px;
  background-color: #4eaa25;
  color: white;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background-color: #3e8e41;
}

.dataTables_wrapper .dataTables_length {
  display: none;
}

.dataTables_wrapper .pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 auto;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}
</style>

<div class="content justify-center mt-3 mb-6">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          {{-- Encabezado y buscador --}}
          <div class="row">
            <div class="col-6">
            <h3 class="mb-0">
              <strong>Estado de las subvenciones sin actualizar +
                {{ session('success_dias') ?? $dias }} días
              </strong>
            </h3>
            </div>
            <div class="col-6 text-end">
              <input type="text" id="tableSearch" class="input-control" placeholder="Buscar">
            </div>
          </div>
          
          
          {{-- Botones de filtros --}}
          <div class="menu_estado_dias d-flex flex-row justify-content-between align-items-center mt-4">
            
              <div class="d-flex flex-row justify-content-start align-items-center">
                <form method="GET" action="{{ route('kitDigital.sin_actualizar') }}" class="d-flex align-items-center justify-content-center me-2" id="empresaFilterForm">
                    <input type="hidden" name="dias" value="{{ request('dias', 15) }}">
                    <input type="hidden" name="dias_laborales" value="{{ request('dias_laborales', 21) }}">
                    <select name="empresa" class="form-select w-auto" onchange="document.getElementById('empresaFilterForm').submit();">
                      <option value="">Todas las empresas</option>
                      <option value="IPOINT COMUCACIÓN MASIVA SL" {{ request('empresa') == 'IPOINT COMUCACIÓN MASIVA SL' ? 'selected' : '' }}>IPOINT COMUCACIÓN MASIVA SL</option>
                      <option value="TH WORK" {{ request('empresa') == 'TH WORK' ? 'selected' : '' }}>TH WORK</option>
                    </select>
                  </form>
                <form class="me-2">
                    <select id="perPageSelect" class="form-select" style="width: auto;">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                        <option value="-1">Todos</option>
                    </select>
                </form>

                <form method="GET" action="{{ route('kitDigital.exportar_estados') }}" class="me-2">
                    <input type="hidden" name="dias" value="{{ request('dias', 15) }}">
                    <input type="hidden" name="dias_laborales" value="{{ request('dias_laborales', 21) }}">
                    <input type="hidden" name="empresa" value="{{ request('empresa') }}">
                    @if(request('mas6Meses'))
                        <input type="hidden" name="mas6Meses" value="true">
                    @endif
                    <button type="submit" class="btn btn-success">
                        Exportar Excel
                    </button>
                </form>
                
              </div>
              

              <div>
                  <a href="{{ route('kitDigital.sin_actualizar', ['dias' => 15, 'dias_laborales' => 21, 'empresa' => request('empresa')]) }}" class="btn btn-outline-primary">+15 días</a>
                  <a href="{{ route('kitDigital.sin_actualizar', ['dias' => 30, 'dias_laborales' => 42, 'empresa' => request('empresa')]) }}" class="btn btn-outline-primary">+30 días</a>
                  <a href="{{ route('kitDigital.sin_actualizar', ['dias' => 45, 'dias_laborales' => 63, 'empresa' => request('empresa')]) }}" class="btn btn-outline-primary">+45 días</a>
                  <a href="{{ route('kitDigital.sin_actualizar', ['dias' => 60, 'dias_laborales' => 82, 'empresa' => request('empresa')]) }}" class="btn btn-outline-primary">+60 días</a>
                  <a href="{{ route('kitDigital.sin_actualizar', ['mas6Meses' => true]) }}" class="btn btn-outline-primary">+6 Meses</a>
              </div>
          </div>

          {{-- Mensaje si no hay registros --}}
          @if (session('success_message'))
            <div class="mt-5 bg-green-100 border border-green-400 text-green-700 px-4 py-5 rounded text-center text-xl font-semibold shadow">
              {{session('success_message') }}
            </div>
          @else
          @php
              $hayEmpresa = collect($resultados)->contains(function ($item) {
                    return !empty($item->empresa);
                });
          @endphp
            {{-- Tabla --}}
            <div class="pt-5 table-responsive-mobile">
                <div class="mb-3 text-end pe-2" id="tablaContadorRegistros" style="font-size: 14px; color: #555;"></div>

              <table id="comprasTable" class="w-100 table-clientportal display">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Contrato</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Estado actualizado</th>
                    <th>Sasak Enviado</th>
                    @if($hayEmpresa)
                    <th>Empresa</th>
                    @endif

                  </tr>
                </thead>
                <tbody>
                  @foreach ($resultados as $resultado)
                  <tr>
                    <td class="sorting_1">{{$resultado->reference_id}}</td>
                    <td><strong>{{$resultado->contratos}}</strong></td>
                    <td>
                        <span class="badge bg-secondary p-2" style="font-size: 12px;">
                          {{ $resultado->categoria_dias }}
                        </span>
                      </td>
                      
                    <td width="20">
                      @switch($resultado->estado)
                        @case('8')
                          <span class="badge bg-warning p-2 text-uppercase" style="font-size: 12px">Justificado</span>
                          @break
                        @case('9')
                          <span class="badge bg-warning text-dark p-2 text-uppercase" style="font-size: 12px">Justificado Parcial</span>
                          @break
                        @case('10')
                          <span class="badge bg-info text-dark p-2 text-uppercase" style="font-size: 12px">Validada</span>
                          @break
                        @case('12')
                          <span class="badge bg-warning text-dark p-2 text-uppercase" style="font-size: 12px">Pendiente subsanar 1</span>
                          @break
                        @case('13')
                          <span class="badge bg-warning text-dark p-2 text-uppercase" style="font-size: 12px">Pendiente subsanar 2</span>
                          @break
                        @case('14')
                          <span class="badge bg-info p-2 text-uppercase" style="font-size: 12px">Subsanado 1</span>
                          @break
                        @case('15')
                          <span class="badge bg-info text-white p-2 text-uppercase" style="font-size: 12px">Subsanado 2</span>
                          @break
                        @case('20')
                          <span class="badge bg-warning p-2 text-uppercase" style="font-size: 12px">Pendiente 2ª Justificacion</span>
                          @break
                        @case('21')
                          <span class="badge bg-warning p-2 text-uppercase" style="font-size: 12px">2º Justificacion Realizada</span>
                          @break
                        @case('25')
                          <span class="badge bg-info p-2 text-uppercase" style="font-size: 12px">Validada 2ª justificacion</span>
                          @break
                        @case('29')
                          <span class="badge bg-info text-white p-2 text-uppercase" style="font-size: 12px">Subsanado 3</span>
                          @break
                        @case('30')
                          <span class="badge bg-success text-white p-2 text-uppercase" style="font-size: 12px">Sasak enviado</span>
                          @break
                        @case('31')
                          <span class="badge bg-primary text-white p-2 text-uppercase" style="font-size: 12px">Respuesta sasak</span>
                          @break
                        @case('32')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">2º Subsanado 1</span>
                          @break
                        @case('33')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">2º Subsanado 2</span>
                          @break
                        @case('34')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">2º Subsanado 3</span>
                          @break
                        @case('35')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">Subsanacion incorrecta</span>
                          @break
                        @case('36')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">Finalizado plazo de subsanacion</span>
                          @break
                        @case('37')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">C.aleatoria</span>
                          @break
                        @case('39')
                          <span class="badge bg-warning text-white p-2 text-uppercase" style="font-size: 12px">AUDITORIA</span>
                          @break
                        @default
                          <span class="badge bg-danger p-2 text-uppercase" style="font-size: 12px">Anulado</span>
                      @endswitch
                    </td>
                    <td>
                      <span class="sorting_1">{{$resultado->fecha_estado}}</span>
                    </td>
                    <td>
                      <span class="sorting_1">{{$resultado->fecha_sasak}}</span>
                    </td>
                    @if(isset($resultado->empresa))
                    <td>
                      <span class="sorting_1">{{isset($resultado->empresa) ? $resultado->empresa : ''}}</span>
                    </td>
                    @endif
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif

        </div>
      </div>
    </div>
  </div>

</div>

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css" rel="stylesheet">

<script>
//   $(document).ready(function() {
//     var table = $('#comprasTable').DataTable({
//       paging: true,
//       info: false,
//       dom: 'lrtip',
//       language: {
//         zeroRecords: "No se encontraron resultados",
//         emptyTable: "No hay datos disponibles en la tabla",
//       },
//       lengthChange: false,
//     });

//     $('#tableSearch').on('keyup', function() {
//       table.search(this.value).draw();
//     });

//     $('#comprasTable_filter').hide();
//   });

$(document).ready(function() {
  var table = $('#comprasTable').DataTable({
    paging: true,
    info: true,
    dom: 'lrtip',
    language: {
        zeroRecords: "No se encontraron resultados",
        emptyTable: "No hay datos disponibles en la tabla",
        paginate: {
            previous: "Anterior",
            next: "Siguiente"
        },
        info: "", // lo dejamos vacío para que usemos nuestro contador personalizado
    },
    lengthChange: false,
    pageLength: 10, // valor inicial
  });

  $('#tableSearch').on('keyup', function() {
    table.search(this.value).draw();
  });

  $('#comprasTable_filter').hide();

  // Contador dinámico de registros
  function actualizarContador() {
    var pageInfo = table.page.info();
    $('#tablaContadorRegistros').text(`Mostrando ${pageInfo.end} de ${pageInfo.recordsTotal} registros`);
  }

  table.on('draw', actualizarContador);
  actualizarContador();

  // Select de número de registros por página
  $('#perPageSelect').on('change', function () {
    var val = parseInt($(this).val(), 10);
    table.page.len(val === -1 ? table.data().length : val).draw();
  });
});

</script>
@endsection
