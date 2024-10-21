@extends('layouts.app')

@section('titulo', 'Responder Correo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.css">
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-envelope"></i> Responder Correo</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.emails.index')}}">Bandeja de Entrada</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Responder Correo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row">
            <div class="col-md-12">

                <!-- Vista del correo original -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-4">
                                <div>
                                    <p><strong>From:</strong> {{ $email->sender }}</p>
                                    <p><strong>To:</strong> {{ $email->user->name }}</p>
                                    <p><strong>Category:</strong> {{ optional($email->category)->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-{{ optional($email->status)->color ?? 'secondary' }}">{{ optional($email->status)->name ?? 'N/A' }}</span></p>
                                    <p><strong>Received:</strong> {{ $email->created_at->format('F d, Y h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-8 text-md-end">
                                <h4 class="h5">{{ $email->subject }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulario para responder el correo -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('admin.emails.sendReply', $email->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="message" class="form-label">Mensaje de Respuesta</label>
                                <textarea class="form-control summernote @error('message') is-invalid @enderror" id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Archivos Adjuntos</label>
                                <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                                @error('attachments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Enviar Respuesta</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <p style="white-space: pre-wrap;">{!! $email->body !!}</p>
                    </div>
                </div>

                <!-- Sección para mostrar adjuntos -->
                @if($email->attachments()->exists())
                <div class="card mb-4">
                    <div class="card-header">Adjuntos</div>
                    <ul class="list-group list-group-flush">
                        @foreach ($email->attachments as $attachment)
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
<script src="https://cdn.jsdelivr.net/npm/summernote/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });
    });
</script>
@endsection
