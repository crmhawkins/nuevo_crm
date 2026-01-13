<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Configurar Método de Pago - {{ config('app.name', 'Laravel') }}</title>

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
        .payment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .payment-body {
            padding: 40px;
        }
        .method-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .method-option:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }
        .method-option.active {
            border-color: #667eea;
            background-color: #f0f4ff;
        }
        .iban-input {
            font-size: 18px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .alert-box {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="payment-card">
                    <div class="payment-header">
                        <h2 class="mb-2"><i class="fas fa-credit-card me-2"></i>Configurar Método de Pago</h2>
                        <p class="mb-0">Dominio: <strong>{{ $dominio->dominio }}</strong></p>
                        <p class="mb-0">Caduca el: <strong>{{ $dominio->getFechaCaducidad() ? $dominio->getFechaCaducidad()->format('d/m/Y') : 'N/A' }}</strong></p>
                    </div>
                    
                    <div class="payment-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-box">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success alert-box">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                        @endif

                        <div class="alert alert-info alert-box">
                            <i class="fas fa-info-circle me-2"></i>
                            Para asegurar la continuidad de su servicio, configure un método de pago válido.
                        </div>

                        <!-- Opción IBAN -->
                        <div class="method-option" id="iban-option">
                            <h5><i class="fas fa-university me-2"></i>Domiciliación SEPA (IBAN)</h5>
                            <p class="text-muted mb-3">Configure su cuenta bancaria para pagos automáticos</p>
                            
                            <form method="POST" action="{{ route('dominio.pago.iban', $token) }}" id="iban-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="iban" class="form-label">IBAN</label>
                                    <input type="text" 
                                           class="form-control iban-input @error('iban') is-invalid @enderror" 
                                           id="iban" 
                                           name="iban" 
                                           placeholder="ES12 3456 7890 1234 5678 9012"
                                           maxlength="34"
                                           value="{{ old('iban', $dominio->iban) }}"
                                           required>
                                    <small class="form-text text-muted">Formato: ES00 0000 0000 0000 0000 0000</small>
                                    @error('iban')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save me-2"></i>Guardar IBAN
                                </button>
                            </form>
                        </div>

                        <!-- Opción Stripe -->
                        <div class="method-option" id="stripe-option">
                            <h5><i class="fab fa-cc-stripe me-2"></i>Tarjeta de Crédito (Stripe)</h5>
                            <p class="text-muted mb-3">Configure su tarjeta de crédito para pagos automáticos recurrentes</p>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('dominio.pago.checkout', $token) }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card me-2"></i>Pagar con Tarjeta
                                </a>
                                <small class="text-muted text-center">
                                    Será redirigido a la pasarela segura de Stripe para completar el pago
                                </small>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <a href="{{ route('dominio.pago.confirmacion', $token) }}" class="text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Ver estado del método de pago
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Formatear IBAN automáticamente
        document.getElementById('iban').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').toUpperCase();
            if (value.length > 34) value = value.substring(0, 34);
            e.target.value = value.match(/.{1,4}/g)?.join(' ') || value;
        });
    </script>
</body>
</html>
