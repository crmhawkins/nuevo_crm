@extends('layouts.app')

@section('titulo', 'LLamadas realizadas')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    {{-- Títulos --}}
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-sm-12 col-md-4 order-md-1 order-last">
                <h3><i class="fa-regular fa-chart-bar"></i> LLamadas realizadas</h3>
                <p class="text-subtitle text-muted">Listado de llamadas realizadas</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">llamadas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                {{-- Selector de Mes y Año --}}
                <form id="formFiltros" action="{{ route('llamadas.index') }}" method="GET" class="mb-4">
                    <div class="row align-items-end">
                        <div class="mb-3 px-2" style="width: 85px">
                            <label class="form-label" for="" >Nª</label>
                            <select name="perPage" class="form-select">
                                <option {{ $perPage == 10 ? 'selected' : '' }} value="10">10</option>
                                <option {{ $perPage == 25 ? 'selected' : '' }} value="25">25</option>
                                <option {{ $perPage == 50 ? 'selected' : '' }} value="50">50</option>
                                <option {{ $perPage == 'all' ? 'selected' : '' }} value="all">Todo</option>
                            </select>
                        </div>
                        <div class="w-20 mb-3 px-2 flex-fill" style="width: 140px">
                            <label class="titulo_filtros" for="">Buscar</label>
                            <input name="buscar" type="text" class="form-control w-100" value="{{old('buscar',$buscar)}}" placeholder="Escriba la palabra a buscar...">
                        </div>
                        <div class="mb-3 px-2 flex-fill" style="width: 140px">
                            <label class="form-label" for="">Gestor</label>
                            <select name="selectedGestor" id="selectedGestor" class="form-select">
                                <option value=""> Gestor </option>
                                @foreach ($gestores as $gestor)
                                    <option {{$selectedGestor == $gestor->id ? 'selected' : ''}} value="{{$gestor->id}}">{{$gestor->name.' '.$gestor->surname}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div  class="mb-3 px-2 flex-fill" style="width: 140px">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div  class="mb-3 px-2 flex-fill" style="width: 140px">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                        <div  class="mb-3  flex-fill" style="width: 140px">
                            <button type="submit" class="btn btn-outline-primary btn-lg">Ver llamadas</button>
                        </div>
                        <input type="hidden" name="sortColumn" id="sortColumn" value="{{old('sortColumn',$sortColumn)}}">
                        <input type="hidden" name="sortDirection" id="sortDirection" value="{{ old('sortDirection',$sortDirection)}}">
                    </div>
                </form>

                {{-- Tabla de Productividad --}}
                <table class="table" id="table1">
                    <thead class="header-table">
                        @foreach ([
                            'admin_user_id' => 'GESTOR',
                            'client_id' => 'CLIENTE',
                            'kit_id' => 'CLI.KIT',
                            'phone' => 'TElÉFONO',
                            'comentario' => 'COMENTARIO',
                            'created_at' => 'F.CREA.',

                            ] as $field => $label)
                            <th class="px-3">
                                <a class="sort" data-column="{{$field}}" >
                                    {{ $label }}
                                    @if ($sortColumn == $field)
                                        <span>{!! $sortDirection == 'asc' ? '&#9650;' : '&#9660;' !!}</span>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="text-center" style="font-size:0.75rem">DURACÓN</th>

                    </thead>
                    <tbody>
                        @foreach($llamadas as $llamada)
                            <tr>
                                <td>{{ Optional($llamada->user)->name }}</td>
                                <td>{{ Optional($llamada->client)->name ?? Optional($llamada->client)->company}}</td>
                                <td>{{ Optional($llamada->kit)->cliente }}</td>
                                <td>{{ $llamada->phone }}</td>
                                <td>{{ $llamada->comentario }}</td>
                                <td>{{ Carbon\Carbon::parse($llamada->start_time)->format('d/m/Y') }}</td>
                                <td>{{ $llamada->duration }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($perPage !== 'all')
                    {{ $llamadas->appends(request()->except('page'))->links() }}
                @endif
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
        });
    });
    $(document).ready(function () {
        // Añadir lógica para abrir y cerrar los detalles
        $('.sort').on('click', function (e) {
            e.preventDefault();
            // Obtener la columna seleccionada del atributo data-column
            var column = $(this).data('column');
            // Obtener el valor actual del formulario
            var currentColumn = $('#sortColumn').val();
            var currentDirection = $('#sortDirection').val();
            // Si la columna seleccionada es la misma, cambiar la dirección
            if (column === currentColumn) {
                var newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                $('#sortDirection').val(newDirection);
            } else {
                // Si es una columna diferente, establecer 'asc' por defecto
                $('#sortDirection').val('desc');
            }

            // Actualizar el valor de la columna seleccionada
            $('#sortColumn').val(column);

            // Enviar el formulario
            $('#formFiltros').submit();
        });
    });
</script>
@endsection
