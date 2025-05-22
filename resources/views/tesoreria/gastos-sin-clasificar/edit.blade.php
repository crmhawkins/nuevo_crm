@extends('layouts.app')

@section('titulo', 'Editar Gasto Sin Clasificar')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
 <div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Editar Gasto Sin Clasificar</h3>
                <p class="text-subtitle text-muted">Formulario para editar un gasto sin clasificar</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('gasto-asociados.index')}}">Gastos Sin Clasificar</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar Gasto</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="row">
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('gasto-sin-clasificar.update', $unclassifiedExpense->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="title">Título:</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $unclassifiedExpense->company_name }}">
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="quantity">Cantidad:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $unclassifiedExpense->amount }}">
                                    @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="received_date">Fecha de recepción:</label>
                                    <input type="date" class="form-control" id="received_date" name="received_date" value="{{ $unclassifiedExpense->formatted_created_at }}">
                                    @error('received_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="reference">Referencia:</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ $unclassifiedExpense->invoice_number }}">
                                    @error('reference')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="date">Fecha de pago:</label>
                                    <input type="date" class="form-control" id="date" name="date" >
                                    @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="bank_id">Banco:</label>
                                    <select class="form-select" id="bank_id" name="bank_id">
                                        <option value="">-- Seleccione un Banco --</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{($unclassifiedExpense->OrdenCompra->bank_id ?? '')== $bank->id   ? 'selected' : '' }}>{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="purchase_order_id">Orden de compra:</label>
                                    <select class="form-select choices" id="purchase_order_id" name="purchase_order_id">
                                        <option value="">-- Selecciona un Orden de compra --</option>
                                        @if (count($purchaseOrders) > 0)
                                            @foreach($purchaseOrders as $order)
                                                <option value="{{ $order->id }}" {{ $order->id == $unclassifiedExpense->order_number ? 'selected' : '' }}>{{ $order->id }} - {{ $order->concepto->total ?? ''}} €</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('purchase_order_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="state">Estado:</label>
                                    <select class="form-select" id="state" name="state">
                                        <option value="PENDIENTE">Pendiente</option>
                                        <option value="PAGADO">Pagado</option>
                                    </select>
                                    @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="form-label" for="payment_method_id">Método de pago:</label>
                                    <select class="form-select" id="payment_method_id" name="payment_method_id">
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <input type="hidden" id="documents" name="documents" value="{{$unclassifiedExpense->documents}}">
                                <input type="hidden" id="id" name="id" value="{{$unclassifiedExpense->id}}">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card-body p-3">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                    <div class="card-body">
                        <button type="button" class="btn btn-primary btn-block mt-3" onclick="submitForm('gasto')">Crear Gasto</button>
                        <button type="button" class="btn btn-secondary btn-block mt-3" onclick="submitForm('associated')">Crear Gasto Asociado</button>
                        @if (isset($unclassifiedExpense->documents))
                        <a href="{{ asset('storage/' . $unclassifiedExpense->documents) }}" target="_blank" class="btn btn-dark btn-block mt-3">Ver Documento</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>
        function submitForm(type) {
            var form = document.querySelector('form');
            var actionUrl = form.action;

            if (type === 'gasto') {
                actionUrl = "{{ route('gasto.store') }}"; // Ruta para crear un gasto
            } else if (type === 'associated') {
                actionUrl = "{{ route('gasto-asociado.store') }}"; // Ruta para crear un gasto asociado
            }

            form.action = actionUrl;
            form.submit();
        }
    </script>
@endsection
