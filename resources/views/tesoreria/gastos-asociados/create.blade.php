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
                <h3>Editar Gasto</h3>
                <p class="text-subtitle text-muted">Formulario para editar un gasto</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('gasto-asociados.index')}}">Gastos</a></li>
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
                        <form action="{{ route('gasto-asociado.store')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6 form-group mt-2">
                                    <label for="title">Título:</label>
                                    <input value="{{old('title')}}" type="text" class="form-control" id="title" name="title">
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="reference">Referencia:</label>
                                    <input value="{{old('reference')}}" type="text" class="form-control" id="reference" name="reference" >
                                    @error('reference')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="received_date">Fecha de recepción:</label>
                                    <input type="date" class="form-control" id="received_date" name="received_date" value="{{old('received_date',Carbon\Carbon::now()->format('Y-m-d'))}}">
                                    @error('received_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="date">Fecha de pago:</label>
                                    <input value="{{old('date')}}" type="date" class="form-control" id="date" name="date" >
                                    @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label" for="categoria_id">Categoria:</label>
                                    <select class="form-select choices" id="categoria_id" name="categoria_id">
                                        <option value="">Categorias</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre}}</option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-group mt-2">
                                    <label for="iva">IVA:</label>
                                    <select class="form-select" id="iva" name="iva">
                                        @foreach($tiposIva as $tipo)
                                            <option value="{{ $tipo->valor }}">
                                                {{ $tipo->nombre}}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('iva')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-group mt-2">
                                    <label for="quantity">Cantidad:</label>
                                    <input value="{{old('quantity')}}" type="number" class="form-control" id="quantity" name="quantity">
                                    @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="bank_id">Banco:</label>
                                    <select class="form-select" id="bank_id" name="bank_id">
                                        <option value="">-- Seleccione un Banco --</option>
                                        @foreach($banks as $bank)
                                            <option {{ old('bank_id',3) == $bank->id ? 'selected' : '' }} value="{{ $bank->id }}">{{ $bank->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('bank_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="quantityIva">Cantidad con iva:</label>
                                    <input type="number" class="form-control" id="quantityIva" disabled  name="quantityIva" value="">
                                    @error('quantityIva')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <input type="hidden" name="total" id="total" value="">
                                <div class="col-md-6 form-group mt-2">
                                    <label for="state">Estado:</label>
                                    <select class="form-select" id="state" name="state">
                                        <option value="PENDIENTE">Pendiente</option>
                                        <option value="PAGADO">Pagado</option>
                                    </select>
                                    @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="purchase_order_id">Orden de compra:</label>
                                    <select class="form-select choices" id="purchase_order_id" name="purchase_order_id">
                                        <option value="">-- Selecciona un Orden de compra --</option>
                                        @if (count($purchaseOrders) > 0)
                                            @foreach($purchaseOrders as $order)
                                                <option data-title="{{ $order->concepto->title ?? '' }}" {{ old('purchase_order_id') == $order->id ? 'selected' : '' }} value="{{ $order->id }}">Nº {{ $order->id }} - {{ $order->concepto->purchase_price ?? '' }} €</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('purchase_order_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="payment_method_id">Método de pago:</label>
                                    <select class="form-select" id="payment_method_id" name="payment_method_id">
                                        @foreach($paymentMethods as $method)
                                            <option {{ old('payment_method_id') == $method->id ? 'selected' : '' }} value="{{ $method->id }}">{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label for="documents">Documento:</label>
                                    <input type="file" class="form-control" id="documents" name="documents">
                                    @error('documents')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
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
                        <button id="actualizar" class="btn btn-success btn-block mt-3">Crear Gasto Asociado</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseOrderSelect = document.getElementById('purchase_order_id');
        const choices = new Choices(purchaseOrderSelect, {
            placeholder: true,
            searchEnabled: true,  // Habilita la búsqueda en el select
            itemSelectText: '',   // Texto vacío para el item seleccionado
        });

        purchaseOrderSelect.addEventListener('change', function() {
            const selectedOption = purchaseOrderSelect.options[purchaseOrderSelect.selectedIndex];
            const title = selectedOption.getAttribute('data-title');  // Obtener el título del concepto
            if (title) {
                titleInput.value = title;  // Actualizar el campo de título
            }
        });
    });
    function calculateCantidadConIVA() {
        let quantity = parseFloat(document.getElementById('quantity').value) || 0;
        let iva = parseFloat(document.getElementById('iva').value) || 0;

        // Calculate the total amount with IVA
        let quantityWithIVA = quantity + (quantity * (iva / 100));

        // Set the value to the "Cantidad con iva" field
        document.getElementById('total').value = quantityWithIVA;
        document.getElementById('quantityIva').value = quantityWithIVA.toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateCantidadConIVA);
    document.getElementById('iva').addEventListener('change', calculateCantidadConIVA);
    document.addEventListener('DOMContentLoaded', calculateCantidadConIVA);

    $('#actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('form').submit(); // Esto envía el formulario.
    });
</script>
@endsection
