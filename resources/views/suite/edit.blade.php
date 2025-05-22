@extends('layouts.app')

@section('titulo', 'Editar Usuarios Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-white shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold">Editar Usuarios Suite</span>
                    <a href="{{ route('suite') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Inicio Suite
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Contraseña</th>
                                <th>Guardar Cambios</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suites as $suite)
                            <tr data-id="{{ $suite->id }}">
                                <td><input type="text" class="form-control user" value="{{ $suite->user }}"></td>
                                <td class="input-group">
                                    <input type="password" class="form-control password" placeholder="Introduce la nueva contraseña..." autocomplete="new-password">
                                    <button class="btn btn-outline-secondary btn-sm toggle-password" type="button">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm guardar-cambios">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm eliminar-usuario">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="mensaje" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @include('partials.toast')

    <script>
        // Mostrar / ocultar contraseña
        $('.toggle-password').on('click', function () {
            const input = $(this).closest('tr').find('.password');
            input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
        });

        // Eliminar usuario
        $('.eliminar-usuario').on('click', function () {
            const tr = $(this).closest('tr');
            const id = tr.data('id');

            if (confirm('¿Seguro que quieres eliminar este usuario?')) {
                $.ajax({
                    url: `/suite/delete/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Usuario Eliminado correctamente',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        })

                        tr.remove();
                    }
                });
            }
        });

        // Guardar cambios en todos los usuarios
        $('.guardar-cambios').on('click', function () {
            const tr = $(this).closest('tr');
            const id = tr.data('id');
            const user = tr.find('.user').val();
            const password = tr.find('.password').val();

            console.log({ id, user, password });


            $.ajax({
                url: `/suite/update/${id}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user: user,
                    password: password
                },
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cambios guardados correctamente',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al guardar',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        });
    </script>
@endsection
