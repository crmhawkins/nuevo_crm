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
    .tables{
        overflow-y: auto; /* Vertical scroll on overflow */
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
    }
</style>
@endsection
@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Status Proyectos</h3>
                    <p class="text-subtitle text-muted">Listado de projectos</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Estados de projectos</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section d-flex flex-wrap justify-content-between mt-4">

            @foreach ($usuario->clientes as $cliente)
                @if(count($cliente->presupuestosPorEstado(3)) > 0)
                    <div class="card user-card">
                        <div class="card-body">
                            <p class="card-title">{{$cliente->name}} </p>
                            <div class="tables">
                                @foreach ($cliente->campañas as $campaña)
                                    @if(count($campaña->presupuestosPorEstado(3)) > 0)
                                    <div class="mb-4">
                                        <p class="card-header">{{$campaña->name}}</p>
                                         <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">REFERENCIA</th>
                                                        <th scope="col">GESTOR</th>
                                                        <th scope="col">ESTADO</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ( $campaña->presupuestos as $presupuesto )
                                                        <tr>
                                                            <td>{{$presupuesto->reference}}</td>
                                                            <td>{{$presupuesto->usuario ? $presupuesto->usuario->name.' '.$presupuesto->usuario->surname : 'Gestor no encontrado'  }}</td>
                                                            <td>{{$presupuesto->estadoPresupuesto->name ?? 'Estado no encontrado'}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </section>
    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

