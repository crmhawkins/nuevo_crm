@extends('layouts.tempappPortal')

@section('content')

@section('css')
@endsection

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
  -webkit-border-radius: 12px 0 0 12px;
  -moz-border-radius: 12px 0 0 12px;
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
  -webkit-border-radius: 0 12px 12px 0;
  -moz-border-radius: 0 12px 12px 0;
  border-radius: 0 12px 12px 0;
}

.table-responsive-mobile {
  overflow-x: auto;
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
  display: none; /* Ocultar la opción de "entries per page" */
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

<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <h3><strong>Estado de las subvenciones sin actualizar + {{$dias}} días</strong></h3>
            </div>
            <div class="col-6 text-end">
              <input type="text" id="tableSearch" class="input-control" placeholder="Buscar">
            </div>
          </div>
          <div class="pt-5 table-responsive-mobile">
            <table id="comprasTable" class="w-100 table-clientportal display">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Contrato</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($resultados as $resultado)
                <tr>
                  <td class="sorting_1">{{$resultado->reference_id}}</td>
                  <td class="table__invoice-num"><strong>{{$resultado->contratos}}</strong></td>
                  <td width="20">
                    @switch($resultado->estado)
                        @case('8')
                            <span class="label label-warning"><span class="text-uppercase badge bg-warning p-2" style="font-size: 12px">Justificado</span></span>
                            @break
                        @case('9')
                            <span class="label label-dark"><span class="text-uppercase badge bg-warning text-dark p-2" style="font-size: 12px">Justificado Parcial</span></span>
                            @break
                        @case('14')
                            <span class="label label-success"><span class="text-uppercase badge bg-info p-2" style="font-size: 12px">Subsanado 1</span></span>
                            @break
                        @case('15')
                            <span class="label label-success"><span class="text-uppercase badge bg-info text-white p-2" style="font-size: 12px">Subsanado 2</span></span>
                            @break
                        @case('29')
                            <span class="label label-success"><span class="text-uppercase badge bg-info text-white p-2" style="font-size: 12px">Subsanado 3</span></span>
                            @break
                        @case('30')
                            <span class="label label-success"><span class="text-uppercase badge bg-success text-white p-2" style="font-size: 12px">Sasak enviado</span></span>
                            @break
                        @case('31')
                            <span class="label label-success"><span class="text-uppercase badge bg-primary text-white p-2" style="font-size: 12px">Respuesta sasak</span></span>
                            @break
                        @case('32')
                            <span class="label label-success"><span class="text-uppercase badge bg-warning text-white p-2" style="font-size: 12px">2º Subsanado 1</span></span>
                            @break
                        @case('33')
                            <span class="label label-success"><span class="text-uppercase badge bg-warning text-white p-2" style="font-size: 12px">2º Subsanado 2</span></span>
                            @break
                        @case('34')
                            <span class="label label-success"><span class="text-uppercase badge bg-warning text-white p-2" style="font-size: 12px">2º Subsanado 3</span></span>
                            @break
                        @default
                            <span class="label label-danger"><span class="text-uppercase badge bg-danger p-2" style="font-size: 12px">Anulado</span></span>
                    @endswitch
                  </td>
                  <td>
                    <p class="docdesc">{{$resultado->fecha}}</p>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
@include('partials.toast')
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css" rel="stylesheet">
<script>
  $(document).ready(function() {
    var table = $('#comprasTable').DataTable({
      paging: true,   // Activa la paginación
      info: false,    // Oculta el recuento de registros
      dom: 'lrtip',   // 't' es para la tabla, 'r' es para el procesamiento, 'i' es para la información, 'p' es para la paginación
      language: {
        zeroRecords: "No se encontraron resultados",
        emptyTable: "No hay datos disponibles en la tabla",
      },
      lengthChange: false,  // Desactiva el control de entradas por páginas
    });

    // Sincroniza el buscador personalizado con el de DataTables
    $('#tableSearch').on('keyup', function() {
      table.search(this.value).draw();
    });

    // Oculta el buscador original de DataTables
    $('#comprasTable_filter').hide();
  });
</script>
@endsection
