
@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div id="page-container" class="main-content-boxed">

    <!-- Main Container -->
    <main id="main-container">

            <!-- Hero -->
            <div class="bg-image overflow-hidden">
                <div class="" style="background-color: black">
                    <div class="content content-narrow content-full">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                            <div class="flex-sm-fill text-center" style="width: 100%">

                                <h2 class="font-w800 text-white-75 mb-0"  >Bienvenido {{$usuario->name}}</h2>
                                <h1 class="font-w500 text-center text-white mb-0" >Quedan {{$diasDiferencia}} dias para finalizar el mes</h1>
                                <h2 class="h4 font-w500 text-white-75 text-center mb-0" >Y tienes  {{$pedienteCierre}} € pendiente por tramitar</h2>
                                <span class="d-inline-block mt-4" >
                                    <button id="sendLogout" type="button" class="btn btn-warning py-2 mr-2" data-dismiss="modal">Salir</button>
                                    <a class="btn btn-primary px-4 py-2" href="{{ route('admin.petition.createFromCommcercial') }}">
                                        <i class="fa fa-plus mr-1"></i> Nueva Peticion
                                    </a>
                                    @if ($taskGestor)
                                        <a class="btn-fichar btn btn-success ml-2 mt-0" href="#">Control de Jornada</a>
                                    @endif
                                </span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- END Hero -->

            <!-- Page Content -->
            <div class="content content-narrow">
                <!-- Stats -->
                <div class="row justify-content-center">
                    <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                        <a class="block block-rounded block-link-pop border-left border-warning border-4x" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Pendiente de cierre</div>
                            <div class="font-size-h2 font-w400 text-dark">{{$pedienteCierre}} €</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                        <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Comision En Curso</div>
                                <div class="font-size-h2 font-w400 text-dark">{{$comisionCurso}} €</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                        <a class="block block-rounded block-link-pop border-left border-danger border-4x" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Comision Pendiente</div>
                                <div class="font-size-h2 font-w400 text-dark">{{$comisionPendiente}} €</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                        <a class="block block-rounded block-link-pop border-left border-success border-4x" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Comision Tramitada</div>
                                <div class="font-size-h2 font-w400 text-dark">{{$comisionTramitadas}} €</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2 col-xl-2">
                        <a class="block block-rounded block-link-pop border-left border-info border-4x" href="javascript:void(0)">
                            <div class="block-content block-content-full">
                                <div class="font-size-sm font-w600 text-uppercase text-muted">Comision Restante</div>
                                <div class="font-size-h2 font-w400 text-dark">{{$comisionRestante}} €</div>
                            </div>
                        </a>
                    </div>

                </div>
                <!-- END Stats -->
                <div class="row justify-content-center">
                    <!-- Latest Customers -->
                    <div class="col-lg-12">
                        <div class="block block-mode-loading-oneui">
                            <div class="block-header border-bottom">
                                <h3 class="block-title">Agregar Cliente</h3>
                            </div>
                            <div class="block-body">
                                <form action="{{ route('admin.ayudas.storeAyudasComercial') }}" method="POST" enctype="multipart/form-data" data-callback="formCallback">
                                    @csrf
                                    <div class="row mt-3 justify-content-center">
                                        @if (session('status'))
                                            <div class="alert alert-success">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                        <div class="col-md-2 col-sm-12">
                                            <input name="cliente" class="form-control" type="text" placeholder="Nombre del cliente">
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <input name="telefono" class="form-control" type="text" placeholder="Numero de Telefono">
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <input name="email" class="form-control" type="text" placeholder="Email">
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <select name="segmento" class="form-control" id="am_aplicacion_id">
                                                <option value="{{null}}">Segmento seleccione</option>
                                                <option value="1">Segmento 1</option>
                                                <option value="2">Segmento 2</option>
                                                <option value="3">Segmento 3</option>
                                                <option value="30">Segmento 3 Extra</option>
                                                <option value="4">Segmento 4</option>
                                                <option value="5">Segmento 5</option>
                                                <option value="A">Segmento A</option>
                                                <option value="B">Segmento B</option>
                                                <option value="C">Segmento C</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <select name="estado" class="form-control" id="estado">
                                                <option value="{{null}}">Seleccione estado</option>
                                                <option value="24">Interesados</option>
                                                <option value="18">Leads</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-12">
                                            <div class="form-floating">
                                                <textarea name="comentario" class="form-control" placeholder="Comentario" id="floatingTextarea"></textarea>
                                                </div>
                                        </div>
                                        <div class="col-md-1 col-sm-12">
                                            <input type="submit" value="Guardar" class="btn btn-primary">
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Customers and Latest Orders -->
                <div class="row justify-content-center">
                    <!-- Latest Customers -->
                    <div class="col-lg-12">
                        <div class="block block-mode-loading-oneui">
                            <div class="block-header border-bottom">
                                <h3 class="block-title">Kit Digital</h3>
                                <div class="block-subtitle">
                                    <select id="estadoFilter" class="form-control w-75">
                                        <option value="">Todos los Estados</option>

                                        @if (isset($estadosKit))
                                            @foreach ($estadosKit as $estado)
                                                <option value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                                            @endforeach

                                        @endif
                                    </select>
                                </div>
                                <div class="block-options">
                                    <a href="{{route('admin.ayudas.createComercial')}}" class="btn-block-option">
                                            <i class="si si-plus"></i>
                                    </a>
                                    <a href="{{route('admin.clients.my-clients')}}" class="btn-block-option">
                                        <i class="fa fa-list"></i>
                                    </a>
                                </div>
                            </div>


                            <div class="block-content block-content-full">
                                <table class="table data_table_comercial table-striped table-hover table-borderless table-vcenter font-size-sm mb-0">
                                    <thead class="thead-dark">
                                        <tr class="text-uppercase">
                                            <th class="font-w700 pl-3">Fecha</th>
                                            <th class="d-none d-sm-table-cell font-w700 pl-3">Concepto</th>
                                            <th class="font-w700 pl-3">Estado</th>
                                            <th class="font-w700 pl-3">Teléfono</th>
                                            <th class="font-w700 pl-3">Email</th>
                                            <th class="font-w700 pl-3">Comentario</th>
                                            <th class="d-none d-sm-table-cell font-w700 text-right px-3" style="width: 150px;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ayudas as $ayuda)
                                            <tr>
                                                <td>
                                                    <span class="font-w600">{{ \Carbon\Carbon::parse($ayuda->created_at)->format('d-m-Y') }}
                                                    </span>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <span class="font-size-sm text-muted">{{$ayuda->cliente}}</span>
                                                </td>
                                                <td>
                                                    <span class="font-w600 text-warning">{{$ayuda->estadoName->nombre}}</span>
                                                </td>
                                                <td>
                                                    <span class="font-w600 text-warning">{{$ayuda->telefono}}</span>
                                                </td>
                                                <td>
                                                    <span class="font-w600 text-warning">{{$ayuda->email}}</span>
                                                </td>
                                                <td>
                                                    <span class="font-w600 text-warning">{{$ayuda->comentario}}</span>
                                                </td>
                                                <td class="d-none d-sm-table-cell text-right">
                                                    {{$ayuda->importe}} €
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END Customers and Latest Orders -->
            </div>
            <!-- END Page Content -->
        </main>
        {{ TawkTo::widgetCode() }}
    <!-- END Main Container -->
