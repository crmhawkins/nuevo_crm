@extends('layouts.appPortal')
@section('content')

@section('css')

@endsection

<style>
.input-control{
  font-size: 16px;
  border: 1px solid #ececec;
  padding: 0.2rem 1rem;
}
.table-clientportal tbody tr>td {
    background-color: rgba(237, 239, 243, .49);
    font-size: 14px;
    color: #424b5a;
    padding-top: 16px;
    padding-bottom: 16px;
}

.table-clientportal  {  border-collapse: separate; border-spacing: 0 10px; /* 0 horizontal spacing, 10px vertical spacing */}

.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
    vertical-align: top;
}

.table-clientportal tbody tr td:first-of-type {
    -webkit-border-radius: 12px 0 0 12px;
    -moz-border-radius: 12px 0 0 12px;
    border-radius: 12px 0 0 12px;
}
.table-clientportal thead tr>th:first-of-type, .table-clientportal tbody tr>td:first-of-type {
    padding-left: 25px;
}
.table-clientportal thead tr>th:last-of-type, .table-clientportal tbody tr>td:last-of-type {
    padding-right: 25px !important;
}
.table-clientportal tbody tr td:last-of-type {
    -webkit-border-radius: 0 12px 12px 0;
    -moz-border-radius: 0 12px 12px 0;
    border-radius: 0 12px 12px 0;
}

table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled {
    cursor: pointer;
    position: relative;
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
.table-clientportal {
  overflow-x: auto
}
body * {
    scrollbar-color: #ccc transparent;
    scrollbar-height: thin;
    scrollbar-width: thin;
}
</style>

<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <h3><strong>Facturas</strong></h3>
            </div>
            <div class="col-6 text-end">
              <input type="text" id="tableSearch" class="input-control" placeholder="Buscar">
            </div>
          </div>
          <div class="pt-5 table-responsive">
            <table id="ventasTable" class="w-100 table-clientportal display">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Num</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th>Bruto</th>
                  <th>Descuento</th>
                  <th>Base</th>
                  <th>IVA</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($facturas as $factura)
                <tr onclick="window.location='{{ route('portal.showInvoice', $factura->id) }}'" class="clicklink" data-type="estimate" data-pdf="1">
                  <td class="sorting_1">{{$factura->created_at->format('d/m/Y')}}</td>
                  <td class="table__invoice-num"><strong>{{$factura->reference}}</strong></td>
                  <td>
                    <p class="docdesc">{{$factura->concept}}</p>
                  </td>
                  <td width="20">
                    @switch($factura->invoice_status_id)
                        @case(1)
                            <span class="label label-warning"><span class="text-uppercase badge bg-warning text-dark p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                            @break
                        @case(2)
                            <span class="label label-dark"><span class="text-uppercase badge bg-dark p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                            @break
                        @case(3)
                            <span class="label label-success"><span class="text-uppercase badge bg-success p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                            @break
                        @case(4)
                            <span class="label label-info"><span class="text-uppercase badge bg-info text-dark p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                            @break
                        @case(5)
                            <span class="label label-danger"><span class="text-uppercase badge bg-danger p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                            @break
                        @default
                            <span class="label label-warning"><span class="text-uppercase badge bg-warning p-2" style="font-size: 12px">{{$factura->invoiceStatus->name}}</span></span>
                    @endswitch
                  </td>
                  <td class="text-right">{{$factura->gross}}&euro;</td>
                  <td class="text-right">{{$factura->discount}}&euro;</td>
                  <td class="text-right">{{$factura->base}}&euro;</td>
                  <td class="text-right">{{$factura->iva}}&euro;</td>
                  <td class="table__total text-right">{{$factura->total}}&euro;</td>
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
@include('partials.toast')s
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css" rel="stylesheet">
<script>

  $(document).ready(function() {
    var table = $('#ventasTable').DataTable({
      paging: false,   // Desactiva la paginación
      info: false,     // Oculta el recuento de registros
      dom: 't',          // Solo muestra la tabla, sin el buscador ni otros elementos

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
    $('#ventasTable_filter').hide();
  });
</script>
@endsection
