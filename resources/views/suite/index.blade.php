@extends('layouts.app')

@section('titulo', 'Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />

@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-white shadow position-relative" style="z-index: 10;">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        Usuarios
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('suite.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Crear Usuario
                        </a>
                        <a href="{{ route('suite.edit') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i> Editar Usuario
                        </a>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Último Acceso</th>
                                    <th>Fecha de Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suites as $suite)
                                <tr>
                                    <td>{{ $suite->user }}</td>
                                    <td>
                                        {{ $suite->logged_at ? \Carbon\Carbon::parse($suite->logged_at)->format('d/m/Y H:i') : 'Nunca' }}
                                    </td>
                                    <td>{{ $suite->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr class="my-5">

                <div class="card bg-white shadow">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Justificaciones</span>
                            <select id="tipoSelect" class="form-control w-auto">
                                <option value="">Seleccione tipo</option>
                                <option value="crm">CRM</option>
                                <option value="erp">ERP</option>
                                <option value="facturas">Facturas</option>
                                <option value="fichaje">Fichaje</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="text" id="buscador" class="form-control mb-3" placeholder="Filtrar por nombre...">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre de archivo</th>
                                        <th>Descargar</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaArchivos">
                                    <!-- Dinámico -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('tipoSelect');
    const buscador = document.getElementById('buscador');
    const tabla = document.getElementById('tablaArchivos');

    if (!select || !buscador || !tabla) {
        console.error('Elementos del DOM no encontrados');
        return;
    }

    select.addEventListener('change', function () {
        const tipo = this.value;
        if (!tipo) return;

        fetch(`/suite/archivos/${tipo}`)
            .then(res => res.json())
            .then(data => {
                tabla.innerHTML = '';

                data.forEach(file => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${file.nombre}</td>
                        <td><a href="${file.url}" class="btn btn-sm btn-primary" download>Descargar</a></td>
                    `;
                    tabla.appendChild(tr);
                });

                buscador.value = '';
                buscador.addEventListener('input', () => {
                    const valor = buscador.value.toLowerCase();
                    Array.from(tabla.children).forEach(tr => {
                        tr.style.display = tr.children[0].textContent.toLowerCase().includes(valor) ? '' : 'none';
                    });
                });
            });
    });
});
</script>
@endsection

