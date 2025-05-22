@extends('layouts.tempappPortal')
@extends('layouts.header')

@section('content')
<div class="content">
  @if (session('success_message'))
  <div class="alert alert-success">
      {!! session('success_message') !!}
  </div>
  @endif

  @if (session('error_message'))
  <div class="alert alert-danger">
      {!! session('error_message') !!}
  </div>
  @endif

<!-- Modal -->
<h1 class="text-center h1">Bienvenido, ¿que desea hacer?</h1>
<div id="b2bModal" tabindex="-1" aria-labelledby="b2bModalLabel">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 90%; width: 90%;">
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body text-center">
          <div class="row justify-content-center">
            <div class="col">
              <div class="image-container">
                <h4 class="">B2B Sitio web</h4>
                <br>
                <a href="/portal/estructura/web">
                  <img src="/assets/images/plantillas/web1.png" alt="B2B Sitio web" class="img-fluid clickable-image">
                </a>
              </div>
            </div>
            <div class="col">
              <div class="image-container">
                <h4 class="">B2B Ecommerce</h4>
                <br>
                <a href="/portal/estructura/eccommerce">
                  <img src="/assets/images/plantillas/ecommerce2.png" alt="B2B Ecommerce" class="img-fluid clickable-image">
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


@endsection

@section('scripts')
@include('partials.toast')

<style>/* Contenedor para las imágenes */
.image-container {
  position: relative;
  display: inline-block;
  margin: 10px;
}

/* Texto sobre la imagen */
.image-text {
  position: absolute;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);
  color: white;
  font-size: 18px;
  font-weight: bold;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
}

/* Estilo para las imágenes, para hacerlas más pequeñas y clicables */
.clickable-image {
  cursor: pointer;
  width: 100%; /* Asegura que la imagen ocupe el 100% del ancho del contenedor */
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

/* Efecto hover para las imágenes */
.clickable-image:hover {
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
}
</style>
@endsection
