@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="page-heading card" style="box-shadow: none !important">
    <main id="main-container">
        <!-- Hero Section -->
        <div class="bg-image overflow-hidden" style="background-color: black">
            <div class="content content-narrow content-full">
                <div class="text-center mt-5 mb-2">
                    <h2 class="h2 text-white mb-0">Bienvenido {{$user->name}}</h2>
                    <h1 class="h1 text-white mb-0">Quedan {{$diasDiferencia}} días para finalizar el mes</h1>
                    <h2 class="h3 text-white mb-0">Tienes {{$pedienteCierre}} € pendiente por tramitar</h2>
                    <div class="mt-4">
                        <button id="sendLogout" type="button" class="btn btn-warning py-2 mb-4">Salir</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Hero -->

        <!-- Stats Section -->
        <div class="content content-narrow">
            <div class="row d-flex justify-content-center my-3">
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Pendiente de Cierre</h6>
                            <h2 class="font-weight-bold">{{$pedienteCierre}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión En Curso</h6>
                            <h2 class="font-weight-bold">{{$comisionCurso}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Pendiente</h6>
                            <h2 class="font-weight-bold">{{$comisionPendiente}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Tramitada</h6>
                            <h2 class="font-weight-bold">{{$comisionTramitadas}} €</h2>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h6 class="text-muted text-uppercase">Comisión Restante</h6>
                            <h2 class="font-weight-bold">{{$comisionRestante}} €</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Stats Section -->

        <!-- Form Section -->
        <div class="row justify-content-center my-4">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Agregar Cliente</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mt-3 justify-content-center">
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="cliente" class="form-control" type="text" placeholder="Nombre del cliente">
                                </div>
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="telefono" class="form-control" type="text" placeholder="Número de Teléfono">
                                </div>
                                <div class="col-md-2 col-sm-12 mb-3">
                                    <input name="email" class="form-control" type="email" placeholder="Email">
                                </div>
                                <div class="col-md-1 col-sm-12 mb-3">
                                    <select name="segmento" class="form-control">
                                        <option value="">Segmento</option>
                                        <option value="1">Segmento 1</option>
                                        <option value="2">Segmento 2</option>
                                        <option value="3">Segmento 3</option>
                                    </select>
                                </div>
                                <div class="col-md-1 col-sm-12 mb-3">
                                    <select name="estado" class="form-control">
                                        <option value="">Estado</option>
                                        <option value="24">Interesados</option>
                                        <option value="18">Leads</option>
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12 mb-3">
                                    <textarea name="comentario" class="form-control" placeholder="Comentario" rows="1"></textarea>
                                </div>
                                <div class="col-md-1 col-sm-12 d-flex align-items-end">
                                    <input type="submit" value="Guardar" class="btn btn-primary w-100">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Form Section -->

        <!-- Table Section -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Kit Digital</h3>
                        <div class="w-50">
                            <select id="estadoFilter" class="form-control">
                                <option value="">Todos los Estados</option>
                                @if (isset($estadosKit))
                                    @foreach ($estadosKit as $estado)
                                        <option value="{{ $estado->nombre }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive my-2">
                            <table id="kitDigitalTable" class="table table-striped table-hover table-borderless table-vcenter mb-0">
                                <thead class="thead-dark">
                                    <tr class="text-uppercase text-center">
                                        <th>Fecha</th>
                                        <th class="d-none d-md-table-cell">Concepto</th>
                                        <th>Estado</th>
                                        <th>Teléfono</th>
                                        <th>Email</th>
                                        <th>Comentario</th>
                                        <th class="d-none d-md-table-cell text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ayudas as $ayuda)
                                        <tr class="text-center">
                                            <td>{{ \Carbon\Carbon::parse($ayuda->created_at)->format('d-m-Y') }}</td>
                                            <td class="d-none d-md-table-cell">{{ $ayuda->cliente }}</td>
                                            <td class="text-warning">{{ $ayuda->estados->nombre }}</td>
                                            <td>{{ $ayuda->telefono }}</td>
                                            <td>{{ $ayuda->email }}</td>
                                            <td>{{ $ayuda->comentario }}</td>
                                            <td class="d-none d-md-table-cell text-right">{{ $ayuda->importe }} €</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Table Section -->
    </main>
</div>
@endsection
@section('scripts')

<!-- Cargar primero jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Luego cargar DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        // Inicializar DataTables para la tabla de Kit Digital
        $('#kitDigitalTable').DataTable({
            paging: true,
            searching: false,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            language: {
                decimal: "",
                emptyTable: "No hay datos disponibles",
                info: "_TOTAL_ entradas en total",
                infoEmpty: "0 entradas",
                infoFiltered: "(filtrado de _MAX_ entradas en total)",
                lengthMenu: "Nº de entradas _MENU_",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No hay entradas que cumplan el criterio",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        // Filtro para el dropdown de Estado
        $('#estadoFilter').on('change', function() {
            var table = $('#kitDigitalTable').DataTable();
            table.column(2).search(this.value).draw();
        });

        // Botón de logout
        $('#sendLogout').on('click', function(e) {
            e.preventDefault();
            $.post('/admin/logout', {_token: '{{ csrf_token() }}'}, function(data) {
                window.location.href = '/admin';
            });
        });
    });
</script>

@endsection
