@extends('layouts.app')

@section('titulo', 'Conversaciones - Eleven Labs')

@section('css')
<style>
    .category-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3><i class="fas fa-list"></i> Conversaciones</h3>
            </div>
            <div class="col-12 col-md-6">
                <div class="float-end">
                    <a href="{{ route('elevenlabs.agents') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-robot"></i> Agentes
                    </a>
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

    <!-- Filtros Avanzados -->
    <div class="card-body">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros y Búsqueda</h6>
            </div>
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <!-- Fila 1: Búsqueda -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-12">
                            <label class="form-label"><i class="fas fa-search"></i> Búsqueda General</label>
                            <input type="text" name="search" class="form-control form-control-lg" 
                                   placeholder="Buscar por ID de conversación, agente, palabras clave en transcripción..." 
                                   value="{{ request('search') }}">
                            <small class="text-muted">Busca en: ID de conversación, nombre de agente, transcripción y resumen</small>
                        </div>
                    </div>

                    <!-- Fila 2: Filtros principales -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Agente</label>
                            <select name="agent_id" class="form-select">
                                <option value="">Todos los agentes</option>
                                @foreach(\App\Models\ElevenlabsAgent::orderBy('name')->get() as $agent)
                                    <option value="{{ $agent->agent_id }}" {{ request('agent_id') == $agent->agent_id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select name="category" class="form-select">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $key => $category)
                                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                                        {{ $category['label'] }}
                                    </option>
                                @endforeach
                                <option value="sin_categoria" {{ request('category') == 'sin_categoria' ? 'selected' : '' }}>Sin categoría</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Estado de Procesamiento</label>
                            <select name="status" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pendiente</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>🔄 Procesando</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✅ Completado</option>
                                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>❌ Fallido</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Satisfacción</label>
                            <select name="satisfaction" class="form-select">
                                <option value="">Todas</option>
                                <option value="contentos" {{ request('satisfaction') == 'contentos' ? 'selected' : '' }}>😊 Contentos</option>
                                <option value="descontentos" {{ request('satisfaction') == 'descontentos' ? 'selected' : '' }}>😞 Descontentos</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 3: Filtros adicionales -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Tiene Transcripción</label>
                            <select name="has_transcript" class="form-select">
                                <option value="">Todas</option>
                                <option value="yes" {{ request('has_transcript') == 'yes' ? 'selected' : '' }}>✅ Con transcripción</option>
                                <option value="no" {{ request('has_transcript') == 'no' ? 'selected' : '' }}>❌ Sin transcripción</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tiene Resumen</label>
                            <select name="has_summary" class="form-select">
                                <option value="">Todas</option>
                                <option value="yes" {{ request('has_summary') == 'yes' ? 'selected' : '' }}>✅ Con resumen</option>
                                <option value="no" {{ request('has_summary') == 'no' ? 'selected' : '' }}>❌ Sin resumen</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                                <a href="{{ route('elevenlabs.conversations') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de filtro -->
                    <div class="row">
                        <div class="col-md-12">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Mostrando <strong>{{ $conversations->total() }}</strong> conversaciones
                                @if(request()->hasAny(['search', 'agent_id', 'category', 'status', 'satisfaction', 'has_transcript', 'has_summary', 'start_date', 'end_date']))
                                    (con filtros aplicados)
                                @endif
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="card-body">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Agente</th>
                                <th>Cliente</th>
                                <th>Duración</th>
                                <th>Categoría</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($conversations as $conv)
                            <tr class="{{ $conv->attended ? 'table-success' : '' }}" style="{{ $conv->attended ? 'opacity: 0.7; font-weight: 500;' : '' }}">
                                <td><small>{{ substr($conv->conversation_id, 0, 12) }}...</small></td>
                                <td>
                                    @if($conv->attended)
                                        <i class="fas fa-check-circle text-success" title="Atendida"></i>
                                    @endif
                                    {{ $conv->conversation_date->format('d/m/Y H:i') }}
                                </td>
                                <td><small class="text-muted">{{ $conv->agent_name ?? 'N/A' }}</small></td>
                                <td>{{ $conv->client->name ?? 'N/A' }}</td>
                                <td>{{ $conv->duration_formatted }}</td>
                                <td>
                                    @if($conv->sentiment_category)
                                        <span class="category-badge" style="background-color: {{ $conv->sentiment_color }}; font-size: 0.75rem;">
                                            {{ $conv->sentiment_label }}
                                        </span>
                                    @endif
                                    @if($conv->specific_category)
                                        <span class="category-badge" style="background-color: {{ $conv->specific_color }}; font-size: 0.75rem;">
                                            {{ $conv->specific_label }}
                                        </span>
                                    @endif
                                    @if(!$conv->sentiment_category && !$conv->specific_category)
                                        <span class="badge bg-secondary">Sin categoría</span>
                                    @endif
                                </td>
                                <td>
                                    @if($conv->processing_status == 'completed')
                                        <span class="badge bg-success">{{ $conv->status_label }}</span>
                                    @elseif($conv->processing_status == 'processing')
                                        <span class="badge bg-info">{{ $conv->status_label }}</span>
                                    @elseif($conv->processing_status == 'failed')
                                        <span class="badge bg-danger">{{ $conv->status_label }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ $conv->status_label }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" onclick="verConversacion({{ $conv->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($conv->processing_status != 'processing')
                                    <button onclick="reprocesar({{ $conv->id }})" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-redo"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No hay conversaciones</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $conversations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver conversación -->
<div class="modal fade" id="conversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-phone-alt"></i> Detalle de Conversación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conversationContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Cargando conversación...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnAtendido" onclick="toggleAttendedModal()">
                    <i class="fas fa-check"></i> <span id="btnAtendidoText">Marcar como Atendido</span>
                </button>
                <button type="button" class="btn btn-warning" id="btnReprocesarModal" onclick="reprocesarModal()">
                    <i class="fas fa-redo"></i> Reprocesar con IA
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Función para formatear fechas sin conversión de timezone
function formatDateWithoutTimezone(dateString) {
    if (!dateString) return '';
    
    // Parsear directamente desde el string sin crear objeto Date
    // Formato esperado: "YYYY-MM-DD HH:MM:SS" o "YYYY-MM-DD HH:MM"
    const parts = dateString.split(/[-: T]/);
    const year = parts[0];
    const month = parts[1];
    const day = parts[2];
    const hour = parts[3] || '00';
    const minute = parts[4] || '00';
    
    // Formatear directamente sin conversiones
    return `${day}/${month}/${year}, ${hour}:${minute}`;
}

let currentConversationId = null;
let currentConversationAttended = false;

function verConversacion(id) {
    currentConversationId = id;
    const modal = new bootstrap.Modal(document.getElementById('conversationModal'));
    modal.show();
    
    document.getElementById('conversationContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
            <p class="mt-2">Cargando conversación...</p>
        </div>
    `;

    fetch(`/api/elevenlabs-monitoring/conversations/${id}`, {
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer {{ auth()->user()->createToken("temp")->plainTextToken ?? "" }}'
        }
    })
    .then(r => r.json())
    .then(conv => {
        currentConversationData = conv; // Guardar datos para edición
        currentConversationAttended = conv.attended || false;
        
        // Actualizar botón de atendido
        updateAttendedButton(currentConversationAttended);
        
        document.getElementById('conversationContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-info-circle"></i> Información</h6>
                            <p><strong>ID:</strong> <small>${conv.conversation_id}</small></p>
                            <p><strong>Fecha:</strong> ${new Date(conv.conversation_date).toLocaleString('es-ES')}</p>
                            ${conv.agent_name ? `<p><strong>Agente:</strong> <i class="fas fa-robot"></i> ${conv.agent_name}</p>` : ''}
                            <p><strong>Duración:</strong> ${conv.duration_formatted || '0:00'}</p>
                            ${conv.client ? `<p><strong>Cliente:</strong> ${conv.client.name || 'N/A'}</p>` : ''}
                            <p><strong>Estado:</strong> 
                                <span class="badge bg-${conv.processing_status == 'completed' ? 'success' : 'warning'}">
                                    ${conv.status_label}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6><i class="fas fa-brain"></i> Análisis IA</h6>
                            
                            <!-- Categorías actuales con opción de editar -->
                            <div id="currentCategories">
                                ${['baja', 'llamada_agendada'].includes(conv.sentiment_category) ? `
                                    <p><strong>Acción:</strong> 
                                        <span class="category-badge" style="background-color: ${conv.sentiment_color || '#6B7280'}">${conv.sentiment_label || conv.sentiment_category}</span>
                                    </p>
                                ` : `
                                    <p><strong>Sentimiento:</strong> 
                                        ${conv.sentiment_category ? `<span class="category-badge" style="background-color: ${conv.sentiment_color || '#6B7280'}">${conv.sentiment_label || conv.sentiment_category}</span>` : '<span class="text-muted">-</span>'}
                                    </p>
                                    <p><strong>Categoría Específica:</strong> 
                                        ${conv.specific_category ? `<span class="category-badge" style="background-color: ${conv.specific_color || '#6B7280'}">${conv.specific_label || conv.specific_category}</span>` : '<span class="text-muted">-</span>'}
                                    </p>
                                `}
                                ${conv.confidence_score ? `<p><strong>Confianza:</strong> ${(conv.confidence_score * 100).toFixed(1)}%</p>` : ''}
                                <button class="btn btn-sm btn-outline-secondary mt-2" onclick="toggleEditCategories()">
                                    <i class="fas fa-edit"></i> Cambiar Categorías
                                </button>
                            </div>
                            
                            <!-- Editor de categorías (oculto por defecto) -->
                            <div id="editCategories" style="display: none;">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Editando categorías manualmente
                                </div>
                                <div class="mb-3">
                                    <label class="form-label"><strong>Sentimiento/Acción:</strong></label>
                                    <select id="sentimentSelect" class="form-select form-select-sm">
                                        <option value="">Cargando...</option>
                                    </select>
                                    <small class="text-muted">Contento, Descontento, Sin Respuesta, Baja, Llamada Agendada</small>
                                </div>
                                <div class="mb-3" id="specificContainer">
                                    <label class="form-label"><strong>Categoría Específica:</strong></label>
                                    <select id="specificSelect" class="form-select form-select-sm">
                                        <option value="">Cargando...</option>
                                    </select>
                                </div>
                                <button class="btn btn-sm btn-success" onclick="saveCategories()">
                                    <i class="fas fa-save"></i> Guardar Cambios
                                </button>
                                <button class="btn btn-sm btn-secondary" onclick="toggleEditCategories()">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            </div>
                            
                            ${conv.scheduled_call_datetime ? `
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-calendar-check"></i> <strong>Llamada Agendada:</strong><br>
                                    📅 ${formatDateWithoutTimezone(conv.scheduled_call_datetime)}<br>
                                    ${conv.scheduled_call_notes ? `📝 ${conv.scheduled_call_notes}` : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
            ${conv.summary_es ? `
                <div class="card mb-3">
                    <div class="card-body">
                        <h6><i class="fas fa-file-alt"></i> Resumen en Español</h6>
                        <div class="p-3" style="background: #e7f3ff; border-left: 4px solid #0d6efd; border-radius: 5px;">
                            ${conv.summary_es}
                        </div>
                    </div>
                </div>
            ` : ''}
            ${conv.transcript ? `
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-comment-dots"></i> Transcripción Completa</h6>
                        <div class="p-3" style="background: #f8f9fa; border-left: 4px solid #667eea; border-radius: 5px; font-family: 'Courier New', monospace; white-space: pre-wrap; max-height: 400px; overflow-y: auto;">
${conv.transcript}
                        </div>
                    </div>
                </div>
            ` : '<div class="alert alert-warning">Sin transcripción disponible</div>'}
        `;
    })
    .catch(e => {
        document.getElementById('conversationContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Error al cargar: ${e.message}
            </div>
        `;
    });
}

// Ya no es necesaria - el color viene desde la BD

let availableCategories = null;
let currentConversationData = null;

function toggleEditCategories() {
    const currentDiv = document.getElementById('currentCategories');
    const editDiv = document.getElementById('editCategories');
    
    if (editDiv.style.display === 'none') {
        loadCategoriesForEdit();
        currentDiv.style.display = 'none';
        editDiv.style.display = 'block';
    } else {
        currentDiv.style.display = 'block';
        editDiv.style.display = 'none';
    }
}

function loadCategoriesForEdit() {
    if (!currentConversationData || !currentConversationData.agent_id) {
        console.error('No hay datos de conversación o agent_id');
        return;
    }
    
    console.log('Cargando categorías para agente:', currentConversationData.agent_id);
    
    fetch(`/api/elevenlabs-monitoring/agents/${currentConversationData.agent_id}/available-categories`)
        .then(r => r.json())
        .then(data => {
            console.log('Categorías recibidas:', data);
            if (data.success) {
                availableCategories = data;
                populateCategorySelects();
            } else {
                console.error('Error en respuesta:', data);
            }
        })
        .catch(e => console.error('Error cargando categorías:', e));
}

function populateCategorySelects() {
    const sentimentSelect = document.getElementById('sentimentSelect');
    const specificSelect = document.getElementById('specificSelect');
    
    if (!sentimentSelect || !specificSelect) {
        console.error('Selectores no encontrados');
        return;
    }
    
    console.log('Poblando selectores con:', availableCategories);
    
    // Poblar sentimientos
    sentimentSelect.innerHTML = '<option value="">Seleccionar...</option>';
    if (availableCategories.sentiment_categories) {
        availableCategories.sentiment_categories.forEach(cat => {
            const selected = currentConversationData.sentiment_category === cat.category_key ? 'selected' : '';
            sentimentSelect.innerHTML += `<option value="${cat.category_key}" ${selected}>${cat.category_label}</option>`;
        });
    }
    
    // Poblar específicas
    specificSelect.innerHTML = '<option value="">Sin categoría específica</option>';
    if (availableCategories.specific_categories) {
        availableCategories.specific_categories.forEach(cat => {
            const selected = currentConversationData.specific_category === cat.category_key ? 'selected' : '';
            specificSelect.innerHTML += `<option value="${cat.category_key}" ${selected}>${cat.category_label}</option>`;
        });
    }
    
    // Listener para deshabilitar específica si es baja, sin_respuesta o llamada_agendada
    sentimentSelect.addEventListener('change', function() {
        const specificContainer = document.getElementById('specificContainer');
        if (['baja', 'sin_respuesta', 'llamada_agendada'].includes(this.value)) {
            specificSelect.value = '';
            specificContainer.style.display = 'none';
        } else {
            specificContainer.style.display = 'block';
        }
    });
    
    // Verificar estado inicial
    if (['baja', 'sin_respuesta', 'llamada_agendada'].includes(currentConversationData.sentiment_category)) {
        document.getElementById('specificContainer').style.display = 'none';
    }
}

function saveCategories() {
    const sentiment = document.getElementById('sentimentSelect').value;
    const specific = document.getElementById('specificSelect').value;
    
    const btn = event.target;
    const html = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    btn.disabled = true;
    
    fetch(`/api/elevenlabs-monitoring/conversations/${currentConversationId}/update-categories`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sentiment_category: sentiment || null,
            specific_category: specific || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            verConversacion(currentConversationId);
            toggleEditCategories();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .finally(() => {
        btn.innerHTML = html;
        btn.disabled = false;
    });
}

function reprocesarModal() {
    if (!currentConversationId || !confirm('¿Reprocesar esta conversación con la IA?')) return;

    const btn = document.getElementById('btnReprocesarModal');
    const html = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Reprocesando...';
    btn.disabled = true;

    fetch(`/elevenlabs/conversations/${currentConversationId}/reprocess`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('conversationModal')).hide();
            setTimeout(() => location.reload(), 1000);
        }
    })
    .finally(() => {
        btn.innerHTML = html;
        btn.disabled = false;
    });
}
</script>

<script>
function reprocesar(id) {
    if (!confirm('¿Reprocesar esta conversación?')) return;

    fetch(`/elevenlabs/conversations/${id}/reprocess`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(r => r.json())
    .then(data => {
        alert(data.success ? '✅ ' + data.message : '❌ ' + data.message);
        if (data.success) setTimeout(() => location.reload(), 1000);
    });
}

function exportar() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = '{{ route("elevenlabs.export") }}?' + params.toString();
}

// Funciones para marcar como atendido
function updateAttendedButton(attended) {
    const btn = document.getElementById('btnAtendido');
    const text = document.getElementById('btnAtendidoText');
    
    if (attended) {
        btn.className = 'btn btn-outline-success';
        text.textContent = 'Atendida ✓';
    } else {
        btn.className = 'btn btn-success';
        text.textContent = 'Marcar como Atendido';
    }
}

function toggleAttendedModal() {
    if (!currentConversationId) return;
    
    const btn = document.getElementById('btnAtendido');
    const text = document.getElementById('btnAtendidoText');
    const wasAttended = currentConversationAttended;
    
    btn.disabled = true;
    text.textContent = wasAttended ? 'Desmarcando...' : 'Marcando...';
    
    const url = wasAttended 
        ? `/elevenlabs/conversations/${currentConversationId}/unmark-attended`
        : `/elevenlabs/conversations/${currentConversationId}/mark-attended`;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentConversationAttended = data.attended;
            updateAttendedButton(data.attended);
            
            // Recargar la página para actualizar la tabla
            setTimeout(() => location.reload(), 500);
        }
    })
    .catch(e => {
        alert('Error: ' + e.message);
    })
    .finally(() => {
        btn.disabled = false;
    });
}
</script>
@endsection

