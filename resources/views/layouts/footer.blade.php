<footer class="bg-light py-4 mt-5 border-top">
    <div class="container text-center">

        {{-- Logo de la empresa --}}
        <div class="mb-4">
            <img src="{{ asset('/assets/images/logo/logo.png') }}" alt="Logo Empresa" class="img-fluid" style="max-height: 60px;">
        </div>

        {{-- Métodos de pago --}}
        <div class="mb-3 d-flex justify-content-center align-items-center gap-4 flex-nowrap">
            <img src="{{ asset('/assets/icons/visa.png') }}" alt="Visa" class="img-fluid" style="height: 35px;">
            <img src="{{ asset('/assets/icons/mastercard.png') }}" alt="Mastercard" class="img-fluid" style="height: 35px;">

        {{-- Iconos de confianza y seguridad --}}
            <img src="{{ asset('/assets/icons/iso.png') }}" alt="SSL Seguro" class="img-fluid" style="height: 35px;">
            <img src="{{ asset('/assets/icons/premios.svg') }}" alt="Opiniones Verificadas" class="img-fluid" style="height: 35px;">
        </div>

        {{-- Texto final --}}
        <p class="text-muted mt-3 mb-0 small">
            &copy; {{ date('Y') }} Hawkins. Todos los derechos reservados. Pagos seguros y protegidos.
        </p>

    </div>
</footer>
