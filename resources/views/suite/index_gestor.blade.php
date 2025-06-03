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
                <div class="card bg-white shadow">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Justificaciones</span>
                            <select id="tipoSelect" class="form-control w-auto">
                                <option value="">Seleccione tipo</option>
                                <option value="crm">CRM</option>
                                <option value="erp">ERP</option>
                                <option value="logs">Logs</option>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('tipoSelect');
    const buscador = document.getElementById('buscador');
    const tabla = document.getElementById('tablaArchivos');

    if (!select || !buscador || !tabla) {
        console.error('Elementos del DOM no encontrados');
        return;
    }

    // Búsqueda por nombre (una sola vez)
    buscador.addEventListener('input', () => {
        const valor = buscador.value.toLowerCase();
        Array.from(tabla.children).forEach(tr => {
            tr.style.display = tr.children[0].textContent.toLowerCase().includes(valor) ? '' : 'none';
        });
        console.log(valor);
    });

    // Carga archivos al cambiar el tipo
    select.addEventListener('change', function () {
        const tipo = this.value;
        if (!tipo) {
            tabla.innerHTML = '<tr><td colspan="2" class="text-center">Seleccione un tipo de archivo</td></tr>';
            return;
        }

        // Mostrar loading
        tabla.innerHTML = '<tr><td colspan="2" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></td></tr>';

        fetch(`/api/suite/archivos/${tipo}`)
            .then(res => {
                if (!res.ok) {
                    console.log(res);
                    throw new Error('Error al cargar los archivos');
                }
                return res.json();
            })
            .then(data => {
                if (data.length === 0) {
                    tabla.innerHTML = '<tr><td colspan="2" class="text-center">No hay archivos disponibles</td></tr>';
                    return;
                }

                tabla.innerHTML = '';
                data.forEach(file => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${file.nombre}</td>
                        <td><a href="${file.url}" class="btn btn-sm btn-primary" download>Descargar</a></td>
                    `;
                    tabla.appendChild(tr);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                tabla.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error al cargar los archivos</td></tr>';
            })
            .finally(() => {
                buscador.value = '';
            });
    });
});
</script>
@endsection

