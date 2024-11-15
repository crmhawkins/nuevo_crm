@extends('layouts.app')

@section('titulo', 'Crear Dominio')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Crear Contraseña</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar contraseñas</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('passwords.index')}}">Contraseñas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear contraseña</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('passwords.store')}}" method="POST">
                        @csrf
                        <h3 class="mb-2 text-left form-label uppercase">Cliente Asociado</h3>
                        <div class="flex flex-col mb-4">
                            <div class="form-group flex flex-row align-items-center mb-0">
                                <select class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
                                  @if ($clientes->count() > 0)
                                      <option value="{{null}}">--- Seleccione un cliente ---</option>
                                        @foreach ( $clientes as $cliente )
                                            <option data-id="{{$cliente->id}}" value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">No existen clientes todavia</option>
                                    @endif
                                </select>
                            </div>
                            @error('client_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        {{-- Nombre --}}
                        <div class="form-group mb-4">
                            <label class="text-uppercase form-label" style="font-weight: bold" for="website">Web:</label>
                            <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" value="{{ old('website') }}" name="website">
                            @error('website')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="text-uppercase form-label" style="font-weight: bold" for="user">Usuario:</label>
                            <input type="text" class="form-control @error('user') is-invalid @enderror" id="user" value="{{ old('user') }}" name="user">
                            @error('user')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group mb-4">
                            <label class="text-uppercase form-label" style="font-weight: bold" for="password">Contraseña:</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" value="{{ old('password') }}" name="password">
                            @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Boton --}}
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
<script>

</script>
@endsection

