@extends('layouts.appPortal')
@section('content')
@include('layouts.header')
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

    @if ($errors->any())
    <div class="alert alert-danger">
        <h4>Se han producido algunos errores:</h4>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<span style="margin-top: 1vw; margin-right: 1vw;" class="position-absolute top-0 end-0 text-dark fw-bold fs-3">1/4</span>

<div class="container mt-5">
    <div class="card shadow-lg p-4 bg-white rounded">
        <div class="position-relative mb-3">
            <h1 class="text-center text-dark display-5 mb-0">Seleccione la estructura de su página</h1>
        </div>
               
        <h4 class="text-center text-muted fs-4">Elige el diseño que mejor se adapte a tu negocio</h4>
        <hr>
        <div class="card-body">
            <input type="search" class="form-control rounded" placeholder="Filtrar por categorías" aria-label="Search" aria-describedby="search-addon" />                    
            <br>
            <form action="{{ route('portal.generateForm') }}" method="POST">
                @csrf
                <div class="row justify-content-center">
                    @php
                        $estructuras = [];

                        if ($type === 'web') {
                            $estructuras = [
                                ['src' => '/assets/images/plantillas/web1.png', 'name' => 'Web 1', 'category' => 'corporativa', 'url' => 'https://www.maquetacionweb1.hawkins.es'],
                                ['src' => '/assets/images/plantillas/web2.png', 'name' => 'Web 2', 'category' => 'personal', 'url' => 'https://www.maquetacionweb02.hawkins.es'],
                                ['src' => '/assets/images/plantillas/web3.png', 'name' => 'Web 3', 'category' => 'portfolio', 'url' => 'https://www.maquetacionweb03.hawkins.es'],
                                ['src' => '/assets/images/plantillas/web4.png', 'name' => 'Web 4', 'category' => 'tienda', 'url' => 'https://www.maquetacionweb04.hawkins.es'],
                                ['src' => '/assets/images/plantillas/web5.png', 'name' => 'Web 5', 'category' => 'corporativa', 'url' => 'https://www.maquetacionweb05.hawkins.es'],
                                ['src' => '/assets/images/plantillas/web6.png', 'name' => 'Web 6', 'category' => 'corporativa', 'url' => 'https://www.maquetacionweb06.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                            ];
                        } elseif ($type === 'eccommerce') {
                            $estructuras = [
                                ['src' => '/assets/images/plantillas/ecommerce1.png', 'name' => 'Ecommerce 1', 'category' => 'corporativa', 'url' => ''],
                                ['src' => '/assets/images/plantillas/ecommerce2.png', 'name' => 'Ecommerce 2', 'category' => 'personal', 'url' => ''],
                                ['src' => '/assets/images/plantillas/ecommerce3.png', 'name' => 'Ecommerce 3', 'category' => 'portfolio', 'url' => ''],
                                ['src' => '/assets/images/plantillas/ecommerce4.png', 'name' => 'Ecommerce 4', 'category' => 'tienda', 'url' => ''],
                                ['src' => '/assets/images/plantillas/ecommerce5.png', 'name' => 'Ecommerce 5', 'category' => 'corporativa', 'url' => ''],
                                ['src' => '/assets/images/plantillas/ecommerce6.png', 'name' => 'Ecommerce 6', 'category' => 'corporativa', 'url' => ''],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Proximamente', 'category' => 'proximamente', 'url' => ''],
                            ];
                        }
                    @endphp

                    @foreach ($estructuras as $index => $estructura)
                    @php
                        $uniqueId = 'struct_' . $index;
                        $isComingSoon = $estructura['category'] === 'proximamente';
                    @endphp
                    <div class="col-md-4 mb-4 plantilla-card" data-category="{{ $estructura['category'] }}">
                        <div class="card h-100 shadow-sm text-center structure-card" id="{{ $uniqueId }}">
                            <div class="card-img-container">
                                <img src="{{ $estructura['src'] }}" alt="{{ $estructura['name'] }}" class="card-img-top structure-option">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $estructura['name'] }}</h5>
                                <!-- Eliminamos el botón de previsualización si es 'Proximamente' -->
                                @if(!$isComingSoon)
                                    <a target="_blank" href="{{ $estructura['url'] }}" type="button" class="btn btn-outline-secondary me-2">Previsualizar</a>
                                @endif
                                <!-- Eliminamos el botón de 'Seleccionar' si es 'Proximamente', pero si es web, redirigimos automáticamente -->
                                <button type="button" class="btn btn-primary" 
                                        onclick="selectstructure('{{ $uniqueId }}', '{{ $estructura['name'] }}', '{{ $type }}')" 
                                        @if($isComingSoon) disabled @endif>Seleccionar</button>
                            </div>
                        </div>
                    </div>
                @endforeach


                </div>

                <input type="hidden" name="structure" id="selected_structure" required>
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="text-center mt-4">
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de previsualización -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img src="" id="previewImage" class="img-fluid w-100">
            </div>
        </div>
    </div>
</div>

<style>
    .structure-option {
        transition: all ease-in-out 0.5s;
        width: 100%;
        height: auto;
        cursor: pointer;
        display: block;
        border: none !important;
        border-radius: 0;
    }

    .structure-card {
        transition: all ease-in-out 0.5s;
        cursor: pointer;
        transition: background-color 0.3s, box-shadow 0.3s;
        border: 1px solid rgba(12, 12, 12, 0.3);
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .structure-card:hover {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .structure-card.selected .card-img-container,
    .structure-card.selected .card-body {
        transition: all ease-in-out 0.5s;
        border-radius: 0 !important;
        background-color: rgba(0, 0, 0, 0.22) !important;
    }

    .card-img-container {
        transition: all ease-in-out 0.5s;
        background-color: transparent;
    }
</style>

<script>
    function selectstructure(id, name, type) {
    // Desmarcar todas las tarjetas seleccionadas
    document.querySelectorAll('.structure-card').forEach(card => {
        card.classList.remove('selected');
    });

    // Marcar la tarjeta seleccionada
    const selectedCard = document.getElementById(id);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }

    // Guardar el nombre de la estructura seleccionada
    document.getElementById('selected_structure').value = name;

    // Redirigir automáticamente si el tipo es 'web' o 'ecommerce'
    if (type === 'web' || type === 'eccommerce') {
        document.querySelector('form').submit();  // Enviar el formulario para pasar a la siguiente página
    }
}



    function previewstructure(src) {
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        document.getElementById('previewImage').src = src;
        modal.show();
    }
</script>

<script>
    document.querySelector('input[type="search"]').addEventListener('input', function () {
        const filtro = this.value.toLowerCase();

        document.querySelectorAll('.plantilla-card').forEach(card => {
            const categoria = card.getAttribute('data-category').toLowerCase();
            if (categoria.includes(filtro) || filtro === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

@endsection

@section('scripts')
@include('partials.toast')
@endsection
