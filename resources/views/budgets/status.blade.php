@extends('layouts.app')

@section('titulo', 'Cola de trabajo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<style>
    .user-card {
        width: 100%;
        height: auto;
        overflow: hidden;
        margin-bottom: 20px;
        cursor: move;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    @media (min-width: 576px) {
        .user-card {
            width: calc(50% - 1rem);
        }
    }
    @media (min-width: 992px) {
        .user-card {
            width: calc(25% - 1rem);
        }
    }
    .card-body {
        position: relative;
        padding: 15px;
        display: flex;
        flex-direction: column;
    }
    .card-title {
        font-size: 1.25rem;
        margin-bottom: .5rem;
        font-weight: 600;
    }
    .table-responsive {
        flex-grow: 1;
    }
    .tables {
        overflow-y: auto;
    }
    .table {
        margin-bottom: 0;
    }
    .presupuesto-header {
        font-size: 1rem;
        padding: 0.5rem 0;
        cursor: pointer;
        border-bottom: 1px solid #ddd;
    }
    .presupuesto-header:hover {
        background-color: #f8f9fa;
    }
    .presupuesto-details {
        display: none;
        padding: 0.75rem 1.25rem;
        background-color: #f1f1f1;
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
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Estados de proyectos</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section d-flex flex-wrap justify-content-between mt-4" id="sortable-container">
        @foreach ($clientes as $cliente)
            @if(count($cliente->presupuestosPorEstado(3)) > 0)
                <div class="card user-card" id="cliente-{{$cliente->id}}">
                    <div class="card-body">
                        <p class="card-title">{{$cliente->name}}</p>
                        <div class="tables">
                            @foreach ($cliente->presupuestosPorEstado(3) as $presupuesto)
                                <div class="presupuesto-header" data-presupuesto-id="{{$presupuesto->id}}">
                                    {{$presupuesto->reference}} - {{$presupuesto->usuario ? $presupuesto->usuario->name.' '.$presupuesto->usuario->surname : 'Gestor no encontrado'}}
                                </div>
                                <div class="presupuesto-details" id="presupuesto-details-{{$presupuesto->id}}">
                                    <p><strong>Estado:</strong> {{$presupuesto->estadoPresupuesto->name ?? 'Estado no encontrado'}}</p>
                                    <p><strong>Descripción:</strong> {{$presupuesto->descripcion ?? 'Sin descripción'}}</p>
                                    <p><strong>Fecha de creación:</strong> {{ $presupuesto->created_at}}</p>
                                    <p><strong>Fecha de entrega:</strong> {{ $presupuesto->fecha_entrega ? $presupuesto->fecha_entrega->format('d/m/Y') : 'Sin fecha' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </section>
</div>
@endsection

@section('scripts')
    @include('partials.toast')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function() {
            // Inicializa la funcionalidad de arrastrar y soltar
            $('#sortable-container').sortable({
                placeholder: "ui-state-highlight",
                update: function(event, ui) {
                    var sortedIDs = $('#sortable-container .user-card').map(function() {
                        return $(this).attr('id').replace('cliente-', '');
                    }).get();

                    // Enviar la nueva orden al servidor
                    $.ajax({
                        url: '{{ route("save.order") }}',
                        method: 'POST',
                        data: {
                            order: sortedIDs,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log('Orden guardado');
                        }
                    });
                }
            });

            // Mostrar/ocultar detalles de presupuesto al hacer clic
            $('.presupuesto-header').click(function() {
                var presupuestoId = $(this).data('presupuesto-id');
                var details = $('#presupuesto-details-' + presupuestoId);

                // Ocultar otros detalles
                $('.presupuesto-details').not(details).slideUp();

                // Alternar visibilidad del actual
                details.slideToggle();
            });
        });
    </script>
@endsection
