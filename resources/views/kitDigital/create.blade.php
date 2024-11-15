@extends('layouts.app')

@section('titulo', 'Añadir nuevo registro')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Añadir nuevo registro</h3>
                    <p class="text-subtitle text-muted">Formulario para añadir un nuevo registro de ayuda</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Panel de usuario</a></li>
                            <li class="breadcrumb-item"><a href="{{route('kitDigital.index')}}">Todas las ayudas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Añadir</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('kitDigital.store') }}" enctype="multipart/form-data" id="ayudaForm">
                        @csrf
                        <input type="hidden" name="admin_user_id" value="{{ $usuario }}" />

                        <div class="row">
                            <!-- Empresa -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="empresa">Empresa</label>
                                <input type="text" class="form-control" id="empresa" name="empresa" value="{{ old('empresa') }}">
                                @if ($errors->has('empresa'))
                                    <div class="alert alert-danger">{{ $errors->first('empresa') }}</div>
                                @endif
                            </div>

                            <!-- Segmento -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="segmento">Segmentos</label>
                                <select class="form-control" id="segmento" name="segmento">
                                    <option value="">Seleccione una opción</option>
                                    <option value="1" {{ old('segmento') == '1' ? 'selected' : '' }}>Segmento 1</option>
                                    <option value="2" {{ old('segmento') == '2' ? 'selected' : '' }}>Segmento 2</option>
                                    <option value="3" {{ old('segmento') == '3' ? 'selected' : '' }}>Segmento 3</option>
                                    <option value="30" {{ old('segmento') == '30' ? 'selected' : '' }}>Segmento 3 Extra</option>
                                    <option value="4" {{ old('segmento') == '4' ? 'selected' : '' }}>Segmento 4</option>
                                    <option value="5" {{ old('segmento') == '5' ? 'selected' : '' }}>Segmento 5</option>
                                    <option value="A" {{ old('segmento') == 'A' ? 'selected' : '' }}>Segmento A</option>
                                    <option value="B" {{ old('segmento') == 'B' ? 'selected' : '' }}>Segmento B</option>
                                    <option value="C" {{ old('segmento') == 'C' ? 'selected' : '' }}>Segmento C</option>
                                </select>
                                @if ($errors->has('segmento'))
                                    <div class="alert alert-danger">{{ $errors->first('segmento') }}</div>
                                @endif
                            </div>

                            <!-- Cliente -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="cliente_id">Cliente</label>
                                <select class="form-control choices" id="cliente_id" name="cliente_id" onchange="setClienteName()">
                                    <option value="">Seleccione una opción</option>
                                  @foreach ($clientes as $cliente)
                                  <option value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                  @endforeach
                                </select>
                                @if ($errors->has('cliente_id'))
                                    <div class="alert alert-danger">{{ $errors->first('cliente_id') }}</div>
                                @endif
                            </div>

                            <!-- Cliente -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="cliente">Nombre</label>
                                <input type="text" class="form-control" id="cliente" name="cliente" value="{{ old('cliente') }}">
                                @if ($errors->has('cliente'))
                                    <div class="alert alert-danger">{{ $errors->first('cliente') }}</div>
                                @endif
                            </div>

                            <!-- Expediente -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="expediente">Expediente</label>
                                <input type="text" class="form-control" id="expediente" name="expediente" value="{{ old('expediente') }}">
                                @if ($errors->has('expediente'))
                                    <div class="alert alert-danger">{{ $errors->first('expediente') }}</div>
                                @endif
                            </div>

                            <!-- Contratos -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="contratos">Contratos</label>
                                <input type="text" class="form-control" id="contratos" name="contratos" value="{{ old('contratos') }}">
                                @if ($errors->has('contratos'))
                                    <div class="alert alert-danger">{{ $errors->first('contratos') }}</div>
                                @endif
                            </div>

                            <!-- Servicios -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="servicio_id">Servicios</label>
                                <select name="servicio_id" id="servicio_id" class="form-control">
                                    <option value="" selected>Selecciona un servicio</option>
                                    @foreach($servicios as $servicio)
                                        <option value="{{ $servicio->id }}" {{ old('servicio_id') == $servicio->id ? 'selected' : '' }}>{{ $servicio->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('servicio_id'))
                                    <div class="alert alert-danger">{{ $errors->first('servicio_id') }}</div>
                                @endif
                            </div>

                            <!-- Estado -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}" style="background-color:{{ $estado->color }}; color:{{ $estado->text_color }}" {{ old('estado') == $estado->id ? 'selected' : '' }}>
                                            {{ $estado->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('estado'))
                                    <div class="alert alert-danger">{{ $errors->first('estado') }}</div>
                                @endif
                            </div>

                            <!-- Fecha de Actualización -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="fecha_actualizacion">Fecha de Actualización</label>
                                <input type="date" id="fecha_actualizacion" name="fecha_actualizacion" class="form-control" value="{{ old('fecha_actualizacion') }}">
                                @if ($errors->has('fecha_actualizacion'))
                                    <div class="alert alert-danger">{{ $errors->first('fecha_actualizacion') }}</div>
                                @endif
                            </div>

                            <!-- Importe -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="importe">Importe</label>
                                <input type="text" id="importe" name="importe" class="form-control" value="{{ old('importe') }}">
                                @if ($errors->has('importe'))
                                    <div class="alert alert-danger">{{ $errors->first('importe') }}</div>
                                @endif
                            </div>

                            <!-- Estado Factura -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="estado_factura">Estado Factura</label>
                                <select name="estado_factura" id="estado_factura" class="form-control">
                                    <option value="">Seleccione estado de la factura</option>
                                    <option style="background-color: #E8EAED; color:black" value="">Sin definir</option>
                                    <option style="background-color: #11734B; color:white" value="1" {{ old('estado_factura') == '1' ? 'selected' : '' }}>Aprobada</option>
                                    <option style="background-color: #B10202; color:white" value="0" {{ old('estado_factura') == '0' ? 'selected' : '' }}>No Aprobada</option>
                                </select>
                                @if ($errors->has('estado_factura'))
                                    <div class="alert alert-danger">{{ $errors->first('estado_factura') }}</div>
                                @endif
                            </div>

                            <!-- En Banco -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="banco">En Banco</label>
                                <input type="date" id="banco" name="banco" class="form-control" value="{{ old('banco') }}">
                            </div>

                            <!-- Gestor -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="gestor">Gestor</label>
                                <select name="gestor" id="gestor" class="form-control">
                                    <option value="">Seleccione un gestor</option>
                                    @foreach($gestores as $gestor)
                                        <option value="{{ $gestor->id }}" {{ old('gestor') == $gestor->id ? 'selected' : '' }}>{{ $gestor->name }} {{ $gestor->surname }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('gestor'))
                                    <div class="alert alert-danger">{{ $errors->first('gestor') }}</div>
                                @endif
                            </div>

                            <!-- Comercial -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="comercial_id">Comercial</label>
                                <select name="comercial_id" id="comercial_id" class="form-control">
                                    <option value="">Seleccione un comercial</option>
                                    @foreach($comerciales as $comercial)
                                        <option value="{{ $comercial->id }}" {{ old('comercial_id') == $comercial->id ? 'selected' : '' }}>{{ $comercial->name }} {{ $comercial->surname }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('comercial_id'))
                                    <div class="alert alert-danger">{{ $errors->first('comercial_id') }}</div>
                                @endif
                            </div>

                            <!-- Fecha del Acuerdo -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="fecha_acuerdo">Fecha del Acuerdo</label>
                                <input type="date" id="fecha_acuerdo" name="fecha_acuerdo" class="form-control" value="{{ old('fecha_acuerdo') }}">
                                @if ($errors->has('fecha_acuerdo'))
                                    <div class="alert alert-danger">{{ $errors->first('fecha_acuerdo') }}</div>
                                @endif
                            </div>

                            <!-- Plazo Máximo de Entrega -->
                            <div class="col-12 col-md-6 form-group mt-2">
                                <label class="form-label" for="plazo_maximo_entrega">Plazo Máximo de Entrega</label>
                                <input type="date" id="plazo_maximo_entrega" name="plazo_maximo_entrega" class="form-control" value="{{ old('plazo_maximo_entrega') }}">
                                @if ($errors->has('plazo_maximo_entrega'))
                                    <div class="alert alert-danger">{{ $errors->first('plazo_maximo_entrega') }}</div>
                                @endif
                            </div>

                            <!-- Comentarios -->
                            <div class="col-12 form-group">
                                <label class="form-label" for="comentario">Comentario</label>
                                <textarea class="form-control" rows="5" id="comentario" name="comentario">{{ old('comentario') }}</textarea>
                            </div>

                            <div class="col-12 form-group">
                                <label class="form-label" for="nuevo_comentario">Nuevo Comentario</label>
                                <textarea class="form-control" rows="5" id="nuevo_comentario" name="nuevo_comentario">{{ old('nuevo_comentario') }}</textarea>
                            </div>

                            <!-- Botón de acción -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-success btn-block w-100">Guardar</button>
                            </div>
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

    const clientes = @json($clientes->pluck('name', 'id'));
    function setClienteName() {
        // Obtener el select de cliente
        var clienteSelect = document.getElementById("cliente_id");
        // Obtener el nombre del cliente seleccionado desde el atributo data-nombre
        var selectedOption = clienteSelect.options[clienteSelect.selectedIndex];
        var nombreCliente = clientes[selectedOption.value]; // Obtener el nombre correspondiente
        console.log(selectedOption);
        console.log(nombreCliente);

        // Asignar el nombre del cliente al campo de texto "Nombre"
        document.getElementById("cliente").value = nombreCliente;
    }

</script>
@endsection
