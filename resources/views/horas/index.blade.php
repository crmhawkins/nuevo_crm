@extends('layouts.app')

@section('titulo', 'Jornadas Semanales')

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
                    <h3><i class="fa-regular fa-clock"></i> Jornadas</h3>
                    <p class="text-subtitle text-muted">Listado de jornada semanal</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jornadas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    {{-- Selector de Semana --}}
                    <form action="{{ route('horas.index') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="week" class="form-label">Seleccione la Semana</label>
                                <input type="week" id="week" name="week" class="form-control" value="{{ request('week', now()->format('Y-\WW')) }}">
                            </div>
                            <div class="col-md-2 align-self-end">
                                <button type="submit" class="btn btn-primary">Ver Jornada</button>
                            </div>
                            <div class="col-md-2 align-self-end">
                                <a href="{{ route('horas.export', ['week' => request('week', now()->format('Y-\WW'))]) }}" class="btn btn-success">Exportar a Excel</a>
                            </div>
                        </div>
                    </form>

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Departamente</th>
                                <th>Vacaciones</th>
                                <th>Horas Trabajadas / Horas Producidas</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr class="usuario-row">
                                    <td>{{ $usuario['usuario'] }}</td>
                                    <td>{{ $usuario['departamento'] }}</td>
                                    <td>{{ $usuario['vacaciones'] }}</td>
                                    <td>{{ $usuario['horas_trabajadas'] }} / {{ $usuario['horas_producidas'] }}</td>
                                    <td>
                                        <button class="btn btn-secondary toggle-details" type="button" data-toggle="collapse" data-target="#detalles-{{ $loop->index }}" aria-expanded="false">
                                            Ver Detalles
                                        </button>
                                        <div id="detalles-{{ $loop->index }}" class="collapse">
                                            <div class="card card-body mt-2">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Horas Trabajadas:</strong></p>
                                                        <ul>
                                                            <li><strong>Lunes:</strong> {{ $usuario['horasTrabajadasLunes'] }} </li>
                                                            <li><strong>Martes:</strong> {{ $usuario['horasTrabajadasMartes'] }} </li>
                                                            <li><strong>Miércoles:</strong> {{ $usuario['horasTrabajadasMiercoles'] }} </li>
                                                            <li><strong>Jueves:</strong> {{ $usuario['horasTrabajadasJueves'] }} </li>
                                                            <li><strong>Viernes:</strong> {{ $usuario['horasTrabajadasViernes'] }} </li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Horas Producidas:</strong></p>
                                                        <ul>
                                                            <li><strong>Lunes:</strong> {{ $usuario['horasProducidasLunes'] }} </li>
                                                            <li><strong>Martes:</strong> {{ $usuario['horasProducidasMartes'] }} </li>
                                                            <li><strong>Miércoles:</strong> {{ $usuario['horasProducidasMiercoles'] }} </li>
                                                            <li><strong>Jueves:</strong> {{ $usuario['horasProducidasJueves'] }} </li>
                                                            <li><strong>Viernes:</strong> {{ $usuario['horasProducidasViernes'] }} </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                $(this).text('Ocultar Detalles');
            });
            $(target).on('hidden.bs.collapse', () => {
                $(this).text('Ver Detalles');
            });
        });
    });

</script>

@include('partials.toast')
@endsection
