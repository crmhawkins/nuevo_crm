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
                            <div class="col-md-4">
                                <label for="mes" class="form-label">Seleccione el Mes</label>
                                <input type="month" id="mes" name="mes" class="form-control" value="{{ request('mes', now()->format('Y-m')) }}">
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
                                <th>Productividad (%)</th>
                                <th>Tareas finalizadas</th>
                                <th>Horas Estimadas</th>
                                <th>Horas Reales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($productividadUsuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario['id'] }}</td>
                                    <td>{{ $usuario['nombre'] }}</td>
                                    <td>{{ $usuario['productividad'] }}%</td>
                                    <td>{{ $usuario['tareasfinalizadas'] }}</td>
                                    <td>{{ $usuario['horasEstimadas'] }}</td>
                                    <td>{{ $usuario['horasReales'] }}</td>
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
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#table1').DataTable({
            paging: false, // Quitar paginación
            info: false, // Quitar información de la tabla
            searching: false, // Quitar barra de búsqueda
            ordering: true, // Permitir ordenar
            responsive: true // Hacer la tabla responsive
        });
    });
</script>

@include('partials.toast')
@endsection
