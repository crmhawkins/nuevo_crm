@extends('layouts.app')

@section('titulo', 'Jornadas Por Fecha')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        {{-- Títulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="fa-regular fa-clock"></i> Jornadas</h3>
                    <p class="text-subtitle text-muted">Listado de jornadas por fechas</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jornadas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    {{-- Selector de Fecha --}}
                    <form action="{{ route('horas.index') }}" method="GET" class="mb-4">
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
                                <button type="submit" class="btn btn-outline-primary">Ver Jornada</button>
                            </div>
                            <div class="col-md-2 align-self-end">
                                <a href="{{ route('horas.export', ['fecha_inicio' => request('fecha_inicio'), 'fecha_fin' => request('fecha_fin')]) }}" class="btn btn-outline-success">Exportar a Excel</a>
                            </div>
                        </div>
                    </form>

                    <table class="table" id="table1">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Departamento</th>
                                {{-- <th>Vacaciones</th>
                                <th>Puntualidad</th>
                                <th>Baja</th> --}}
                                <th>Horas Trabajadas </th>
                                <th>Horas Producidas</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr class="usuario-row">
                                    <td>{{ $usuario['usuario'] }}</td>
                                    <td>{{ $usuario['departamento'] }}</td>
                                    {{-- <td>{{ $usuario['vacaciones'] }} días</td>
                                    <td>{{ $usuario['puntualidad'] }} días</td>
                                    <td>{{ $usuario['baja'] }} días</td> --}}
                                    <td>{{ $usuario['total_horas_trabajadas'] }} </td>
                                    <td>{{ $usuario['total_horas_producidas'] }}</td>
                                    <td>
                                        <button class="btn btn-outline-secondary toggle-details" type="button" data-toggle="collapse" data-target="#detalles-{{ $loop->index }}" aria-expanded="false">
                                            Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                                <tr id="detalles-{{ $loop->index }}" class="collapse">
                                    <td colspan="7">
                                        <table class="table table-sm border-0">
                                            <tbody>
                                                @foreach($todosLosDias as $fecha)
                                                <tr>
                                                    <td><strong>{{ $fecha }}:</strong></td>
                                                    <td>Trabajadas: {{ $usuario['horas_trabajadas'][$fecha] ?? '0' }} </td>
                                                    <td>Producidas: {{ $usuario['horas_producidas'][$fecha] ?? '0' }} </td>
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
<script>
    // Añadir lógica para abrir y cerrar los detalles
    $(document).ready(function () {
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

@include('partials.toast')
@endsection
