<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .stripe-element {
            padding: 12px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            background: white;
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
                                           class="form-control iban-input" 
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
    <script src="https://js.stripe.com/v3/"></script>
    
    <script>
        // Configurar Stripe
        const stripe = Stripe('{{ config('services.stripe.key') }}');
        const elements = stripe.elements();
        
        // Obtener precio del dominio (en centavos)
        const precioVenta = {{ ($dominio->precio_venta ?? 0) * 100 }};
        const precioConIva = Math.round(precioVenta * 1.21);
        
        // Asegurar que el precio sea al menos 50 céntimos (Stripe requiere mínimo para Payment Request)
        const precioFinal = precioConIva >= 50 ? precioConIva : 5000; // 50€ mínimo si no hay precio configurado
        
        console.log('=== Configuración Payment Request ===');
        console.log('Precio venta (céntimos):', precioVenta);
        console.log('Precio con IVA (céntimos):', precioConIva);
        console.log('Precio final usado (céntimos):', precioFinal);
        console.log('Dominio:', '{{ $dominio->dominio }}');
        console.log('Stripe Key:', '{{ config('services.stripe.key') }}'.substring(0, 20) + '...');
        
        // Crear Payment Request para Apple Pay / Google Pay
        let paymentRequest = null;
        let paymentRequestButton = null;
        
        // Inicializar Payment Request
        try {
            paymentRequest = stripe.paymentRequest({
                country: 'ES',
                currency: 'eur',
                total: {
                    label: 'Renovación de dominio {{ $dominio->dominio }}',
                    amount: precioFinal,
                },
                requestPayerName: true,
                requestPayerEmail: true,
            });
            
            console.log('Payment Request creado correctamente');
            
            // Verificar si el navegador soporta Payment Request ANTES de crear el botón
            paymentRequest.canMakePayment().then(function(result) {
                console.log('=== Resultado canMakePayment ===');
                console.log('Resultado completo:', result);
                console.log('Protocolo:', window.location.protocol);
                console.log('Es HTTPS:', window.location.protocol === 'https:');
                console.log('User Agent:', navigator.userAgent);
                
                if (result) {
                    console.log('✅ Payment Request DISPONIBLE');
                    console.log('Apple Pay disponible:', result.applePay);
                    console.log('Google Pay disponible:', result.googlePay);
                    
                    // Crear botón de Payment Request solo si está disponible
                    try {
                        paymentRequestButton = elements.create('paymentRequestButton', {
                            paymentRequest: paymentRequest,
                            style: {
                                paymentRequestButton: {
                                    theme: 'dark',
                                    height: '48px',
                                    type: 'default',
                                },
                            },
                        });
                        
                        paymentRequestButton.mount('#payment-request-button');
                        console.log('✅ Botón de Payment Request montado correctamente');
                        
                        // Ocultar mensaje informativo si el botón se montó
                        const infoDiv = document.getElementById('payment-request-info');
                        if (infoDiv) {
                            infoDiv.style.display = 'none';
                        }
                    } catch (mountError) {
                        console.error('❌ Error al montar el botón:', mountError);
                        console.error('Stack:', mountError.stack);
                    }
                } else {
                    console.warn('❌ Payment Request NO disponible');
                    console.warn('Razones posibles:');
                    console.warn('1. No hay tarjetas configuradas en Apple Pay/Google Pay');
                    console.warn('2. Navegador no soporta Payment Request API');
                    console.warn('3. Dominio no verificado en Stripe (solo en producción)');
                    
                    // Mostrar mensaje informativo
                    const infoDiv = document.getElementById('payment-request-info');
                    const messageSpan = document.getElementById('payment-request-message');
                    if (infoDiv && messageSpan) {
                        if (window.location.protocol !== 'https:') {
                            messageSpan.textContent = 'Apple Pay y Google Pay requieren conexión HTTPS. Por favor, use el formulario de tarjeta tradicional.';
                        } else {
                            messageSpan.textContent = 'Apple Pay/Google Pay no está disponible. Verifique que tenga tarjetas configuradas en Apple Wallet o Google Pay.';
                        }
                        infoDiv.style.display = 'block';
                    }
                    
                    // Ocultar el contenedor si no está disponible
                    const paymentRequestContainer = document.getElementById('payment-request-button');
                    const separator = document.querySelector('#stripe-option .text-center');
                    if (paymentRequestContainer) {
                        paymentRequestContainer.style.display = 'none';
                    }
                    if (separator) {
                        separator.style.display = 'none';
                    }
                }
            }).catch(function(error) {
                console.error('❌ Error al verificar Payment Request:', error);
                console.error('Error completo:', error);
                console.error('Stack:', error.stack);
                
                const paymentRequestContainer = document.getElementById('payment-request-button');
                const separator = document.querySelector('#stripe-option .text-center');
                if (paymentRequestContainer) {
                    paymentRequestContainer.style.display = 'none';
                }
                if (separator) {
                    separator.style.display = 'none';
                }
            });
            
            // Manejar el evento de pago con Payment Request
            paymentRequest.on('paymentmethod', async function(ev) {
                try {
                    // Crear SetupIntent
                    const response = await fetch('{{ route('dominio.pago.setup-intent', $token) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const { client_secret } = await response.json();
                    
                    // Confirmar el método de pago
                    const { error: confirmError } = await stripe.confirmCardSetup(
                        client_secret,
                        { payment_method: ev.paymentMethod.id },
                        { handleActions: false }
                    );
                    
                    if (confirmError) {
                        ev.complete('fail');
                        document.getElementById('card-errors').textContent = confirmError.message;
                    } else {
                        ev.complete('success');
                        
                        // Guardar el método de pago
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('dominio.pago.stripe', $token) }}';
                        
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(csrfInput);
                        
                        const paymentMethodInput = document.createElement('input');
                        paymentMethodInput.type = 'hidden';
                        paymentMethodInput.name = 'payment_method_id';
                        paymentMethodInput.value = ev.paymentMethod.id;
                        form.appendChild(paymentMethodInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }
                } catch (error) {
                    ev.complete('fail');
                    document.getElementById('card-errors').textContent = 'Error al procesar el pago. Por favor, intente de nuevo.';
                }
            });
        } catch (error) {
            console.error('Error al inicializar Payment Request:', error);
            document.getElementById('payment-request-button').style.display = 'none';
            if (document.querySelector('#stripe-option .text-center')) {
                document.querySelector('#stripe-option .text-center').style.display = 'none';
            }
        }
        
        // Crear elemento de tarjeta
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#424770',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#9e2146',
                },
            },
        });
        
        cardElement.mount('#card-element');
        
        // Manejar errores de la tarjeta
        cardElement.on('change', function(event) {
            const displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
        
        // Manejar envío del formulario Stripe
        const stripeForm = document.getElementById('stripe-form');
        stripeForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const submitButton = document.getElementById('stripe-submit');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
            
            try {
                // Crear SetupIntent
                const response = await fetch('{{ route('dominio.pago.setup-intent', $token) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const { client_secret } = await response.json();
                
                // Confirmar SetupIntent
                const { error, setupIntent } = await stripe.confirmCardSetup(client_secret, {
                    payment_method: {
                        card: cardElement,
                    }
                });
                
                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>Guardar Tarjeta';
                } else {
                    // Añadir payment_method_id al formulario y enviar
                    const input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', 'payment_method_id');
                    input.setAttribute('value', setupIntent.payment_method);
                    stripeForm.appendChild(input);
                    stripeForm.submit();
                }
            } catch (error) {
                document.getElementById('card-errors').textContent = 'Error al procesar el pago. Por favor, intente de nuevo.';
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>Guardar Tarjeta';
            }
        });
        
        // Formatear IBAN
        document.getElementById('iban').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').toUpperCase();
            if (value.length > 34) value = value.substring(0, 34);
            e.target.value = value.match(/.{1,4}/g)?.join(' ') || value;
        });
    </script>
</body>
</html>
