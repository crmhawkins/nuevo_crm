@extends('layouts.app')

@section('titulo', 'Crear Cliente')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

<div class="page-heading card" style="box-shadow: none !important" >

    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Crear Petición</h3>
                <p class="text-subtitle text-muted">Formulario para registrar una petición</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Peticiones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear petición</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card">
            <div class="card-body">
                <form action="{{route('peticion.store')}}" method="POST">
                    @csrf
                    <div class="bloque-formulario">
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="mb-2 text-left">Cliente Asociado</label>
                                    <div class="flex flex-row align-items-start mb-0">
                                        <select id="cliente" class="w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
                                            <option value="">Seleccione o escriba un Cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option data-email="{{ $cliente->email }}" data-phone="{{ $cliente->phone }}" value="{{ $cliente->id }}">{{ $cliente->company ?? $cliente->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('client_id')
                                    <p class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </p>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6" id="nombreClienteDiv" style="display:none;">
                                <div class="form-group mb-3">
                                    <label for="name">Nombre del Cliente:</label>
                                    <input placeholder="Nombre del cliente" type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name') }}" name="name">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label class="mb-2 text-left">Gestor</label>
                                    <select class="choices form-select w-100 @error('admin_user_id') is-invalid @enderror" name="admin_user_id" id="gestor">
                                        @if ($gestores->count() > 0)
                                        <option value="">Seleccione un Gestor</option>
                                        @foreach ( $gestores as $gestor )
                                        <option {{$gestorId != null ? ($gestorId == $gestor->id ? 'selected' : '') : (old('admin_user_id',Auth::user()->id) == $gestor->id ? 'selected' : '')}}  value="{{$gestor->id}}">{{$gestor->name}}</option>
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
                                <div class="form-group mb-3">
                                    <label for="email">Email:</label>
                                    <input placeholder="Direccion de correo electronico" type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email') }}" name="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label for="phone">Teléfono:</label>
                                    <input placeholder="Telefono principal..." type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone') }}" name="phone">
                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12">
                            <div class="form-group mb-3">
                                <label class="mb-2 text-left" for="note">Concepto:</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ old('note') }}</textarea>
                                @error('note')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

<script>
    $(document).ready(function() {
        $('#cliente').select2({
            tags: true,
            placeholder: 'Seleccione o escriba un Cliente',
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: 0,
                    text: term,
                    newOption: true
                }
            }
        }).on('select2:select', function (e) {
            var selectedOption = e.params.data;
            if (selectedOption.newOption) {
                $('#name').val(selectedOption.text);
                $('#email').val('');
                $('#phone').val('');
            } else {
                $('#name').val('');
                var email = $(this).find('option:selected').data('email');
                var phone = $(this).find('option:selected').data('phone');
                $('#email').val(email);
                $('#phone').val(phone);
            }

            var clienteId = selectedOption.id;

            if (clienteId && !selectedOption.newOption) {
                $.ajax({
                    url: '{{ route("cliente.getGestor") }}',
                    type: 'POST',
                    data: {
                        client_id: clienteId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var select = $('#gestor');
                        select.val(null);

                        if (response.length === 0) {
                            alert('El cliente no tiene gestor asociado');
                        } else {
                            var gestorId = response;
                            select.val(gestorId);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
        });
    });
</script>
@endsection
