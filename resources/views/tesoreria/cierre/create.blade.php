@extends('layouts.app')

@section('titulo', 'Crear tipo de iva')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
 <div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Crear o editar Cierre de año</h3>
                <p class="text-subtitle text-muted">Formulario para crear o editar un cierre de año</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('cierre.index')}}">Cierres</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Cierre</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="row">
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('cierre.store')}}" method="POST" >
                            @csrf
                            @foreach ($bankAccounts as $index => $banco )
                            <div class="row mb-3">
                                <div class="col-md-4 mt-4">
                                    <label for="nombre_{{ $index }}" class="mb-2">Banco:</label>
                                    <input type="text" class="form-control" id="nombre_{{ $index }}" name="cierres[{{ $index }}][nombre]" value="{{ $banco->name }}" disabled>
                                    <input type="hidden" name="cierres[{{ $index }}][banco]" value="{{ $banco->id }}">
                                    @error("cierres.$index.nombre")
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label for="valor_{{ $index }}" class="mb-2">Balance:</label>
                                    <input type="number" class="form-control" id="valor_{{ $index }}" name="cierres[{{ $index }}][valor]" value="{{ old("cierres.$index.valor") }}">
                                    @error("cierres.$index.valor")
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-4">
                                    <label for="year_{{ $index }}" class="mb-2">Año:</label>
                                    <input type="number" class="form-control" id="year_{{ $index }}" name="cierres[{{ $index }}][year]" value="{{ old("cierres.$index.year", now()->year) }}">
                                    @error("cierres.$index.year")
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card-body p-3">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                    <div class="card-body">
                        <button id="actualizar" class="btn btn-success btn-block mt-3">Crear Cierre</button>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    $('#actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('form').submit(); // Esto envía el formulario.
    });
</script>
@endsection
