@extends('layouts.app')

@section('titulo', 'Crear Concepto Propio')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Crear Concepto Propio</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar un concepto propio</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Conceptos Propios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear concepto propio</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('budgetConcepts.storeTypeOwn', $presupuesto->id)}}" method="POST">
                        @csrf

                        {{-- Observaciones --}}
                        <div class="form-group mb-3">
                            <label class="text-uppercase" style="font-weight: bold" for="services_category_id">Categoría:</label>
                            <select class="js-example-basic-single form-control @error('services_category_id') is-invalid @enderror" name="services_category_id" >
                                <option value="{{null}}">Seleccione una categoria</option>

                                @foreach ($categorias as $categoria)
                                    <option value="{{$categoria->id}}">{{$categoria->name}}</option>
                                @endforeach
                            </select>
                            @error('services_category_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Servicios --}}
                        <div class="form-group mb-3">
                            <label class="text-uppercase" style="font-weight: bold" for="service_id">Servicio:</label>
                            <select class="js-example-basic-single form-control @error('service_id') is-invalid @enderror" name="service_id" >
                                <option value="{{null}}">Seleccione una categoria</option>
                            </select>
                            @error('service_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Titulo --}}
                        <div class="form-group mb-3">
                            <label class="text-uppercase" style="font-weight: bold" for="title">Titulo:</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" value="{{ old('title') }}" name="title">
                            @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Concepto --}}
                        <div class="form-group mb-3">
                            <label class="text-uppercase" style="font-weight: bold" for="concept">Concepto:</label>
                            <textarea class="form-control @error('concept') is-invalid @enderror" id="concept" name="concept"></textarea>
                            @error('concept')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Unidades --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="units">Unidades:</label>
                            <input type="double" class="form-control @error('units') is-invalid @enderror" id="units" value="{{ old('units') }}" name="units">
                            @error('units')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Precio --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="sale_price">Precio:</label>
                            <input type="double" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" value="{{ old('sale_price') }}" name="sale_price">
                            @error('sale_price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="precio_medio">Precio  Sugerido:</label>
                            <input type="text" class="form-control" id="precio_medio" readonly>
                        </div>

                        {{-- Total --}}
                        <div class="form-group">
                            <label class="text-uppercase" style="font-weight: bold" for="total">Total:</label>
                            <input type="double" class="form-control @error('total') is-invalid @enderror" id="total" value="{{ old('total') }}" name="total" readonly >
                            @error('total')
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('#precio_medio').closest('.form-group').hide();

        $('.js-example-basic-single').select2();

        // Calcula el total automáticamente
        $('#units, #sale_price').on('input', function() {
            var units = parseFloat($('#units').val()) || 0;
            var price = parseFloat($('#sale_price').val()) || 0;
            var total = units * price;
            $('#total').val(total.toFixed(2)); // Asumiendo dos decimales
        });
    });

    // Escucha el cambio en la selección de categoría
    $('select[name="services_category_id"]').on('change', function() {
        var categoryId = $(this).val();
        var serviceSelect = $('select[name="service_id"]');

        $.ajax({
            url: '/budget-concepts/' + categoryId, // Asegúrate de reemplazar esto por tu URL real
            type: 'GET',
            success: function(data) {
                serviceSelect.empty();
                console.log(data)
                serviceSelect.append('<option value="'+ null +'">Seleccione un servicio</option>');

                $.each(data, function(key, value) {
                    // console.log(key)
                    serviceSelect.append('<option value="'+ value.id +'">'+ value.title +'</option>');
                });
            }
        });
    });

     // Escucha el cambio en la selección de servicio
     $('select[name="service_id"]').on('change', function() {
        var categoryId = $(this).val();

        // Asegúrate de reemplazar '/ruta-a-tu-controlador/servicios-por-categoria' con la ruta correcta
        $.ajax({
            url: '/budget-concepts/category-service', // Reemplaza con tu URL correcta
            type: 'POST',
            data: {
                categoryId: categoryId,
                _token: '{{ csrf_token() }}' // Necesario para solicitudes POST en Laravel
            },
            success: function(data) {
                // Asume que 'data' es el array de servicios y que se selecciona el primero
                if (data.length > 0) {
                    var selectedService = data[0]; // Asume que quieres usar el primer servicio retornado

                    // Actualiza los campos con los datos del servicio seleccionado
                    $('#title').val(selectedService.title);
                    $('#concept').val(selectedService.concept);
                    $('#sale_price').val(selectedService.price);
                    $('#units').val(1);
                    $('#total').val(1*selectedService.price);

                    adjustTextareaHeight(document.getElementById('concept'));

                     // Mostrar precio medio si está disponible
                    if (selectedService.preciomedi > 0) {
                        $('#precio_medio').val(1*selectedService.preciomedi);
                        $('#precio_medio').closest('.form-group').show();
                    } else {
                        $('#precio_medio').closest('.form-group').hide();
                    }
                }
            }
        });
    });

    function adjustTextareaHeight(textarea) {
        textarea.style.height = "auto"; // Resetea el alto antes de calcular el nuevo
        textarea.style.height = textarea.scrollHeight + "px"; // Ajusta al contenido actual
    }


    $(document).ready(function() {
        // Boton añadir campaña
        $('#newCampania').click(function(){
            var clientId = $('select[name="client_id"]').val();
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
            window.open("{{ route('campania.createFromBudget', 0) }}", '_blank');
        });

        // Boton añadir cliente
        $('#newClient').click(function(){

            // Abrimos pestaña para crear campaña
            window.open("{{ route('campania.createFromBudget', 0) }}", '_blank');
        });
    });
</script>
@endsection

