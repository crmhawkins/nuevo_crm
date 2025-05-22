@extends('layouts.appWhatsapp')

@section('titulo', 'Configuración Básica')

<style>
    .sortable {
        cursor: pointer;
    }

    .sort-icon {
        font-size: 12px;
        margin-left: 5px;
    }

    .css-96uzu9 {
        z-index: -1 !important;
    }

    .form-label {
        font-weight: 600;
    }

    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }
</style>

@section('content')
    <div class="col-md-10 p-4 bg-white rounded">
        <h2 class="mb-4">Configuración de la plataforma</h2>

        <form id="configForm" enctype="multipart/form-data" action="{{ route('plataforma.configuracionStore') }}" method="POST">
            @csrf
            <div class="form-section">
                <label for="company_logo" class="form-label">Foto de perfil</label>
                <div class="position-relative d-inline-block">
                    <label for="company_logo" class="cursor-pointer">
                        @if(isset($config?->company_logo))
                            <img src="{{ asset($config->company_logo) }}" alt="Foto de perfil" class="img-thumbnail" style="max-width: 200px; cursor: pointer;">
                        @else
                            <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 200px; height: 200px; background: #f8f9fa; cursor: pointer;">
                                <i class="fas fa-camera fa-2x text-muted"></i>
                            </div>
                        @endif
                        <input type="file" class="d-none" id="company_logo" name="company_logo" accept="image/*">
                    </label>
                    <div class="mt-2">
                        <small class="text-muted">Haz clic en la imagen para cambiarla</small>
                    </div>
                </div>
            </div>

            <div class="row form-section">
                <div class="col-md-6">
                    <label for="company_name" class="form-label">Nombre de la empresa</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $config?->company_name }}" required>
                </div>
                <div class="col-md-6">
                    <label for="company_phone" class="form-label">Número de teléfono</label>
                    <input type="tel" class="form-control" id="company_phone" name="company_phone" value="{{ $config?->company_phone }}" required>
                </div>
            </div>

            <div class="row form-section">
                <div class="col-md-6">
                    <label for="company_cat_id" class="form-label">Categoría de la empresa</label>
                    <select class="form-control" id="company_cat_id" name="company_cat_id" required>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ $config?->company_cat_id == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="company_address" class="form-label">Dirección de la empresa</label>
                    <input type="text" class="form-control" id="company_address" name="company_address" value="{{ $config?->company_address }}" required>
                </div>
            </div>

            <div class="row form-section">
                <div class="col-md-6">
                    <label for="company_mail" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="company_mail" name="company_mail" value="{{ $config?->company_mail }}" required>
                </div>
                <div class="col-md-6">
                    <label for="company_web" class="form-label">Sitio web</label>
                    <input type="url" class="form-control" id="company_web" name="company_web" value="{{ $config?->company_web }}">
                </div>
            </div>

            <div class="form-section">
                <label for="company_description" class="form-label">Descripción de la empresa</label>
                <textarea class="form-control" id="company_description" name="company_description" rows="3">{{ $config?->company_description }}</textarea>
            </div>

            <div class="row form-section">
                <div class="col-md-12">
                    <label for="company_apikey" class="form-label">API KEY Whatsapp (Meta)</label>
                    @if(isset($config?->company_apikey))
                        <input type="text" class="form-control" id="company_apikey" name="company_apikey" value="*****************" required>
                    @else
                        <input type="text" class="form-control" id="company_apikey" name="company_apikey" value="" required>
                    @endif
                </div>
            </div>

            <div class="form-section text-end">
                <button id="saveConfig" type="button" class="btn btn-primary px-4">Guardar Configuración</button>
            </div>
        </form>
        </div>
@endsection

@section('scripts')
        <script>
            apikey = ""

            $(document).ready(function() {
                $('#company_apikey').val('*****************');
            $('#company_apikey').on('focus', function() {
                if ($(this).val() === '*****************') {
                    $(this).val(apikey);
                }
            });

            $('#company_apikey').on('blur', function() {
                apikey = $(this).val()
                $(this).val('*****************');
            });
            });

            $('#saveConfig').on('click', function() {
                $('#company_apikey').val(apikey);
                $('#configForm').submit();
            });
        </script>
@endsection