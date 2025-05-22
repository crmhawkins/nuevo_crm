@extends('layouts.app')

@section('titulo', 'Configuración de Correo')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-4 order-md-1 order-last">
                <h3 class="display-6"><i class="bi bi-gear"></i> Configuración de Correo</h3>
            </div>
            <div class="col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Configuración de Correo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        @if($configuracion->isEmpty())
                            <form id="formStore"  action="{{ route('admin.emailConfig.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_host" class="form-label">Host Smtp</label>
                                            <input type="text" class="form-control @error('smtp_host') is-invalid @enderror" id="smtp_host"  value="{{old('smtp_host')}}" name="smtp_host" placeholder="smtp.ionos.es" >
                                            @error('smtp_host')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_port" class="form-label">Port Smtp</label>
                                            <input type="text" class="form-control @error('smtp_port') is-invalid @enderror"  id="smtp_port" value="{{old('smtp_port')}}" name="smtp_port" placeholder="465" >
                                            @error('smtp_port')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="host" class="form-label">Host</label>
                                            <input type="text" class="form-control @error('host') is-invalid @enderror"  id="host" value="{{old('host')}}" name="host" placeholder="imap.ionos.es" >
                                            @error('host')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="port" class="form-label">Port</label>
                                            <input type="text" class="form-control @error('port') is-invalid @enderror"  id="port" value="{{old('port')}}" name="port" placeholder="993" >
                                            @error('port')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror"  value="{{old('username')}}" id="username" name="username" >
                                            @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"  value="{{old('password')}}" id="password" name="password" >
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma</label>
                                            <textarea id="firma" class="form-control @error('firma') is-invalid @enderror"   name="firma" >{{old('firma')}}"</textarea>
                                            @error('firma')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form id="formUpdate" action="{{ route('admin.emailConfig.update', $configuracion->first()->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_host" class="form-label">Host Smtp</label>
                                            <input type="text" class="form-control @error('smtp_host') is-invalid @enderror"  id="smtp_host" name="smtp_host" value="{{old('smtp_host',$configuracion->first()->smtp_host) }}"  placeholder="smtp.ionos.es" >
                                            @error('smtp_host')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="smtp_port" class="form-label">Port Smtp</label>
                                            <input type="text" class="form-control @error('smtp_port') is-invalid @enderror"  id="smtp_port" name="smtp_port" value="{{ old('smtp_port', $configuracion->first()->smtp_port) }}" placeholder="465" >
                                            @error('smtp_port')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="host" class="form-label">Host</label>
                                            <input type="text" class="form-control @error('host') is-invalid @enderror"  id="host" name="host" value="{{ old('host',$configuracion->first()->host) }}" >
                                            @error('host')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="port" class="form-label">Port</label>
                                            <input type="text" class="form-control @error('port') is-invalid @enderror"  id="port" name="port" value="{{ old('port',$configuracion->first()->port) }}" >
                                            @error('port')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control @error('username') is-invalid @enderror"  id="username" name="username" value="{{ old('username',$configuracion->first()->username) }}" >
                                            @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"  id="password" name="password" value="{{ old('password',$configuracion->first()->password) }}" >
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma</label>
                                            <textarea id="firma" class="form-control @error('firma') is-invalid @enderror"  name="firma" > {{ old('firma',$configuracion->first()->firma) }}</textarea>
                                            @error('firma')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
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
                        @if($configuracion->isEmpty())
                        <button id="Guardar" type="button" class="btn btn-primary">Guardar Configuración</button>
                        @else
                        <button id="Actualizar" type="button" class="btn btn-primary">Actualizar Configuración</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('scripts')
@include('partials.toast')

<script>
     $('#Guardar').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('#formStore').submit(); // Esto envía el formulario.
        });
    $('#Actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('#formUpdate').submit(); // Esto envía el formulario.
    });
</script>
@endsection
