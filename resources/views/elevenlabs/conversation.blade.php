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
    .info-item {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }
    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }
    .info-value {
        font-size: 1.1rem;
        color: #212529;
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
    .metadata-table {
        font-size: 0.9rem;
    }
    .metadata-table th {
        background: #f8f9fa;
        font-weight: 600;
        width: 30%;
    }
    .confidence-meter {
        height: 20px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }
    .confidence-fill {
        height: 100%;
        transition: width 0.3s ease;
    }
    .timeline-item {
        padding: 15px;
        border-left: 3px solid #e9ecef;
        margin-left: 20px;
        position: relative;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #667eea;
        border: 2px solid white;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <!-- Header -->
    <div class="card-body">
        <div class="conversation-header">
            <div class="row align-items-center">
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

    <!-- Información General -->
    <div class="card-body">
        <div class="row">
            <!-- Información Básica -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-info-circle"></i> Información Básica</h5>
                    
                    <div class="info-item">
                        <div class="info-label">Fecha y Hora</div>
                        <div class="info-value">
                            <i class="far fa-calendar-alt"></i>
                            {{ $conversation->conversation_date->format('d/m/Y H:i:s') }}
                            <small class="text-muted">({{ $conversation->conversation_date->diffForHumans() }})</small>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Cliente</div>
                        <div class="info-value">
                            @if($conversation->client)
                                <a href="/clients/{{ $conversation->client_id }}" target="_blank">
                                    <i class="fas fa-user"></i> {{ $conversation->client->name }}
                                </a>
                            @else
                                <span class="text-muted">No asignado</span>
                            @endif
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Duración</div>
                        <div class="info-value">
                            <i class="far fa-clock"></i> {{ $conversation->duration_formatted }}
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Estado de Procesamiento</div>
                        <div class="info-value">
                            @if($conversation->processing_status == 'completed')
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> {{ $conversation->status_label }}
                                </span>
                            @elseif($conversation->processing_status == 'processing')
                                <span class="badge bg-info">
                                    <i class="fas fa-spinner fa-spin"></i> {{ $conversation->status_label }}
                                </span>
                            @elseif($conversation->processing_status == 'failed')
                                <span class="badge bg-danger">
                                    <i class="fas fa-times-circle"></i> {{ $conversation->status_label }}
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="fas fa-hourglass-half"></i> {{ $conversation->status_label }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($conversation->processed_at)
                    <div class="info-item">
                        <div class="info-label">Procesada el</div>
                        <div class="info-value">
                            {{ $conversation->processed_at->format('d/m/Y H:i:s') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Análisis de IA -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3"><i class="fas fa-brain"></i> Análisis de IA</h5>
                    
                    <div class="info-item">
                        <div class="info-label">Categoría</div>
                        <div class="info-value">
                            @if($conversation->category)
                                <span class="category-badge" 
                                      style="background-color: {{ config('elevenlabs.categories.'.$conversation->category.'.color') }}">
                                    <i class="{{ config('elevenlabs.categories.'.$conversation->category.'.icon') }}"></i>
                                    {{ $conversation->category_label }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Sin categorizar</span>
                            @endif
                        </div>
                    </div>

                    @if($conversation->confidence_score)
                    <div class="info-item">
                        <div class="info-label">Nivel de Confianza</div>
                        <div class="info-value">
                            <div class="confidence-meter mb-2">
                                <div class="confidence-fill" 
                                     style="width: {{ $conversation->confidence_score * 100 }}%; 
                                            background-color: {{ $conversation->confidence_score >= 0.8 ? '#10B981' : ($conversation->confidence_score >= 0.5 ? '#F59E0B' : '#EF4444') }}">
                                </div>
                            </div>
                            {{ number_format($conversation->confidence_score * 100, 2) }}%
                            @if($conversation->confidence_score >= 0.8)
                                <span class="badge bg-success">Alta</span>
                            @elseif($conversation->confidence_score >= 0.5)
                                <span class="badge bg-warning">Media</span>
                            @else
                                <span class="badge bg-danger">Baja</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($conversation->category == 'queja')
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>¡Atención!</strong> Esta conversación fue categorizada como QUEJA. 
                        Requiere seguimiento urgente.
                    </div>
                    @elseif($conversation->category == 'baja')
                    <div class="alert alert-danger">
                        <i class="fas fa-user-times"></i>
                        <strong>¡Alerta!</strong> El cliente solicita darse de baja. 
                        Contactar inmediatamente.
                    </div>
                    @elseif($conversation->category == 'necesita_asistencia')
                    <div class="alert alert-warning">
                        <i class="fas fa-hand-paper"></i>
                        <strong>Acción requerida:</strong> Cliente necesita asistencia adicional.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen -->
    @if($conversation->summary_es)
    <div class="card-body">
        <div class="info-card">
            <h5 class="mb-3"><i class="fas fa-file-alt"></i> Resumen Ejecutivo</h5>
            <div class="summary-box">
                {{ $conversation->summary_es }}
            </div>
        </div>
    </div>
    @endif

    <!-- Transcripción -->
    @if($conversation->transcript)
    <div class="card-body">
        <div class="info-card">
            <h5 class="mb-3"><i class="fas fa-comment-dots"></i> Transcripción Completa</h5>
            <div class="transcript-box">{{ $conversation->transcript }}</div>
        </div>
    </div>
    @endif

    <!-- Metadata -->
    @if($conversation->metadata)
    <div class="card-body">
        <div class="info-card">
            <h5 class="mb-3"><i class="fas fa-database"></i> Metadatos de Eleven Labs</h5>
            <div class="table-responsive">
                <table class="table metadata-table">
                    @foreach($conversation->metadata as $key => $value)
                    <tr>
                        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        <td>
                            @if(is_array($value))
                                <pre>{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Timeline de Procesamiento -->
    <div class="card-body">
        <div class="info-card">
            <h5 class="mb-3"><i class="fas fa-history"></i> Timeline</h5>
            <div class="timeline-item">
                <strong>Conversación creada</strong><br>
                <small class="text-muted">{{ $conversation->created_at->format('d/m/Y H:i:s') }}</small>
            </div>
            @if($conversation->processed_at)
            <div class="timeline-item">
                <strong>Procesada por IA</strong><br>
                <small class="text-muted">{{ $conversation->processed_at->format('d/m/Y H:i:s') }}</small>
            </div>
            @endif
            <div class="timeline-item">
                <strong>Última actualización</strong><br>
                <small class="text-muted">{{ $conversation->updated_at->format('d/m/Y H:i:s') }}</small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function reprocesar() {
    if (!confirm('¿Estás seguro de reprocesar esta conversación? Se perderán los datos actuales de análisis.')) {
        return;
    }

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reprocesando...';
    btn.disabled = true;

    fetch('/elevenlabs/conversations/{{ $conversation->id }}/reprocess', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            setTimeout(() => location.reload(), 2000);
        } else {
            alert('❌ ' + data.message);
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
@endsection

