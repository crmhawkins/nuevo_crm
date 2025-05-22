@extends('layouts.appPortal')

@section('content')
<style>
/* Coloca el CSS en un archivo de estilo si es posible */
.input-control { font-size: 16px; border: 1px solid #ececec; padding: 0.2rem 1rem; }
.table-clientportal tbody tr>td { background-color: rgba(237, 239, 243, .49); font-size: 14px; color: #424b5a; padding: 16px 20px;}
.table-clientportal  {  border-collapse: separate; border-spacing: 0 10px; /* 0 horizontal spacing, 10px vertical spacing */}
.table-clientportal tbody tr td:first-of-type { border-radius: 12px 0 0 12px; padding-left: 25px; }
.table-clientportal tbody tr td:last-of-type { border-radius: 0 12px 12px 0; padding-right: 25px; }
.table-clientportal thead tr>th { color: #9fa5ae; font-size: 12px; }
.time-info { display: flex; justify-content: flex-end; gap: 20px; /* Espacio entre los elementos */}
.table-clientportal thead tr th, .table-clientportal tbody tr td {font-weight: 400;line-height: 24px; border: none; padding-left: 4px; padding-right: 20px !important;}
</style>

<div class="content">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <div class="row">
            <div class="col-6">
              <h3><strong>Proyectos Activos</strong></h3>
            </div>
            <div class="col-6 text-end">
              <input id="tableSearch" type="text" class="input-control" placeholder="Buscar">
            </div>
          </div>
           <!-- Mostrar el tiempo total en la parte superior de la tabla -->
           <div class="row mt-4">
            <div class="col-12 text-end time-info">
                <h5><strong>Tiempo Total: {{$tiempoTotalFormato}}</strong></h5>
                <h5><strong>Tiempo Gastado: {{$tiempoGastadoFormato}}</strong></h5>
                <h5><strong>Tiempo Restante: {{$tiempoRestanteFormato}}</strong></h5>
            </div>
        </div>
          <div class="pt-1 table-responsive">
            <table id="proyectosTable" class="w-100 table-clientportal">
              <thead>
                <tr>
                  <th>Proyecto</th>
                  <th>Tarea</th>
                  <th>Tiempo Estimado</th>
                  <th>Tiempo Real</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($proyectos as $proyecto)
                  @foreach ($proyecto['tasks'] as $tarea)
                    <tr>
                      <td>{{$proyecto->concept}}</td>
                      <td>{{$tarea->title}}</td>
                      <td>{{$tarea->estimated_time}}</td>
                      <td>{{$tarea->real_time}}</td>
                    </tr>
                  @endforeach
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
    var table = $('#proyectosTable').DataTable({
      paging: false,
      info: false,
      dom: 't',
      language: { zeroRecords: "No se encontraron resultados", emptyTable: "No hay datos disponibles" }
    });

    $('#tableSearch').on('keyup', function() { table.search(this.value).draw(); });
    $('#proyectosTable_filter').hide();
});
</script>
@endsection
