@extends('layouts.app')

@section('titulo', 'Gestión de Agentes - Eleven Labs')

@section('css')
<style>
    .agent-card {
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    .agent-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .category-tag {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        margin: 3px;
        color: white;
    }
    .category-tag.default {
        border: 2px solid #fbbf24;
    }
    .suggested-category {
        background: #f3f4f6;
        border: 2px dashed #d1d5db;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <h3><i class="fas fa-robot"></i> Gestión de Agentes IA</h3>
                <p class="text-subtitle text-muted">Configura categorías personalizadas por agente</p>
            </div>
            <div class="col-12 col-md-6">
                <div class="float-end">
                    <a href="{{ route('elevenlabs.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Agentes -->
    <div class="card-body">
        <div class="row">
            @foreach($agents as $agent)
            <div class="col-md-6">
                <div class="agent-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5><i class="fas fa-robot"></i> {{ $agent->name }}</h5>
                            <small class="text-muted">ID: {{ $agent->agent_id }}</small>
                        </div>
                        @if($agent->description && $agent->categories->where('is_default', false)->count() > 0)
                            <button class="btn btn-sm btn-success" onclick="editAgent('{{ $agent->agent_id }}', '{{ $agent->name }}')">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        @else
                            <button class="btn btn-sm btn-primary" onclick="configureAgent('{{ $agent->agent_id }}', '{{ $agent->name }}')">
                                <i class="fas fa-cog"></i> Configurar
                            </button>
                        @endif
                    </div>

                    @if($agent->description)
                        <p class="text-muted"><i class="fas fa-info-circle"></i> {{ $agent->description }}</p>
                    @else
                        <p class="text-muted"><i class="fas fa-exclamation-triangle"></i> Sin descripción configurada</p>
                    @endif

                    <div class="mt-3">
                        <strong>Categorías:</strong><br>
                        @if($agent->categories->count() > 0)
                            @foreach($agent->categories as $cat)
                                <span class="category-tag {{ $cat->is_default ? 'default' : '' }}" 
                                      style="background-color: {{ $cat->color }}">
                                    {{ $cat->category_label }}
                                </span>
                            @endforeach
                        @else
                            <span class="badge bg-secondary">Sin categorías configuradas</span>
                        @endif
                    </div>

                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="far fa-clock"></i> Última llamada: 
                            @if($agent->last_call_time_unix_secs)
                                {{ \Carbon\Carbon::createFromTimestamp($agent->last_call_time_unix_secs)->diffForHumans() }}
                            @else
                                Nunca
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal de Configuración de Agente -->
<div class="modal fade" id="agentConfigModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title"><i class="fas fa-cog"></i> Configurar Agente: <span id="agentNameTitle"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Paso 1: Descripción -->
                <div id="step1">
                    <h6><i class="fas fa-info-circle"></i> Paso 1: Descripción del Agente</h6>
                    <p class="text-muted">Describe brevemente qué hace este agente, qué tipo de llamadas atiende, etc.</p>
                    <textarea id="agentDescription" class="form-control" rows="4" placeholder="Ej: Este agente atiende llamadas de reservas de apartamentos turísticos, maneja consultas sobre disponibilidad, precios, check-in/check-out..."></textarea>
                    <div class="mt-3">
                        <button class="btn btn-primary" onclick="generateCategories()">
                            <i class="fas fa-magic"></i> Generar Categorías con IA
                        </button>
                    </div>
                </div>

                <!-- Paso 2: Categorías Sugeridas -->
                <div id="step2" style="display: none;">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i> <strong>Categorías generadas por IA</strong>
                        <p class="mb-0">Las siguientes son sugerencias. Puedes editarlas antes de guardar.</p>
                    </div>

                    <h6>Categorías Obligatorias (fijas)</h6>
                    <div class="row mb-3">
                        <div class="col-md-20pct">
                            <div class="category-tag default" style="background-color: #10B981; display: block; text-align: center; padding: 8px; font-size: 0.85rem;">
                                😊 Contento
                            </div>
                        </div>
                        <div class="col-md-20pct">
                            <div class="category-tag default" style="background-color: #EF4444; display: block; text-align: center; padding: 8px; font-size: 0.85rem;">
                                😞 Descontento
                            </div>
                        </div>
                        <div class="col-md-20pct">
                            <div class="category-tag default" style="background-color: #9CA3AF; display: block; text-align: center; padding: 8px; font-size: 0.85rem;">
                                📵 Sin Respuesta
                            </div>
                        </div>
                        <div class="col-md-20pct">
                            <div class="category-tag default" style="background-color: #DC2626; display: block; text-align: center; padding: 8px; font-size: 0.85rem;">
                                🚫 Baja
                            </div>
                        </div>
                        <div class="col-md-20pct">
                            <div class="category-tag default" style="background-color: #3B82F6; display: block; text-align: center; padding: 8px; font-size: 0.85rem;">
                                📅 Llamada Agendada
                            </div>
                        </div>
                    </div>
                    
                    <style>
                        .col-md-20pct {
                            flex: 0 0 20%;
                            max-width: 20%;
                        }
                    </style>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6>Categorías Personalizadas</h6>
                        <button class="btn btn-sm btn-outline-primary" onclick="addNewCategory()">
                            <i class="fas fa-plus"></i> Agregar Categoría
                        </button>
                    </div>
                    <div id="suggestedCategoriesContainer"></div>

                    <div class="mt-3">
                        <button class="btn btn-secondary" onclick="backToStep1()">
                            <i class="fas fa-arrow-left"></i> Atrás
                        </button>
                        <button class="btn btn-success" onclick="saveCategories()">
                            <i class="fas fa-save"></i> Guardar Configuración
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentAgentId = null;
let suggestedCategories = [];

function configureAgent(agentId, agentName) {
    currentAgentId = agentId;
    document.getElementById('agentNameTitle').textContent = agentName;
    document.getElementById('agentDescription').value = '';
    
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('agentConfigModal'));
    modal.show();
}

function editAgent(agentId, agentName) {
    currentAgentId = agentId;
    document.getElementById('agentNameTitle').textContent = agentName + ' (Editando)';
    
    // Cargar configuración actual
    fetch(`/api/elevenlabs-monitoring/agents/${agentId}/categories`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Filtrar solo las personalizadas
                const customCats = data.categories.filter(c => !c.is_default);
                suggestedCategories = customCats.map(c => ({
                    key: c.category_key,
                    label: c.category_label,
                    description: c.category_description || '',
                    color: c.color
                }));
                
                // Saltar directamente al paso 2
                showSuggestedCategories(suggestedCategories);
                document.getElementById('step1').style.display = 'none';
                document.getElementById('step2').style.display = 'block';
                
                const modal = new bootstrap.Modal(document.getElementById('agentConfigModal'));
                modal.show();
            }
        });
}

