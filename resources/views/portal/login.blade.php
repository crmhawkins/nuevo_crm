<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts and Styles -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        body, html {
            height: 100%;
            background-color: #f4f5f7;
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container d-flex flex-column align-items-center justify-content-center" style="height: 100%">
        <div class="card mb-4" style="width: 100%; max-width: 400px;">
            <div class="card-body text-center">
                <img src={{ asset('assets/images/logo/logo.png') }} alt="Logo de la Compañía" class="img-fluid mb-4">
            </div>
        </div>
        <div class="card" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h5 class="card-title text-center mb-4">Iniciar Sesión</h5>

                <form method="POST" action="{{ route('portal.loginPost') }}">
                    @csrf
                    <div class="form-group">
                        <label for="usuario">Usuario</label>
                        <input id="Usuario" type="text" class="form-control" name="usuario" value="{{ old('usuario') }}" required autocomplete="usuario" autofocus>
                    </div>
                    <div class="form-group">
                        <label for="pin">Pin</label>
                        <input id="pin" type="text" class="form-control" name="pin" required autocomplete="pin" autofocus>
                    </div>
                    <button type="submit" class="btn btn-dark w-100">Iniciar sesión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
@include('partials.toast')
<script>
</script>
</html>
