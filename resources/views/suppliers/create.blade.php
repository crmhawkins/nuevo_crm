@extends('layouts.app')

@section('titulo', 'Crear proveedor')

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
                    <h3><i class="bi bi-person"></i> Crear Proveedor</h3>
                    <p class="text-subtitle text-muted">Formulario para registrar a un proveedor.</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('proveedores.index')}}">Proveedores</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Crear proveedor</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    @include('suppliers.form', [
                        'action' => route('proveedores.store'),
                        'buttonText' => 'Guardar proveedor',
                        'countries' => $countries,
                    ])
                </div>
            </div>
        </section>
    </div>

@endsection

@section('scripts')
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/i18n/datepicker-es.min.js"></script>

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

<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
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

