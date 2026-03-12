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
        .login-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 20px;
        }
        .login-tab {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            background: none;
            color: #6c757d;
            font-weight: 500;
        }
        .login-tab.active {
            color: #212529;
            border-bottom: 2px solid #212529;
            margin-bottom: -2px;
        }
        .login-tab-content {
            display: none;
        }
        .login-tab-content.active {
            display: block;
        }
        .certificate-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
        }
        .certificate-upload-area:hover {
            border-color: #212529;
            background-color: #e9ecef;
        }
        .certificate-upload-area.dragover {
            border-color: #212529;
            background-color: #e9ecef;
        }
        .certificate-file-name {
            margin-top: 10px;
            font-size: 0.9em;
            color: #28a745;
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

                <!-- Tabs para seleccionar método de autenticación -->
                <div class="login-tabs d-flex justify-content-center">
                    <button type="button" class="login-tab active" data-tab="password">Usuario/Contraseña</button>
                    <button type="button" class="login-tab" data-tab="certificate">Certificado</button>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario de usuario/contraseña -->
                <div id="password-tab" class="login-tab-content active">
                    <form method="POST" action="{{ route('login') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="username">Usuario</label>
                            <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">Recordar contraseña</label>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Iniciar sesión</button>
                    </form>
                </div>

                <!-- Formulario de certificado -->
                <div id="certificate-tab" class="login-tab-content">
                    <form method="POST" action="{{ route('login') }}" enctype="multipart/form-data" id="certificate-form">
                        @csrf
                        <div class="form-group">
                            <label>Certificado X.509</label>
                            <div class="certificate-upload-area" id="certificate-upload-area">
                                <i class="fas fa-file-certificate fa-3x mb-2" style="color: #6c757d;"></i>
                                <p class="mb-0">Haz clic para seleccionar o arrastra tu certificado aquí</p>
                                <small class="text-muted">Formatos soportados: .pem, .crt, .cer</small>
                            </div>
                            <input type="file" id="certificate" name="certificate" accept=".pem,.crt,.cer" style="display: none;" required>
                            <div id="certificate-file-name" class="certificate-file-name" style="display: none;"></div>
                            @error('certificate')
                                <span class="text-danger" style="font-size: 0.875em;">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember-cert" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember-cert">Recordar sesión</label>
                        </div>
                        <button type="submit" class="btn btn-dark w-100">Iniciar sesión con certificado</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        $(document).ready(function() {
            // Manejo de tabs
            $('.login-tab').on('click', function() {
                const tabName = $(this).data('tab');
                
                // Actualizar tabs
                $('.login-tab').removeClass('active');
                $(this).addClass('active');
                
                // Actualizar contenido
                $('.login-tab-content').removeClass('active');
                $('#' + tabName + '-tab').addClass('active');
            });

            // Manejo de carga de certificado
            const uploadArea = $('#certificate-upload-area');
            const fileInput = $('#certificate');
            const fileNameDisplay = $('#certificate-file-name');

            // Click en el área de carga
            uploadArea.on('click', function() {
                fileInput.click();
            });

            // Cambio de archivo
            fileInput.on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    fileNameDisplay.text('Archivo seleccionado: ' + file.name).show();
                    uploadArea.css({
                        'border-color': '#28a745',
                        'background-color': '#d4edda'
                    });
                }
            });

            // Drag and drop
            uploadArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('dragover');
            });

            uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
            });

            uploadArea.on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('dragover');
                
                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    fileInput[0].files = files;
                    fileNameDisplay.text('Archivo seleccionado: ' + files[0].name).show();
                    uploadArea.css({
                        'border-color': '#28a745',
                        'background-color': '#d4edda'
                    });
                }
            });

            // Validación antes de enviar formulario de certificado
            $('#certificate-form').on('submit', function(e) {
                if (!fileInput[0].files || fileInput[0].files.length === 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Por favor, selecciona un certificado'
                    });
                    return false;
                }
            });
        });
    </script>
</body>
</html>
