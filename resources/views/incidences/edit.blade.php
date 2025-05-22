@extends('layouts.app')

@section('titulo', 'Editar incidencia')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Incidencia</h3>
                    <p class="text-subtitle text-muted">Formulario para editar una incidencia existente</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('incidencias.index')}}">Incidencias</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar incidencia</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    @include('incidences.form', [
                        'action' => route('incidencias.update', $incidencia->id),
                        'buttonText' => 'Actualizar Incidencia',
                        'incidencia' => $incidencia,
                        'usuarios' => $users,
                        'clientes' => $clientes,
                        'presupuestos' => $presupuestos,
                        'suppliers' => $suppliers,
                        'estados' => $estados,
                    ])
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
@endsection
