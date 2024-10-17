@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>{{ $email->subject }}</h1>

            <div class="mb-4">
                <strong>From:</strong> {{ $email->user->name }} <br>
                <strong>Category:</strong> {{ optional($email->category)->name ?? 'N/A' }} <br>
                <strong>Status:</strong> <span class="badge bg-{{  optional($email->status)->color ?? 'white' }}">{{ optional($email->status)->name ?? 'N/A' }}</span> <br>
                <strong>Received:</strong> {{ $email->created_at->format('F d, Y h:i A') }}
            </div>

            <div class="card">
                <div class="card-body">
                    <pre>{{ $email->body }}</pre>
                </div>
            </div>

            <!-- Sección para mostrar adjuntos -->
            @if($email->attachments()->exists())
            <div class="card mt-3">
                <div class="card-header">Attachments</div>
                <ul class="list-group list-group-flush">
                    @foreach ($email->attachments as $attachment)
                    <li class="list-group-item">
                        {{ $attachment->file_name }}
                        <a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-primary btn-sm float-right" download>
                            Download
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Botón para volver a la lista de correos -->
            <a href="{{ route('admin.emails.index') }}" class="btn btn-primary mt-3">Back to Inbox</a>
        </div>
    </div>
</div>
@endsection
