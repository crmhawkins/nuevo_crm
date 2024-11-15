@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<style>
    .user-card {
        width: 100% !important; /* Full width on small screens */
        height: 500px !important; /* Full height on small screens */
        overflow: hidden; /* Ensures no content spills out */
        margin-bottom: 20px; /* Space between cards */
    }
    @media (min-width: 768px) {
        .user-card { /* Adjust width on larger screens */
            width: calc(50% - 1rem) !important; /* Adapt width with a small gap */
        }
    }
    .card-body {
        position: relative; /* Positioning context */
        height: calc(100% - 20px); /* Full height minus padding */
        display: flex;
        flex-direction: column; /* Stack children vertically */
    }
    .card-title {
        font-size: 2rem; /* Larger title */
        margin-bottom: .75rem; /* Space below title */
        font-weight: 400;
    }
    .card-subtitle {
        font-size: 1.2rem; /* Larger title */
        margin-bottom: .75rem; /* Space below title */
        font-weight: 400;
    }
    .table-responsive {
        flex-grow: 1; /* Allows table container to fill available space */
        overflow-y: auto; /* Vertical scroll on overflow */
    }
</style>
@endsection
@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Cola de trabajo</h3>
                    <p class="text-subtitle text-muted">Listado de colas de trabajo</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('tareas.index')}}">Tareas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cola de trabajo</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section d-flex flex-wrap justify-content-between mt-4">
            @foreach ($usuarios as $usuario)
            <div class="card user-card">
                <div class="card-body ">
                    <div class="row">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-title">{{$usuario->name}} {{$usuario->surname}}</p>
                                <p class="card-subtitle">{{$usuario->departamento->name}}</p>
                            </div>
                            <a class="btn btn-outline-secondary" href="{{route('tarea.calendar',$usuario->id)}}" target="_blank">
                                Ver calendario
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                 <tr>
                                    <th scope="col">TITULO</th>
                                    <th scope="col">CLIENTE</th>
                                    <th scope="col">ESTIMADO</th>
                                    <th scope="col">REAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $usuario->tareas->whereIn('task_status_id', [1, 2]) as $tarea )
                                    <tr @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->title}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->presupuesto ? ($tarea->presupuesto->cliente ? ($tarea->presupuesto->cliente->company ??  $tarea->presupuesto->cliente->name)  : 'Cliente Borrado') : 'Presupuesto Borrado'}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->estimated_time}}</td>
                                        <td @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>{{$tarea->real_time}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </section>
    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

