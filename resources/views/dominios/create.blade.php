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
                    <h3>Crear Dominio</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar un dominio</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('dominios.index')}}">Dominios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear dominio</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('dominios.store')}}" method="POST">
                        @csrf

                        <h3 class="mb-2 text-left uppercase">Cliente Asociado</h3>
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

                                {{-- <button id="newClient" type="button" class="btn btn-primary mb-4 ml-3">+</button> --}}
                            </div>
                            @error('client_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        {{-- Nombre --}}
                        <div class="form-group mb-4">
                            <label class="text-uppercase" style="font-weight: bold" for="dominio">Dominio:</label>
                            <input type="text" class="form-control @error('dominio') is-invalid @enderror" id="dominio" value="{{ old('dominio') }}" name="dominio">
                            @error('dominio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Fecha contratacion --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="date">Fecha de Contratacion:</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" value="{{ old('date') }}" name="date">
                            @error('date')
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
    $(document).ready(function() {
        // Boton añadir campaña
        $('#newCampania').click(function(){
            var clientId = $('select[name="client_id"]').val();
            if (clientId == '' || clientId == null) {
                // Alerta Toast de error
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                // Lanzamos la alerta
                Toast.fire({
                    icon: "error",
                    title: "Por favor, selecciona un cliente."
                });
                return;
            }

            // Abrimos pestaña para crear campaña
            window.open("{{ route('campania.createFromBudget', 0) }}", '_blank');
        });

        // Boton añadir cliente
        $('#newClient').click(function(){

            // Abrimos pestaña para crear campaña
            window.open("{{ route('campania.createFromBudget', 0) }}", '_blank');
        });
    });
</script>
@endsection

