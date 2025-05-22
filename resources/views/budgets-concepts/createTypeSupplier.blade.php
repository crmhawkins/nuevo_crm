@extends('layouts.app')

@section('titulo', 'Crear Concepto Proveedor')

@section('css')
{{-- <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" /> --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Crear Concepto de Proveedor</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar un concepto de proveedor</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Conceptos de Proveedor</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear concepto de proveedor</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('budgetConcepts.storeTypeSupplier', $presupuesto->id)}}" method="POST">
                        @csrf

                        {{-- Observaciones --}}
                        <div class="form-group mb-3">
                            <label class="text-uppercase" style="font-weight: bold" for="services_category_id">Categoría:</label>
                            <select class="choices js-example-basic-single form-control @error('services_category_id') is-invalid @enderror" name="services_category_id" >
                                <option value="">Seleccione una categoria</option>

                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('services_category_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->name }}</option>
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
                                <option value="">Seleccione una categoria</option>
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
                            <textarea class="form-control @error('concept') is-invalid @enderror" id="concept" name="concept">{{ old('concept') }}</textarea>
                            @error('concept')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        {{-- Unidades --}}
                        <div class="form-group row align-items-end" id="filaAgregar">
                            <div class="col-6">
                                <label class="text-uppercase" style="font-weight: bold" for="units0">Unidades:</label>
                                <input type="text" class="form-control @error('units.0') is-invalid @enderror" id="units0" value="{{ old('units.0') }}" name="units[]">
                                @error('units.0')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-6">
                                <button id="añadirFilaUnidades" class="btn btn-secondary">Añadir más unidades</button>
                            </div>
                        </div>

                        {{-- Adjunto --}}
                        <div class="form-group mt-3">
                            <label class="text-uppercase" style="font-weight: bold" for="file">Archivo Adjunto:</label>
                            <input type="file" class="form-control" id="file" name="file[]" multiple>
                            @error('file')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        {{-- Enviar Email --}}
                        <div class="row my-5">
                            <div class="col-md-6">
                                <label class="text-uppercase mb-2" style="font-weight: bold; display:block" for="em">Enviar email a todos los proveedores:</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkMail" name="checkMail" value="true" >
                                    <label class="form-check-label" for="checkMail">
                                        Si
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">Enviara el email a la lista de proveedores con los archivos adjuntos de la opcion de arriba.</small>
                                @error('checkMail')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Proveedores --}}
                        <div class="col-12 form-group ">
                            <div class="col-12 form-group">
                                <div class="row">
                                    <label class="text-uppercase" style="font-weight: bold" for="total">Proveedores:</label>
                                    <input id="selectedSupplierId" name="selectedSupplierId" type="hidden">
                                    <div class="col-12 flex flex-row justify-between mb-3">
                                        <div class="input-group list-row-supplier mr-3">
                                            <select id="supplierId1" name="supplierId1" class="@error('supplierId1') is-invalid @enderror choices form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="1" data-show-subtext="true" data-live-search="true">
                                                <option value="">-- Seleccione proveedor--</option>
                                                @if($suppliers)
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ old('supplierId1') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('supplierId1')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <input class="form-control mr-3" id="supplierEmail1" name="supplierEmail1" type="text" placeholder="Email" value="{{ old('supplierEmail1') }}">
                                        <input class="form-control " id="supplierPrice1" placeholder="Formato: 0.00" name="supplierPrice1" value="{{ old('supplierPrice1') }}">
                                    </div>
                                    <div class="col-12 flex flex-row justify-between mb-3">
                                        <div class="input-group list-row-supplier mr-3" >
                                            <select id="supplierId2" name="supplierId2" class="@error('supplierId2') is-invalid @enderror  choices selectpicker select2 form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="2" data-show-subtext="true" data-live-search="true">
                                                <option value="">-- Seleccione proveedor--</option>
                                                @if($suppliers)
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ old('supplierId2') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('supplierId2')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <input  id="supplierEmail2" name="supplierEmail2" type="text" placeholder="Email" class="form-control mr-3" value="{{ old('supplierEmail2') }}">
                                        <input class="form-control"  id="supplierPrice2" placeholder="Formato: 0.00" name="supplierPrice2" value="{{ old('supplierPrice2') }}">

                                        <br>
                                    </div>
                                    <div class="col-12 flex flex-row justify-between mb-3">
                                        <div class="input-group list-row-supplier mr-3" >
                                            <select id="supplierId3" name="supplierId3" class="@error('supplierId3') is-invalid @enderror  choices selectpicker select2 form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="3" data-show-subtext="true" data-live-search="true">
                                                <option value="">-- Seleccione proveedor--</option>
                                                @if($suppliers)
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}" {{ old('supplierId3') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('supplierId3')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                        <input id="supplierEmail3"  name="supplierEmail3" type="text" placeholder="Email" class="form-control mr-3" value="{{ old('supplierEmail3') }}">
                                        <input class="form-control" id="supplierPrice3" placeholder="Formato: 0.00" name="supplierPrice3" value="{{ old('supplierPrice3') }}">

                                        <br>
                                    </div>
                                </div>
                            </div>
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
<style>
    .select2-container--default .select2-selection--single {
        height: 100%;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }
</style>
@endsection

