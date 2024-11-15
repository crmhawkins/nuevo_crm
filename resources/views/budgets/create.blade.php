@extends('layouts.app')

@section('titulo', 'Crear Presupuesto')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Crear Presupuesto</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar un presupuesto</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Presupuestos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear presupuesto</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('presupuesto.store')}}" method="POST">
                        @csrf
                        @if (isset($petitionId))
                        <input type="text" name="petitionId" value="{{$petitionId}}" hidden>
                        @endif
                        <div class="bloque-formulario">
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    {{-- Cliente model:Client --}}
                                    <div class="form-group">
                                        <label class="mb-2 text-left">Cliente Asociado</label>
                                        <div class="flex flex-row align-items-start mb-0">
                                            <select id="cliente" class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id" >
                                                @if ($clientes->count() > 0)
                                                <option value="">Seleccione un Cliente</option>
                                                    @foreach ( $clientes as $cliente )
                                                        <option @if(old('client_id', $clienteId) == $cliente->id) {{'selected'}} @endif data-id="{{$cliente->id}}" value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                                    @endforeach
                                                @else
                                                    <option value="">No existen clientes todavia</option>
                                                @endif
                                            </select>
                                            <button id="newClient" type="button" class="btn btn-color-1 ml-3" style="height: fit-content" @if(isset($petitionId)){{'disabled'}}@endif><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                        @error('client_id')
                                            <p class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    {{-- Campaña model:Project --}}
                                    <div class="form-group">
                                        <label class="mb-2 text-left">Campañas</label>
                                        <div class="flex flex-row align-items-start mb-0">

                                            <select class="form-select w-100 @error('project_id') is-invalid @enderror" name="project_id"  id="proyecto" @if($campanias != null )@if( $campanias->count() < 0){{'disabled'}} @endif @endif >
                                                @if ($campanias != null)
                                                    @if ($campanias->count() > 0)
                                                        <option value="">Seleccione una Campaña</option>
                                                        @foreach ( $campanias as $campania )
                                                            <option @if(old('project_id', $projectId) == $campania->id) {{'selected'}} @endif value="{{$campania->id}}">{{$campania->name}}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="">No existen campañas todavia</option>
                                                    @endif
                                                @else
                                                    <option value="">No existen campañas todavia</option>
                                                @endif

                                            </select>
                                            <button id="newCampania" type="button" class="btn btn-color-1 ml-3" style="height: fit-content"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                        @error('project_id')
                                            <p class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    {{-- Gestor model:User --}}
                                    <div class="form-group mb-3">
                                        <label class="mb-2 text-left">Gestor</label>
                                        <select class="choices form-select w-100 @error('admin_user_id') is-invalid @enderror" name="admin_user_id" id="gestor">
                                            @if ($gestores->count() > 0)
                                                <option value="">Seleccione gestor</option>
                                                @foreach ( $gestores as $gestor )
                                                    <option {{old('admin_user_id') != null ? (old('admin_user_id') == $gestor->id ? 'selected' : '' ) : ($gestorId != null ? ($gestorId == $gestor->id ? 'selected' : '') : ( Auth::user()->id == $gestor->id ? 'selected' : '')) }}  value="{{$gestor->id}}">{{$gestor->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="{{null}}">No existen gestores todavia</option>
                                            @endif
                                        </select>
                                        @error('admin_user_id')
                                            <p class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    {{-- Comercial model:User --}}
                                    <div class="form-group mb-3">
                                        <label class="mb-2 text-left">Comercial</label>
                                        <select class="choices form-select w-75" name="commercial_id">
                                            @if ($comerciales->count() > 0)
                                            <option value="">S.Comercial</option>
                                                @foreach ( $comerciales as $comercial )
                                                    <option {{old('commercial_id') == $comercial->id ? 'selected' : '' }} value="{{$comercial->id}}">{{$comercial->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="{{null}}">No existen comerciales todavia</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    {{-- Formas de Pago model:PaymentMethod --}}
                                    <div class="form-group mb-3">
                                        <label class="mb-2 text-left">Forma de Pago</label>
                                        <select class="choices form-select w-75" name="payment_method_id">
                                            @if ($formasPago->count() > 0)
                                                @foreach ( $formasPago as $formaPago )
                                                    <option @if ( $formaPago->id === 1 ) {{'selected'}} @endif value="{{$formaPago->id}}">{{$formaPago->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="{{null}}">No existen formas de pago todavia</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    {{-- Concepto --}}
                                    <div class="form-group mb-3">
                                        <label class="mb-2 text-left" for="concept">Titulo:</label>
                                        <input type="text" class="form-control @error('concept') is-invalid @enderror" id="concept" value="{{ old('concept') }}" name="concept">
                                        @error('concept')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    {{-- Observaciones --}}
                                    <div class="form-group">
                                        <label for="description">Observaciones:</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    {{-- Nota --}}
                                    <div class="form-group">
                                        <label for="note">Nota Interna:</label>
                                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ old('note') }}</textarea>
                                        @error('note')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            {{-- Boton --}}
                            <div class="form-group mt-5">
                                <button type="submit" class="btn btn-success w-100 text-uppercase">
                                    {{ __('Registrar') }}
                                </button>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </section>
    </div>
@php
    $peticionexiste = isset($petitionId) ? @json($petitionId) : null;
@endphp
@endsection

@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha512-rMGGF4wg1R73ehtnxXBt5mbUfN9JUJwbk21KMlnLZDJh7BkPmeovBuddZCENJddHYYMkCh9hPFnPmS9sspki8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
    var urlTemplate = "{{ route('campania.createFromBudget', ['cliente' => 'CLIENTE_ID']) }}";
    var urlTemplatePetition = "{{ route('campania.createFromBudgetAndPetition', ['cliente' => 'CLIENTE_ID','petitionid' => 'PETITION_ID']) }}";
    var urlTemplateCliente = "{{ route('cliente.createFromBudget') }}";

</script>

<script>
      $(document).ready(function() {

        var petitionId = @json($peticionexiste);

        // Ahora puedes usar petitionId como necesites
        // Boton añadir campaña
        $('#newCampania').click(function(){
            var clientId = $('select[name="client_id"]').val();
            console.log(clientId)
            if (clientId == '' || clientId == null) {
                // Alerta Toast de error
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                // Lanzamos la alerta
                Toast.fire({
                    icon: "error",
                    title: "Por favor, selecciona un cliente."
                });
                return;
            }

            var finalUrl = '';
            if (petitionId && petitionId !== '') {
                finalUrl = urlTemplatePetition.replace('CLIENTE_ID', clientId).replace('PETITION_ID', petitionId);
            } else {
                finalUrl = urlTemplate.replace('CLIENTE_ID', clientId);
            }

            window.open(finalUrl, '_self');
        });

        // Boton añadir cliente
        $('#newClient').click(function(){
            // Abrimos pestaña para crear campaña
            window.open(urlTemplateCliente, '_self');
        });

        // Ejecutar tanto en el cambio del select como cuando la página carga
        function cargarDatosCliente(clienteId) {
            if (clienteId) {
                // Primera llamada AJAX para obtener los proyectos del cliente
                $.ajax({
                    url: '{{ route("campania.postProjectsFromClient") }}', // Asegúrate de que la URL es correcta
                    type: 'POST',
                    data: {
                        client_id: clienteId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                    },
                    success: function(response) {
                        console.log(response);

                        var select = $('#proyecto');
                        select.empty(); // Limpia las opciones actuales
                        if (response.length === 0) {
                            select.attr("disabled", true);
                            select.append($('<option></option>').attr('value', null).text('No hay campaña de este cliente'));
                        } else {
                            select.append($('<option></option>').attr('value', null).text('Seleccione una Campaña'));
                            $.each(response, function(key, value) {
                                select.append($('<option></option>').attr('value', value.id).text(value.name));
                            });
                            select.attr("disabled", false);
                            select.val({{ old('project_id') }});
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });

            }
        }

        // Ejecutar cuando el cliente cambia
        $('#cliente').on("change", function() {
            var clienteId = $(this).val();
            cargarDatosCliente(clienteId);
        });

        //Ejecutar cuando la página se carga si hay un cliente ya seleccionado
        var clienteIdSeleccionado = $('#cliente').val();
        if ( clienteIdSeleccionado &&  {{ old('project_id') ? 'true' : 'false' }}) {
            cargarDatosCliente(clienteIdSeleccionado);
        }
    });

</script>
@endsection

