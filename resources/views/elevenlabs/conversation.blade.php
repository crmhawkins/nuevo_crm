@extends('layouts.app')

@section('titulo', 'Detalle de Conversación')

@section('css')
<style>
    .conversation-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 30px;
    }
    .info-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .category-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
    }
    .transcript-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 20px;
        border-radius: 5px;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        max-height: 500px;
        overflow-y: auto;
    }
    .summary-box {
        background: #e7f3ff;
        border-left: 4px solid #0d6efd;
        padding: 20px;
        border-radius: 5px;
        font-size: 1.05rem;
        line-height: 1.6;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="card-body">
        <div class="conversation-header">
            <div class="row">
                <div class="col-md-8">
                    <h3><i class="fas fa-phone-alt"></i> Conversación</h3>
                    <p class="mb-0">ID: {{ $conversation->conversation_id }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('elevenlabs.conversations') }}" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    @if($conversation->processing_status != 'processing')
                    <button onclick="reprocesar()" class="btn btn-warning">
                        <i class="fas fa-redo"></i> Reprocesar
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="fas fa-info-circle"></i> Información</h5>
                    <p><strong>Fecha:</strong> {{ $conversation->conversation_date->format('d/m/Y H:i:s') }}</p>
                    @if($conversation->agent_name)
                    <p><strong>Agente IA:</strong> <i class="fas fa-robot"></i> {{ $conversation->agent_name }}</p>
                    @endif
                    <p><strong>Cliente:</strong> {{ $conversation->client->name ?? 'N/A' }}</p>
                    <p><strong>Duración:</strong> {{ $conversation->duration_formatted }}</p>
                    <p><strong>Estado:</strong> 
                        @if($conversation->processing_status == 'completed')
                            <span class="badge bg-success">{{ $conversation->status_label }}</span>
                        @else
                            <span class="badge bg-warning">{{ $conversation->status_label }}</span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="fas fa-brain"></i> Análisis IA</h5>
                    <p><strong>Sentimiento:</strong> 
                        @if($conversation->sentiment_category)
                            <span class="category-badge" style="background-color: {{ $conversation->sentiment_color }}">
                                {{ $conversation->sentiment_label }}
                            </span>
                        @else
                            <span class="text-muted">Sin clasificar</span>
                        @endif
                    </p>
                    <p><strong>Categoría Específica:</strong> 
                        @if($conversation->specific_category)
                            <span class="category-badge" style="background-color: {{ $conversation->specific_color }}">
                                {{ $conversation->specific_label }}
                            </span>
                        @else
                            <span class="text-muted">Sin clasificar</span>
                        @endif
                    </p>
                    @if($conversation->confidence_score)
                    <p><strong>Confianza:</strong> {{ number_format($conversation->confidence_score * 100, 2) }}%</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    @if($conversation->summary_es)
    <div class="card-body">
        <div class="info-card">
            <h5><i class="fas fa-file-alt"></i> Resumen</h5>
            <div class="summary-box">{{ $conversation->summary_es }}</div>
        </div>
    </div>
    @endif

    <!-- Transcripción -->
    @if($conversation->transcript)
    <div class="card-body">
        <div class="info-card">
            <h5><i class="fas fa-comment-dots"></i> Transcripción</h5>
            <div class="transcript-box">{{ $conversation->transcript }}</div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function reprocesar() {
    if (!confirm('¿Reprocesar esta conversación?')) return;

    fetch('/elevenlabs/conversations/{{ $conversation->id }}/reprocess', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) setTimeout(() => location.reload(), 2000);
    });
}
</script>
@endsection

