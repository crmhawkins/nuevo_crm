@extends('layouts.app')

@section('titulo', 'Crear Cliente')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')
{{var_dump($clienteId)}}
    <div class="page-heading">
        <div class="page-title">
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

                        <h3 class="mb-2 text-left uppercase">Cliente Asociado</h3>
                        {{-- Cliente model:Client --}}
                        <div class="flex flex-col mb-1">
                            <div class="form-group flex flex-row align-items-center mb-0">
                                <select id="cliente" class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
                                    @if ($clientes->count() > 0)
                                        @foreach ( $clientes as $cliente )
                                            <option @if($clienteId != null || $clienteId != null) {{'selected'}} @endif data-id="{{$cliente->id}}" value="{{$cliente->id}}">{{$cliente->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="">No existen clientes todavia</option>
                                    @endif
                                </select>

                                <button id="newClient" type="button" class="btn btn-primary mb-4 ml-3">+</button>
                            </div>
                            @error('client_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        {{-- Campaña model:Project --}}
                        <h3 class="mb-2 text-left uppercase">Campañas</h3>
                        <div class="flex flex-col mb-4">
                            <div class="form-group flex flex-row align-items-center mb-0" id="Proyecto">
                                <select class=" form-select w-100 @error('projetc_id') is-invalid @enderror" name="projetc_id" disabled id="proyecto">
                                    @if ($campanias != null)
                                        @if ($campanias->count() > 0)
                                            @foreach ( $campanias as $campania )
                                                <option value="{{$campania->id}}">{{$campania->name}}</option>
                                            @endforeach
                                        @else
                                            <option value="{{null}}">No existen campañas todavia</option>
                                        @endif
                                    @else
                                        <option value="{{null}}">No existen campañas todavia</option>
                                    @endif

                                </select>
                                <button id="newCampania" type="button" class="btn btn-primary  ml-3">+</button>
                            </div>
                            @error('projetc_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        {{-- Gestor model:User --}}
                        <h3 class="mb-2 text-left uppercase">Gestor</h3>
                        <div class="flex flex-col mb-4">
                            <div class="form-group flex flex-row align-items-center mb-0">
                                <select class="choices form-select w-100 @error('admin_user_id') is-invalid @enderror" name="admin_user_id">
                                    @if ($gestores->count() > 0)
                                        @foreach ( $gestores as $gestor )
                                            <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                                        @endforeach
                                    @else
                                        <option value="{{null}}">No existen gestores todavia</option>
                                    @endif
                                </select>
                            </div>
                            @error('admin_user_id')
                                <p class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </p>
                            @enderror
                        </div>
                        <br>
                        {{-- Comercial model:User --}}
                        <h3 class="mb-2 text-left uppercase">Comercial</h3>
                        <div class="form-group flex flex-row align-items-center">
                            <select class="choices form-select w-75" name="commercial_id">
                                @if ($gestores->count() > 0)
                                    @foreach ( $gestores as $gestor )
                                        <option value="{{$gestor->id}}">{{$gestor->name}}</option>
                                    @endforeach
                                @else
                                    <option value="{{null}}">No existen gestores todavia</option>
                                @endif
                            </select>
                        </div>
                        <br>
                        {{-- Formas de Pago model:PaymentMethod --}}
                        <h3 class="mb-2 text-left uppercase">Forma de Pago</h3>
                        <div class="form-group flex flex-row align-items-center">
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
                        <br>
                        {{-- Concepto --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="concept">Concepto:</label>
                            <input type="text" class="form-control @error('concept') is-invalid @enderror" id="concept" value="{{ old('concept') }}" name="concept">
                            @error('concept')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Observaciones --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="description">Observaciones:</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Nota --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="note">Nota Interna:</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ old('note') }}</textarea>
                            @error('note')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Boton --}}
                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Registrar') }}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha512-rMGGF4wg1R73ehtnxXBt5mbUfN9JUJwbk21KMlnLZDJh7BkPmeovBuddZCENJddHYYMkCh9hPFnPmS9sspki8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
    var urlTemplate = "{{ route('campania.createFromBudget', ['cliente' => 'CLIENTE_ID']) }}";
    var urlTemplateCliente = "{{ route('cliente.createFromBudget') }}";

</script>

<script>
    $(document).ready(function() {
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

            // Abrimos pestaña para crear campaña
            var finalUrl = urlTemplate.replace('CLIENTE_ID', clientId);
            window.open(finalUrl, '_self');
        });

        // Boton añadir cliente
        $('#newClient').click(function(){
            // Abrimos pestaña para crear campaña
            window.open(urlTemplateCliente, '_self');
        });

        $('#cliente').on( "change", function(){
            var clienteId = $(this).val();



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

                    var select = $('#proyecto'); // Reemplaza 'tuSelect' con el ID de tu select
                    select.empty(); // Limpia las opciones actuales
                    if (response.length === 0) {
                        select.attr("disabled", true)
                        select.append($('<option></option>').attr('value', null).text('No hay campaña de este cliente'));
                    }else{
                        $.each(response, function(key, value) {
                            select.append($('<option></option>').attr('value', value.id).text(value.name));
                        });
                        select.attr("disabled", false)
                    }
                },
                error: function(xhr, status, error) {
                    // Manejo de errores
                    console.error(error);
                }
            });

        })
    });
</script>
@endsection

