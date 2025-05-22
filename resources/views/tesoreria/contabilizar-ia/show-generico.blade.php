@extends('layouts.app')

@section('titulo', 'Contabilizar IA')

@section('content')

<div class="page-heading card" style="box-shadow: none !important">
        <div class="bg-image overflow-hidden mb-10">
            <div class="content content-narrow content-full">
                <div class="text-center mt-5 mb-2">
                    <div class="mt-4 row d-flex justify-content-center ">
                        <div class="col-6 mb-3">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<div class="text-center mt-5 mb-2">
    <div class="table-responsive">
        <table id="genericTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ref.</th>
                    <th>Descripción</th>
                    <th>Importe</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($relacion))
                <tr>
                    <td>{{ $relacion->id }}</td>
                    <td>{{ $relacion->reference ?? $relacion->invoice_number ?? 'N/A' }}</td>
                    <td>{{ $relacion->description ?? $relacion->concept ?? $relacion->title ?? $relacion->note ?? 'N/A' }}</td>
                    <td>{{ $relacion->amount ?? $relacion->quantity ?? $relacion->total ?? 'N/A' }} €</td>
                    <td>{{ $relacion->date ?? $relacion->paid_date ?? $relacion->creation_date ?? 'N/A' }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#genericTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        },
        pageLength: 25
    });
});

function showDetails(type, id) {
    // Implementar lógica para mostrar detalles
    console.log('Mostrar detalles de ' + type + ' con ID: ' + id);
}
</script>

</div>
@endsection
@section('scripts')

    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css"
        rel="stylesheet">

    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js">
    </script>

    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection
