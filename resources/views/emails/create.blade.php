@extends('layouts.app')

@section('titulo', 'Enviar Nuevo Correo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.css">
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-envelope"></i> Enviar Nuevo Correo Electrónico</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Bandeja de Entrada</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Enviar Correo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.emails.send') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Destinatario --}}
                            <div class="mb-3">
                                <label for="to" class="form-label">Destinatario</label>
                                <input type="text" class="form-control @error('to') is-invalid @enderror" id="to" name="to" value="{{ old('to') }}" required>
                                @error('to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CC --}}
                            <div class="mb-3">
                                <label for="cc" class="form-label">CC (Con Copia)</label>
                                <input type="text" class="form-control @error('cc') is-invalid @enderror" id="cc" name="cc" value="{{ old('cc') }}" placeholder="Opcional: correos separados por comas">
                                @error('cc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BCC --}}
                            <div class="mb-3">
                                <label for="bcc" class="form-label">BCC (Copia Oculta)</label>
                                <input type="text" class="form-control @error('bcc') is-invalid @enderror" id="bcc" name="bcc" value="{{ old('bcc') }}" placeholder="Opcional: correos separados por comas">
                                @error('bcc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Asunto --}}
                            <div class="mb-3">
                                <label for="subject" class="form-label">Asunto</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mensaje --}}
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea class="form-control summernote @error('message') is-invalid @enderror" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Archivos Adjuntos --}}
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Archivos Adjuntos</label>
                                <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                                @error('attachments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Botón de Enviar --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Enviar Correo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
@include('partials.toast')
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.js"></script>
<script src="{{ asset('assets/vendors/choices.js/choices.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Inicializa el editor de texto enriquecido
        $('.summernote').summernote({
            height: 200
        });

        // Inicializa Choices.js para autocompletar
        const previousEmails = @json($previousEmails);  // Recoger emails previos desde la base de datos

        // Crear campo de selección para "to", "cc", y "bcc" con sugerencias
        const toField = new Choices('#to', {
            removeItemButton: true,
            duplicateItemsAllowed: false,
            choices: previousEmails.map(email => ({ value: email, label: email })),
        });

        const ccField = new Choices('#cc', {
            removeItemButton: true,
            duplicateItemsAllowed: false,
            choices: previousEmails.map(email => ({ value: email, label: email })),
        });

        const bccField = new Choices('#bcc', {
            removeItemButton: true,
            duplicateItemsAllowed: false,
            choices: previousEmails.map(email => ({ value: email, label: email })),
        });
    });
</script>
@endsection
