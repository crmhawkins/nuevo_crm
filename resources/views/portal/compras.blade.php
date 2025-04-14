@extends('layouts.appPortal')

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
</style>

<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <h3><strong>Compras</strong></h3>
            </div>
            <div class="col-6 text-end">
              <input type="text" id="tableSearch" class="input-control" placeholder="Buscar">
            </div>
          </div>
          <div class="pt-5 table-responsive-mobile">
            <table id="comprasTable" class="w-100 table-clientportal display">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Num</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($compras as $compra)
                <tr>
                  <td class="sorting_1">{{$compra->created_at->format('d/m/Y')}}</td>
                  <td class="table__invoice-num"><strong>{{$compra->id}}</strong></td>
                  <td>
                    <p class="docdesc">{{$compra->purchase_type}}</p>
                  </td>
                  <td width="20">
                    @switch($compra->status)
                        @case('completado')
                            <span class="label label-warning"><span class="text-uppercase badge bg-dark p-2" style="font-size: 12px">Completado</span></span>
                            @break
                        @case('procesando')
                            <span class="label label-dark"><span class="text-uppercase badge bg-warning text-dark p-2" style="font-size: 12px">Procesando</span></span>
                            @break
                        @case('pagado')
                            <span class="label label-success"><span class="text-uppercase badge bg-success p-2" style="font-size: 12px">Pagado</span></span>
                            @break
                        @case('enviado')
                            <span class="label label-success"><span class="text-uppercase badge bg-warning text-white p-2" style="font-size: 12px">Enviado</span></span>
                            @break
                        @default
                            <span class="label label-danger"><span class="text-uppercase badge bg-danger p-2" style="font-size: 12px">Anulado</span></span>
                    @endswitch
                  </td>
                  <td class="table__total text-right">{{$compra->amount}}&euro;</td>
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
      paging: false,   // Desactiva la paginación
      info: false,     // Oculta el recuento de registros
      dom: 't',        // Solo muestra la tabla, sin el buscador ni otros elementos

      language: {
        zeroRecords: "No se encontraron resultados",
        emptyTable: "No hay datos disponibles en la tabla",
      }
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