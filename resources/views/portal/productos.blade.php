@extends('layouts.appPortal')

@section('content')
@include('layouts.header')

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
            <h2 class="text-center text-dark display-5 mb-0">Productos disponibles</h2>
        </div>
        <hr>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="image-container text-center p-3">
                        <h4>B2B Sitio web</h4>
                        <a href="/portal/estructura/web">
                            <img src="/assets/images/plantillas/web1.png" alt="B2B Sitio web" class="img-fluid clickable-image">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="image-container text-center p-3">
                        <h4>B2B Ecommerce</h4>
                        <a href="/portal/estructura/eccommerce">
                            <img src="/assets/images/plantillas/ecommerce2.png" alt="B2B Ecommerce" class="img-fluid clickable-image">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="image-container text-center p-3">
                        <h4>Cambios En Web</h4>
                        <a href="/portal/estructura/eccommerce">
                            <img src="/assets/images/plantillas/ecommerce2.png" alt="Cambios En Web" class="img-fluid clickable-image">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('partials.toast')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
