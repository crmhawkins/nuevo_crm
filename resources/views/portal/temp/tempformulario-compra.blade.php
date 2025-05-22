@extends('layouts.tempappPortal')
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

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <span style="margin-top: 1vw; margin-right: 1vw;" class="position-absolute top-0 end-0 text-dark fw-bold fs-3">2/4</span>

    <div class="card shadow-lg p-4 bg-white rounded">
        <div class="position-relative mb-3">
            <h1 class="text-center text-dark display-5 mb-0">Rellene la información de su web</h1>
        </div>
        <h6 class="text-center">Nosotros complementaremos su información.</h6>
        <hr>
        <div class="card-body">
            <form id="formulario" action="{{ route('portal.storeForm') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre de la marca</label>
                        <input type="text" name="marca" class="form-control" required maxlength="22">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color principal</label>
                        <input type="color" name="color_principal" class="form-control form-control-color" title="Elija el color principal" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Color secundario</label>
                        <input type="color" name="color_secundario" class="form-control form-control-color" title="Elija el color secundario" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Historia de la empresa</label>
                    <textarea name="historia" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Principales servicios</label>
                    <textarea name="servicios" class="form-control" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Enlaces a redes</label>
                    <textarea name="redes" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Política de privacidad y cookies</label>
                    <textarea type="text" name="politica" class="form-control" id="politicaFile"></textarea>
                </div>

                <input type="hidden" name="structure" value="{{ $template }}">
                <input type="hidden" name="purchase_id" value="{{ $id }}">
                <input type="hidden" name="type" value="{{ $type }}">

                <div class="text-center">
                    <input type="submit" class="btn btn-success btn-lg" value="Siguiente">
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@include('partials.toast')
<script>
    document.getElementById('formulario').addEventListener('submit', function (e) {
    });
</script>
@endsection