function generateCategories() {
    const description = document.getElementById('agentDescription').value;
    
    if (!description || description.length < 10) {
        alert('Por favor, escribe una descripción más detallada (mínimo 10 caracteres)');
        return;
    }

    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando con IA...';
    btn.disabled = true;

    // Guardar descripción primero
    fetch(`/elevenlabs/agents/${currentAgentId}/description`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ description: description })
    })
    .then(r => {
        if (!r.ok) throw new Error('Error ' + r.status);
        return r.json();
    })
    .then(() => {
        // Luego generar categorías
        return fetch(`/elevenlabs/agents/${currentAgentId}/generate-categories`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ description: description })
        });
    })
    .then(r => {
        if (!r.ok) throw new Error('Error ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            suggestedCategories = data.categories;
            showSuggestedCategories(data.categories);
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(e => alert('Error: ' + e))
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}

function showSuggestedCategories(categories) {
    suggestedCategories = categories; // Actualizar array global
    const container = document.getElementById('suggestedCategoriesContainer');
    container.innerHTML = '';

    const availableColors = ['#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#84CC16'];

    suggestedCategories.forEach((cat, index) => {
        container.innerHTML += `
            <div class="suggested-category" id="category_${index}">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Clave</label>
                        <input type="text" class="form-control form-control-sm" id="cat_key_${index}" value="${cat.key}">
                        <small class="text-muted">Sin espacios</small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control form-control-sm" id="cat_label_${index}" value="${cat.label}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control form-control-sm" id="cat_desc_${index}" rows="2">${cat.description || ''}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Color</label>
                        <select class="form-control form-control-sm" id="cat_color_${index}">
                            ${availableColors.map(color => `
                                <option value="${color}" ${cat.color === color ? 'selected' : ''} style="background-color: ${color}; color: white;">
                                    ${color}
                                </option>
                            `).join('')}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Vista Previa</label>
                        <div class="category-tag" id="preview_${index}" style="background-color: ${cat.color}; display: block; text-align: center; padding: 6px; font-size: 0.85rem;">
                            ${cat.label}
                        </div>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button class="btn btn-sm btn-danger w-100" onclick="removeCategory(${index})" title="Eliminar categoría">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Agregar listener para actualizar vista previa
    suggestedCategories.forEach((cat, index) => {
        setTimeout(() => {
            const colorSelect = document.getElementById(`cat_color_${index}`);
            const labelInput = document.getElementById(`cat_label_${index}`);
            if (colorSelect && labelInput) {
                colorSelect.addEventListener('change', () => updatePreview(index));
                labelInput.addEventListener('input', () => updatePreview(index));
            }
        }, 100);
    });
}

function updatePreview(index) {
    const label = document.getElementById(`cat_label_${index}`).value;
    const color = document.getElementById(`cat_color_${index}`).value;
    const preview = document.getElementById(`preview_${index}`);
    if (preview) {
        preview.textContent = label;
        preview.style.backgroundColor = color;
    }
}

function removeCategory(index) {
    if (!confirm('¿Eliminar esta categoría?')) return;
    
    // Eliminar del array
    suggestedCategories.splice(index, 1);
    
    // Re-renderizar
    showSuggestedCategories(suggestedCategories);
}

function addNewCategory() {
    const availableColors = ['#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#84CC16'];
    const randomColor = availableColors[Math.floor(Math.random() * availableColors.length)];
    
    suggestedCategories.push({
        key: 'nueva_categoria',
        label: 'Nueva Categoría',
        description: 'Descripción de cuándo usar esta categoría',
        color: randomColor
    });
    
    showSuggestedCategories(suggestedCategories);
    
    // Scroll al final
    const container = document.getElementById('suggestedCategoriesContainer');
    setTimeout(() => container.lastElementChild?.scrollIntoView({ behavior: 'smooth' }), 100);
}

function backToStep1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
}

function saveCategories() {
    const categories = suggestedCategories.map((cat, index) => {
        return {
            key: document.getElementById(`cat_key_${index}`).value.toLowerCase().replace(/\s+/g, '_').replace(/[áéíóúñ]/g, match => {
                const replacements = {'á':'a','é':'e','í':'i','ó':'o','ú':'u','ñ':'n'};
                return replacements[match] || match;
            }),
            label: document.getElementById(`cat_label_${index}`).value,
            description: document.getElementById(`cat_desc_${index}`).value,
            color: document.getElementById(`cat_color_${index}`).value,
            icon: '',
        };
    });

    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
    btn.disabled = true;

    fetch(`/elevenlabs/agents/${currentAgentId}/categories`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ categories: categories })
    })
    .then(r => {
        if (!r.ok) throw new Error('Error ' + r.status);
        return r.json();
    })
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            bootstrap.Modal.getInstance(document.getElementById('agentConfigModal')).hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(e => alert('Error: ' + e))
    .finally(() => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    });
}
</script>
@endsection

