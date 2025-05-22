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
                <h3>Editar Servicio</h3>
                <p class="text-subtitle text-muted">Formulario para editar un servicio</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('servicios.index')}}">Servicios</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar servicio</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card">
            <div class="card-body">
                <form action="{{route('servicios.update',$servicio->id )}}" method="POST">
                    @csrf
                    <div class="bloque-formulario">
                        <div class="row">
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label for="title">Titulo:</label>
                                    <input placeholder="Titulo del servicio" type="text" class="form-control @error('title') is-invalid @enderror" id="title" value="{{ old('title',$servicio->title) }}" name="title">
                                    @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label class="mb-2 text-left" for="services_categories_id">Categoria</label>
                                    <div class="flex flex-row align-items-start mb-0">
                                        <select id="services_categories_id" class="choices w-100 form-select  @error('services_categories_id') is-invalid @enderror" name="services_categories_id">
                                            <option value="">Seleccione una Categoria</option>
                                            @foreach ($categorias as $categoria)
                                                <option {{$servicio->services_categories_id == $categoria->id ? 'selected' : '' }} value="{{ $categoria->id }}">{{ $categoria->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('services_categories_id')
                                    <p class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label class="mb-2 text-left" for="concept">Descripcion:</label>
                                    <textarea class="form-control @error('concept') is-invalid @enderror" id="concept" name="concept">{{ old('concept',$servicio->concept) }}</textarea>
                                    @error('concept')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label for="price">Precio:</label>
                                    <input placeholder="Precio..." type="number" class="form-control @error('price') is-invalid @enderror" id="price" value="{{ old('price',$servicio->price) }}" name="price" step="0.01">
                                    @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <div class="form-group mb-3">
                                    <label class="text-left" for="inactive">Ocultar:</label>
                                    <input type="checkbox" id="inactive" name="inactive" value="1" {{ old('inactive', $servicio->inactive) ? 'checked' : '' }}>
                                    @error('inactive')
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
                            {{ __('Actualizar') }}
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
@include('partials.toast')

@endsection
