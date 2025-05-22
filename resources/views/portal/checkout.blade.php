@extends('layouts.appPortal')

@section('content')
@include('layouts.header')

@php
    $cliente = $cliente ?? session('cliente');
    $type = $type ?? session('type');
    $price = $price ?? session('price');
    $purchase_type = $purchase_type ?? session('purchase_type');
    $iva = $iva ?? session('iva');
    $purchase_id = $purchase_id ?? session('purchase_id');
@endphp

<span style="margin-top: 1vw; margin-right: 1vw;" class="position-absolute top-0 end-0 text-dark fw-bold fs-3">4/4</span>

<div class="container mt-5">
    @if (session('success_message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success_message') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error_message'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error_message') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-lg p-4 bg-white rounded">
        <div class="position-relative mb-3">
            <h2 class="text-center text-dark display-5 mb-0">Datos fiscales</h2>
        </div>
        <hr>
        <div class="card-body">
            <h4 class="text-dark">Estás comprando:</h4>
            <p><strong>{{ ucfirst($type) }} personalizada.</strong></p>
            <p>Descripción: {{ ucfirst($type) }} personalizada a tu medida.</p>

            <div class="mb-4">
                <h4 class="text-primary">Total: {{ number_format($price, 2, ',', '.') }} €</h4>
                <h5 class="small">{{ number_format($iva, 2, ',', '.') }} € + {{ number_format($price - $iva, 2, ',', '.') }} € (IVA)</h5>
            </div>

            <form action="{{ route('portal.processPayment') }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="purchase_type" value="{{ old('purchase_type', $type) }}">
                <input type="hidden" name="purchase_id" value="{{ old('purchase_id', $purchase_id) }}">

                <div class="mb-3">
                    <label class="form-label text-dark">Nombre completo / Nombre de empresa</label>
                    <input type="text" class="form-control" name="full_name" value="{{ old('full_name', $cliente->name . ' ' . $cliente->primerApellido . ' ' . $cliente->segundoApellido) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Teléfono</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $cliente->phone) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Correo electrónico</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $cliente->email) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Dirección fiscal</label>
                    <input type="text" class="form-control mb-3" name="country" value="{{ old('country', $cliente->country) }}" placeholder="País " required>
                    <input type="text" class="form-control mb-3" name="province" value="{{ old('province', $cliente->province) }}" placeholder="Provincia " required>
                    <input type="text" class="form-control mb-3" name="city" value="{{ old('city', $cliente->city) }}" placeholder="Ciudad " required>
                    <input type="text" class="form-control mb-3" name="address" value="{{ old('address', $cliente->address) }}" placeholder="Dirección " required>
                    <input type="text" class="form-control mb-3" name="zipcode" value="{{ old('zipcode', $cliente->zipcode) }}" placeholder="Código postal " required>
                </div>


                <div class="mb-3">
                    <label class="form-label text-dark">NIF/CIF</label>
                    <input type="text" class="form-control" name="nif" value="{{ old('nif', $cliente->nif) }}" required>
                </div>

                <div class="mb-4">
                    <label for="card-element" class="form-label text-dark">Tarjeta de crédito</label>
                    <div class="form-control" id="card-element"></div>
                    <div id="card-errors" role="alert" class="text-danger"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Código de descuento</label>
                    <input type="text" class="form-control" name="coupon" value="{{ old('coupon') }}" placeholder="Escribe tu cupón si tienes uno">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">Pagar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    html, body {
        padding: 0 !important;
        margin: 0 !important;
        width: 100vw !important;
        overflow-x: hidden !important;
        background-color: #f5f7fb !important;
    }

    main,
    .fondoPortal,
    .contenedor,
    .portal-sidebar,
    .wrapper,
    #appPortal,
    .main-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    .container,
    .container-fluid {
        padding: 0 !important;
        margin: 0 auto !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    .card {
        width: 100vw !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 2rem 1rem !important;
    }

    .card-body {
        padding: 0 !important;
    }

    .form-label {
        font-size: 1rem;
        margin-bottom: 0.3rem;
    }

    .form-control,
    input.form-control,
    select.form-control,
    textarea.form-control,
    #card-element {
        width: 100% !important;
        max-width: 100% !important;
        font-size: 1rem;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }

    #card-element {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        height: 44px;
        padding: 0.5rem;
        background: #fff;
    }

    #card-errors {
        margin-top: 0.5rem;
        font-size: 0.95rem;
    }

    .btn-lg {
        width: 100% !important;
        font-size: 1.1rem;
        padding: 0.75rem;
    }

    h2.display-5 {
        font-size: 1.6rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    h4, h5 {
        text-align: left;
        font-size: 1.1rem;
    }

    hr {
        margin: 1.5rem 0;
    }

    .alert {
        margin: 1rem;
        font-size: 0.95rem;
    }

    .mb-3, .mb-4 {
        margin-bottom: 1.5rem !important;
    }
}
</style>

@endsection

@section('scripts')
@include('partials.toast')

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();
    var card = elements.create('card');
    card.mount('#card-element');

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        const {token, error} = await stripe.createToken(card);

        if (error) {
            var errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;
        } else {
            var hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'stripeToken');
            hiddenInput.setAttribute('value', token.id);
            form.appendChild(hiddenInput);
            form.submit();
        }
    });
</script>
@endsection
