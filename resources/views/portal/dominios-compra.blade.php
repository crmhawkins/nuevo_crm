@extends('layouts.appPortal')
@section('content')
@include('layouts.header')
<span style="margin-top: 1vw; margin-right: 1vw;" class="position-absolute top-0 end-0 text-dark fw-bold fs-3">3/4</span>

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
            <h1 class="text-center text-dark display-5 mb-0">Datos de dominio y hosting.</h1>
        </div>
        <hr>
        <div class="card-body">
            <form id="formulario" action="{{ route('portal.dominiosStore') }}" method="POST" enctype="multipart/form-data">
            @csrf

                <!-- Parte 1: Pregunta sobre dominio -->
                <div class="mb-3">
                    <label class="form-label">
                        ¿Quiere contratar un dominio?
                    </label>
                    <div class="tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Un dominio es la dirección de tu sitio web, por ejemplo, www.misitio.com.">
                        <span class="tooltip-link">¿Qué es?</span>
                    </div>
                    <select name="dominio" class="form-control" id="dominio_select" required>
                        <option value="" disabled>Seleccione una opción</option>
                        <option selected="selected" value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                    <div id="dominio_input_wrapper" class="mt-3" style="display: none;">
                        <label class="form-label">Nombre del dominio</label>
                        <input type="text" name="nombre_dominio" id="nombre_dominio" class="form-control" placeholder="Ingrese el nombre del dominio">
                    </div>
                    <div id="dominio_externo_wrapper" class="mt-3" style="display: none;">
                        <label class="form-label">Indique un dominio para la justificación.</label>
                        <input type="text" name="dominio_externo" id="dominio_externo" class="form-control" placeholder="Ingrese el dominio externo que desea usar">
                    </div>
                </div>

                <hr>

                <!-- Parte 2: Pregunta sobre hosting -->
                <div class="mb-3">
                    <label class="form-label">
                        ¿Quiere contratar hosting?
                    </label>
                    <div class="tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="El hosting es el servicio que te permite almacenar tu sitio web y hacerlo accesible en internet.">
                        <span class="tooltip-link">¿Qué es hosting?</span>
                    </div>
                    <select name="hosting" class="form-control" id="hosting_select" required>
                        <option value="" disabled>Seleccione una opción</option>
                        <option selected="selected" value="si">Sí</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <hr>

                <!-- Parte 3: Subir archivo -->
                <div class="mb-3">
                    <label class="form-label">Adjunte las imagenes que desea usar en su web.</label>
                    <h6 class="d-block mb-2">(Si no adjunta nada, usaremos nuestro banco de imagenes premium)</h6>
                    <input type="file" name="archivo[]" class="form-control" accept=".jpg, .png, .jpeg, .zip, .rar, .pdf" multiple>
                </div>

                <input type="hidden" name="purchase_id" value="{{ $purchase_id }}">
                <button type="submit" class="btn btn-success btn-lg">Siguiente</button>
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
        max-width: 100vw !important;
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
        left: 0 !important;
        position: static !important;
    }

    .container,
    .container-fluid,
    .card,
    .card-body,
    .row,
    .col-sm-12 {
        padding: 0 !important;
        margin: 0 auto !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    .card {
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 2rem 1rem !important;
    }

    .form-label {
        font-size: 1rem;
        margin-bottom: 0.3rem;
    }

    .form-control,
    select.form-control,
    input.form-control,
    textarea.form-control {
        width: 100% !important;
        max-width: 100% !important;
        font-size: 1rem;
        padding: 0.75rem;
        margin-bottom: 1rem;
    }

    .btn-lg {
        width: 100% !important;
        font-size: 1.1rem;
        padding: 0.75rem;
    }

    .alert {
        margin: 1rem;
        font-size: 0.95rem;
    }

    h1.display-5 {
        font-size: 1.6rem;
        text-align: center;
        margin-bottom: 1rem;
    }

    hr {
        margin: 1.5rem 0;
    }

    .mb-3 {
        margin-bottom: 1.5rem !important;
    }

    /* Oculta el sidebar si sigue visible en móviles */
    .portal-sidebar {
        display: none !important;
    }
}
@media (max-width: 768px) {
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        overflow-x: hidden !important;
        background-color: #f5f7fb !important;
    }

    /* Fuerza contenedores principales a 100% de ancho */
    .container,
    .container-fluid,
    .row,
    .col-sm-12,
    .card,
    .card-body {
        margin: 0 !important;
        padding: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
    }

    /* Soluciona el padding horizontal de Bootstrap en .container */
    .container {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Asegura que el formulario y sus campos usen todo el ancho */
    form,
    .form-control,
    .btn-lg,
    select.form-control,
    input.form-control,
    textarea.form-control {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .card {
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 2rem 1rem !important;
    }

    .alert {
        margin: 1rem;
    }

    .portal-sidebar {
        display: none !important;
    }
}

</style>

@endsection

@section('scripts')
@include('partials.toast')

<style>
    .tooltip-link {
        font-size: 0.85rem;
        color: #0d6efd;
        text-decoration: underline;
        cursor: pointer;
    }
    
    .tooltip-container {
        display: inline-block;
        cursor: pointer;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Inicializar tooltips de Bootstrap
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover',
                delay: { "show": 100, "hide": 100 }
            });
        });

        const dominioSelect = document.getElementById('dominio_select');
        const dominioInputWrapper = document.getElementById('dominio_input_wrapper');
        const dominioExternoWrapper = document.getElementById('dominio_externo_wrapper');
        const nombreDominio = document.getElementById('nombre_dominio');
        const dominioExterno = document.getElementById('dominio_externo');

        function actualizarCamposDominio() {
            const valor = dominioSelect.value;

            if (valor === 'si') {
                dominioInputWrapper.style.display = 'block';
                dominioExternoWrapper.style.display = 'none';

                nombreDominio.setAttribute('required', 'required');
                dominioExterno.removeAttribute('required');
            } else if (valor === 'no') {
                dominioInputWrapper.style.display = 'none';
                dominioExternoWrapper.style.display = 'block';

                dominioExterno.setAttribute('required', 'required');
                nombreDominio.removeAttribute('required');
            } else {
                dominioInputWrapper.style.display = 'none';
                dominioExternoWrapper.style.display = 'none';

                nombreDominio.removeAttribute('required');
                dominioExterno.removeAttribute('required');
            }
        }

        dominioSelect.addEventListener('change', actualizarCamposDominio);
        actualizarCamposDominio(); // Ejecutar al cargar

        // Hosting (por si más adelante se necesita hacer algo)
        const hostingValue = document.getElementById('hosting_select').value;
        const hostingInputWrapper = document.getElementById('hosting_input_wrapper');

        if (hostingInputWrapper) {
            hostingInputWrapper.style.display = hostingValue === 'si' ? 'block' : 'none';

            document.getElementById('hosting_select').addEventListener('change', function () {
                hostingInputWrapper.style.display = this.value === 'si' ? 'block' : 'none';
            });
        }
    });
</script>
@endsection