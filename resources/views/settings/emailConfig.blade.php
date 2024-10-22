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
                                            <label for="host" class="form-label">Host</label>
                                            <input type="text" class="form-control" id="host" name="host" placeholder="imap.ionos.es" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="port" class="form-label">Port</label>
                                            <input type="text" class="form-control" id="port" name="port" placeholder="993" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma</label>
                                            <textarea id="firma" class="form-control" name="firma" required></textarea>
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
                                            <label for="host" class="form-label">Host</label>
                                            <input type="text" class="form-control" id="host" name="host" value="{{ $configuracion->first()->host }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="port" class="form-label">Port</label>
                                            <input type="text" class="form-control" id="port" name="port" value="{{ $configuracion->first()->port }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="{{ $configuracion->first()->username }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" value="{{ $configuracion->first()->password }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="firma" class="form-label">Firma</label>
                                            <textarea id="firma" class="form-control" name="firma" required> {{ $configuracion->first()->firma }}</textarea>
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
