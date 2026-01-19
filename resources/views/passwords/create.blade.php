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
                            <div class="input-group">
                                <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" value="{{ old('password') }}" name="password">
                                <button type="button" class="btn btn-outline-primary" id="btn-generar-password" title="Generar contraseña determinista">
                                    <i class="bi bi-key-fill"></i> Generar
                                </button>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    const btnGenerar = document.getElementById('btn-generar-password');
    const inputPassword = document.getElementById('password');
    const inputWebsite = document.getElementById('website');

    if (btnGenerar && inputPassword && inputWebsite) {
        btnGenerar.addEventListener('click', function() {
            const dominio = inputWebsite.value.trim();

            if (!dominio) {
                alert('Por favor, ingresa primero el dominio/website para generar la contraseña.');
                inputWebsite.focus();
                return;
            }

            // Deshabilitar botón mientras se genera
            btnGenerar.disabled = true;
            btnGenerar.innerHTML = '<i class="bi bi-hourglass-split"></i> Generando...';

            // Realizar petición AJAX
            fetch('{{ route("passwords.generar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ dominio: dominio })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error === false) {
                    inputPassword.value = data.password;
                    // Opcional: mostrar notificación
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Contraseña generada correctamente');
                    }
                } else {
                    alert('Error al generar la contraseña: ' + (data.mensaje || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar la contraseña. Por favor, intenta nuevamente.');
            })
            .finally(() => {
                // Rehabilitar botón
                btnGenerar.disabled = false;
                btnGenerar.innerHTML = '<i class="bi bi-key-fill"></i> Generar';
            });
        });
    }
});
</script>
@endsection

