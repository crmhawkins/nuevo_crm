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

<!-- 🖥️ Vista Desktop -->
<div class="container mt-5 desktop-structure-selector">
    <div class="card shadow-lg p-4 bg-white rounded">
        <div class="position-relative mb-3">
            <h1 class="text-center text-dark display-5 mb-0">Seleccione la estructura de su página</h1>
        </div>
        <h4 class="text-center text-muted fs-4">Elige el diseño que mejor se adapte a tu negocio</h4>
        <hr>
        <div class="card-body">
            <input type="search" class="form-control rounded" placeholder="Filtrar por categorías" aria-label="Search" />
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
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Web 7', 'category' => 'proximamente', 'url' => 'https://www.maquetacionweb07.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Web 8', 'category' => 'proximamente', 'url' => 'https://www.maquetacionweb08.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Web 9', 'category' => 'proximamente', 'url' => 'https://www.maquetacionweb09.hawkins.es'],
                            ];
                        } elseif ($type === 'eccommerce') {
                            $estructuras = [
                                ['src' => '/assets/images/plantillas/ecommerce1.png', 'name' => 'Ecommerce 1', 'category' => 'corporativa', 'url' => 'https://www.maquetacion1.hawkins.es'],
                                ['src' => '/assets/images/plantillas/ecommerce2.png', 'name' => 'Ecommerce 2', 'category' => 'personal', 'url' => 'https://www.maquetacion2.hawkins.es'],
                                ['src' => '/assets/images/plantillas/ecommerce3.png', 'name' => 'Ecommerce 3', 'category' => 'portfolio', 'url' => 'https://www.maquetacion3.hawkins.es'],
                                ['src' => '/assets/images/plantillas/ecommerce4.png', 'name' => 'Ecommerce 4', 'category' => 'tienda', 'url' => 'https://www.maquetacion4.hawkins.es'],
                                ['src' => '/assets/images/plantillas/ecommerce5.png', 'name' => 'Ecommerce 5', 'category' => 'corporativa', 'url' => 'https://www.maquetacion5.hawkins.es'],
                                ['src' => '/assets/images/plantillas/ecommerce6.png', 'name' => 'Ecommerce 6', 'category' => 'corporativa', 'url' => 'https://www.maquetacion6.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Ecommerce 7', 'category' => 'proximamente', 'url' => 'https://www.maquetacion7.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Ecommerce 8', 'category' => 'proximamente', 'url' => 'https://www.maquetacion8.hawkins.es'],
                                ['src' => '/assets/images/logo/logo.png', 'name' => 'Ecommerce 9', 'category' => 'proximamente', 'url' => 'https://www.maquetacion9.hawkins.es'],
                            ];
                        }
                    @endphp

                    @foreach ($estructuras as $index => $estructura)
                        @php
                            $uniqueId = 'struct_' . $index;
                            $isComingSoon = $estructura['category'] === 'proximamente';
                        @endphp
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 plantilla-card" data-category="{{ $estructura['category'] }}">
                            <div class="card h-100 shadow-sm structure-card text-center" id="{{ $uniqueId }}">
                                <div class="card-img-container">
                                    <img src="{{ $estructura['src'] }}" alt="{{ $estructura['name'] }}" class="card-img-top structure-option">
                                </div>
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <h5 class="card-title mb-3">{{ $estructura['name'] }}</h5>
                                    @if(!$isComingSoon)
                                        <div class="d-flex flex-column flex-md-row justify-content-center gap-2">
                                            <a target="_blank" href="{{ $estructura['url'] }}" class="btn btn-outline-secondary w-100">Previsualizar</a>
                                            <button type="button" class="btn btn-primary w-100" onclick="selectstructure('{{ $uniqueId }}', '{{ $estructura['name'] }}', 'web')">Seleccionar</button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-secondary w-100" disabled>Próximamente</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="structure" id="selected_structure" required>
                <input type="hidden" name="type" value={{ $type }}>
            </form>
        </div>
    </div>
</div>

<!-- Vista móvil -->
<div class="mobile-structure-selector d-none">
    <div id="mobile-structure-list"></div>
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
/* Estilos para corregir el centrado completo en móviles */

@media (max-width: 767.98px) {
    html, body {
        padding: 0 !important;
        margin: 0 !important;
        width: 100vw !important;
        overflow-x: hidden !important;
    }

    #appPortal, .mobile-structure-selector, .mobile-structure-item {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    .mobile-structure-item img {
        width: 100% !important;
        max-width: 100% !important;
    }

    .mobile-structure-item .btn,
    .mobile-structure-item h4 {
        padding-left: 1rem;
        padding-right: 1rem;
    }

    /* Evitar que el layout general tenga padding inesperado */
    .fondoPortal,
    .contenedor,
    main#mainPortal {
        padding: 0 !important;
        margin: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    /* Botón del menú hamburguesa */
    #toggle-sidebar {
        z-index: 10001;
    }
}
</style>


<script>
function selectstructure(id, name, type) {
    document.getElementById('selected_structure').value = name;
    document.querySelector('form').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const isMobile = window.innerWidth <= 767;
    if (!isMobile) return;

    const list = document.getElementById('mobile-structure-list');
    const estructuras = @json($estructuras);

    estructuras.forEach((estructura, index) => {
        const isComingSoon = estructura.category === 'proximamente';
        const div = document.createElement('div');
        div.className = 'mobile-structure-item';

        div.innerHTML = `
            <img src="${estructura.src}" alt="${estructura.name}">
            <h4>${estructura.name}</h4>
            ${!isComingSoon ? `<a href="${estructura.url}" target="_blank" class="btn btn-secondary">Previsualizar</a>` : ''}
            <button class="btn btn-primary" ${isComingSoon ? 'disabled' : ''} onclick="selectstructure('struct_${index}', '${estructura.name}', 'web')">
                ${isComingSoon ? 'Próximamente' : 'Seleccionar'}
            </button>
        `;
        list.appendChild(div);
    });
});
</script>
@endsection

@section('scripts')
@include('partials.toast')
@endsection
