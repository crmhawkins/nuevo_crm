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
                    <h3>Crear Campaña</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar una Campaña</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Campañas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear campaña</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('campania.storeFromBudget')}}" method="POST">
                        @csrf
                        <input type="text" name="client_id" value="{{$cliente->id}}" hidden>
                        @if (isset($petitionid))
                        <input type="text" name="petition_id" value="{{$petitionid}}" hidden>
                        @endif
                        @if (isset($presupuesto))
                        <input type="text" name="presupuesto_id" value="{{$presupuesto}}" hidden>
                        @endif

                        <h3 class="mb-2 text-left uppercase">Cliente Asociado</h3>
                        <div class="form-control mb-4">{{$cliente->company ?? $cliente->name}}</div>
                        {{-- Nombre --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="name">Nombre:</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name')}}" name="name">
                            @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Observaciones --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="description">Observaciones:</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">

                            </textarea>
                            @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Nota --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="notes">Nota:</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes">

                            </textarea>
                            @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Boton --}}
                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-primary">
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

