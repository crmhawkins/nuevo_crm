@extends('layouts.app')

@section('titulo', 'Editar Dominio')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Dominio</h3>
                    <p class="text-subtitle text-muted">Formulario para editar un dominio</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('dominios.index')}}">Dominios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar dominio</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dominios.update', $dominio->id) }}" method="POST">
                        @csrf

                        <h3 class="mb-2 text-left uppercase">Cliente Asociado</h3>
                        <div class="flex flex-col mb-4">
                            <div class="form-group flex flex-row align-items-center mb-0">
                                <select class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
                                    @if ($clientes->count() > 0)
                                        <option value="{{null}}">--- Seleccione un cliente ---</option>
                                        @foreach ($clientes as $cliente)
                                            <option data-id="{{ $cliente->id }}" value="{{ $cliente->id }}" {{ $dominio->client_id == $cliente->id ? 'selected' : '' }}>{{ $cliente->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No existen clientes todavia</option>
                                    @endif
                                </select>
                            </div>
                            @error('client_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div class="form-group mb-4">
                            <label class="text-uppercase" style="font-weight: bold" for="dominio">Dominio:</label>
                            <input type="text" class="form-control @error('dominio') is-invalid @enderror" id="dominio" value="{{ old('dominio', $dominio->dominio) }}" name="dominio">
                            @error('dominio')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <h3 class="mb-2 text-left uppercase">Estado</h3>
                        <div class="flex flex-col mb-4">
                            <div class="form-group flex flex-row align-items-center mb-0">
                                <select class="w-100 form-select @error('estado_id') is-invalid @enderror" name="estado_id">
                                    @if ($estados->count() > 0)
                                        <option value="{{null}}">--- Seleccione un estado ---</option>
                                        @foreach ($estados as $estado)
                                            <option data-id="{{ $estado->id }}" value="{{ $estado->id }}" {{ $dominio->estado_id == $estado->id ? 'selected' : '' }}>{{ $estado->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">No existen estado todavia</option>
                                    @endif
                                </select>
                            </div>
                            @error('estado_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        {{-- Fecha contratacion --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="date">Fecha de renovacion:</label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" value="{{ old('date_start', \Carbon\Carbon::parse($dominio->date_start)->format('Y-m-d')) }}" name="date">
                            @error('date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Información de Precios e IBAN --}}
                        <h3 class="mb-2 text-left uppercase">Información Financiera</h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-uppercase" style="font-weight: bold" for="precio_compra">Precio de Compra (€):</label>
                                    <input type="number" step="0.01" class="form-control @error('precio_compra') is-invalid @enderror" id="precio_compra" value="{{ old('precio_compra', $dominio->precio_compra) }}" name="precio_compra">
                                    @error('precio_compra')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="text-uppercase" style="font-weight: bold" for="precio_venta">Precio de Venta (€):</label>
                                    <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" value="{{ old('precio_venta', $dominio->precio_venta) }}" name="precio_venta">
                                    @error('precio_venta')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase" style="font-weight: bold" for="iban">IBAN:</label>
                            <input type="text" class="form-control @error('iban') is-invalid @enderror" id="iban" value="{{ old('iban', $dominio->iban) }}" name="iban" placeholder="ES1234567890123456789012">
                            @error('iban')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        @if($dominio->precio_compra && $dominio->precio_venta)
                        <div class="alert alert-info">
                            <h5>📊 Información de Beneficios:</h5>
                            <p><strong>Margen de Beneficio:</strong> €{{ number_format($dominio->margen_beneficio, 2) }}</p>
                            <p><strong>Porcentaje de Margen:</strong> {{ number_format($dominio->porcentaje_margen, 2) }}%</p>
                        </div>
                        @endif

                        @if($dominio->sincronizado)
                        <div class="alert alert-success">
                            <h5>✅ Sincronización:</h5>
                            <p><strong>Estado:</strong> Sincronizado</p>
                            <p><strong>Última sincronización:</strong> {{ $dominio->ultima_sincronizacion_formateada }}</p>
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <h5>⚠️ Sincronización:</h5>
                            <p><strong>Estado:</strong> No sincronizado</p>
                            <p>Los datos de precios e IBAN no están sincronizados con la base externa.</p>
                        </div>
                        @endif

                        {{-- Boton --}}
                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-success w-100 text-uppercase">
                                {{ __('Actualizar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script>

</script>
@endsection
