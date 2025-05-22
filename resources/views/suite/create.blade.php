@extends('layouts.app')

@section('titulo', 'Crear Usuario Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-white shadow position-relative" style="z-index: 10;">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Crear Usuario Suite</span>
                    <a href="{{ route('suite') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Inicio Suite
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label for="user" class="form-label"> Usuario </label>
                        <input id="user" type="text" class="form-control" required>
                        <div class="invalid-feedback" id="userError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label"> Contraseña </label>
                        <div class="input-group">
                            <input id="password" type="password" class="form-control" required>
                            <button class="btn btn-outline-secondary btn-sm toggle-password" type="button">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" onclick="crearUsuario()">
                            Crear Usuario
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('partials.toast')

    <script>
        function crearUsuario() {
            // Reset previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').empty();

            const user = $('#user').val();
            const password = $('#password').val();

            if (!user || !password) {
                if (!user) {
                    $('#user').addClass('is-invalid');
                    $('#userError').text('El usuario es requerido');
                }
                if (!password) {
                    $('#password').addClass('is-invalid');
                    $('#passwordError').text('La contraseña es requerida');
                }
                return;
            }

            $.ajax({
                url: '{{ route("suite.store") }}',
                method: 'POST',
                data: {
                    user: user,
                    password: password,
                    _token: '{{ csrf_token() }}'
                },
                success: function(xhr) {
                    if(xhr.status === 200) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Usuario creado correctamente',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        })

                        // Clear inputs
                        $('#user').val('');
                        $('#password').val('');
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error al crear el usuario',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        })
                    }
                }
            });
        }

        $(document).ready(function () {
            $(document).on('click', '.toggle-password', function (e) {
                e.preventDefault();

                const input = $(this).siblings('input');

                const type = input.attr('type') === 'password' ? 'text' : 'password';
                input.attr('type', type);

                // Cambiar icono (opcional)
                const icon = $(this).find('i');
                icon.toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>
@endsection

