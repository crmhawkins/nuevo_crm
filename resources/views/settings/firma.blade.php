@extends('layouts.app')

@section('titulo', 'Configuración de Correo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3 class="display-6"><i class="bi bi-gear"></i> Configuración de Firma de Correo</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Configuración Firma Correo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        @if($firma->isEmpty())
                            <form id="formStore"  action="{{ route('admin.firma.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma Html</label>
                                            <textarea id="firma" class="form-control @error('firma') is-invalid @enderror"   name="firma" >{{old('firma')}}</textarea>
                                            @error('firma')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form id="formUpdate" action="{{ route('admin.firma.update', $firma->first()->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma Html</label>
                                            <textarea id="firma" class="form-control @error('firma') is-invalid @enderror"  name="firma" > {{ old('firma',$firma->first()->firma) }}</textarea>
                                            @error('firma')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                        <div class="mt-4">
                            <h5>Vista previa de la firma</h5>
                            <div id="firmaPreview" class="border p-3" style="min-height: 100px; background-color: #f8f9fa;">
                                <!-- Aquí se mostrará la vista previa -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card-body p-3">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                    <div class="card-body">
                        @if($firma->isEmpty())
                        <button id="Guardar" type="button" class="btn btn-primary">Guardar Firma</button>
                        @else
                        <button id="Actualizar" type="button" class="btn btn-primary">Actualizar Firma</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('scripts')
@include('partials.toast')

<script>
     $('#Guardar').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('#formStore').submit(); // Esto envía el formulario.
        });
    $('#Actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('#formUpdate').submit(); // Esto envía el formulario.
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const firmaTextarea = document.getElementById('firma');
        const firmaPreview = document.getElementById('firmaPreview');

        // Función para actualizar la vista previa
        const updatePreview = () => {
            const content = firmaTextarea.value;
            firmaPreview.innerHTML = content;
        };

        // Escucha cambios en el textarea
        firmaTextarea.addEventListener('input', updatePreview);

        // Actualiza la vista previa al cargar la página
        updatePreview();
    });
</script>
@endsection
