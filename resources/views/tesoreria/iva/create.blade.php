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
                <h3>Crear Iva</h3>
                <p class="text-subtitle text-muted">Formulario para crear un tipo de iva</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('iva.index')}}">Tipo de iva</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear tipo de iva</li>
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
                        <form action="{{ route('iva.store')}}" method="POST" >
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6 mt-4">
                                    <label for="nombre" class="mb-2">Nombre:</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                    @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label for="valor" class="mb-2">Valor:</label>
                                    <input type="number" class="form-control" id="valor" name="valor">
                                    @error('valor')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                            </div>
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
                        <button id="actualizar" class="btn btn-success btn-block mt-3">Crear Iva</button>

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
