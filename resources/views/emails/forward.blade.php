@extends('layouts.app')

@section('titulo', 'Reenviar Correo')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.css">

<style>
    .input-correos{
        padding: 0 !important;
        display: flex !important;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-envelope"></i> Reenviar Correo</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.emails.index') }}">Bandeja de Entrada</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reenviar Correo</li>
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
                        <form action="{{ route('admin.emails.sendforward', $correo->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Destinatario --}}
                            <div class="mb-3">
                                <label for="to" class="form-label">Destinatario</label>
                                <input type="text" class="form-control input-correos @error('to') is-invalid @enderror" id="to" name="to" required>
                                @error('to')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- CC --}}
                            <div class="mb-3">
                                <label for="cc" class="form-label">CC (Con Copia)</label>
                                <input type="text" class="form-control input-correos @error('cc') is-invalid @enderror" id="cc" name="cc">
                                @error('cc')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- BCC --}}
                            <div class="mb-3">
                                <label for="bcc" class="form-label">BCC (Copia Oculta)</label>
                                <input type="text" class="form-control input-correos @error('bcc') is-invalid @enderror" id="bcc" name="bcc">
                                @error('bcc')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Asunto --}}
                            <div class="mb-3">
                                <label for="subject" class="form-label">Asunto</label>
                                <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="Fwd: {{ $correo->subject }}" required>
                                @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mensaje --}}
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje</label>
                                <textarea class="form-control summernote @error('message') is-invalid @enderror" id="message" name="message" rows="6" required>
                                    <br><hr>
                                    <strong>Mensaje original:</strong><br>{!! $correo->body !!}
                                </textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Archivos Adjuntos --}}
                            <div class="mb-3">
                                <label for="attachments" class="form-label">Adjuntar Archivos</label>
                                <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                                @error('attachments')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Botón --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Reenviar Correo</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Mostrar Adjuntos Originales --}}
                @if($correo->attachments->isNotEmpty())
                <div class="card mt-4">
                    <div class="card-header">Adjuntos Originales</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($correo->attachments as $attachment)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $attachment->file_name }}
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-outline-primary btn-sm" download>
                                <i class="bi bi-download"></i> Descargar
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa Summernote
        $('.summernote').summernote({
            height: 200
        });

        // Lista de correos previos
        const previousEmails = Object.values(@json($previousEmails)); // Convierte a array

        // Configura Tagify
        ['#to', '#cc', '#bcc'].forEach(selector => {
            new Tagify(document.querySelector(selector), {
                whitelist: previousEmails,
                pattern: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                dropdown: {
                    enabled: 1,
                    maxItems: 10
                },
                delimiters: ","
            });
        });
    });
</script>
@endsection
