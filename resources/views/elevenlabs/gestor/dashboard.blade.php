@extends('layouts.elevenlabs')

@section('titulo', 'Gestor ElevenLabs')

@section('css')
<style>
    :root {
        --surface: #ffffff;
        --surface-muted: #f5f7fb;
        --border: rgba(15, 23, 42, 0.08);
        --primary: #2563eb;
        --text-muted: #6b7280;
        --radius-xl: 18px;
    }

    .page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .page-header {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: space-between;
        align-items: flex-end;
    }

    .page-header h1 {
        font-size: 1.9rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .page-header p {
        color: var(--text-muted);
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 10px;
    }

    .header-actions .btn {
        border-radius: 999px;
        padding: 10px 18px;
        font-weight: 600;
    }

    .quick-stats {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .quick-stat {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        padding: 18px 20px;
        box-shadow: 0 12px 36px -30px rgba(15, 23, 42, 0.7);
    }

    .quick-stat span {
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .08em;
        color: var(--text-muted);
    }

    .quick-stat strong {
        display: block;
        font-size: 1.9rem;
        margin-top: 8px;
        color: #111827;
    }

    .workspace {
        display: grid;
        grid-template-columns: minmax(260px, 300px) 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .workspace {
            grid-template-columns: 1fr;
        }
    }

    .card-surface {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: 0 15px 45px -34px rgba(15, 23, 42, 0.6);
    }

    .card-surface header {
        padding: 18px 20px;
        border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    }

    .card-surface header h4 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }

    .card-surface header small {
        display: block;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .campaign-list {
        max-height: 480px;
        overflow-y: auto;
    }

    .campaign-item {
        width: 100%;
        text-align: left;
        border: none;
        border-bottom: 1px solid rgba(226, 232, 240, .7);
        padding: 16px 20px;
        background: transparent;
        transition: background .2s ease;
    }

    .campaign-item:last-child {
        border-bottom: none;
    }

    .campaign-item:hover {
        background: rgba(37, 99, 235, 0.06);
    }

    .campaign-item.active {
        background: rgba(37, 99, 235, 0.14);
        border-left: 3px solid var(--primary);
        padding-left: 17px;
    }

    .campaign-item h5 {
        font-size: 1rem;
        margin-bottom: 4px;
        font-weight: 600;
    }

    .campaign-meta {
        display: flex;
        justify-content: space-between;
        font-size: .8rem;
        color: var(--text-muted);
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 8px;
        font-size: .75rem;
    }

    .pill.pending { background: rgba(37, 99, 235, .12); color: #1d4ed8; }
    .pill.rellamando { background: rgba(234, 179, 8, .16); color: #b45309; }
    .pill.gestionada { background: rgba(16, 185, 129, .12); color: #047857; }

    .empty-state {
        text-align: center;
        padding: 60px 30px;
        color: var(--text-muted);
    }

    .empty-state h3 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .call-summary {
        max-width: 320px;
        white-space: pre-line;
    }

    .modal.clean .modal-content {
        border-radius: 16px;
        border: 1px solid rgba(148, 163, 184, 0.35);
        box-shadow: 0 25px 60px -30px rgba(15, 23, 42, .45);
    }

    .modal.clean label {
        font-size: .85rem;
        font-weight: 600;
        color: #374151;
    }

    textarea.form-control {
        min-height: 130px;
    }

    .hint {
        font-size: .75rem;
        color: var(--text-muted);
    }

    .client-phone-list {
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid rgba(148, 163, 184, 0.3);
        border-radius: 12px;
        padding: 6px;
        background: var(--surface-muted);
    }

    .client-phone-item {
        width: 100%;
        text-align: left;
        border: none;
        background: transparent;
        border-radius: 10px;
        padding: 10px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        transition: background .2s ease;
        font-size: .9rem;
    }

    .client-phone-item:hover {
        background: rgba(37, 99, 235, 0.08);
    }

    .client-phone-item .details {
        color: var(--text-muted);
        font-size: .8rem;
    }

    .client-phone-item .badge {
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<div class="page-wrapper">
    <div class="page-header">
        <div>
            <h1>Campañas ElevenLabs</h1>
        </div>
        <div class="header-actions">
            <button class="btn btn-outline-secondary" id="toggleResolved">Ver gestionadas</button>
            <button class="btn btn-primary" id="openNewCampaignBtn">
                <i class="fa-solid fa-plus me-1"></i> Nueva campaña
            </button>
        </div>
    </div>

    <div class="quick-stats">
        <div class="quick-stat">
            <span>Campañas</span>
            <strong>{{ $stats['total_campaigns'] }}</strong>
        </div>
        <div class="quick-stat">
            <span>Pendientes</span>
            <strong>{{ $stats['pending_calls'] }}</strong>
        </div>
        <div class="quick-stat">
            <span>Gestionadas por ti</span>
            <strong>{{ $stats['managed_calls'] }}</strong>
        </div>
    </div>

    <div class="workspace">
        <div class="card-surface">
            <header>
                <h4>Tus campañas</h4>
                <small>Selecciona una para ver sus llamadas.</small>
            </header>

            @if($campaigns->isEmpty())
                <div class="empty-state">
                    <h3>Todavía no hay campañas</h3>
                    <p>Pulsa “Nueva campaña”, elige agente, pega los teléfonos y listo.</p>
                    <button class="btn btn-primary mt-3" id="emptyStateCreateBtn">Crear campaña</button>
                </div>
            @else
                <div class="campaign-list">
                    @foreach($campaigns as $campaign)
                        <button class="campaign-item"
                                data-campaign="{{ $campaign->id }}"
                                data-calls-url="{{ route('elevenlabs.gestor.campaign.calls', $campaign) }}">
                            <h5>{{ $campaign->name }}</h5>
                            <div class="campaign-meta">
                                <span>{{ $campaign->created_at->format('d M Y · H:i') }}</span>
                                <span>
                                    <span class="pill pending">{{ $campaign->pendientes_count }} pendientes</span>
                                    <span class="pill gestionada">{{ $campaign->gestionadas_count }} gestionadas</span>
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card-surface" id="callsPanel" style="@if($campaigns->isEmpty())display:none;@endif">
            <header class="d-flex flex-wrap justify-content-between align-items-start gap-2" id="callsHeader">
                <div>
                    <h4 id="campaignTitle">Selecciona una campaña</h4>
                    <small id="campaignSubtitle">Las llamadas aparecerán aquí al instante.</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="pill pending" id="pendingCounter">Pendientes: 0</span>
                    <span class="pill rellamando" id="rellamandoCounter">Rellamando: 0</span>
                    <span class="pill gestionada" id="gestionadasCounter">Gestionadas: 0</span>
                </div>
            </header>
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Sentimiento</th>
                        <th>Categoría</th>
                        <th>Resumen</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="callsTableBody">
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            Elige una campaña para ver los detalles de sus llamadas.
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade clean" id="newCampaignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form id="newCampaignForm">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-semibold">Nueva campaña</h5>
                        <small class="text-muted">Elige el agente, define el mensaje y pega los teléfonos.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="newCampaignAlert"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="campaignName" maxlength="255" placeholder="Ej. Seguimiento Kit Digital" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Agente *</label>
                            <select class="form-select" id="campaignAgent" required>
                                <option value="">Cargando agentes...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Número saliente *</label>
                            <select class="form-select" id="campaignPhoneNumber" required disabled>
                                <option value="">Selecciona un agente</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mensaje inicial</label>
                            <textarea class="form-control" id="campaignPrompt" rows="3" placeholder="Hola {nombre}, te llamo de Hawkins..."></textarea>
                            <span class="hint d-block mt-1">Opcional. Se lee al inicio de la llamada.</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Contactos del CRM</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="openClientPickerBtn">
                                <i class="fa-solid fa-users"></i> Seleccionar clientes
                            </button>
                        </div>
                        <span class="hint d-block mb-2">Filtra por nombre, fecha o facturación y marca los clientes que quieras incluir.</span>
                        <div id="selectedClientsSummary" class="small text-muted mb-2">Aún no has añadido teléfonos.</div>
                        <div id="selectedClientsList" class="d-flex flex-column gap-2"></div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Añadir número manualmente</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="manualPhoneInput" placeholder="+34600111222">
                            <button class="btn btn-outline-primary" type="button" id="manualPhoneAddBtn">
                                <i class="fa-solid fa-plus"></i> Añadir
                            </button>
                        </div>
                        <span class="hint d-block mt-1">Usa formato internacional (+34...).</span>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Listado final de teléfonos *</label>
                        <textarea class="form-control" id="campaignPhones" rows="6" placeholder="+34600111222&#10;+34688123456" required></textarea>
                        <span class="hint d-block mt-1">Puedes pegar números directamente; se limpiarán y eliminarán duplicados automáticamente.</span>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submitCampaignBtn">Lanzar campaña</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade clean" id="clientPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-semibold">Seleccionar clientes</h5>
                    <small class="text-muted">Filtra y marca los clientes cuyos teléfonos quieras incluir en la campaña.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-3">
                    <div class="col-lg-3">
                        <label class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="pickerSearch" placeholder="Nombre, empresa o teléfono">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Ordenar por</label>
                        <select class="form-select" id="pickerSort">
                            <option value="recent">Más recientes</option>
                            <option value="oldest">Más antiguos</option>
                            <option value="billing_desc">Facturación (alta a baja)</option>
                            <option value="billing_asc">Facturación (baja a alta)</option>
                            <option value="name">Nombre</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Mostrar</label>
                        <select class="form-select" id="pickerLimit">
                            <option value="15">15</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="150" selected>150</option>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Fecha desde</label>
                        <input type="date" class="form-control" id="pickerDateFrom">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">Fecha hasta</label>
                        <input type="date" class="form-control" id="pickerDateTo">
                    </div>
                    <div class="col-lg-1">
                        <label class="form-label">Fact. mín.</label>
                        <input type="number" class="form-control" id="pickerBillingMin" placeholder="0">
                    </div>
                    <div class="col-lg-1">
                        <label class="form-label">Fact. máx.</label>
                        <input type="number" class="form-control" id="pickerBillingMax" placeholder="">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted" id="pickerResultsCount"></div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="pickerSelectAllBtn">Seleccionar visibles</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="pickerClearBtn">Limpiar selección</button>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="pickerToggleAll">
                            </th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Creado</th>
                            <th>Facturación</th>
                        </tr>
                        </thead>
                        <tbody id="clientPickerBody"></tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="small text-muted" id="pickerPageSummary"></span>
                    <nav>
                        <ul class="pagination pagination-sm mb-0" id="pickerPagination"></ul>
                    </nav>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="pickerApplyBtn">Añadir seleccionados</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const clientApiUrl = "{{ route('elevenlabs.gestor.clients.data') }}";
    const callUpdateUrlTemplate = "{{ route('elevenlabs.gestor.call.update', ['call' => '__CALL__']) }}";
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const toggleResolvedBtn = document.getElementById('toggleResolved');
    const callsPanel = document.getElementById('callsPanel');
    const campaignTitle = document.getElementById('campaignTitle');
    const campaignSubtitle = document.getElementById('campaignSubtitle');
    const callsTableBody = document.getElementById('callsTableBody');
    const pendingCounter = document.getElementById('pendingCounter');
    const rellamandoCounter = document.getElementById('rellamandoCounter');
    const gestionadasCounter = document.getElementById('gestionadasCounter');
    const openNewCampaignBtn = document.getElementById('openNewCampaignBtn');
    const emptyStateCreateBtn = document.getElementById('emptyStateCreateBtn');
    const openClientPickerBtn = document.getElementById('openClientPickerBtn');
    const selectedClientsSummary = document.getElementById('selectedClientsSummary');
    const selectedClientsList = document.getElementById('selectedClientsList');
    const clientPickerModalEl = document.getElementById('clientPickerModal');
    const pickerSearch = document.getElementById('pickerSearch');
    const pickerSort = document.getElementById('pickerSort');
    const pickerLimit = document.getElementById('pickerLimit');
    const pickerDateFrom = document.getElementById('pickerDateFrom');
    const pickerDateTo = document.getElementById('pickerDateTo');
    const pickerBillingMin = document.getElementById('pickerBillingMin');
    const pickerBillingMax = document.getElementById('pickerBillingMax');
    const pickerResultsCount = document.getElementById('pickerResultsCount');
    const pickerSelectAllBtn = document.getElementById('pickerSelectAllBtn');
    const pickerClearBtn = document.getElementById('pickerClearBtn');
    const pickerApplyBtn = document.getElementById('pickerApplyBtn');
    const pickerToggleAll = document.getElementById('pickerToggleAll');
    const pickerPagination = document.getElementById('pickerPagination');
    const pickerPageSummary = document.getElementById('pickerPageSummary');
    const clientPickerBody = document.getElementById('clientPickerBody');
    const newCampaignModalEl = document.getElementById('newCampaignModal');
    const newCampaignForm = document.getElementById('newCampaignForm');
    const newCampaignAlert = document.getElementById('newCampaignAlert');
    const submitCampaignBtn = document.getElementById('submitCampaignBtn');
    const campaignAgentSelect = document.getElementById('campaignAgent');
    const campaignPhoneSelect = document.getElementById('campaignPhoneNumber');
    const campaignNameInput = document.getElementById('campaignName');
    const campaignPromptInput = document.getElementById('campaignPrompt');
    const campaignPhonesInput = document.getElementById('campaignPhones');
    const manualPhoneInput = document.getElementById('manualPhoneInput');
    const manualPhoneAddBtn = document.getElementById('manualPhoneAddBtn');

    const newCampaignModal = new bootstrap.Modal(newCampaignModalEl);
    const clientPickerModal = clientPickerModalEl ? new bootstrap.Modal(clientPickerModalEl) : null;

    let selectedCampaign = null;
    let showingResolved = false;
    let agentsCache = [];
    let phoneCacheByAgent = {};
    const selectedPhones = new Set();
    const selectedMeta = new Map();
    const clientEntryCache = new Map();
    const pickerSelection = new Set();
    const clientFilters = {
        search: '',
        sort: pickerSort ? pickerSort.value : 'recent',
        per_page: pickerLimit ? parseInt(pickerLimit.value, 10) || 50 : 50,
        page: 1,
        date_from: '',
        date_to: '',
        billing_min: '',
        billing_max: '',
    };
    let clientPageData = [];
    let clientPagination = { total: 0, current_page: 1, last_page: 1, from: 0, to: 0 };
    let isFetchingClients = false;

    document.querySelectorAll('.campaign-item').forEach(item => {
        item.addEventListener('click', () => {
            document.querySelectorAll('.campaign-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            selectedCampaign = {
                id: item.dataset.campaign,
                url: item.dataset.callsUrl,
                name: item.querySelector('h5').textContent.trim()
            };
            loadCalls();
        });
    });

    toggleResolvedBtn.addEventListener('click', () => {
        if (!selectedCampaign) return;
        showingResolved = !showingResolved;
        toggleResolvedBtn.classList.toggle('btn-primary', showingResolved);
        toggleResolvedBtn.classList.toggle('btn-outline-secondary', !showingResolved);
        toggleResolvedBtn.textContent = showingResolved ? 'Ver pendientes' : 'Ver gestionadas';
        loadCalls();
    });

    [openNewCampaignBtn, emptyStateCreateBtn].forEach(btn => {
        if (btn) {
            btn.addEventListener('click', () => {
                resetCampaignForm();
                newCampaignModal.show();
            });
        }
    });

    if (openClientPickerBtn && clientPickerModal) {
        openClientPickerBtn.addEventListener('click', () => {
            openClientPicker();
        });
    }

    if (clientPickerModal) {
        pickerSearch.addEventListener('input', () => {
            clientFilters.search = pickerSearch.value.trim();
            clientFilters.page = 1;
            fetchClients();
        });
        pickerSort.addEventListener('change', () => {
            clientFilters.sort = pickerSort.value;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerLimit.addEventListener('change', () => {
            clientFilters.per_page = parseInt(pickerLimit.value, 10) || 50;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerDateFrom.addEventListener('change', () => {
            clientFilters.date_from = pickerDateFrom.value;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerDateTo.addEventListener('change', () => {
            clientFilters.date_to = pickerDateTo.value;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerBillingMin.addEventListener('input', () => {
            clientFilters.billing_min = pickerBillingMin.value;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerBillingMax.addEventListener('input', () => {
            clientFilters.billing_max = pickerBillingMax.value;
            clientFilters.page = 1;
            fetchClients();
        });
        pickerSelectAllBtn.addEventListener('click', () => {
            selectVisibleClients(true);
        });
        pickerClearBtn.addEventListener('click', () => {
            pickerSelection.clear();
            renderClientPicker();
        });
        pickerToggleAll.addEventListener('change', event => {
            selectVisibleClients(event.target.checked);
        });
        clientPickerBody.addEventListener('change', event => {
            if (!event.target.classList.contains('client-picker-checkbox')) return;
            const phone = normalizePhone(event.target.dataset.phone);
            const clientId = Number(event.target.dataset.clientId || 0);
            const key = clientKey(clientId, phone);
            if (!phone) return;
            const entry = clientPageData.find(item => clientKey(item.client_id, item.phone) === key);
            if (entry) {
                clientEntryCache.set(key, entry);
            }
            if (event.target.checked) {
                pickerSelection.add(key);
            } else {
                pickerSelection.delete(key);
            }
            updatePickerToggleState();
        });
        pickerApplyBtn.addEventListener('click', () => {
            let added = 0;
            pickerSelection.forEach(key => {
                const entry = clientEntryCache.get(key);
                if (!entry) return;
                if (addPhone(entry.phone, entry, true)) {
                    added++;
                }
            });
            clientPickerModal.hide();
            if (added > 0) {
                showCampaignAlert('success', `${added} teléfono(s) añadido(s) desde el CRM.`);
            } else {
                showCampaignAlert('info', 'No se añadieron nuevos teléfonos.');
            }
        });
    }

    manualPhoneAddBtn.addEventListener('click', () => {
        const value = manualPhoneInput.value.trim();
        if (!value) {
            showCampaignAlert('warning', 'Introduce un número válido para añadirlo.');
            return;
        }
        if (addPhone(value, { label: 'Manual', billing: 0, client_id: null, name: null, company: null })) {
            manualPhoneInput.value = '';
        }
    });

    manualPhoneInput.addEventListener('keypress', event => {
        if (event.key === 'Enter') {
            event.preventDefault();
            manualPhoneAddBtn.click();
        }
    });

    selectedClientsList.addEventListener('click', event => {
        const trigger = event.target.closest('[data-action="remove-phone"]');
        if (!trigger) return;
        const phone = trigger.dataset.phone;
        if (!phone) return;
        const meta = selectedMeta.get(phone);
        selectedPhones.delete(phone);
        selectedMeta.delete(phone);
        if (meta && meta.client_id) {
            pickerSelection.delete(clientKey(meta.client_id, phone));
        }
        refreshPhoneTextarea();
        refreshSelectedClientsList();
        showCampaignAlert('info', `Número ${phone} eliminado de la campaña.`);
    });

    campaignAgentSelect.addEventListener('change', event => {
        loadPhoneNumbers(event.target.value);
    });

    newCampaignForm.addEventListener('submit', event => {
        event.preventDefault();
        submitCampaign();
    });

    campaignPhonesInput.addEventListener('input', () => {
        if (isFetchingClients) return;
        syncPhonesFromTextarea();
    });

    callsTableBody.addEventListener('click', event => {
        const actionBtn = event.target.closest('[data-call-action]');
        if (!actionBtn) return;
        const action = actionBtn.dataset.callAction;
        const callId = actionBtn.dataset.callId;
        if (!action || !callId) return;
        handleCallAction(callId, action, actionBtn);
    });

    loadAgents();
    syncPhonesFromTextarea();
    refreshSelectedClientsList();

    async function fetchClients() {
        if (!clientPickerModalEl) return;
        if (isFetchingClients) return;
        isFetchingClients = true;
        clientPickerBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4"><span class="spinner-border spinner-border-sm"></span> Cargando clientes...</td></tr>`;
        pickerPagination.innerHTML = '';
        pickerPageSummary.textContent = '';

        const params = new URLSearchParams({
            page: clientFilters.page,
            per_page: clientFilters.per_page,
            sort: clientFilters.sort,
            search: clientFilters.search,
            date_from: clientFilters.date_from,
            date_to: clientFilters.date_to,
            billing_min: clientFilters.billing_min,
            billing_max: clientFilters.billing_max,
        });

        try {
            const response = await fetch(`${clientApiUrl}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar los clientes.');
            }

            const payload = await response.json();
            clientPageData = Array.isArray(payload.data) ? payload.data : [];
            clientPagination = payload.pagination || { total: 0, current_page: 1, last_page: 1, from: 0, to: 0 };
            clientFilters.page = clientPagination.current_page || 1;

            clientPageData.forEach(entry => {
                clientEntryCache.set(clientKey(entry.client_id, entry.phone), entry);
            });

            renderClientPicker();
        } catch (error) {
            console.error(error);
            clientPickerBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">${error.message}</td></tr>`;
            pickerPagination.innerHTML = '';
            pickerPageSummary.textContent = '';
        } finally {
            isFetchingClients = false;
        }
    }

    function openClientPicker() {
        clientFilters.page = 1;
        syncPickerSelectionFromState();
        fetchClients();
        clientPickerModal.show();
    }

    function renderClientPicker() {
        const total = clientPagination.total || 0;
        const from = clientPagination.from || 0;
        const to = clientPagination.to || 0;
        const lastPage = clientPagination.last_page || 1;

        pickerResultsCount.textContent = total === 0
            ? '0 clientes'
            : `${from}-${to} de ${total} clientes`;
        pickerPageSummary.textContent = `Página ${clientPagination.current_page || 1} de ${lastPage}`;

        renderPickerPagination();

        if (!clientPageData.length) {
            clientPickerBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No se encontraron clientes con esos filtros.</td></tr>`;
            pickerToggleAll.checked = false;
            pickerToggleAll.indeterminate = false;
            return;
        }

        clientPickerBody.innerHTML = clientPageData.map(entry => {
            const phone = normalizePhone(entry.phone);
            const key = clientKey(entry.client_id, phone);
            const checked = pickerSelection.has(key) ? 'checked' : '';
            return `
                <tr>
                    <td><input type="checkbox" class="form-check-input client-picker-checkbox" data-phone="${escapeHtml(phone)}" data-client-id="${entry.client_id}" ${checked}></td>
                    <td>
                        <div class="fw-semibold">${escapeHtml(entry.name || 'Sin nombre')}</div>
                        <div class="text-muted small">${escapeHtml(entry.company || '—')}</div>
                    </td>
                    <td>
                        ${escapeHtml(entry.phone || '')}
                        ${entry.label ? `<div class="text-muted small">${escapeHtml(entry.label)}</div>` : ''}
                    </td>
                    <td>${entry.created_at || '—'}</td>
                    <td>${formatCurrency(entry.billing)}</td>
                </tr>
            `;
        }).join('');

        updatePickerToggleState();
    }

    function renderPickerPagination() {
        if (!pickerPagination) return;
        pickerPagination.innerHTML = '';

        const lastPage = Math.max(1, clientPagination.last_page || 1);
        const currentPage = Math.min(lastPage, Math.max(1, clientPagination.current_page || 1));

        const createItem = (label, targetPage, disabled = false, active = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = label;
            if (!disabled && !active) {
                a.addEventListener('click', event => {
                    event.preventDefault();
                    clientFilters.page = Math.max(1, Math.min(lastPage, targetPage));
                    fetchClients();
                });
            }
            li.appendChild(a);
            return li;
        };

        pickerPagination.appendChild(createItem('&laquo; Anterior', currentPage - 1, currentPage <= 1));

        const windowSize = 5;
        let start = Math.max(1, currentPage - Math.floor(windowSize / 2));
        let end = Math.min(lastPage, start + windowSize - 1);
        if (end - start + 1 < windowSize) {
            start = Math.max(1, end - windowSize + 1);
        }

        for (let page = start; page <= end; page++) {
            pickerPagination.appendChild(createItem(page, page, false, page === currentPage));
        }

        pickerPagination.appendChild(createItem('Siguiente &raquo;', currentPage + 1, currentPage >= lastPage));
    }

    function selectVisibleClients(select) {
        if (!clientPageData.length) {
            pickerToggleAll.checked = false;
            pickerToggleAll.indeterminate = false;
            return;
        }

        clientPageData.forEach(entry => {
            const phone = normalizePhone(entry.phone);
            const key = clientKey(entry.client_id, phone);
            if (select) {
                pickerSelection.add(key);
                clientEntryCache.set(key, entry);
            } else {
                pickerSelection.delete(key);
            }
        });

        renderClientPicker();
    }

    function updatePickerToggleState() {
        if (!pickerToggleAll) return;
        if (!clientPageData.length) {
            pickerToggleAll.checked = false;
            pickerToggleAll.indeterminate = false;
            return;
        }

        const keys = clientPageData.map(entry => clientKey(entry.client_id, entry.phone));
        const selectedCount = keys.filter(key => pickerSelection.has(key)).length;
        pickerToggleAll.checked = selectedCount === keys.length;
        pickerToggleAll.indeterminate = selectedCount > 0 && selectedCount < keys.length;
    }

    function syncPickerSelectionFromState() {
        pickerSelection.clear();
        selectedMeta.forEach((meta, phone) => {
            if (!selectedPhones.has(phone)) return;
            if (!meta || meta.client_id === null || meta.client_id === undefined) return;
            pickerSelection.add(clientKey(meta.client_id, phone));
        });
    }

    function addPhone(number, meta = null, silent = false) {
        const normalized = normalizePhone(number);
        if (!normalized) {
            if (!silent) showCampaignAlert('warning', 'Introduce un número válido.');
            return false;
        }
        if (selectedPhones.has(normalized)) {
            if (!silent) showCampaignAlert('info', 'Ese número ya está en la campaña.');
            if (meta && !selectedMeta.has(normalized)) {
                selectedMeta.set(normalized, meta);
            }
            return false;
        }
        selectedPhones.add(normalized);
        if (meta) {
            selectedMeta.set(normalized, meta);
            if (meta.client_id) {
                clientEntryCache.set(clientKey(meta.client_id, normalized), meta);
            }
        } else if (!selectedMeta.has(normalized)) {
            selectedMeta.set(normalized, { billing: 0, client_id: null, name: null, company: null, label: 'Manual' });
        }
        refreshPhoneTextarea();
        refreshSelectedClientsList();
        if (!silent) {
            showCampaignAlert('success', `Número ${normalized} añadido a la campaña.`);
        }
        return true;
    }

    function refreshPhoneTextarea() {
        const values = Array.from(selectedPhones);
        campaignPhonesInput.value = values.join('\n');
    }

    function refreshSelectedClientsList() {
        if (!selectedClientsList || !selectedClientsSummary) return;
        const total = selectedPhones.size;
        let totalBilling = 0;
        selectedMeta.forEach((meta, phone) => {
            if (!selectedPhones.has(phone)) return;
            totalBilling += Number(meta?.billing || 0);
        });
        selectedClientsSummary.textContent = total === 0
            ? 'Aún no has añadido teléfonos.'
            : `${total} número${total === 1 ? '' : 's'} añadidos · ${formatCurrency(totalBilling)}`;

        selectedClientsList.innerHTML = '';

        if (total === 0) {
            const empty = document.createElement('div');
            empty.className = 'text-muted small';
            empty.textContent = 'Todavía no hay contactos seleccionados.';
            selectedClientsList.appendChild(empty);
            return;
        }

        selectedPhones.forEach(phone => {
            const meta = selectedMeta.get(phone) || {};
            const chip = document.createElement('div');
            chip.className = 'd-flex justify-content-between align-items-center bg-light border rounded-pill px-3 py-1';

            const info = document.createElement('div');
            info.className = 'small';

            const title = document.createElement('div');
            title.className = 'fw-semibold';
            title.textContent = meta.name || phone;
            info.appendChild(title);

            const subtitle = document.createElement('div');
            subtitle.className = 'text-muted';
            const details = [];
            if (meta.label) details.push(meta.label);
            if (meta.company) details.push(meta.company);
            details.push(phone);
            if (meta.billing) details.push(formatCurrency(meta.billing));
            subtitle.textContent = details.join(' · ');
            info.appendChild(subtitle);

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-link text-danger btn-sm p-0 ms-3';
            removeBtn.dataset.action = 'remove-phone';
            removeBtn.dataset.phone = phone;
            removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

            chip.appendChild(info);
            chip.appendChild(removeBtn);
            selectedClientsList.appendChild(chip);
        });
    }

    function syncPhonesFromTextarea() {
        const phones = parsePhoneList(campaignPhonesInput.value);
        selectedPhones.clear();
        phones.forEach(phone => {
            selectedPhones.add(phone);
            if (!selectedMeta.has(phone)) {
                selectedMeta.set(phone, { billing: 0, client_id: null, name: null, company: null, label: null });
            }
        });
        selectedMeta.forEach((_, phone) => {
            if (!selectedPhones.has(phone)) {
                selectedMeta.delete(phone);
            }
        });
        refreshSelectedClientsList();
    }

    function parsePhoneList(rawInput) {
        if (!rawInput) return [];
        const separators = /[\n,;]+/;
        const seen = new Set();
        const result = [];
        rawInput
            .split(separators)
            .map(phone => normalizePhone(phone))
            .forEach(phone => {
                if (phone && !seen.has(phone)) {
                    seen.add(phone);
                    result.push(phone);
                }
            });
        return result;
    }

    function buildRecipients(phoneNumbers, firstMessage) {
        return phoneNumbers.map(number => {
            const recipient = { phone_number: number };
            if (firstMessage) {
                recipient.conversation_initiation_client_data = {
                    conversation_config_override: {
                        agent: {
                            first_message: firstMessage
                        }
                    }
                };
            }
            return recipient;
        });
    }

    async function loadAgents() {
        campaignAgentSelect.innerHTML = '<option value="">Cargando agentes...</option>';
        try {
            const response = await fetch('/api/elevenlabs-monitoring/batch-calls/agentes');
            const data = await response.json();
            if (!data.success || !Array.isArray(data.data)) {
                throw new Error('No se pudieron obtener los agentes.');
            }
            agentsCache = data.data;
            campaignAgentSelect.innerHTML = '<option value="">Selecciona un agente...</option>';
            data.data.forEach(agent => {
                const option = document.createElement('option');
                option.value = agent.agent_id;
                option.textContent = agent.name;
                campaignAgentSelect.appendChild(option);
            });
        } catch (error) {
            console.error(error);
            campaignAgentSelect.innerHTML = '<option value="">Error al cargar agentes</option>';
            showCampaignAlert('danger', 'No se pudieron cargar los agentes de ElevenLabs.');
        }
    }

    async function loadPhoneNumbers(agentId) {
        campaignPhoneSelect.disabled = true;
        campaignPhoneSelect.innerHTML = '<option value="">Cargando números...</option>';

        if (!agentId) {
            campaignPhoneSelect.innerHTML = '<option value="">Selecciona agente primero...</option>';
            return;
        }

        if (phoneCacheByAgent[agentId]) {
            populatePhoneSelect(phoneCacheByAgent[agentId]);
            return;
        }

        try {
            const response = await fetch(`/api/elevenlabs-monitoring/batch-calls/agentes/${agentId}/phone-numbers`);
            const data = await response.json();
            if (!data.success || !Array.isArray(data.data)) {
                throw new Error('Sin números disponibles para este agente.');
            }
            phoneCacheByAgent[agentId] = data.data;
            populatePhoneSelect(data.data);
        } catch (error) {
            console.error(error);
            campaignPhoneSelect.innerHTML = '<option value="">Error al cargar números</option>';
            showCampaignAlert('danger', error.message || 'No se pudieron obtener los números del agente.');
        }
    }

    function populatePhoneSelect(phoneNumbers) {
        campaignPhoneSelect.innerHTML = '<option value="">Selecciona un número...</option>';
        if (!phoneNumbers.length) {
            campaignPhoneSelect.innerHTML = '<option value="">Sin números configurados</option>';
            return;
        }
        phoneNumbers.forEach(phone => {
            const option = document.createElement('option');
            option.value = phone.phone_number_id;
            option.dataset.phoneNumber = phone.phone_number;
            let label = phone.label || phone.phone_number;
            if (phone.assigned_agent_name) {
                label += ` → ${phone.assigned_agent_name}`;
            }
            if (phone.provider) {
                label += ` (${phone.provider})`;
            }
            option.textContent = label;
            campaignPhoneSelect.appendChild(option);
        });
        campaignPhoneSelect.disabled = false;
    }

    function clientKey(clientId, phone) {
        return `${clientId || 0}::${normalizePhone(phone)}`;
    }

    function normalizePhone(number) {
        if (!number) return '';
        let cleaned = number.toString().trim();
        cleaned = cleaned.replace(/[-()]/g, '').replace(/\s+/g, '');
        if (cleaned.startsWith('00')) {
            cleaned = '+' + cleaned.slice(2);
        }
        return cleaned;
    }

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[c]));
    }

    function formatCurrency(value) {
        const number = Number(value || 0);
        return number.toLocaleString('es-ES', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 });
    }

    function resetCampaignForm() {
        newCampaignAlert.innerHTML = '';
        newCampaignForm.reset();
        campaignPhoneSelect.innerHTML = '<option value="">Selecciona un agente</option>';
        campaignPhoneSelect.disabled = true;
        selectedPhones.clear();
        selectedMeta.clear();
        pickerSelection.clear();
        refreshPhoneTextarea();
        refreshSelectedClientsList();
        if (clientPickerModalEl) {
            pickerSearch.value = '';
            pickerSort.value = 'recent';
            pickerLimit.value = '150';
            pickerDateFrom.value = '';
            pickerDateTo.value = '';
            pickerBillingMin.value = '';
            pickerBillingMax.value = '';
            clientFilters.search = '';
            clientFilters.sort = 'recent';
            clientFilters.per_page = 150;
            clientFilters.page = 1;
            clientFilters.date_from = '';
            clientFilters.date_to = '';
            clientFilters.billing_min = '';
            clientFilters.billing_max = '';
        }
    }

    async function submitCampaign() {
        const callName = campaignNameInput.value.trim();
        const agentId = campaignAgentSelect.value;
        const phoneNumberId = campaignPhoneSelect.value;
        const phoneNumber = campaignPhoneSelect.selectedOptions[0]?.dataset.phoneNumber || null;
        const prompt = campaignPromptInput.value.trim();
        const phoneNumbers = Array.from(selectedPhones);

        if (!callName || !agentId || !phoneNumberId || phoneNumbers.length === 0) {
            showCampaignAlert('danger', 'Completa todos los campos obligatorios y añade al menos un teléfono.');
            return;
        }

        submitCampaignBtn.disabled = true;
        submitCampaignBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Enviando...';
        showCampaignAlert('info', 'Enviando campaña a ElevenLabs, espera unos segundos...');

        const recipients = buildRecipients(phoneNumbers, prompt);

        const payload = {
            call_name: callName,
            agent_id: agentId,
            agent_phone_number_id: phoneNumberId,
            recipients,
        };

        if (phoneNumber) {
            payload.agent_phone_number = phoneNumber;
        }

        try {
            const response = await fetch('/api/elevenlabs-monitoring/batch-calls/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Error al enviar la campaña a ElevenLabs.');
            }

            showCampaignAlert('success', 'Campaña enviada correctamente. En segundos aparecerá en el listado.');
            setTimeout(() => {
                newCampaignModal.hide();
                window.location.reload();
            }, 1500);

        } catch (error) {
            console.error(error);
            showCampaignAlert('danger', error.message || 'No se pudo enviar la campaña.');
        } finally {
            submitCampaignBtn.disabled = false;
            submitCampaignBtn.innerHTML = 'Lanzar campaña';
        }
    }

    function loadCalls() {
        if (!selectedCampaign) return;

        callsPanel.style.display = '';
        campaignTitle.textContent = selectedCampaign.name;
        campaignSubtitle.textContent = showingResolved ? 'Mostrando llamadas gestionadas.' : 'Mostrando llamadas pendientes.';
        callsTableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    Cargando llamadas...
                </td>
            </tr>
        `;

        const url = new URL(selectedCampaign.url, window.location.origin);
        url.searchParams.set('resolved', showingResolved ? '1' : '0');

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('No se pudieron obtener las llamadas');
                }
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error('Respuesta inesperada del servidor');
                }
                renderCalls(data.calls);
            })
            .catch(error => {
                console.error(error);
                callsTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-5">
                            No se pudieron cargar las llamadas. Inténtalo más tarde.
                        </td>
                    </tr>
                `;
            });
    }

    function renderCalls(calls) {
        if (!Array.isArray(calls) || calls.length === 0) {
            callsTableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        ${showingResolved ? 'No hay llamadas gestionadas para esta campaña.' : 'No hay llamadas pendientes para esta campaña.'}
                    </td>
                </tr>
            `;
            updateCallCounters([]);
            return;
        }

        callsTableBody.innerHTML = calls.map(call => renderCallRow(call)).join('');
        updateCallCounters(calls);
    }

    function renderCallRow(call) {
        const phone = escapeHtml(call.phone_number || '—');
        const sentiment = call.sentiment_label || call.sentiment;
        const category = call.specific_label || call.specific_category;
        const highlightClass = call.status === 'rellamando' ? 'bg-warning-subtle' : call.status === 'gestionada' ? 'table-success' : '';
        const actions = showingResolved ? '' : `
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-outline-secondary" data-call-action="rellamar" data-call-id="${call.id}">
                    ${call.status === 'rellamando' ? '<i class="fa-solid fa-rotate-left"></i> Marcar como pendiente' : '<i class="fa-solid fa-phone"></i> Rellamar'}
                </button>
                <button type="button" class="btn btn-primary" data-call-action="gestionar" data-call-id="${call.id}">
                    <i class="fa-solid fa-clipboard-check"></i> Gestionar
                </button>
            </div>
        `;

        return `
            <tr class="${highlightClass}">
                <td>
                    <div class="fw-semibold">${phone}</div>
                    ${call.custom_prompt ? '<div class="text-muted small">Prompt personalizado</div>' : ''}
                </td>
                <td>${getStatusBadge(call.status)}</td>
                <td>${sentiment ? `<span class="badge text-bg-info">${escapeHtml(sentiment)}</span>` : '<span class="text-muted">—</span>'}</td>
                <td>${category ? `<span class="badge text-bg-secondary">${escapeHtml(category)}</span>` : '<span class="text-muted">—</span>'}</td>
                <td>${buildCallSummary(call.summary)}</td>
                <td class="text-end">${actions}</td>
            </tr>
        `;
    }

    function buildCallSummary(summary) {
        if (!summary) {
            return '<span class="text-muted">Sin resumen</span>';
        }
        const clean = summary.trim();
        const truncated = clean.length > 220 ? `${clean.slice(0, 217)}…` : clean;
        return `<div class="small">${escapeHtml(truncated)}</div>`;
    }

    function getStatusBadge(status) {
        switch (status) {
            case 'rellamando':
                return '<span class="badge text-bg-warning text-dark">Rellamando</span>';
            case 'gestionada':
                return '<span class="badge text-bg-success">Gestionada</span>';
            case 'pendiente':
            default:
                return '<span class="badge text-bg-secondary">Pendiente</span>';
        }
    }

    function updateCallCounters(calls) {
        let pendientes = 0;
        let rellamando = 0;
        let gestionadas = 0;

        calls.forEach(call => {
            if (call.status === 'gestionada') {
                gestionadas += 1;
            } else if (call.status === 'rellamando') {
                rellamando += 1;
            } else {
                pendientes += 1;
            }
        });

        pendingCounter.textContent = `Pendientes: ${pendientes}`;
        rellamandoCounter.textContent = `Rellamando: ${rellamando}`;
        gestionadasCounter.textContent = `Gestionadas: ${gestionadas}`;
    }

    async function handleCallAction(callId, action, button) {
        const url = callUpdateUrlTemplate.replace('__CALL__', callId);
        toggleActionLoading(button, true);

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action })
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'No se pudo actualizar el estado de la llamada.');
            }
            showToast('success', action === 'gestionar' ? 'Llamada gestionada.' : 'Estado actualizado.');
            loadCalls();
        } catch (error) {
            console.error(error);
            showToast('error', error.message || 'Error al actualizar la llamada.');
        } finally {
            toggleActionLoading(button, false);
        }
    }

    function toggleActionLoading(button, loading) {
        if (!button) return;
        if (loading) {
            button.dataset.originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        } else {
            if (button.dataset.originalHtml) {
                button.innerHTML = button.dataset.originalHtml;
                delete button.dataset.originalHtml;
            }
            button.disabled = false;
        }
    }

    function showToast(icon, title) {
        if (typeof Swal === 'undefined') {
            console.log(title);
            return;
        }
        Swal.fire({
            toast: true,
            icon,
            title,
            position: 'top-end',
            timer: 1800,
            showConfirmButton: false,
            timerProgressBar: true
        });
    }

    function showCampaignAlert(type, message) {
        if (!newCampaignAlert) return;
        newCampaignAlert.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        `;
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadCalls();
    });
</script>
@endsection