@section('scripts')
{{-- <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    (function($) {
        $.fn.inputFilter = function(inputFilter) {
            return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
            });
        };
    }(jQuery));

    let selectedCategory = '{{ old('services_category_id') }}';
    let selectedService = '{{ old('service_id') }}';

    if (selectedCategory) {
        fetchServices(selectedCategory, selectedService);
    }

    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        $('.choices').select2();
        // Calcula el total automáticamente
        $('#units, #sale_price').on('input', function() {
            var units = parseFloat($('#units').val()) || 0;
            var price = parseFloat($('#sale_price').val()) || 0;
            var total = units * price;
            $('#total').val(total.toFixed(2)); // Asumiendo dos decimales
        });
    });

    // Escucha el cambio en la selección de categoría
    // $('select[name="services_category_id"]').on('change', function() {
    //     var categoryId = $(this).val();
    //     var serviceSelect = $('select[name="service_id"]');

    //     $.ajax({
    //         url: '/budget-concepts/' + categoryId, // Asegúrate de reemplazar esto por tu URL real
    //         type: 'GET',
    //         success: function(data) {
    //             serviceSelect.empty();
    //             console.log(data)
    //             serviceSelect.append('<option value="'+ null +'">Seleccione un servicio</option>');

    //             $.each(data, function(key, value) {
    //                 // console.log(key)
    //                 serviceSelect.append('<option value="'+ value.id +'">'+ value.title +'</option>');
    //             });
    //         }
    //     });
    // });

    $('select[name="services_category_id"]').on('change', function() {
        var categoryId = $(this).val();
        fetchServices(categoryId, null);
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
                    console.log(data)
                    var selectedService = data[0]; // Asume que quieres usar el primer servicio retornado

                    // Actualiza los campos con los datos del servicio seleccionado
                    $('#title').val(selectedService.title);
                    $('#concept').val(selectedService.concept);
                    $('#sale_price').val(selectedService.price);
                    $('#units').val(1);
                    $('#total').val(1*selectedService.price);

                    adjustTextareaHeight(document.getElementById('concept'));

                }
            }
        });
    });

    function adjustTextareaHeight(textarea) {
        textarea.style.height = "auto"; // Resetea el alto antes de calcular el nuevo
        textarea.style.height = textarea.scrollHeight + "px"; // Ajusta al contenido actual
    }

    function fetchServices(categoryId, selectedService) {
        var serviceSelect = $('select[name="service_id"]');
            serviceSelect.append('<option value="">Seleccione un servicio</option>');

        $.ajax({
            url: '/budget-concepts/' + categoryId, // Asegúrate de reemplazar esto por tu URL real
            type: 'GET',
            success: function(data) {
                serviceSelect.empty();
                console.log(data)
                serviceSelect.append('<option value="'+ null +'">Seleccione un servicio</option>');

                $.each(data, function(key, service) {
                    let isSelected = selectedService == service.id ? 'selected' : '';
                    serviceSelect.append(`<option value="${service.id}" ${isSelected}>${service.title}</option>`);
                });
            }
        });
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

        $("#supplierPrice1").inputFilter(function(value) {
            return /^-?\d*[.]?\d{0,2}$/.test(value);
        });
        $("#supplierPrice2").inputFilter(function(value) {
            return /^-?\d*[.]?\d{0,2}$/.test(value);
        });
        $("#supplierPrice3").inputFilter(function(value) {
            return /^-?\d*[.]?\d{0,2}$/.test(value);
        });

        $('.selectSupplier').on('change', function(){
            var supplierID = $(this).val();
            var supplierOptionNumber = $(this).attr("data-supplier-number");
            var appURL = '{{ env('APP_URL') }}';
            if(supplierID && supplierOptionNumber) {
                $.ajax({
                    url: '/suppliers/'+supplierID+'/get-supplier',
                    type:"GET",
                    dataType:"json",
                    success:function(data) {
                        $('#supplierEmail'+supplierOptionNumber).val(data.getSupplier.email);
                    },
                });
            } else {
                $('#supplierEmail'+supplierOptionNumber).empty();
            }
        });
        $('#checkUnidades').on('change', function(){
            if (this.checked) {

            }
        })
    });

    $(document).ready(function() {
        let unidadesCounter = 0; // Inicializa el contador. 1 porque ya existe un input para unidades.

        $('#añadirFilaUnidades').click(function(e){
            e.preventDefault(); // Previene la acción por defecto del botón.

            unidadesCounter++; // Incrementa el contador.

            // Crea un nuevo elemento de input para unidades con el índice actualizado, incluyendo un botón de borrar.
            let newUnidadesInput = `
            <div class="form-group mb-3 col-6 d-flex align-items-end" id="filaAgregar${unidadesCounter}">
                <div class="flex-grow-1">
                    <label class="text-uppercase" style="font-weight: bold" for="units${unidadesCounter}">Otras Unidades:</label>
                    <input type="text" class="form-control" id="units${unidadesCounter}" name="units[]" value="">
                </div>
                <button class="btn btn-danger ml-2" type="button" onclick="removeFila(${unidadesCounter})">Borrar</button>
            </div>
            `;

            // Verifica si existe el elemento anterior para decidir dónde insertar el nuevo.
            if($('#filaAgregar' + (unidadesCounter-1)).length > 0){
                $(newUnidadesInput).insertAfter('#filaAgregar' + (unidadesCounter-1));
            } else {
                // Si no existe el elemento anterior, lo insertas en el lugar específico que desees.
                $(newUnidadesInput).insertAfter('#filaAgregar'); // Asegúrate de tener un contenedor inicial 'filaAgregar' para este caso.
            }
        });
    });

    // Función para eliminar la fila correspondiente
    function removeFila(index) {
        $('#filaAgregar' + index).remove();
    }

</script>
@endsection

