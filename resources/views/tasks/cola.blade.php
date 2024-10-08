@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<style>
    .user-card {
        width: 100%; /* Full width on small screens */
        height: 400px; /* Fixed height for each card */
        overflow: hidden; /* Ensures no content spills out */
        margin-bottom: 20px; /* Space between cards */
    }
    @media (min-width: 768px) {
        .user-card { /* Adjust width on larger screens */
            width: calc(50% - 1rem); /* Adapt width with a small gap */
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
                <div class="card-body">
                    <p class="card-title">{{$usuario->name}} {{$usuario->surname}}</p>
                    <p class="card-subtitle">{{$usuario->departamento->name}}</p>
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
                                @foreach ( $usuario->tareas as $tarea )
                                    <tr @if($tarea->task_status_id == 1) style="background-color: #7ede6d;" @endif>
                                        <td>{{$tarea->title}}</td>
                                        <td>{{$tarea->presupuesto ? $tarea->presupuesto->cliente->name : 'Presupuesto Borrado'}}</td>
                                        <td>{{$tarea->estimated_time}}</td>
                                        <td>{{$tarea->real_time}}</td>
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

