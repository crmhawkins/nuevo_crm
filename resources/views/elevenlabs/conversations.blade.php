@extends('layouts.app')

@section('titulo', 'Conversaciones - Eleven Labs')

@section('css')
<style>
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .category-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
    }
    .status-badge {
        font-size: 0.8rem;
        padding: 4px 10px;
    }
    .table-actions {
        white-space: nowrap;
    }
    .search-box {
        position: relative;
    }
    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }
    .search-box input {
        padding-left: 35px;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><i class="fas fa-list"></i> Conversaciones</h3>
                <p class="text-subtitle text-muted">Listado completo de conversaciones</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <div class="float-end">
                    <a href="{{ route('elevenlabs.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                    <button class="btn btn-success" onclick="exportar()">
                        <i class="fas fa-file-export"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-body">
        <div class="filter-card">
            <form method="GET" action="{{ route('elevenlabs.conversations') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Búsqueda -->
                    <div class="col-md-3">
                        <label class="form-label">Búsqueda</label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Buscar en transcripciones..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div class="col-md-2">
                        <label class="form-label">Categoría</label>
                        <select name="category" class="form-select">
                            <option value="">Todas</option>
                            @foreach($categories as $key => $category)
                                <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                    {{ $category['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="status" class="form-select">
                            <option value="">Todos</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Procesando</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completado</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                        </select>
                    </div>

                    <!-- Fecha Inicio -->
                    <div class="col-md-2">
                        <label class="form-label">Desde</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date') }}">
                    </div>

                    <!-- Fecha Fin -->
                    <div class="col-md-2">
                        <label class="form-label">Hasta</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date') }}">
                    </div>

                    <!-- Botones -->
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Conversaciones -->
    <div class="card-body">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>Duración</th>
                                <th>Categoría</th>
                                <th>Confianza</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conversations as $conversation)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ substr($conversation->conversation_id, 0, 8) }}...</small>
                                </td>
                                <td>{{ $conversation->conversation_date->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($conversation->client)
                                        <a href="/clients/{{ $conversation->client_id }}" target="_blank">
                                            {{ $conversation->client->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $conversation->duration_formatted }}</td>
                                <td>
                                    @if($conversation->category)
                                        <span class="category-badge" 
                                              style="background-color: {{ config('elevenlabs.categories.'.$conversation->category.'.color') }}">
                                            <i class="{{ config('elevenlabs.categories.'.$conversation->category.'.icon') }}"></i>
                                            {{ $conversation->category_label }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Sin categoría</span>
                                    @endif
                                </td>
                                <td>
                                    @if($conversation->confidence_score)
                                        <span class="badge {{ $conversation->confidence_score >= 0.8 ? 'bg-success' : ($conversation->confidence_score >= 0.5 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($conversation->confidence_score * 100, 1) }}%
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($conversation->processing_status == 'completed')
                                        <span class="badge bg-success status-badge">
                                            <i class="fas fa-check"></i> {{ $conversation->status_label }}
                                        </span>
                                    @elseif($conversation->processing_status == 'processing')
                                        <span class="badge bg-info status-badge">
                                            <i class="fas fa-spinner fa-spin"></i> {{ $conversation->status_label }}
                                        </span>
                                    @elseif($conversation->processing_status == 'failed')
                                        <span class="badge bg-danger status-badge">
                                            <i class="fas fa-times"></i> {{ $conversation->status_label }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning status-badge">
                                            <i class="fas fa-clock"></i> {{ $conversation->status_label }}
                                        </span>
                                    @endif
                                </td>
                                <td class="table-actions">
                                    <a href="{{ route('elevenlabs.conversation.show', $conversation->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($conversation->processing_status != 'processing')
                                    <button onclick="reprocesar({{ $conversation->id }})" 
                                            class="btn btn-sm btn-outline-warning" title="Reprocesar">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No se encontraron conversaciones con los filtros seleccionados</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-3">
                    {{ $conversations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function reprocesar(conversationId) {
    if (!confirm('¿Estás seguro de reprocesar esta conversación?')) {
        return;
    }

    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.disabled = true;

    fetch(`/elevenlabs/conversations/${conversationId}/reprocess`, {
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
            setTimeout(() => location.reload(), 1000);
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

function exportar() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route('elevenlabs.export') }}?' + params.toString();
}
</script>
@endsection

