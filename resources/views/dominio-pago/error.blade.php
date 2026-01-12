<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Roboto', sans-serif;
            padding: 20px 0;
        }
        .error-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .error-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .error-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-card">
                    <div class="error-header">
                        <i class="fas fa-exclamation-triangle" style="font-size: 64px; margin-bottom: 20px;"></i>
                        <h2 class="mb-2">Error de Acceso</h2>
                    </div>
                    
                    <div class="error-body text-center">
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle me-2"></i>
                            {{ $mensaje }}
                        </div>
                        
                        <p class="text-muted">
                            Si necesita un nuevo enlace para configurar su método de pago, 
                            por favor contacte con nuestro equipo de soporte.
                        </p>
                        
                        <a href="mailto:soporte@hawkins.es" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Contactar Soporte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
