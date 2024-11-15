@extends('layouts.app')

@section('titulo', 'Editar Presupuesto')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Petición</h3>
                    <p class="text-subtitle text-muted">Formulario para editar una petición</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Peticiones</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar petición</li>
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
                            <form action="{{route('peticion.update', $peticion->id)}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="mb-2 text-left">Cliente Asociado</label>
                                            <div class="flex flex-row align-items-start mb-0">
                                                <select id="cliente" class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
                                                    @if ($clientes->count() > 0)
                                                    <option value="">Seleccione un Cliente</option>
                                                        @foreach ( $clientes as $cliente )
                                                            <option data-id="{{$cliente->id}}" value="{{$cliente->id}}" {{ $peticion->client_id == $cliente->id ? 'selected' : '' }}>{{$cliente->company ?? $cliente->name}}</option>
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
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left">Gestor</label>
                                            <select class="choices form-select w-100 @error('admin_user_id') is-invalid @enderror" name="admin_user_id">
                                                @if ($gestores->count() > 0)
                                                    @foreach ( $gestores as $gestor )
                                                        <option value="{{$gestor->id}}" {{$peticion->admin_user_id == $gestor->id ? 'selected' : '' }}>{{$gestor->name}}</option>
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
                                    <div class="col-md-12 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="note">Nota Interna:</label>
                                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ $peticion->note}}</textarea>
                                            @error('note')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
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
                            <a href="" id="actualizarPresupuesto" class="btn btn-success mb-3 btn-block">Actualizar Peticion</a>
                            <a href="" id="eliminarPeticion"  data-id="{{$peticion->id}}" class="btn btn-outline-danger btn-block mb-3">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @include('partials.toast')

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


        $('#actualizarPresupuesto').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('form').submit(); // Esto envía el formulario.
        });

        $('#eliminarPeticion').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
            botonAceptar(id);
        });

        // Boton añadir cliente
        $('#newClient').click(function(){
            // Abrimos pestaña para crear campaña
            window.open(urlTemplateCliente, '_self');
        });

        $('#deletePresupuesto').on('click', function(e){
            e.preventDefault();
            let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
            botonAceptar(id);
        })

        function botonAceptar(id){
            // Salta la alerta para confirmar la eliminacion
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar esta peticion?",
                html: "<p>Esta acción es irreversible.</p>", // Corrige aquí
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
                // denyButtonText: `No Borrar`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Llamamos a la funcion para borrar el usuario
                    $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                        console.log(data)
                        if (!data.status) {
                            // Si recibimos algun error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            // Todo a ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            })
                            .then(() => {
                                window.location.href = "{{ route('peticion.index') }}";
                            })
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            // Ruta de la peticion
            const url = '{{route("peticion.delete")}}'
            // Peticion
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                },
                dataType: "json"
            });
        }

    });
</script>
@endsection

