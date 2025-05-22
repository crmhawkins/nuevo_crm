@extends('layouts.appWhatsapp')

@section('titulo', 'Clientes')

<style>
    .sortable {
        cursor: pointer;
    }

    .sort-icon {
        font-size: 12px;
        margin-left: 5px;
    }

    .css-96uzu9 {
        z-index: -1 !important;
    }

    #searchResults {
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
    }

    .search-result-item {
        cursor: pointer;
        padding: 8px 12px;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }
</style>

@section('content')
    <!-- Contenido principal -->
    <div class="col-md-10 p-4 bg-white rounded">
        <div class="d-flex justify-content-between mb-4 ">
            <h2 class="mb-0">Lista de contactos</h2>
            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                Recargar contactos
            </button>
        </div>

        <div class="table-responsive">
            <div class="mb-3">
                <input type="text"
                       id="searchInput"
                       class="form-control"
                       placeholder="Buscar contacto..."
                       autocomplete="off"
                       oninput="searchContacts(this.value)">
                <div id="searchResults" class="position-absolute bg-white border rounded shadow-sm w-100" style="display:none"></div>
                <script>
                    function searchContacts(query) {
                        if (query.length > 2) {
                            fetch(`/plataforma/search-contacts?query=${query}`)
                                .then(response => response.json())
                                .then(data => {
                                    const resultsDiv = document.getElementById('searchResults');
                                    resultsDiv.innerHTML = '';
                                    resultsDiv.style.display = 'block';

                                    data.forEach(client => {
                                        const div = document.createElement('div');
                                        div.className = 'search-result-item';
                                        div.textContent = `${client.name} - ${client.phone}`;
                                        div.onclick = () => {
                                            document.getElementById('searchInput').value = client.name;
                                            resultsDiv.style.display = 'none';
                                            // Actualizar la tabla con el resultado
                                            updateTable([client]);
                                        };
                                        resultsDiv.appendChild(div);
                                    });
                                });
                        } else {
                            document.getElementById('searchResults').style.display = 'none';
                        }
                    }

                    function updateTable(clients) {
                        const tbody = document.querySelector('#contactsTable tbody');
                        tbody.innerHTML = '';

                        clients.forEach(client => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${client.id}</td>
                                <td>${client.name}</td>
                                <td>${client.phone}</td>
                                <td>${client.status || ''}</td>
                                <td>${client.sent || ''}</td>
                                <td>
                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contactoModal${client.id}">
                                        Ver Contacto
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }

                    // Cerrar resultados al hacer clic fuera
                    document.addEventListener('click', (e) => {
                        if (!e.target.matches('#searchInput')) {
                            document.getElementById('searchResults').style.display = 'none';
                        }
                    });
                </script>
            </div>
            <table id="contactsTable" class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="sortable" data-column="id">ID <span class="sort-icon">↕</span></th>
                        <th class="sortable" data-column="name">Nombre <span class="sort-icon">↕</span></th>
                        <th>Número</th>
                        <th class="sortable" data-column="status">Estado <span class="sort-icon">↕</span></th>
                        <th>Nº Enviado</th>
                        <th>Ver contacto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clients as $client)
                        <tr>
                            <td>{{ $client->id }}</td>
                            <td>{{ $client->name }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ $client->estado }}</td>
                            <td>{{ $client->sent }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contactoModal{{ $client->id }}">
                                    Ver Contacto
                                </button>

                                <!-- Modal para ver contacto -->
                                <div class="modal fade" id="contactoModal{{ $client->id }}" tabindex="-1" aria-labelledby="contactoModalLabel{{ $client->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="contactoModalLabel{{ $client->id }}">Detalles del Contacto</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <strong>ID:</strong> {{ $client->id }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Nombre:</strong> {{ $client->name }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6">
                                                        <strong>Teléfono:</strong> {{ $client->phone }}
                                                    </div>
                                                    <div class="col-6">
                                                        <strong>Estado:</strong>
                                                        @php
                                                            $whatsappClient = \App\Models\Plataforma\WhatsappContacts::where('client_id', $client->id)->first();
                                                        @endphp
                                                        {{ $whatsappClient ? $whatsappClient->status : 'No registrado' }}
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        @php
                                                            $whatsappClients = \App\Models\Plataforma\WhatsappContacts::where('client_id', $client->id)->get();
                                                            $campanias = [];
                                                            foreach($whatsappClients as $whatsappClient) {
                                                                if ($whatsappClient->campania_id) {
                                                                    $campania = \App\Models\Plataforma\CampaniasWhatsapp::find($whatsappClient->campania_id);
                                                                    if ($campania) {
                                                                        $campanias[] = [
                                                                            'nombre' => $campania->nombre,
                                                                            'sent' => $whatsappClient->sent ?? 0
                                                                        ];
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        <strong>Mensajes Enviados:</strong><br>
                                                        @if(count($campanias) > 0)
                                                            @foreach($campanias as $campania)
                                                                - {{ $campania['nombre'] }} - Enviado {{ $campania['sent'] }} veces<br>
                                                            @endforeach
                                                        @else
                                                            No registrado
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {{ $clients->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('contactsTable');
            const tbody = table.getElementsByTagName('tbody')[0];
            let searchTimeout;

            // Set initial search value from URL
            const urlParams = new URLSearchParams(window.location.search);
            const initialSearch = urlParams.get('search');
            if (initialSearch) {
                searchInput.value = initialSearch;
            }

            function updateTable(data) {
                tbody.innerHTML = '';
                // Remove existing modals
                document.querySelectorAll('[id^="contactoModal"]').forEach(modal => modal.remove());

                data.results.forEach(client => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${client.id}</td>
                        <td>${client.name}</td>
                        <td>${client.phone}</td>
                        <td>${client.status || ''}</td>
                        <td>${client.sent || 0}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#contactoModal${client.id}">
                                Ver Contacto
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);

                    // Create modal for this client
                    const modalHtml = `
                        <div class="modal fade" id="contactoModal${client.id}" tabindex="-1" aria-labelledby="contactoModalLabel${client.id}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="contactoModalLabel${client.id}">Detalles del Contacto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <strong>ID:</strong> ${client.id}
                                            </div>
                                            <div class="col-6">
                                                <strong>Nombre:</strong> ${client.name}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <strong>Teléfono:</strong> ${client.phone}
                                            </div>
                                            <div class="col-6">
                                                <strong>Estado:</strong> ${client.status || 'No registrado'}
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <strong>Mensajes Enviados:</strong><br>
                                                ${client.sent ? `Enviado ${client.sent} veces` : 'No registrado'}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.insertAdjacentHTML('beforeend', modalHtml);
                });

                // Update pagination
                const paginationContainer = document.querySelector('.pagination');
                if (paginationContainer) {
                    paginationContainer.innerHTML = '';

                    // Previous page
                    if (data.pagination.current_page > 1) {
                        const prevLi = document.createElement('li');
                        prevLi.className = 'page-item';
                        prevLi.innerHTML = `<a class="page-link" href="#" data-page="${data.pagination.current_page - 1}">Previous</a>`;
                        paginationContainer.appendChild(prevLi);
                    }

                    // Page numbers
                    for (let i = 1; i <= data.pagination.last_page; i++) {
                        const li = document.createElement('li');
                        li.className = `page-item ${i === data.pagination.current_page ? 'active' : ''}`;
                        li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                        paginationContainer.appendChild(li);
                    }

                    // Next page
                    if (data.pagination.current_page < data.pagination.last_page) {
                        const nextLi = document.createElement('li');
                        nextLi.className = 'page-item';
                        nextLi.innerHTML = `<a class="page-link" href="#" data-page="${data.pagination.current_page + 1}">Next</a>`;
                        paginationContainer.appendChild(nextLi);
                    }

                    // Add click handlers
                    paginationContainer.querySelectorAll('.page-link').forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const page = e.target.dataset.page;
                            const searchTerm = searchInput.value.trim();
                            fetchResults(searchTerm, page);
                        });
                    });
                }
            }

            function fetchResults(searchTerm, page = 1) {
                const url = new URL(window.location.href);
                if (searchTerm) {
                    url.searchParams.set('search', searchTerm);
                } else {
                    url.searchParams.delete('search');
                }
                url.searchParams.set('page', page);
                window.history.pushState({}, '', url);

                fetch(`/plataforma/search?search=${encodeURIComponent(searchTerm)}&page=${page}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results) {
                            updateTable(data);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();

                searchTimeout = setTimeout(() => {
                    fetchResults(searchTerm);
                }, 300);
            });

            // Handle sorting
            const sortableHeaders = document.querySelectorAll('.sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.dataset.column;
                    const rows = Array.from(tbody.getElementsByTagName('tr'));
                    const direction = this.classList.contains('asc') ? -1 : 1;

                    // Update sort direction
                    sortableHeaders.forEach(h => h.classList.remove('asc', 'desc'));
                    this.classList.add(direction === 1 ? 'asc' : 'desc');

                    // Sort rows
                    rows.sort((a, b) => {
                        const aValue = a.cells[this.cellIndex].textContent;
                        const bValue = b.cells[this.cellIndex].textContent;

                        if (column === 'id') {
                            return direction * (parseInt(aValue) - parseInt(bValue));
                        }
                        return direction * aValue.localeCompare(bValue);
                    });

                    // Reorder rows in the table
                    rows.forEach(row => tbody.appendChild(row));
                });
            });

            // Initial load if there's a search term
            if (initialSearch) {
                fetchResults(initialSearch);
            }
        });
    </script>
@endsection
