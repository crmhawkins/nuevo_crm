@extends('layouts.app')

@section('titulo', 'Crear Concepto Propio')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    
    .modal-xl {
        max-width: 1200px;
    }
    
    #autoseoModal .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
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
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                {{ __('Registrar') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal AutoSEO (obligatorio para servicio 485) -->
    <div class="modal fade" id="autoseoModal" tabindex="-1" aria-labelledby="autoseoModalLabel" 
         aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="autoseoModalLabel">
                        <i class="fas fa-robot"></i> Configuración de Cliente AutoSEO
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Servicio AutoSEO detectado.</strong> Por favor, completa la información del cliente para configurar el sistema automático de SEO.
                    </div>

                    <div class="row g-3">
                        <!-- Información básica -->
                        <div class="col-12"><h6 class="border-bottom pb-2">Información Básica</h6></div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_client_name" class="form-label">Nombre del Cliente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="autoseo_client_name" name="autoseo_client_name" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_client_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="autoseo_client_email" name="autoseo_client_email" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label for="autoseo_url" class="form-label">URL del Sitio <span class="text-danger">*</span></label>
                            <input type="url" class="form-control" id="autoseo_url" name="autoseo_url" required>
                        </div>

                        <!-- Credenciales WordPress -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Credenciales de WordPress</h6></div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_username" class="form-label">Usuario WordPress <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="autoseo_username" name="autoseo_username" required autocomplete="off">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_password" class="form-label">Contraseña WordPress <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="autoseo_password" name="autoseo_password" required autocomplete="off">
                        </div>

                        <!-- Dirección de la Empresa -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Dirección de la Empresa</h6></div>
                        
                        <div class="col-md-12">
                            <label for="autoseo_company_name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="autoseo_company_name" name="autoseo_company_name">
                        </div>
                        
                        <div class="col-md-12">
                            <label for="autoseo_address_line1" class="form-label">Dirección</label>
                            <input type="text" class="form-control" id="autoseo_address_line1" name="autoseo_address_line1" placeholder="Calle y número">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_locality" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="autoseo_locality" name="autoseo_locality">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_admin_district" class="form-label">Provincia/Región</label>
                            <input type="text" class="form-control" id="autoseo_admin_district" name="autoseo_admin_district">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_postal_code" class="form-label">Código Postal</label>
                            <input type="text" class="form-control" id="autoseo_postal_code" name="autoseo_postal_code">
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_country_region" class="form-label">País</label>
                            <input type="text" class="form-control" id="autoseo_country_region" name="autoseo_country_region" placeholder="ES" maxlength="2">
                        </div>

                        <!-- Contexto Empresarial -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">Contexto Empresarial</h6></div>
                        
                        <div class="col-md-12">
                            <label for="autoseo_company_context" class="form-label">Descripción de la Empresa</label>
                            <textarea class="form-control" id="autoseo_company_context" name="autoseo_company_context" rows="4" 
                                maxlength="2000" placeholder="Describe brevemente qué hace la empresa, a qué se dedica, qué servicios o productos ofrece..."></textarea>
                            <small class="form-text text-muted">
                                <span id="autoseo_context_counter">0 / 2000 caracteres</span>
                            </small>
                        </div>

                        <!-- Configuración Periódica SEO -->
                        <div class="col-12"><h6 class="border-bottom pb-2 mt-3">📅 Configuración Periódica SEO</h6></div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_seo_frequency" class="form-label">Frecuencia</label>
                            <select class="form-control" id="autoseo_seo_frequency" name="autoseo_seo_frequency">
                                <option value="manual" selected>Manual</option>
                                <option value="weekly">Semanal</option>
                                <option value="biweekly">Quincenal</option>
                                <option value="monthly">Mensual</option>
                                <option value="bimonthly">Bimensual</option>
                                <option value="quarterly">Trimestral</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="autoseo_seo_time" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="autoseo_seo_time" name="autoseo_seo_time" value="09:00">
                        </div>
                        
                        <div class="col-md-6" id="autoseo_day_of_month_div" style="display: none;">
                            <label for="autoseo_seo_day_of_month" class="form-label">Día del Mes</label>
                            <select class="form-control" id="autoseo_seo_day_of_month" name="autoseo_seo_day_of_month">
                                <option value="1">1</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15" selected>15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="last">Último día</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6" id="autoseo_day_of_week_div" style="display: none;">
                            <label for="autoseo_seo_day_of_week" class="form-label">Día de la Semana</label>
                            <select class="form-control" id="autoseo_seo_day_of_week" name="autoseo_seo_day_of_week">
                                <option value="monday">Lunes</option>
                                <option value="tuesday">Martes</option>
                                <option value="wednesday">Miércoles</option>
                                <option value="thursday">Jueves</option>
                                <option value="friday" selected>Viernes</option>
                                <option value="saturday">Sábado</option>
                                <option value="sunday">Domingo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="confirmAutoseo">
                        <i class="fas fa-check"></i> Confirmar y Continuar
                    </button>
                </div>
            </div>
        </div>
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


    let autoseoModalShown = false;
    let formCanSubmit = false;

    $(document).ready(function() {
        // Contador de caracteres para contexto empresarial
        $('#autoseo_company_context').on('input keyup', function() {
            const length = $(this).val().length;
            $('#autoseo_context_counter').text(length + ' / 2000 caracteres');
        });

        // Control de campos de frecuencia SEO
        $('#autoseo_seo_frequency').on('change', function() {
            const frequency = $(this).val();
            
            if (frequency === 'manual') {
                $('#autoseo_day_of_month_div').hide();
                $('#autoseo_day_of_week_div').hide();
            } else if (frequency === 'weekly' || frequency === 'biweekly') {
                $('#autoseo_day_of_month_div').hide();
                $('#autoseo_day_of_week_div').show();
            } else { // monthly, bimonthly, quarterly
                $('#autoseo_day_of_month_div').show();
                $('#autoseo_day_of_week_div').hide();
            }
        });

        // Interceptar envío del formulario
        $('form').on('submit', function(e) {
            const serviceId = $('select[name="service_id"]').val();
            
            // Si es servicio 485 o 471 y aún no se mostró el modal
            if (serviceId == 485 || serviceId == 471 || serviceId == 486 && !formCanSubmit) {
                e.preventDefault();
                
                // Mostrar modal solo una vez
                if (!autoseoModalShown) {
                    const autoseoModal = new bootstrap.Modal(document.getElementById('autoseoModal'));
                    autoseoModal.show();
                    autoseoModalShown = true;
                }
                
                return false;
            }
        });

        // Confirmar modal AutoSEO
        $('#confirmAutoseo').click(function() {
            // Validar campos requeridos
            const requiredFields = [
                'autoseo_client_name',
                'autoseo_client_email',
                'autoseo_url',
                'autoseo_username',
                'autoseo_password'
            ];
            
            let allValid = true;
            requiredFields.forEach(field => {
                const input = $('#' + field);
                if (!input.val() || input.val().trim() === '') {
                    input.addClass('is-invalid');
                    allValid = false;
                } else {
                    input.removeClass('is-invalid');
                }
            });
            
            if (!allValid) {
                alert('Por favor, completa todos los campos obligatorios marcados con *');
                return;
            }
            
            // Agregar campos al formulario principal
            const form = $('form');
            requiredFields.forEach(field => {
                const value = $('#' + field).val();
                $('<input>').attr({
                    type: 'hidden',
                    name: field,
                    value: value
                }).appendTo(form);
            });
            
            // Campos opcionales
            const optionalFields = [
                'autoseo_company_name', 'autoseo_address_line1', 'autoseo_locality',
                'autoseo_admin_district', 'autoseo_postal_code', 'autoseo_country_region',
                'autoseo_company_context', 'autoseo_seo_frequency', 'autoseo_seo_day_of_month',
                'autoseo_seo_day_of_week', 'autoseo_seo_time'
            ];
            
            optionalFields.forEach(field => {
                const value = $('#' + field).val();
                if (value) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: field,
                        value: value
                    }).appendTo(form);
                }
            });
            
            // Cerrar modal y permitir envío
            bootstrap.Modal.getInstance(document.getElementById('autoseoModal')).hide();
            formCanSubmit = true;
            
            // Enviar formulario
            form.submit();
        });

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

