@extends('layouts.app')

@section('titulo', 'Jornadas Semanales')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        {{-- Títulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-globe-americas"></i> Jornadas</h3>
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
                        </div>
                    </form>

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Horas Trabajadas</th>
                                <th>Horas Producidas</th>
                                <th>Detalles</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                                <tr>
                                    <td>{{ $usuario['usuario'] }}</td>
                                    <td>{{ $usuario['horas_trabajadas'] }}</td>
                                    <td>{{ $usuario['horas_producidas'] }}</td>
                                    <td>
                                        <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target=".detalles-{{ $loop->index }}" aria-expanded="false" aria-controls="detalles-{{ $loop->index }}">
                                            Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                                <tr class="collapse detalles-{{ $loop->index }}">
                                    <td colspan="4">
                                        <div class="card card-body">
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script>
    // Inicializa la tabla con DataTable y botones de exportación
    document.addEventListener('DOMContentLoaded', function () {
        let table1 = $('#table1').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Exportar a Excel',
                    className: 'btn btn-success'
                }
            ]
        });
    });
</script>

@include('partials.toast')
@endsection
