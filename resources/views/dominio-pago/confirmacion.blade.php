<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Confirmación - {{ config('app.name', 'Laravel') }}</title>

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
        .confirmation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .confirmation-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .confirmation-body {
            padding: 40px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        .status-success {
            background-color: #d4edda;
            color: #155724;
        }
        .status-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="confirmation-card">
                    <div class="confirmation-header">
                        @if(session('success'))
                            <i class="fas fa-check-circle" style="font-size: 64px; margin-bottom: 20px;"></i>
                            <h2 class="mb-2">¡Método de Pago Configurado!</h2>
                        @else
                            <i class="fas fa-info-circle" style="font-size: 64px; margin-bottom: 20px;"></i>
                            <h2 class="mb-2">Estado del Método de Pago</h2>
                        @endif
                    </div>
                    
                    <div class="confirmation-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        <div class="info-box">
                            <h5><i class="fas fa-globe me-2"></i>Información del Dominio</h5>
                            <p class="mb-1"><strong>Dominio:</strong> {{ $dominio->dominio }}</p>
                            <p class="mb-1"><strong>Cliente:</strong> {{ $cliente->name }}</p>
                            <p class="mb-0"><strong>Fecha de caducidad:</strong> {{ $dominio->getFechaCaducidad() ? $dominio->getFechaCaducidad()->format('d/m/Y') : 'N/A' }}</p>
                        </div>

                        <div class="info-box">
                            <h5><i class="fas fa-credit-card me-2"></i>Métodos de Pago Configurados</h5>
                            
                            @if($dominio->iban && $dominio->iban_validado)
                                <div class="mb-3">
                                    <span class="status-badge status-success">
                                        <i class="fas fa-university me-1"></i>IBAN Configurado
                                    </span>
                                    <p class="mt-2 mb-0"><strong>IBAN:</strong> {{ $dominio->iban }}</p>
                                </div>
                            @else
                                <div class="mb-3">
                                    <span class="status-badge status-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>IBAN No Configurado
                                    </span>
                                </div>
                            @endif

                            @if($dominio->stripe_payment_method_id)
                                <div class="mb-3">
                                    <span class="status-badge status-success">
                                        <i class="fab fa-cc-stripe me-1"></i>Tarjeta Configurada
                                    </span>
                                    <p class="mt-2 mb-0">Método de pago guardado de forma segura en Stripe</p>
                                </div>
                            @else
                                <div class="mb-3">
                                    <span class="status-badge status-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Tarjeta No Configurada
                                    </span>
                                </div>
                            @endif
                        </div>

                        @php
                            $tieneMetodoPago = (!empty($dominio->iban) && $dominio->iban_validado) || !empty($dominio->stripe_payment_method_id);
                        @endphp
                        
                        @if(!$tieneMetodoPago)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atención:</strong> No tiene ningún método de pago válido configurado. 
                                Por favor, configure al menos uno para asegurar la continuidad de su servicio.
                            </div>
                            
                            <a href="{{ route('dominio.pago.formulario', $token) }}" class="btn btn-primary w-100">
                                <i class="fas fa-cog me-2"></i>Configurar Método de Pago
                            </a>
                        @else
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Perfecto:</strong> Tiene un método de pago válido configurado. 
                                Su dominio será renovado automáticamente.
                            </div>
                            
                            <a href="{{ route('dominio.pago.formulario', $token) }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-edit me-2"></i>Modificar Método de Pago
                            </a>
                        @endif

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                <small>
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Sus datos están protegidos y encriptados
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
