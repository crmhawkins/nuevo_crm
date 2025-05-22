@extends('layouts.app')

@section('titulo', 'Editar Cliente')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Cliente</h3>
                    <p class="text-subtitle text-muted">Formulario para editar a un cliente/leads.</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar cliente</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('clientes.update', $cliente->id)}}" method="POST" class="form-primary">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre:</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', $cliente->name) }}" name="name">
                                    @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="name">Primer apellido</label>
                                    <input placeholder="Primer apellido (obligatorio en caso de particulares)" type="text" class="form-control @error('primerApellido') is-invalid @enderror" id="primerApellido" value="{{ old('primerApellido',$cliente->primerApellido)}}" name="primerApellido">
                                    @error('primerApellido')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="name">Segundo apellido</label>
                                    <input placeholder="Segundo apellido (obligatorio en caso de particulares)" type="text" class="form-control @error('segundoApellido') is-invalid @enderror" id="segundoApellido" value="{{ old('segundoApellido',$cliente->segundoApellido) }}" name="segundoApellido">
                                    @error('segundoApellido')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label for="company">Nombre de la empresa:</label>
                                    <input type="text" class="form-control @error('company') is-invalid @enderror" id="company"value="{{ old('company', $cliente->company) }}" name="company">
                                    @error('company')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="activity">Actividad:</label>
                                    <input type="text" class="form-control @error('activity') is-invalid @enderror" id="activity" value="{{ old('activity', $cliente->activity) }}" name="activity">
                                    @error('activity')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label for="cif">CIF/DNI:</label>
                                    <input type="text" class="form-control @error('cif') is-invalid @enderror" id="cif" value="{{ old('cif', $cliente->cif) }}" name="cif">
                                    @error('cif')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="identifier">Marca:</label>
                                    <input type="text" class="form-control @error('identifier') is-invalid @enderror" id="identifier" value="{{ old('identifier', $cliente->identifier) }}" name="identifier">
                                    @error('identifier')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="birthdate">Fecha de alta:</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror" id="birthdate" value="{{ old('birthdate', $cliente->birthdate) }}" name="birthdate">
                                    @error('birthdate')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mt-3 ">
                                    <label for="tipoCliente" class="form-label">Tipo de cliente</label>
                                    <div class="d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('tipoCliente') is-invalid @enderror" type="radio" name="tipoCliente" id="empresa" value="0"
                                                   {{ old('tipoCliente',$cliente->tipoCliente) == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="empresa">Empresa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('tipoCliente') is-invalid @enderror" type="radio" name="tipoCliente" id="particular" value="1"
                                                   {{ old('tipoCliente',$cliente->tipoCliente) == '1' ? 'checked' : '' }}>
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
                                    <label for="admin_user_id">Gestor:</label>
                                    <select class="choices form-select @error('admin_user_id') is-invalid @enderror" id="admin_user_id" name="admin_user_id">
                                        <option value="">Seleccione el gestor del cliente</option>
                                        @foreach ($gestores as $gestor)
                                            <option value="{{ $gestor->id }}"
                                                {{ (old('admin_user_id') ?? $cliente->admin_user_id) == $gestor->id ? 'selected' : '' }}>
                                                {{ $gestor->name }} {{ $gestor->surname }}
                                            </option>
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
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" value="{{ old('address', $cliente->address) }}" name="address">
                                    @error('address')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="country">Pais:</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" value="{{ old('country', $cliente->country) }}" name="country">
                                    @error('country')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>

                                <div class="form-group mt-2">
                                    <label for="city">Ciudad:</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city', $cliente->city) }}" name="city">
                                    @error('city')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="province">Provincia:</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" value="{{ old('province', $cliente->province) }}" name="province">
                                    @error('province')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                                <div class="form-group mt-2">
                                    <label for="zipcode">Código postal:</label>
                                    <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode" value="{{ old('zipcode', $cliente->zipcode) }}" name="zipcode">
                                    @error('zipcode')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="form-group mt-3">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Escribe la anotación..."
                                    id="floatingTextarea" name="notes">{{ old('notes', $cliente->notes) }}</textarea>
                                <label for="floatingTextarea">Notas</label>
                            </div>
                            @error('notes')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="row mt-3">
                            <div class="form-group col-6">
                                <label for="Usuario">Usuario portal:</label>
                                <input type="text" disabled class="form-control @error('Usuario') is-invalid @enderror" id="Usuario" value="{{ 'HK#'.$cliente->id }}" >
                            </div>
                            <div class="form-group col-6">
                                <label for="pin">Pin portal:</label>
                                <input type="text" class="form-control @error('pin') is-invalid @enderror" id="pin" value="{{ old('pin', $cliente->pin) }}" name="pin">
                                @error('pin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>
                        <h3 class="mt-5 mb-2 text-center uppercase">Cliente Asociado</h3>
                        <hr class="mb-4">
                        <div class="form-group">
                            <select class="choices form-select" name="client_id">
                                @if ($clientes->count() > 0)
                                <option value="{{null}}">No existen clientes todavia</option>
                                    @foreach ( $clientes as $client )
                                        <option @if($client->id === $cliente->client_id) {{'selected'}} @endif value="{{$client->id}}">{{$client->name}}</option>
                                    @endforeach
                                @else
                                    <option value="{{null}}">No existen clientes todavia</option>
                                @endif
                            </select>
                        </div>

                        <h3 class="mt-5 mb-2 text-center uppercase">Contacto Asociado</h3>
                        <hr class="mb-4">

                        <div class="form-group">
                            <button id="newAssociatedContact" type="button" class="btn btn-secondary  mb-4"><i class="fa-solid fa-plus"></i> Agregar Contacto</button>
                            <h5 hidden id="labelAssociateNew" for="associated_contact_new" class="mb-2">Creación de nuevo/s contacto/s:</h5>
                            <div class="col-12 form-group" id="dynamic_field_associated_contact_new">
                            </div>
                        </div>

                        <h3 class="mt-5 mb-2 text-center uppercase">Informacion de Contacto de la Empresa</h3>
                        <hr class="mb-4">
                        <div class="form-group">
                            <label for="email">Email de contacto:</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', $cliente->email) }}" name="email">
                            @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <button type="button" name="addMails" id="addExtraMail" class="btn btn-secondary mt-3"><i class="fas fa-plus"></i> Añadir email/s extra</button>
                        <br/>
                        <div class="col-12 form-group mt-4" id="dynamic_field_mails">
                        </div>
                        <div class="form-group">
                            <label for="phone">Teléfono movil:</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone', $cliente->phone) }}" name="phone">
                            @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <button type="button" name="addExtraPhone" id="addExtraPhone" class="btn btn-secondary mt-3"><i class="fas fa-plus"></i> Añadir teléfono/s extra</button>
                        <div class="col-12 form-group mt-4" id="dynamic_field_phones">
                        </div>
                        <div class="form-group">
                            <label for="fax">Fax:</label>
                            <input type="text" class="form-control @error('fax') is-invalid @enderror" id="fax" value="{{ old('fax', $cliente->fax) }}" name="fax">
                            @error('fax')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <h3 class="mt-5 mb-2 text-center uppercase">Redes Sociales - webs</h3>
                        <hr class="mb-4">

                        <div class="form-group">
                            <label for="web">Web:</label>
                            <input type="text" class="form-control @error('web') is-invalid @enderror" id="web" value="{{ old('web', $cliente->web) }}" name="web">
                            @error('web')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <button type="button" name="addWebs" id="addExtraWeb" class="btn btn-secondary mt-3"><i class="fas fa-plus"></i> Añadir web/s extra</button>
                        <div class="col-12 form-group mt-4" id="dynamic_field_webs">
                        </div>

                        <h4 class="mt-5 mb-2 text-left uppercase">Redes Sociales</h4>
                        <div class="row form-group">
                            <div class="col-md-4 mb-3">
                                <label for="facebook"><i class="fa-brands fa-facebook"></i> Facebook:</label>
                                <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" value="{{ old('facebook', $cliente->facebook) }}" name="facebook">
                                @error('facebook')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="twitter"><i class="fa-brands fa-x-twitter"></i> Twitter:</label>
                                <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" value="{{ old('twitter', $cliente->twitter) }}" name="twitter">
                                @error('twitter')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="linkedin"><i class="fa-brands fa-linkedin"></i> Linkedin:</label>
                                <input type="text" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" value="{{ old('linkedin', $cliente->linkedin) }}" name="linkedin">
                                @error('linkedin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="instagram"><i class="fa-brands fa-square-instagram"></i> Instagram:</label>
                                <input type="text" class="form-control @error('instagram') is-invalid @enderror" id="instagram" value="{{ old('instagram', $cliente->instagram) }}" name="instagram">
                                @error('instagram')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pinterest"><i class="fa-brands fa-pinterest"></i> Pinterest:</label>
                                <input type="text" class="form-control @error('pinterest') is-invalid @enderror" id="pinterest" value="{{ old('pinterest', $cliente->pinterest) }}" name="pinterest">
                                @error('pinterest')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                @enderror
                            </div>
                        </div>



                        <div class="form-group mt-5">
                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Actualizar') }}
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

