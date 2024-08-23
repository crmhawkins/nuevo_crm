@extends('layouts.app')

@section('titulo', 'Editar Contrato')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar contrato</h3>
                    <p class="text-subtitle text-muted">Formulario para editar un contrato</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('nominas.index')}}">Contrato</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar Contrato</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    @include('contratos.form', [
                        'action' => route('contratos.update', $contrato->id),
                        'buttonText' => 'Actualizar Contrato',
                        'contrato' => $contrato,
                        'usuarios' => $usuarios
                    ])
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>

</script>
@endsection
