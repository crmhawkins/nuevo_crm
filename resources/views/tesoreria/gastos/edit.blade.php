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
                        <li class="breadcrumb-item"><a href="{{route('gasto.index')}}">Gastos</a></li>
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
                        <form action="{{ route('gasto.update', $gasto->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="title">Título:</label>
                                    <input type="text" class="form-control" id="title" name="title" value="{{ $gasto->title }}">
                                    @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group my-2">
                                    <label class="form-label"  for="reference">Referencia:</label>
                                    <input type="text" class="form-control" id="reference" name="reference" value="{{ $gasto->reference }}">
                                    @error('reference')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="date">Fecha de recepción:</label>
                                    <input type="date" class="form-control" id="received_date" name="received_date" value="{{ $gasto->received_date }}">
                                    @error('received_date')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="date">Fecha de pago:</label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ $gasto->date }}">
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
                                    <label class="form-label" for="iva">IVA:</label>
                                    <select class="form-select" id="iva" name="iva">
                                        <option value="">IVA</option>
                                        @foreach($tiposIva as $tipo)
                                        <option value="{{ $tipo->valor }}" {{ old('iva', $gasto->iva) == $tipo->valor ? 'selected' : '' }}>
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
                                    <label class="form-label" for="quantity">Cantidad:</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" value="{{old('quantity', $gasto->quantity)}}" >
                                    @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="bank_id">Banco:</label>
                                    <select class="form-select" id="bank_id" name="bank_id">
                                        <option value="">-- Seleccione un Banco --</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ $bank->id == $gasto->bank_id ? 'selected' : '' }}>{{ $bank->name }}</option>
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
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="state">Estado:</label>
                                    <select class="form-select" id="state" name="state">
                                        <option value="PENDIENTE" {{ "PENDIENTE" == $gasto->state ? 'selected' : '' }}>Pendiente</option>
                                        <option value="PAGADO" {{ "PAGADO" == $gasto->state ? 'selected' : '' }}>Pagado</option>
                                    </select>
                                    @error('state')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="payment_method_id">Método de pago:</label>
                                    <select class="form-select" id="payment_method_id" name="payment_method_id">
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}" {{ $method->id == $gasto->payment_method_id ? 'selected' : '' }}>{{ $method->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('payment_method_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label"  for="documents">Documento:</label>
                                    <input type="file" class="form-control" id="documents" name="documents">
                                    @error('documents')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group mt-2">
                                    <label class="form-label mt-4 mr-2" for="transfer_movement">Movimiento de Transferencia:</label>
                                    <input type="checkbox" class="form-check-input mt-4" id="transfer_movement" name="transfer_movement" {{ old('transfer_movement', $gasto->transfer_movement ?? '') ? 'checked' : '' }}>
                                    @error('transfer_movement')
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
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            Acciones
                            <hr>
                        </div>
                        @if(Auth::user()->access_level_id == 1)
                        @endif
                        <button id="actualizar" class="btn btn-primary btn-block mt-3">Actualizar Gasto</button>
                        @if (isset($gasto->documents) && Storage::disk('public')->exists($gasto->documents))
                        <a href="{{ asset('storage/' . $gasto->documents) }}" target="_blank" class="btn btn-dark btn-block mt-3">Ver Documento</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
   function calculateCantidadConIVA() {
        let quantity = parseFloat(document.getElementById('quantity').value) || 0;
        let iva = parseFloat(document.getElementById('iva').value) || 0;

        // Calculate the total amount with IVA
        let quantityWithIVA = quantity + (quantity * (iva / 100));

        // Set the value to the "Cantidad con iva" field
        document.getElementById('quantityIva').value = quantityWithIVA.toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateCantidadConIVA);
    document.getElementById('iva').addEventListener('change', calculateCantidadConIVA);

    $('#actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('form').submit(); // Esto envía el formulario.
    });
    document.addEventListener('DOMContentLoaded', calculateCantidadConIVA);

</script>
@endsection
