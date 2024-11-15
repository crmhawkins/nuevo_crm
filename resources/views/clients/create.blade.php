@extends('layouts.app')

@section('titulo', 'Crear Cliente')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body p-3">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3><i class="bi bi-person"></i> Crear Cliente</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar a un cliente/leads.</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear cliente</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('clientes.store')}}" method="POST" class="form-primary">
                        @csrf
                        {{-- <h5 class="titulo-form">Informacion del cliente</h5> --}}
                        <div class="bloque-formulario">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label for="name">Nombre:</label>
                                        <input placeholder="Nombre" type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" name="name">
                                        @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group my-3">
                                        <label for="tipoCliente" class="form-label">Tipo de cliente</label>
                                        <div class="d-flex">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input @error('tipoCliente') is-invalid @enderror" type="radio" name="tipoCliente" id="empresa" value="0"
                                                       {{ old('tipoCliente') == '0' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="empresa">Empresa</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input @error('tipoCliente') is-invalid @enderror" type="radio" name="tipoCliente" id="particular" value="1"
                                                       {{ old('tipoCliente') == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="particular">Particular</label>
                                            </div>
                                        </div>
                                        @error('tipoCliente')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="email">Email de contacto:</label>
                                        <input placeholder="Direccion de correo electronico" type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" name="email">
                                        @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="company">Nombre de la empresa:</label>
                                        <input placeholder="Nombre de la empresa..." type="text" class="form-control @error('company') is-invalid @enderror" id="company" value="{{ old('company') }}" name="company">
                                        @error('company')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group boton-picker mt-2">
                                        <label for="birthdate">Fecha de alta</label>
                                        <input autocomplete="no" placeholder="Fecha de alta como cliente..."  type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" value="{{ old('birthdate', Carbon\Carbon::now()->format('d/m/Y')) }}" name="birthdate">
                                        @error('birthdate')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="identifier">Marca:</label>
                                        <input placeholder="Marca de la empresa..." type="text" class="form-control @error('identifier') is-invalid @enderror" id="identifier" value="{{ old('identifier') }}" name="identifier">
                                        @error('identifier')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                </div>

                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label for="name">Primer apellido</label>
                                        <input placeholder="Primer apellido (Obligatorio en caso de particulares)" type="text" class="form-control @error('primerApellido') is-invalid @enderror" id="primerApellido" value="{{ old('primerApellido') }}" name="primerApellido">
                                        @error('primerApellido')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="activity">Actividad:</label>
                                        <input placeholder="Actividad de la empresa..." type="text" class="form-control @error('activity') is-invalid @enderror" id="activity" value="{{ old('activity') }}" name="activity">
                                        @error('activity')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="phone">Teléfono movil:</label>
                                        <input placeholder="Telefono movil..." type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}" name="phone">
                                        @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="cif">CIF/DNI:</label>
                                        <input placeholder="CIF/DNI" type="text" class="form-control @error('cif') is-invalid @enderror" id="cif" value="{{ old('cif') }}" name="cif">
                                        @error('cif')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="web">Web:</label>
                                        <input placeholder="Pagina web..." type="text" class="form-control @error('web') is-invalid @enderror" id="web" value="{{ old('web') }}" name="web">
                                        @error('web')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    {{-- <h3 class="mt-3 mb-2 text-center uppercase">Cliente Asociado</h3>
                                    <hr> --}}
                                    <div class="form-group mt-3">
                                        <label for="client_id">Cliente Asociado:</label>
                                        <select class="choices form-select" name="client_id">
                                            @if ($clientes->count() > 0)
                                                <option value="{{null}}">Seleccione un cliente asociado</option>
                                                @foreach ( $clientes as $cliente )
                                                    <option @if(old('client_id') == $cliente->id) {{'selected'}} @endif value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="{{null}}">No existen clientes todavia</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="form-group mt-4">
                                        <div class="form-floating" >
                                            <textarea class="form-control" placeholder="Escribe la anotación..."
                                                id="floatingTextarea" name="notes" rows="5">{{old('notes')}}</textarea>
                                            <label for="floatingTextarea">Notas</label>
                                        </div>
                                        @error('notes')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label for="name">Segundo apellido</label>
                                        <input placeholder="Segundo apellido (Obligatorio en caso de particulares)" type="text" class="form-control @error('segundoApellido') is-invalid @enderror" id="segundoApellido" value="{{ old('segundoApellido') }}" name="segundoApellido">
                                        @error('segundoApellido')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2 select-wrapper w-100">
                                        <label for="admin_user_id">Gestor:</label>
                                        <select class=" choices form-select w-100 @error('admin_user_id') is-invalid @enderror" id="admin_user_id" name="admin_user_id">
                                            <option value="{{null}}">Seleccione el gestor del cliente</option>
                                            @foreach ( $gestores as $gestor )
                                                <option {{ old('admin_user_id') != null ? (old('admin_user_id') == $gestor->id ? 'selected' : '') : ( Auth::user()->id == $gestor->id ? 'selected' : '') }} value="{{$gestor->id}}">{{$gestor->name}} {{$gestor->surname}}</option>
                                            @endforeach
                                        </select>
                                        @error('admin_user_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="address">Dirección:</label>
                                        <input placeholder="Direccion de la empresa..." type="text" class="form-control @error('address') is-invalid @enderror" id="address" value="{{ old('address') }}" name="address">
                                        @error('address')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>

                                    <div class="form-group mt-3">
                                        <label for="country">Pais:</label>
                                        <select class="choices form-select" name="country">
                                            @if ($countries->count() > 0)
                                                <option value="{{null}}">Seleccione un pais </option>
                                                @foreach ( $countries as $country )
                                                    <option @if(old('country') == $country->name) {{'selected'}} @endif value="{{$country->name}}" >{{$country->name}}</option>
                                                @endforeach
                                            @else
                                                <option value="{{null}}">No existen clientes todavia</option>
                                            @endif
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label for="city">Ciudad:</label>
                                        <input placeholder="Ciudad..." type="text" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city') }}" name="city">
                                        @error('city')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="province">Provincia:</label>
                                        <input placeholder="Provincia..." type="text" class="form-control @error('province') is-invalid @enderror" id="province" value="{{ old('province') }}" name="province">
                                        @error('province')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="zipcode">Código postal:</label>
                                        <input placeholder="Codigo postal..." type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode" value="{{ old('zipcode') }}" name="zipcode">
                                        @error('zipcode')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="w-100">
                                    <div class=" w-100 d-flex align-items-center mt-4 justify-start">
                                        <button id="newAssociatedContact" type="button" class="btn btn-color-1 mr-4" style="height: fit-content">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                        <h3 class="text-center uppercase">
                                            {{-- <i class="bi bi-people text-info fs-4 mr-2"></i> --}}
                                            Contacto Asociado
                                        </h3>
                                    </div>
                                    {{-- <hr class="mb-4"> --}}
                                    <div class="form-group mt-3">
                                        <h5 hidden id="labelAssociateNew" for="associated_contact_new" class="mb-2">Creación de nuevo/s contacto/s:</h5>
                                        <div class="col-12 form-group" id="dynamic_field_associated_contact_new">

                                        </div>
                                    </div>

                                </div>
                                <div class="w-100">
                                    <div class="d-flex align-items-center mt-4">
                                        <button type="button" name="addMails" id="addExtraMail" class="btn btn-color-1 mr-4"><i class="fas fa-plus"></i></button>
                                        <h3 class="text-center uppercase">
                                            {{-- <i class="bi bi-envelope text-info fs-4 mr-2"></i> --}}
                                            Añadir email/s extra
                                        </h3>
                                    </div>
                                    {{-- <hr class="mb-4"> --}}
                                    <div class="col-12 form-group mt-4" id="dynamic_field_mails">
                                    </div>
                                </div>
                                {{-- <hr class="mb-4"> --}}
                                <div class="w-100">
                                    <div class="d-flex align-items-center mt-4">
                                        <button type="button" name="addExtraPhone" id="addExtraPhone" class="btn btn-color-1 mr-4"><i class="fas fa-plus"></i></button>
                                        <h3 class="text-center uppercase">
                                            {{-- <i class="bi bi-telephone text-info fs-4 mr-2"></i>  --}}
                                            Telefonos extra
                                        </h3>

                                    </div>

                                    <div class="col-12 form-group mt-4" id="dynamic_field_phones">
                                    </div>

                                </div>
                                <div class="w-100">
                                    <div class="d-flex align-items-center mt-4">
                                        <button type="button" name="addWebs" id="addExtraWeb" class="btn btn-color-1 mr-4"><i class="fas fa-plus"></i></button>
                                        <h3 class="text-center uppercase">
                                            {{-- <i class="bi bi-globe-americas text-info fs-4 mr-2"></i> --}}
                                            Webs extra
                                        </h3>

                                    </div>
                                    <div class="col-12 form-group mt-4" id="dynamic_field_webs">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <h3 class="mt-3 mb-2 text-center uppercase"><i class="bi bi-share text-color-2 mr-4 fs-4"></i>Redes Sociales</h3>

                                <div class="row form-group mt-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="facebook"><i class="fa-brands fa-facebook"></i> Facebook:</label>
                                        <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" value="{{ old('facebook') }}" name="facebook">
                                        @error('facebook')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="twitter"><i class="fa-brands fa-x-twitter"></i> Twitter:</label>
                                        <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" value="{{ old('twitter') }}" name="twitter">
                                        @error('twitter')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="linkedin"><i class="fa-brands fa-linkedin"></i> Linkedin:</label>
                                        <input type="text" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" value="{{ old('linkedin') }}" name="linkedin">
                                        @error('linkedin')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="instagram"><i class="fa-brands fa-square-instagram"></i> Instagram:</label>
                                        <input type="text" class="form-control @error('instagram') is-invalid @enderror" id="instagram" value="{{ old('instagram') }}" name="instagram">
                                        @error('instagram')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="pinterest"><i class="fa-brands fa-pinterest"></i> Pinterest:</label>
                                        <input type="text" class="form-control @error('pinterest') is-invalid @enderror" id="pinterest" value="{{ old('pinterest') }}" name="pinterest">
                                        @error('pinterest')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-success w-100 text-uppercase">
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
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/datepicker-es.min.js"></script>
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

<script>
    $(function() {
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional["es"]);
        $("#datepicker").datepicker({
                changeYear: true,
                changeMonth: true,
                showOn: "both",
                language: 'es',
                // buttonImage: "calendar.gif",
                buttonText: "Seleccion de Fecha",
                beforeShow: function(input, inst) {
                    setTimeout(function() {
                        $(inst.dpDiv).addClass('custom-datepicker');
                    }, 0);
                },
                create: function() {
                    setTimeout(function() {
                        $(".ui-datepicker-trigger").addClass('custom-datepicker-button');
                    }, 0);
                }
            }).addClass('custom-datepicker-input');
    });
    $(document).ready(function() {
        // Aplicar a todos los inputs que desees
        $('input[type="text"]').each(function() {
            // Guardar el placeholder original en data attribute al cargar
            $(this).data('placeholder', $(this).attr('placeholder'));
        });

        $('input[type="text"]').focus(function() {
            // Borrar el placeholder sólo si el input está vacío
            if ($(this).val() === '') {
                $(this).attr('placeholder', '');
            }
        });

        $('input[type="text"]').blur(function() {
            // Restablecer el placeholder sólo si el input está vacío
            if ($(this).val() === '') {
                $(this).attr('placeholder', $(this).data('placeholder'));
            }
        });

        $('input[type="text"]').on('input', function() {
            // Si se escribe algo, asegurarse de que el placeholder esté vacío
            if ($(this).val() !== '') {
                $(this).attr('placeholder', '');
            }
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();

            var name = $('#name').val();
            var company = $('#company').val();
            var form = this;

            $.ajax({
                url: '{{ route("cliente.verificarExistente") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    name: name,
                    company: company
                },
                success: function(cliente) {
                    if (cliente) {
                        Swal.fire({
                            title: 'Cliente existente',
                            html: `Ya existe un cliente con este nombre o nombre de empresa.<br><br>
                                   <strong>Nombre del contacto:</strong> ${cliente.name}<br>
                                   <strong>Nombre de la empresa:</strong> ${cliente.company}<br><br>
                                   ¿Estás seguro de que quieres crear uno nuevo?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear nuevo',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // Enviar el formulario si el usuario confirma
                            }
                        });
                    } else {
                        form.submit(); // No existe cliente con el mismo nombre, enviar el formulario
                    }
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        var i=1;
        $('#newAssociatedContact').click(function(){
            i++;
            $('#dynamic_field_associated_contact_new').append('<div class="col-12 new-associate-contact" id="createAssociatedContact'+i+'"><div class="input-group list-row-new-associated-contact" ><input  name="newAssociatedContact['+i+'][name]" type="text" placeholder="Nombre completo" class="form-control">&nbsp;&nbsp;<input  name="newAssociatedContact['+i+'][email]" type="text"placeholder="Email" class="form-control">&nbsp;&nbsp;<input  name="newAssociatedContact['+i+'][telephone]" type="text"placeholder="Teléfono" class="form-control">&nbsp;<button type="button" name="remove"  id="'+i+'" class="btn btn-danger btn_remove_new_associated_contact">X</button></div><br></div>');

            $('#labelAssociateNew').attr('hidden', false);

        });
        $(document).on('click', '.btn_remove_new_associated_contact', function(){
            var button_rem_id = $(this).attr("id");
            $('#createAssociatedContact'+button_rem_id+'').remove();
            if($('.new-associate-contact').length === 0){
                $('#labelAssociateNew').attr('hidden', true);
            }
        });
    });
        // Mails extra
    $(document).ready(function() {
        var i=1;
        $('#addExtraMail').click(function(){
            i++;
            $('#dynamic_field_mails').append('<div id="rowMail'+i+'" class="row"><div class="col-md-10"><input type="text" style="margin-bottom:2%" name="mails[]" placeholder="" class="form-control name_list" /></div><div class="col-md-2"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove_mail">X</button></div></div>');

        });
        $(document).on('click', '.btn_remove_mail', function(){
            var button_id = $(this).attr("id");
            $('#rowMail'+button_id+'').remove();
        });
    });
    // Teléfonos extra
    $(document).ready(function() {
        var i=1;
        $('#addExtraPhone').click(function(){
            i++;
            $('#dynamic_field_phones').append('<div id="rowPhone'+i+'" class="dynamic-added row"><div class="col-md-10"><input type="text" style="margin-bottom:2%" name="numbers[]" placeholder="" class="form-control name_list" /></div><div class="col-md-2"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove_phone">X</button></div></div>');
        });
        $(document).on('click', '.btn_remove_phone', function(){
            var button_id = $(this).attr("id");
            $('#rowPhone'+button_id+'').remove();
        });
    });
    // webs extra
    $(document).ready(function() {
        var i=1;
        $('#addExtraWeb').click(function(){
            i++;
            $('#dynamic_field_webs').append('<div id="rowWeb'+i+'" class="dynamic-added row"><div class="col-md-10"><input type="text" style="margin-bottom:2%" name="webs[]" placeholder="" class="form-control name_list" /></div><div class="col-md-2"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove_web">X</button></div></div>');

        });
        $(document).on('click', '.btn_remove_web', function(){
            var button_id = $(this).attr("id");
            $('#rowWeb'+button_id+'').remove();
        });
    });

</script>
@endsection

