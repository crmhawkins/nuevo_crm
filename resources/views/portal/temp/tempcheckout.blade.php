@extends('layouts.tempappPortal')

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
                <h4 class="text-primary">Precio: {{ number_format($price - ($price - $iva), 2, ',', '.') }} €</h4>
                <h5 class="small">{{ number_format($iva, 2, ',', '.') }} € + {{ number_format($price - $iva, 2, ',', '.') }} (IVA) | {{ number_format($price, 2, ',', '.') }} € </h5>
            </div>

            <form action="{{ route('portal.processPayment') }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="purchase_type" value="{{ old('purchase_type', $type) }}">
                <input type="hidden" name="purchase_id" value="{{ old('purchase_id', $purchase_id) }}">

                <div class="mb-3">
                    <label class="form-label text-dark">Nombre completo / Nombre de empresa</label>
                    <input type="text" class="form-control" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark">Teléfono</label>
                    <input type="text" class="form-control" name="phone" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark">Correo electrónico</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Dirección fiscal</label>
                    <input type="text" class="form-control mb-3" name="country" placeholder="País " required>
                    <input type="text" class="form-control mb-3" name="province" placeholder="Provincia " required>
                    <input type="text" class="form-control mb-3" name="city" placeholder="Ciudad " required>
                    <input type="text" class="form-control mb-3" name="address" placeholder="Dirección " required>
                    <input type="text" class="form-control mb-3" name="zipcode" placeholder="Código postal " required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">NIF/CIF</label>
                    <input type="text" class="form-control" name="nif"  required>
                </div>

                <div class="mb-4">
                    <label for="card-element" class="form-label text-dark">Tarjeta de crédito</label>
                    <div class="form-control" id="card-element"></div>
                    <div id="card-errors" role="alert" class="text-danger"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-dark">Código de descuento</label>
                    <input type="text" class="form-control" name="coupon" placeholder="Escribe tu cupón de descuento si tienes uno">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-success btn-lg">Pagar</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