</div>

<style>
    table.dataTable thead .sorting::after {
  content: "\f0dc";
  font-family: 'FontAwesome';
}
table.dataTable thead .sorting_asc::after {
  content: "\f106";
  color: white;
}
table.dataTable th.sorting_desc, table.dataTable th.sorting_asc {
  color: white;
}
</style>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js" integrity="sha384-6khuMg9gaYr5AxOqhkVIODVIvm9ynTT5J4V1cfthmT+emCG6yVmEZsRHdxlotUnm" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/simplebar/5.0.7/simplebar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-scroll-lock@3.1.3/jquery-scrollLock.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.appear/0.4.1/jquery.appear.min.js" integrity="sha256-19/eyBKQKb8IPrt7321hbLkI1+xyM8d/DpzjyiEKnCE=" crossorigin="anonymous"></script>

    <script type="text/javascript" src="{{ asset('assets/js/oneui.app.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/oneui.core.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/2.2.1/js.cookie.min.js" integrity="sha256-oE03O+I6Pzff4fiMqwEGHbdfcW7a3GRRxlL+U49L5sA=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/select2/js/select2.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/vendor/jquery-ui/jquery-ui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/admin.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/momentjs/moment-with-locales.min.js') }}"></script>
    <!-- Page JS Plugins -->
    <script src="{{ asset('assets/js/plugins/easy-pie-chart/jquery.easypiechart.min.js')}}"></script>
    <script src="{{ asset('assets/js/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.2/jquery.flexslider.min.js"></script>
    <script src="https://unpkg.com/babel-polyfill@6.2.0/dist/polyfill.js"></script>
    <script type="text/javascript" src="{{ asset('assets/vendor/momentjs/moment-with-locales.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.2/locale/es.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rome/2.1.22/rome.standalone.js"></script>
    <script src="{{ asset('assets/js/material-datetime-picker.js') }}" charset="utf-8"></script>

    <!-- Page JS Code -->
    <script src="{{ asset('assets/js/be_comp_charts.min.js')}}"></script>

    <!-- Page JS Helpers (Easy Pie Chart + jQuery Sparkline Plugins) -->
    <script>jQuery(function(){ One.helpers(['easy-pie-chart', 'sparkline']); });</script>

    <script>
    function formCallback(data) {
        CommonFunctions.notificationSuccessStayOrBack(data.message, data.entryUrl, "{{route('admin.dashboard')}}");
    }        var APP_URL = {!! json_encode(url('/')) !!};
        @if($alertasActivadas)
            var alertas = @json($alertasActivadas);
            console.log(alertas);
            var alertaAux = alertas;
        @endif
        var picker = new MaterialDatetimePicker({})
        .on('submit', function(d) {
            var id = alertas[0]['id'];
            var fecha = d.format("YYYY-MM-DD HH:mm:ss");
            alertas.shift();
            $.when( postpone(id, fecha) ).then(function( data, textStatus, jqXHR ) {
                if(jqXHR.responseText!=503){
                showAlert(alertas);
                }else{
                    swal(
                        'Error',
                        'Error al realizar la peticion',
                        'error'
                    );
                    showAlert(alertaAux);
                }
            });
        })
        .on('close', () => showAlert(alertaAux));

    $( document ).ready(function() {

        @if($alertasActivadas)
            showAlert(alertas);
        @endif

        var table = $('table.data_table_comercial').DataTable({
            order: [],
            "language": {
                "decimal":        "",
                "emptyTable":     "No hay datos disponibles",
                //"info":           "Mostrando _START_ de _END_ of _TOTAL_ entradas",
                "info":           "_TOTAL_ entradas en total",
                "infoEmpty":      "0 entradas",
                "infoFiltered":   "(filtrado de _MAX_ entradas en total)",
                "lengthMenu":     "Mostrar _MENU_ entradas",
                "infoPostFix":    "",
                "paginate": {
                    "first":      "Primero",
                    "last":       "Último",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                },
                "loadingRecords": "Cargando...",
                "processing":     "Procesando...",
                "search":         "Buscar:",
                "zeroRecords":    "No hay entradas que cumplan el criterio",
            },
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
        // Filtro de select para la columna "Estado"
        $('#estadoFilter').on('change', function() {
            table.column(2).search(this.value).draw();
        });
        $(".dataTables_filter label").contents().filter(function(){ return this.nodeType != 1; }).remove();
        $('.dataTables_filter input').attr("placeholder", "Buscar en tabla");
    });

    function clockUpdate() {
        var date = new Date();
        $('.digital-clock').css({'color': '#000000'});
        function addZero(x) {
            if (x < 10) {
            return x = '0' + x;
            } else {
            return x;
            }
        }

        var h = addZero(date.getHours());
        var m = addZero(date.getMinutes());
        var s = addZero(date.getSeconds());

        $('.digital-clock').text(h + ':' + m + ':' + s);
    }

    $(document).ready(function(){

  });

  $(document).ready(function() {


    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    if(dd<10) {
    dd='0'+dd
    }

    switch(mm){
        case 1:
            mm="Enero";
            break;
        case 2:
            mm="Febrero";
            break;
        case 3:
            mm="Marzo";
            break;
        case 4:
            mm="Abril";
            break;
        case 5:
            mm="Mayo";
            break;
        case 6:
            mm="Junio";
            break;
        case 7:
            mm="Julio";
            break;
        case 8:
            mm="Agosto";
            break;
        case 9:
            mm="Septiembre";
            break;
        case 10:
            mm="Octubre";
            break;
        case 11:
            mm="Noviembre";
            break;
        case 12:
            mm="Diciembre";
            break;
    }

    today = dd+' de '+mm;

    $('.fecha').text(today);

    clockUpdate();
    setInterval(clockUpdate, 1000);

    $(document).on("click", '.tarea', function(){
        var id = $(this).attr("id");

        $.when( getDataTask(id) ).then(function( data, textStatus, jqXHR ) {
            var boton1;
            var boton2;
            var estado;

            switch(data.estado){
                case "Reanudada":
                    boton1 = "Pausar";
                    boton2 = "Revisar";
                    swal({
                        title: 'Tarea',
                        type: 'info',
                        html: 'Titulo: '+data.titulo + '<br>' +
                            'Cliente: '+data.cliente + '<br>' +
                            'Descripcion: '+data.descripcion + '<br>' +
                            'Proyecto: '+data.proyecto + '<br>' +
                            'Gestor: '+data.gestor + '<br>' +
                            'Estado: '+data.estado + '<br>' +
                            'Prioridad: '+data.prioridad + '<br>' +
                            'Tiempo Estimado: '+data.estimado + '<br>' +
                            'Tiempo Real: '+data.real + '<br>' +
                            'ID TAREA: '+data.id + '<br>',
                        showCancelButton: true,
                        customClass: "swal-custom",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#06d67f',
                        confirmButtonText: boton1,
                        cancelButtonText: boton2,
                    }).then((result) => {
                        if (result.value){
                            estado = "Pausada"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                        if (result.dismiss === "cancel"){
                            //Revisión
                            estado = "Revision"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                    break;
                case "Pausada":
                    boton1 = "Reanudar";
                    boton2 = "Revisar";
                    swal({
                        title: 'Tarea Pausada',
                        type: 'info',
                        html: 'Titulo: '+data.titulo + '<br>' +
                            'Cliente: '+data.cliente + '<br>' +
                            'Descripcion: '+data.descripcion + '<br>' +
                            'Proyecto: '+data.proyecto + '<br>' +
                            'Gestor: '+data.gestor + '<br>' +
                            'Estado: '+data.estado + '<br>' +
                            'Prioridad: '+data.prioridad + '<br>' +
                            'Tiempo Estimado: '+data.estimado + '<br>' +
                            'Tiempo Real: '+data.real + '<br>' +
                            'ID TAREA: '+data.id + '<br>',
                        showCancelButton: true,
                        customClass: "swal-custom",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#06d67f',
                        confirmButtonText: boton1,
                        cancelButtonText: boton2,
                    }).then((result) => {
                        if (result.value){
                            //Reanudar
                            estado = "Reanudar"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                        if (result.dismiss === "cancel"){
                            //Revisión
                            estado = "Revision"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                    break;
                case "Revisión":
                    boton1 = "Reanudar";
                    boton2 = "Pausar";
                    swal({
                        title: 'Tarea',
                        type: 'info',
                        html: 'Titulo: '+data.titulo + '<br>' +
                            'Cliente: '+data.cliente + '<br>' +
                            'Descripcion: '+data.descripcion + '<br>' +
                            'Proyecto: '+data.proyecto + '<br>' +
                            'Gestor: '+data.gestor + '<br>' +
                            'Estado: '+data.estado + '<br>' +
                            'Prioridad: '+data.prioridad + '<br>' +
                            'Tiempo Estimado: '+data.estimado + '<br>' +
                            'Tiempo Real: '+data.real + '<br>' +
                            'ID TAREA: '+data.id + '<br>',
                        showCancelButton: true,
                        customClass: "swal-custom",
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#06d67f',
                        confirmButtonText: boton1,
                        cancelButtonText: boton2,
                    }).then((result) => {
                        if (result.value){
                            //Reanudar
                            estado = "Reanudar"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                        if (result.dismiss === "cancel"){
                            //Revisión
                            estado = "Pausada"
                            $.when( setStatusTask(id, estado) ).then(function( data, textStatus, jqXHR ) {
                                if(data.estado=="OK"){
                                    refreshTasks();
                                    swal(
                                        'Éxito',
                                        'Tarea en revisión.',
                                        'success',
                                    ).then((result) => {
                                        if (result.value){
                                            location.reload();
                                        }
                                    });
                                }else{
                                    swal(
                                        'Error',
                                        'Error en la tarea.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                    break;

                    //Alerta horas trabajadas del mes
            case 22:
                swal({title: "Horas Trabajadas del Mes",
                              text: "La horas trabajadas de este mes son :"+alertas[0]['horas']+" acepta si esta conforme",
                              type: 'info',
                              showCancelButton: true,
                              confirmButtonColor: '#3085d6',
                              cancelButtonColor: '#d33',
                              confirmButtonText: 'Aceptar',
                              cancelButtonText: "Rechazar",
                              allowEscapeKey: false,
                              allowEnterKey: false,
                              allowOutsideClick: false
                            }).then((result) => {
                               if(result.value){
                                    var id = alertas[0]['id'];
                                    var status = 2; //Resuelto
                                        $.when( updateStatusAlertAndAcceptHours(id, status) ).then(function( data, textStatus, jqXHR ){
                                            if(jqXHR.responseText!=503){
                                            swal(
                                                'Éxito',
                                                'Aceptadas las hora de trabajo del mes.',
                                                'success'
                                            );
                                            alertas.shift();
                                            showAlert(alertas);
                                            }else{
                                                swal(
                                                    'Error',
                                                    'Error al realizar la peticion',
                                                    'error'
                                                );
                                            }
                                        });
                                }else{
                                    var id = alertas[0]['id'];
                                    var status = 2; //Resuelto
                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                           alertas.shift();
                                           swal({
                                            title: 'Razones para no estar conforme',
                                            type: 'question',
                                            html: '<label for="description"></label><textarea id="description" rows="4" cols="50" required></textarea>',
                                            allowEscapeKey: false,
                                            allowEnterKey: false,
                                            allowOutsideClick: false,
                                            preConfirm: function (value){
                                                return new Promise(function (resolve){
                                                    var text = $("#description").val();

                                                    $(".swal2-messages").remove();
                                                    $("#swal2-content").append("<div class='swal2-messages'>");

                                                    if(value){
                                                        $(".swal2-messages").empty();
                                                        if(text){
                                                            //Crear alerta para enviarla al MegaFullAdmin
                                                            $.when( responseAlert(id, text) ).then(function( data, textStatus, jqXHR ){
                                                                if(jqXHR.responseText!=503){
                                                                    swal(
                                                                        'Éxito',
                                                                        'Respuesta enviada.',
                                                                        'success'
                                                                    );
                                                                    showAlert(alertas);
                                                                }else{
                                                                    swal(
                                                                        'Error',
                                                                        'Error al crear la alerta',
                                                                        'error'
                                                                    );
                                                                }
                                                            });
                                                        }else{
                                                            swal.enableButtons();
                                                            $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                        }
                                                    }

                                                });
                                            },
                                        });
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;
            }
        });
    });
});


function updateStatusAlert(id, status){
  return  $.ajax({
        type: "POST",
        url: 'dashboard/updateStatusAlert',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id,
            'status': status
        },
        dataType: "json"
    });
}



function postpone(id, fecha){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/postpone',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id,
            'fecha': fecha
        },
        dataType: "json"
    });
}

function comprobarPospone(id){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/comprobarPospone',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id
        },
        dataType: "json"
    });
}

function responseAlert(id, text){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/responseAlert',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id,
            'texto': text
        },
        dataType: "json"
    });
}

function alertAdmin(id, text){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/alertAdmin',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id,
            'texto': text
        },
        dataType: "json"
    });
}





function refreshTasks(){
    $.when( getTasksRefresh() ).then(function( data, textStatus, jqXHR ) {
        var datos = "";
        $(".TareaActivada").empty();
        $(".TareasPausadas").empty();
        $(".TareasRevision").empty();


        if(data.taskPlay!=null){
            datos += "<li id="+data.taskPlay.id+" class='tarea'>";
            datos += "<p>"+data.taskPlay.title+"</p>";
            datos += "</li>";
            $(".TareaActivada").append(datos);
        }

        if(data.tasksPause!=null){
            datos = "";
            $.each(data.tasksPause, function(key, value){
                datos += "<li id="+value.id+" class='tarea'>";
                datos += "<p>"+value.title+"</p>";
                datos += "</li>";
            });
            $(".TareasPausadas").append(datos);
        }

        if(data.tasksRevision!=null){
            datos = "";
            $.each(data.tasksRevision, function(key, value){
                datos += "<li id="+value.id+" class='tarea'>";
                datos += "<p>"+value.title+"</p>";
                datos += "</li>";
            });
            $(".TareasRevision").append(datos);
        }

    });
}

function getTasksRefresh(){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/getTasksRefresh',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        dataType: "json"
    });
}

function getDataTask(id){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/getDataTask',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: { 'id': id },
        dataType: "json"
    });
}


function setStatusTask(id, estado){
    return  $.ajax({
        type: "POST",
        url: 'dashboard/setStatusTask',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        data: {
            'id': id,
            'estado': estado
         },
        dataType: "json"
    });
}

function showAlert(alertas){

    if(alertas.length>=1){

        switch(alertas[0]['stage_id']){
            case 1:
                swal({title: 'Alerta Peticion',
                            text: "Tienes una peticion pendiente de " +alertas[0]['client'],
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Crear Presupuesto',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false,
                            }).then((result) => {
                                var id = alertas[0]['id'];
                                if(result.value){
                                    location.href = APP_URL + "/admin/budgets/"+alertas[0]['id']+"/new-from-alert"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });

                                }
                            });
                break;
            case 2:
                swal({title: 'Alerta Presupuesto',
                            text: "Tienes un presupuesto de " +alertas[0]['client'],
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Mandar a facturacion',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    //location.href = APP_URL + "/admin/budgets/"+alertas[0]['id']+"/new-from-alert"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

            //Etapa Presupuesto Pendiente de confirmar
            case 2:
                swal({title: 'Presupuesto Pendiente de Confirmar',
                            text: "Tienes el presupuesto " + alertas[0]['presupuesto'] + " pendiente de confirmar",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Solucionar',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    location.href = APP_URL + "/admin/budgets/" + alertas[0]['reference_id'] + "/edit"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

            //Etapa Presupuesto Pendiente de aceptar
            case 3:
                swal({title: 'Presupuesto Pendiente de Aceptar',
                            text: "Tienes el presupuesto " + alertas[0]['presupuesto'] + " pendiente de aceptar",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Solucionar',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    location.href = APP_URL + "/admin/budgets/" + alertas[0]['reference_id'] + "/edit"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

            //Etapa Presupuesto aceptado
            case 4:
                swal({title: 'Presupuesto Aceptado',
                            text: "Tienes el presupuesto " + alertas[0]['presupuesto'] + " aceptado",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Solucionar',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    location.href = APP_URL + "/admin/budgets/" + alertas[0]['reference_id'] + "/edit"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

            //Etapa Presupuesto finalizado
            case 5:
                swal({title: 'Presupuesto Finalizado',
                            text: "Tienes el presupuesto " + alertas[0]['presupuesto'] + " finalizado",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Solucionar',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    location.href = APP_URL + "/admin/budgets/" + alertas[0]['reference_id'] + "/edit"
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

            //Etapa Tarea Nueva
            case 11:
                swal({title: 'Tarea Nueva',
                            text: "Tarea: " + alertas[0]['tarea'],
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                        alertas.shift();
                                        showAlert(alertas);
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;

            //Aceptado Terminos
            case 12:
                swal({title: alertas[0]['budget_send_client'] +' ha descargado el presupuesto',
                            text: "Los terminos del presupuesto " + alertas[0]['budget_send'] + " han sido aceptados",
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                        alertas.shift();
                                        showAlert(alertas);
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;

            //Alerta recordatorio custom
            case 15:
                swal({title: alertas[0]['remitente'],
                    text: alertas[0]['nota'],
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Solucionar',
                    cancelButtonText: "Posponer",
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    allowOutsideClick: false
                }).then((result) => {
                    if(result.value){
                        var id = alertas[0]['id'];
                        var status = 2; //Resuelto

                        $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                            if(jqXHR.responseText!=503){
                                alertas.shift();
                                swal({
                                title: 'Responder Alerta',
                                type: 'question',
                                html: '<label for="description"></label><textarea id="description" rows="4" cols="50" required></textarea>',
                                allowEscapeKey: false,
                                cancelButtonText: "Cancelar",
                                allowEnterKey: false,
                                allowOutsideClick: false,
                                preConfirm: function (value){
                                    return new Promise(function (resolve){
                                        var text = $("#description").val();

                                        $(".swal2-messages").remove();
                                        $("#swal2-content").append("<div class='swal2-messages'>");

                                        if(value){
                                            $(".swal2-messages").empty();
                                            if(text){
                                                //Crear alerta para enviarla al MegaFullAdmin
                                                $.when( responseAlert(id, text) ).then(function( data, textStatus, jqXHR ){
                                                    if(jqXHR.responseText!=503){
                                                        swal(
                                                            'Éxito',
                                                            'Respuesta enviada.',
                                                            'success'
                                                        );
                                                        showAlert(alertas);
                                                    }else{
                                                        swal(
                                                            'Error',
                                                            'Error al crear la alerta',
                                                            'error'
                                                        );
                                                    }
                                                });
                                            }else{
                                                swal.enableButtons();
                                                $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                            }
                                        }

                                    });
                                },
                            });
                            }else{
                                swal(
                                    'Error',
                                    'Error al realizar la peticion',
                                    'error'
                                );
                            }
                        });
                    }else{
                        picker.open();
                    }
                });
                break;

            //Alerta Vacaciones Aceptadas
            case 17:
                swal({title: "Vacaciones Aceptadas",
                            text: "La fecha es "+alertas[0]['fecha'],
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                        alertas.shift();
                                        showAlert(alertas);
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;

            //Alerta Vacaciones Denegadas
            case 18:
                swal({title: "Vacaciones Denegadas",
                            text: "La fecha es "+alertas[0]['fecha'],
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                        alertas.shift();
                                        showAlert(alertas);
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;

            //Prepuesto No Aceptado tras 48 horas
            case 21:
                swal({title: 'Presupuesto no abierto tras 48 horas',
                            text: "Tienes el presupuesto " + alertas[0]['presupuesto'] + " no abierto por el cliente tras 48 horas",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Solucionar',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            var id = alertas[0]['id'];
                            if(result.value){
                                    var status = 2; //Resuelto
                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                            location.href = APP_URL + "/admin/budgets/" + alertas[0]['reference_id'] + "/edit"
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    })
                                }else{
                                    $.when( comprobarPospone(id) ).then(function( data, textStatus, jqXHR ) {
                                    if(data == 3){
                                    swal({
                                        title: 'Advertencia Posponer',
                                        type: 'question',
                                        html: '<p>Ya has pospuesto mas de 3 veces esta alerta</p><label for="description">Breve descripcion del motivo</label><textarea id="description" rows="4" cols="50" required></textarea>',
                                        allowEscapeKey: false,
                                        allowEnterKey: false,
                                        allowOutsideClick: false,
                                        preConfirm: function (value){
                                            return new Promise(function (resolve){
                                                var text = $("#description").val();

                                                $(".swal2-messages").remove();
                                                $("#swal2-content").append("<div class='swal2-messages'>");

                                                if(value){
                                                    $(".swal2-messages").empty();
                                                    if(text){
                                                        //Crear alerta para enviarla al MegaFullAdmin
                                                        $.when( alertAdmin(id, text) ).then(function( data, textStatus, jqXHR ){
                                                            if(jqXHR.responseText!=503){
                                                                swal(
                                                                    'Éxito',
                                                                    'Respuesta enviada.',
                                                                    'success'
                                                                );
                                                                alertas.shift();
                                                                showAlert(alertas);
                                                            }else{
                                                                swal(
                                                                    'Error',
                                                                    'Error al realizar la peticion',
                                                                    'error'
                                                                );
                                                            }
                                                        });

                                                    }else{
                                                        swal.enableButtons();
                                                        $(".swal2-messages").append("<span style='color:red;font-size: 20px;'>El campo está vacio</span>");
                                                    }
                                                }

                                            });
                                        },
                                    });
                                    //showAlert(alertas);
                                    }else{
                                        picker.open();
                                    }
                                });
                                }
                            });
                break;

                //Alerta Vacaciones Aceptadas
            case 23:
                swal({title: "Mensaje de Puntualidad",
                            text: alertas[0]['nota'],
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok',
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                        alertas.shift();
                                        showAlert(alertas);
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }
                            });
                break;

                case 25:
                swal({title: "Alerta Comercial",
                            text: "El comercial "+alertas[0]['comercial']+" ha creado una peticion para el cliente "+alertas[0]['cliente'],
                            type: 'info',
                            confirmButtonColor: '#3085d6',
                            showCancelButton: true,
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ver Peticion',
                            cancelButtonText: "Posponer",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                            if(result.value){
                                var id = alertas[0]['id'];
                                var status = 2; //Resuelto

                                    $.when( updateStatusAlert(id, status) ).then(function( data, textStatus, jqXHR ){
                                        if(jqXHR.responseText!=503){
                                            location.href = APP_URL + "/admin/petition/" + alertas[0]['reference_id'] + "/edit"
                                        }else{
                                            swal(
                                                'Error',
                                                'Error al realizar la peticion',
                                                'error'
                                            );
                                        }
                                    });
                                }else
                                {
                                    picker.open();
                                }
                            });
                break;

            //Borrar si no pertenece a ningun caso
            default:
                alertas.shift();
                showAlert(alertas);
                break;
        }
    }
}

$(document).ready(function(){
      $('.btn-fichar').click(function(){
        @php
            if($taskGestor)
            {
        @endphp
                $(this).toggleClass('on');
                var id = "{{$taskGestor->id}}";
                console.log("ID TAREA GVESTOR: "+id);
                var estado = "{{$taskGestor->task_status_id}}";
                if (estado == 2){
                    swal({title: 'Control de jornada',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Fichar",
                    cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if(result.value){
                            $.when( setStatusTask(id, "Reanudar") ).then(function( data, textStatus, jqXHR ) {
                            if(data.estado=="OK"){
                                swal(
                                    'Éxito',
                                    'Fichado correctamente',
                                    'success',
                                ).then((result) => {
                                    if (result.value){
                                        location.reload();
                                    }
                                });
                            }else{
                                swal(
                                    'Error',
                                    'Error.',
                                    'error'
                                );
                            }
                        });
                        }
                    });
                }else{
                    swal({title: 'Control de jornada',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: "Pausar",
                        cancelButtonText: "Cancelar",
                    }).then((result) => {
                        if(result.value){
                            $.when( setStatusTask(id, "Pausada") ).then(function( data, textStatus, jqXHR ) {
                            if(data.estado=="OK"){
                                swal(
                                    'Éxito',
                                    'Pausado correctamente',
                                    'success',
                                ).then((result) => {
                                    if (result.value){
                                        location.reload();
                                    }
                                });
                            }else{
                                swal(
                                    'Error',
                                    'Error.',
                                    'error'
                                );
                            }
                        });
                        }
                    });
                }
        @php
            }
        @endphp

      });
      console.log('userID: ', {{Auth::user()->id}})
});

$(document).ready(function() {
    $('#sendLogout').on('click', function(e){
        e.preventDefault();
        $.when( getLogout() ).then(function( data ) {
            if (data != '') {
                window.location.href = '/admin'
            }
            console.log(data);
        });
    })
})
function getLogout(){
    return  $.ajax({
        type: "POST",
        url: '/admin/logout',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        }
    });
}
</script>
@endsection
@push('scripts')
@endpush
