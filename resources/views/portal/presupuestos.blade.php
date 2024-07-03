@extends('layouts.appPortal')
@section('content')
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
              <h3><strong>Presupuestos</strong></h3>
            </div>
            <div class="col-6 text-end">
              <input type="text" class="input-control" placeholder="Buscar">
            </div>
          </div>
          <div class="pt-5 table-responsive">
            <table class="w-100 table-clientportal">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Num</th>
                  <th>Descripcion</th>
                  <th>Estado</th>
                  <th>Subtotal</th>
                  <th>IVA</th>
                  <th>Retencion</th>
                  <th>Rec. de eq.</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr class="clicklink odd" data-link="/portal/estimates/64807187c8237379a9048abe" data-type="estimate" data-pdf="1" role="row">
                  <td class="sorting_1">07/06/2023</td>
                  <td class="table__invoice-num"><strong>E230257</strong></td>
                  <td>
                    <p class="docdesc">[[ VIPS MALAGA PLAZA MAYOR ]]</p>
                  </td>
                  <td width="20">
                    <span class="label label-warning"><span class=" text-uppercase badge bg-warning p-2" style="font-size: 12px">Pendiente</span></span>
                  </td>
                  <td class="text-right">500,00€</td>
                  <td class="text-right">105,00€</td>
                  <td class="text-right">0,00€</td>
                  <td class="text-right">0,00€</td>
                  <td class="table__total text-right">605,00€</td>
                </tr>
              </tbody>
              <tfoot>

              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
