@extends('layouts.app')

@section('titulo', 'Correo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3><i class="bi bi-globe-americas"></i> Bandeja de Entrada</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('admin.emails.index')}}">Bandeja de Entrada</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Correo</li>
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
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-4">
                                <div>
                                    <p><strong>From:</strong> {{ $email->sender }}</p>
                                    @if($email->sender == $correo )
                                    <p><strong>To:</strong> {{ $email->to}}</p>
                                    @else
                                    <p><strong>To:</strong> {{ $email->user->name }}</p>
                                    @endif
                                    <p><strong>CC:</strong> {{ $email->cc ?? '' }}</p>
                                    <p><strong>Category:</strong> {{ optional($email->category)->name ?? 'N/A' }}</p>
                                    <p><strong>Status:</strong> <span class="badge bg-{{ optional($email->status)->color ?? 'secondary' }}">{{ optional($email->status)->name ?? 'N/A' }}</span></p>
                                    <p><strong>Received:</strong> {{ $email->created_at->format('F d, Y h:i A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-8 text-md-end">
                                <h4 class="h5">{{ $email->subject }}</h4>
                                @if ($email->category_id != 6)
                                <a href="{{ route('admin.emails.reply', $email->id) }}" class="btn btn-outline-primary mt-3">
                                    <i class="bi bi-reply"></i> Responder
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        @php
                            // Eliminar solo las etiquetas <a> para la verificación
                            $bodyWithoutLinks = preg_replace('/<a\b[^>]*>(.*?)<\/a>/i', '$1', $email->body);
                        @endphp

                        @if (strip_tags($bodyWithoutLinks) != $bodyWithoutLinks)
                            {{-- Si el cuerpo tiene etiquetas HTML (ignorando <a>) --}}
                            {!! $email->body !!}
                        @else
                            {{-- Si es texto plano (o solo contiene enlaces) --}}
                            <p style="white-space: pre-wrap;">{!! $email->body !!}</p>
                        @endif
                    </div>
                </div>

                <!-- Sección para mostrar adjuntos -->
                @if($email->attachments()->exists())
                <div class="card mt-3">
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
