@extends('layouts.app')

@section('titulo', 'Productividad de Usuarios')

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
                <h3><i class="fa-regular fa-chart-bar"></i> Productividad de Usuarios</h3>
                <p class="text-subtitle text-muted">Listado de productividad mensual por usuario</p>
            </div>
            <div class="col-sm-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Productividad</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                {{-- Selector de Mes y Año --}}
                <form action="{{ route('productividad.index') }}" method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                        </div>
                        <div class="col-md-2 align-self-end">
                            <button type="submit" class="btn btn-outline-primary">Ver Productividad</button>
                        </div>
                    </div>
                </form>

                {{-- Tabla de Productividad --}}
                <table class="table" id="table1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Nota</th>
                            <th>Productividad (%)</th>
                            <th>Tareas finalizadas</th>
                            <th>Horas Estimadas</th>
                            <th>Horas Reales</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productividadUsuarios as $usuario)
                            <tr class="usuario-row">
                                <td>{{ $usuario['id'] }}</td>
                                <td>{{ $usuario['nombre'] }}</td>
                                <td>{{ number_format($usuario['nota'] , 2) }}</td>
                                <td>{{ $usuario['productividad'] }}%</td>
                                <td>{{ $usuario['tareasfinalizadas'] }}</td>
                                <td>{{ $usuario['horasEstimadas'] }}</td>
                                <td>{{ $usuario['horasReales'] }}</td>
                                <td>
                                    <button class="btn btn-outline-secondary toggle-details" type="button" data-toggle="collapse" data-target="#detalles-{{ $loop->index }}" aria-expanded="false">
                                        Ver Detalles
                                    </button>
                                </td>
                            </tr>
                            <tr id="detalles-{{ $loop->index }}" class="collapse">
                                <td colspan="7">
                                    <table class="table table-sm border-0">
                                        <thead>
                                            <tr>
                                                <th>Titulo</th>
                                                <th>Tiempo Estimado</th>
                                                <th>Tiempo Real</th>
                                                <th>Última Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($usuario['tareas'] as $tarea)
                                            <tr>
                                                <td>{{ $tarea->title }}</td>
                                                <td>{{ $tarea->estimated_time }}</td>
                                                <td>{{ $tarea->real_time }}</td>
                                                <td>{{ $tarea->updated_at->format('d-m-Y H:i') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
        $('#table1 tbody').on('click', '.toggle-details', function () {
            let target = $(this).data('target');
            $(target).collapse('toggle');
            $(target).on('shown.bs.collapse', () => {
                $(this).text('Ocultar');
            });
            $(target).on('hidden.bs.collapse', () => {
                $(this).text('Ver Detalles');
            });
        });
    });
</script>
@endsection
