@extends('layouts.app')

@section('titulo', 'Crear Cliente')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
 <div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Crear categoria de servicio</h3>
                <p class="text-subtitle text-muted">Formulario para registrar una categoria de servicio</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('servicios.index')}}">Categorias de Servicios</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear servicio</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card">
            <div class="card-body">
                <form action="{{route('serviciosCategoria.store')}}" method="POST">
                    @csrf
                    <div class="bloque-formulario">
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label for="title">Nombre:</label>
                                    <input placeholder="Nombre de la categoria" type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" name="name">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label class="mb-2 text-left" for="terms">Condiciones:</label>
                                    <textarea class="form-control @error('terms') is-invalid @enderror" id="terms" name="terms">{{ old('terms') }}</textarea>
                                    @error('terms')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label class="mb-2 text-left" for="type">Tipo:</label>
                                    <div class="flex flex-row align-items-start mb-0">
                                        <select id="type" class="choices w-100 form-select  @error('type') is-invalid @enderror" name="type">
                                            <option value="">Seleccione un tipo</option>
                                            <option {{ old('type') == 1 ? 'selected' : '' }} value="1">Proveedor</option>
                                            <option {{ old('type') == 2 ? 'selected' : '' }} value="2">Propio</option>
                                        </select>
                                    </div>
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-5">
                        <button type="submit" class="btn btn-success w-100 text-uppercase">
                            {{ __('Registrar') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

@endsection
