@extends('layouts.app')

@section('titulo', 'Editar Concepto Proveedor')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

<div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Editar Concepto de Proveedor</h3>
                <p class="text-subtitle text-muted">Formulario para editar un concepto de proveedor</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Conceptos de Proveedor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Editar concepto de proveedor</li>
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
                        <form id="formActualizar" action="{{route('budgetConcepts.updateTypeSupplier', $budgetConcept->id)}}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="text-uppercase" style="font-weight: bold" for="services_category_id">Categoría:</label>
                                <select class="js-example-basic-single choices form-control @error('services_category_id') is-invalid @enderror" name="services_category_id" >
                                    <option value="{{null}}">Seleccione una categoria</option>

                                    @foreach ($categorias as $categoria)
                                        <option value="{{$categoria->id}}" {{$budgetConcept->services_category_id == $categoria->id ? 'selected' : ''}}>{{$categoria->name}}</option>
                                    @endforeach
                                </select>
                                @error('services_category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-uppercase" style="font-weight: bold" for="service_id">Servicio:</label>
                                <select class="js-example-basic-single form-control @error('service_id') is-invalid @enderror" name="service_id" >
                                    <option value="{{null}}">Seleccione un servicio</option>
                                        @foreach ($services as $service)
                                            <option value="{{$service->id}}" {{$budgetConcept->service_id == $service->id ? 'selected' : ''}}>{{$service->title}}</option>
                                        @endforeach
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
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" value="{{old('title', $budgetConcept->title)}}" name="title">
                                @error('title')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>

                            {{-- Concepto --}}
                            <div class="form-group mb-3">
                                <label class="text-uppercase" style="font-weight: bold" for="concept">Concepto:</label>
                                <textarea class="form-control @error('concept') is-invalid @enderror" id="concept"  name="concept">{{ old('concept', $budgetConcept->concept) }}</textarea>
                                @error('concept')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>

                            {{-- Unidades --}}
                            <div class="form-group mb-3">
                                <label class="text-uppercase" style="font-weight: bold" for="units">Unidades:</label>
                                <input type="double" class="form-control @error('units') is-invalid @enderror" id="units" value="{{old('units', $budgetConcept->units)}}" name="units">
                                @error('units')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>

                            {{-- Adjunto --}}
                            <div class="form-group">
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
                                    <small class="text-muted d-block">Enviara el email a la lista de proveedores con los archivos adjuntos de la opcion de arriba.</small>
                                    @error('checkMail')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    {{-- Precio --}}
                                    <div class="form-group">
                                        <label class="text-uppercase" style="font-weight: bold" for="purchase_price">Precio:</label>
                                        <input type="double" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" value="{{ old('purchase_price', $budgetConcept->purchase_price ?? 0) }}" name="purchase_price">
                                        @error('purchase_price')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                {{-- Margen --}}
                                <div class="form-group col-md-6">
                                    <label class="text-uppercase" style="font-weight: bold" for="total">Margen %:</label>
                                    <input type="double" class="form-control @error('benefit_margin') is-invalid @enderror" id="benefit_margin" value="{{ old('benefit_margin', $budgetConcept->benefit_margin  ?? 50) }}" name="benefit_margin">
                                    @error('benefit_margin')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                {{-- Total --}}
                                <div class="form-group col-md-6">
                                    <label class="text-uppercase" style="font-weight: bold" for="sale_price">Total (Precio + Margen):</label>
                                    <input type="double" class="form-control @error('sale_price') is-invalid @enderror" id="sale_price" value="{{ old('sale_price', $budgetConcept->sale_price ?? 0) }}" name="sale_price" readonly >
                                    @error('sale_price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 form-group mt-3">
                                <div class="col-12 form-group">
                                    <div class="row">
                                        <label class="text-uppercase" style="font-weight: bold" for="total">Proveedores:</label>
                                        <input id="selectedSupplierId" name="selectedSupplierId" type="hidden">
                                        <div class="col-12 mt-2" >
                                            <div class="input-group list-row-supplier">
                                                <div class="form-check d-flex align-items-center pr-2 pl-0">
                                                    <input id="supplierRadio1" @if($budgetSuppliersSaved[0]->selected == 1) checked=checked @endif type="radio" name="radioOpt" class="form-check-input m-1" style="height: 25px; width: 25px;" value="1">
                                                </div>
                                                <select id="supplierId1" name="supplierId1" class="choices form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="1" data-show-subtext="true" data-live-search="true">
                                                    <option value="">-- Seleccione proveedor--</option>
                                                    @if($suppliers)
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}"  {{$budgetSuppliersSaved->where('option_number',1)->first()->supplier_id == $supplier->id ? 'selected' : ''}}>{{ $supplier->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                &nbsp;&nbsp;<input class="form-control" value="{{old('supplierEmail1',$budgetSuppliersSaved->where('option_number',1)->first()->mail)}}" id="supplierEmail1" name="supplierEmail1" type="text" placeholder="Email" >
                                                &nbsp;&nbsp;<input class="form-control"  value="{{old('supplierPrice1',$budgetSuppliersSaved->where('option_number',1)->first()->price)}}" id="supplierPrice1" placeholder="Formato: 0.00" name="supplierPrice1">
                                            </div>
                                            <br>
                                        </div>
                                        <div class="col-12">
                                            <div class="input-group list-row-supplier" >
                                                <div class="form-check d-flex align-items-center pr-2 pl-0">
                                                    <input id="supplierRadio2" @if($budgetSuppliersSaved[1]->selected == 1) checked=checked @endif type="radio" name="radioOpt" class="form-check-input m-1" style="height: 25px; width: 25px;" value="2">
                                                </div>
                                                <select id="supplierId2" name="supplierId2" class="choices selectpicker select2 form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="2" data-show-subtext="true" data-live-search="true">
                                                    <option value="">-- Seleccione proveedor--</option>
                                                    @if($suppliers)
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}" {{$budgetSuppliersSaved->where('option_number',2)->first()->supplier_id == $supplier->id ? 'selected' : ''}}>{{ $supplier->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                &nbsp;&nbsp;<input  id="supplierEmail2" value="{{old('supplierEmail2',$budgetSuppliersSaved->where('option_number',2)->first()->mail)}}" name="supplierEmail2" type="text" placeholder="Email" class="form-control">
                                                &nbsp;&nbsp;<input class="form-control"  value="{{old('supplierPrice2',$budgetSuppliersSaved->where('option_number',2)->first()->price)}}" id="supplierPrice2" placeholder="Formato: 0.00" name="supplierPrice2">
                                            </div>
                                            <br>
                                        </div>
                                        <div class="col-12">
                                            <div class="input-group list-row-supplier" >
                                                <div class="form-check d-flex align-items-center pr-2 pl-0">
                                                    <input id="supplierRadio3" @if($budgetSuppliersSaved[2]->selected == 1) checked=checked @endif type="radio" name="radioOpt" class="form-check-input m-1" style="height: 25px; width: 25px;" value="3">
                                                </div>
                                                <select id="supplierId3" name="supplierId3" class="choices selectpicker select2 form-control supplier-list-row-select selectSupplier" width="auto" data-supplier-number="3" data-show-subtext="true" data-live-search="true">
                                                    <option value="">-- Seleccione proveedor--</option>
                                                    @if($suppliers)
                                                        @foreach ($suppliers as $supplier)
                                                            <option value="{{ $supplier->id }}" {{$budgetSuppliersSaved->where('option_number',3)->first()->supplier_id == $supplier->id ? 'selected' : ''}}>{{ $supplier->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                &nbsp;&nbsp;<input id="supplierEmail3" value="{{old('supplierEmail3',$budgetSuppliersSaved->where('option_number',3)->first()->mail)}}"  name="supplierEmail3" type="text" placeholder="Email" class="form-control">
                                                &nbsp;&nbsp;<input class="form-control" value="{{old('supplierPrice3',$budgetSuppliersSaved->where('option_number',3)->first()->price)}}" id="supplierPrice3" placeholder="Formato: 0.00" name="supplierPrice3">
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card-body ">
                    <div class="card-title">
                        Acciones
                        <hr>
                    </div>
                    <div class="card-body">
                        <a href="" id="actualizar" class="btn btn-success mt-3 btn-block">Actualizar</a>
                        @if(!$budgetConcept->presupuesto->temp)
                            <a type="button" style="color:white" id="generatePurchaseOrder" class="btn btn-primary mt-3 btn-block">Generar Orden Compra</a>
                            <a id="ordenCompra" style="color:white" class="btn btn-dark mt-3 btn-block" style="display:none"> Enviar Orden de Compra</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <form id ="generatePurchaseOrderForm" method="POST"  action="{{ route('budgetConcepts.generatePurchaseOrder', $budgetConcept->id) }}" class="row">
        @csrf
    </form>

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
 // Boton Actualizar presupuesto
    $('#actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('#formActualizar').submit(); // Esto envía el formulario.
    });
    $("#ordenCompra").hide();

    $(document).ready(function() {
        // Función para verificar el estado de los radios y mostrar/ocultar el botón
        function checkRadiosAndToggleButton() {
            if ($("#supplierRadio1").is(":checked") || $("#supplierRadio2").is(":checked") || $("#supplierRadio3").is(":checked")) {
                $("#ordenCompra").show();
                if($(this).is('#supplierRadio1')){
            // valor submit del marcado
            $('#selectedSupplierId').val(1);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice1').val();
            if( !$('#supplierPrice1').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
        if($(this).is('#supplierRadio2')){
            // valor submit del marcado
            $('#selectedSupplierId').val(2);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice2').val();
            if( !$('#supplierPrice2').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
        if($(this).is('#supplierRadio3')){
            // valor submit del marcado
            $('#selectedSupplierId').val(3);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice3').val();
            if( !$('#supplierPrice3').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
            } else {
                $("#ordenCompra").hide();
            }
        }
        // Llama a la función al cargar la página
        checkRadiosAndToggleButton();
    });

     $( "#supplierRadio1, #supplierRadio2, #supplierRadio3" ).click(function() {
        if($(this).is('#supplierRadio1')){
            // valor submit del marcado
            $('#selectedSupplierId').val(1);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice1').val();
            if( !$('#supplierPrice1').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
        if($(this).is('#supplierRadio2')){
            // valor submit del marcado
            $('#selectedSupplierId').val(2);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice2').val();
            if( !$('#supplierPrice2').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
        if($(this).is('#supplierRadio3')){
            // valor submit del marcado
            $('#selectedSupplierId').val(3);
            // Precio del proveedor seleccionado
            var optPrice= $('#supplierPrice3').val();
            if( !$('#supplierPrice3').val() ){
                $('#purchase_price').val(0);
                $('#total_no_discount').val(0);
            }else{
                $('#purchase_price').val(optPrice);
            }
        }
        // Si hay margen en el input que calcule el precio + margen
        if( $('#benefit_margin').val()  != ''){
            console.log('entra');
            if(optPrice == ''){
                optPrice = 0;
            }

            var units = parseFloat($('#units').val()) || 0;
            var purchasePrice = parseFloat($('#purchase_price').val()) || 0;
            var margin = parseFloat($('#benefit_margin').val()) || 0;
            var marginPercentage = (purchasePrice * margin) / 100;
            var priceMarginResult = (purchasePrice) + (marginPercentage);
            var total =  (priceMarginResult);
            $("#sale_price").val(total.toFixed(2));

        }else{
            var units = $("#units").val();
            var purchasePrice = $('#purchase_price').val();
            var total_no_discount =  parseFloat(purchasePrice) * units;
            var total_no_discount = Math.round(total_no_discount * 100) / 100;
            $("#sale_price").val(total_no_discount);
        }
    });

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

    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
        $('.choices').select2();
        // Calcula el total automáticamente
        $('#units, #purchase_price, #benefit_margin').on('input', function() {
            var units = parseFloat($('#units').val()) || 0;
            var price = parseFloat($('#purchase_price').val()) || 0;
            var margin = parseFloat($('#benefit_margin').val()) || 0;
            var preciodeunidades = units * price;
            var total = price *(1 + margin/100)
            $('#sale_price').val(total.toFixed(2)); // Asumiendo dos decimales
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

    $(document).ready(function() {

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
    });

    $('#generatePurchaseOrder').click(function() {
        Swal.fire({
            type: 'warning',
            title: 'Atención',
            text: "Confirme la generación de la orden de compra de este concepto",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Generar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('{{ route('budgetConcepts.generatePurchaseOrder', $budgetConcept->id) }}', {
                    method: 'POST', // o GET, según tu configuración
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText)
                    }
                    return response.json();
                })
                .catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error}`
                    )
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: '¡Orden Generada!',
                    html: '<a href="' + result.value.entryUrl + '" class="btn btn-dark">Descargar PDF</a>',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    focusConfirm: false,
                    preConfirm: () => window.location.href = result.value.entryUrl
                });
            }
        });
    });





    $('#ordenCompra').click(function() {
        var id = {{ $budgetConcept->id }};
        Swal.fire({
            type: 'warning',
            title: 'Atención',
            html: `
                <p>Confirmar enviar orden de compra</p>
                <form id="sendOrderForm">
                    <label style="text-align:left;" for="attachs">Archivo Adjunto</label>
                    <input type="file" class="form-control" id="attachs" name="attachs[]" multiple><br>
                    <label style="text-align:left;" for="url">Enlaces</label>
                    <input type="text" class="form-control" id="url" name="url" placeholder="Escribe aquí la URL">
                    <!-- Asegúrate de que estos inputs están definidos en tu HTML o los defines aquí -->
                    <input type="hidden" id="idSupplier" name="idSupplier" value="${$("#idSupplier").val()}">
                    <input type="hidden" id="client_empresa" name="client_empresa" value="${$('#client_empresa').val()}">
                    <input type="hidden" id="marca" name="marca" value="${$('#marca').val()}">
                    <input type="hidden" id="telefono" name="telefono" value="${$('#telefono').val()}">
                    <input type="hidden" id="direccion" name="direccion" value="${$('#direccion').val()}">
                    <input type="hidden" id="ciudad" name="ciudad" value="${$('#ciudad').val()}">
                    <input type="hidden" id="provincia" name="provincia" value="${$('#provincia').val()}">
                    <input type="hidden" id="cp" name="cp" value="${$('#cp').val()}">
                    <input type="hidden" id="id" name="id" value="${id}">
                </form>
            `,
            allowEscapeKey: false,
            allowOutsideClick: false,
            allowEnterKey: false,
            showCancelButton: true,
            confirmButtonColor: '#10ba46',
            confirmButtonText: 'Confirmar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    var formData = new FormData(document.getElementById('sendOrderForm'));
                    // Enviar la petición
                    $.ajax({
                        type: 'POST',
                        url: '/budget-concept-supplier/saveOrderForSend',  // Ruta al controlador
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        },
                        data: formData,
                        dataType: "json",
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Éxito', response.message, 'success').then(() => resolve());
                            } else {
                            console.log(response.message);
                                Swal.fire('Error', response.message, 'error').then(() => reject(new Error(response.message)));
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', `Error al procesar la solicitud: ${xhr.statusText}`, 'error').then(() => reject(new Error(xhr.statusText)));
                        }
                    });
                });
            }
        });
    });


    function saveOrderForSend(formData){
        return  $.ajax({
            type: "POST",
            url: '/budget-concept-supplier/saveOrderForSend',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: formData,
            dataType: "json",
            cache: false,
            contentType: false,
            processData: false,
            error :function( data ) {
                if( data.status === 422 ) {
                    swal(
                        'Error',
                        'Mucho peso en los archivos adjuntados.',
                        'error'
                    );
                }
            }
        });
    }
</script>
@endsection

