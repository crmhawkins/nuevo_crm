@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
    .drag-container {
        display: flex;
        flex-wrap: nowrap; /* Allows horizontal scrolling */
        gap: 20px;
        padding: 20px;
        align-items: flex-start;
        overflow-x: auto; /* Enables horizontal scrolling */
        justify-content: flex-start;
    }
    .drag-column {
        background-color: #f9f9f9;
        border-radius: 8px;
        width: 300px;
        min-width: 300px; /* Ensures consistent width */
        padding: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }
    .drag-column-header {
        font-size: 18px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    .drag-column-content {
        margin-top: 10px;
    }
    .drag-item {
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px 12px;
        margin-bottom: 8px;
        cursor: pointer;
        font-size: 14px;
    }
    .drag-item:hover {
        background-color: #f0f0f0;
    }
    .status-indicator {
        width: 30px;
        height: 8px;
        border-radius: 10px;
        display: inline-block;
        margin-right: 0.4rem;
        margin-top: 0.2rem;
    }
</style>
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-12 col-md-4 order-md-1 order-last">
                <h3>Status Proyectos</h3>
                <p class="text-subtitle text-muted">Listado de proyectos</p>
            </div>
            <div class="col-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Estados de proyectos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="drag-container" id="sortable-container">
            @foreach ($clientes as $cliente)
            <div class="drag-column ui-widget-content">
                <div class="drag-column-header">{{ $cliente->company ?? $cliente->name }}</div>
                <div class="drag-column-content">
                    @foreach ($cliente->presupuestos as $presupuesto)
                    <div class="drag-item" data-toggle="modal" data-target="#modalPresupuesto-{{ $presupuesto->id }}">
                        <p>{{ $presupuesto->reference }}</p>
                        <p>
                            <span class="status-indicator" style="background-color: {{ $presupuesto->getStatusColor() }}"></span>
                            {{ optional($presupuesto->estadoPresupuesto)->name }}
                        </p>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="modalPresupuesto-{{ $presupuesto->id }}" tabindex="-1" aria-labelledby="modalLabel-{{ $presupuesto->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalLabel-{{ $presupuesto->id }}">Details for {{ $presupuesto->reference }}</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Status:</strong> {{ $presupuesto->status }}</p>
                                    <p><strong>Description:</strong> {{ $presupuesto->description }}</p>
                                    <p><strong>Creation Date:</strong> {{ $presupuesto->created_at }}</p>
                                    <p><strong>Due Date:</strong> {{ $presupuesto->due_date }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sortable-container').sortable({
            axis: 'x', // Only allow horizontal dragging
            containment: 'parent' // Contain within the parent element
        });
        $('#sortable-container').disableSelection();

        $('[data-toggle="modal"]').on('click', function() {
            var modalId = $(this).data('target');
            $(modalId).modal('show');
        });
    });
</script>
@endsection
