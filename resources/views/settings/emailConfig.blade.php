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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @if($configuracion->isEmpty())
                            <form action="{{ route('admin.emailConfig.store') }}" method="POST">
                                @csrf
                                <div class="row mb-3">
                                    <label for="host" class="col-md-2 col-form-label">Host</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="host" name="host" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="port" class="col-md-2 col-form-label">Port</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="port" name="port" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="username" class="col-md-2 col-form-label">Username</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="password" class="col-md-2 col-form-label">Password</label>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                            </form>
                        @else
                            <form action="{{ route('admin.emailConfig.update', $configuracion->first()->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <label for="host" class="col-md-2 col-form-label">Host</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="host" name="host" value="{{ $configuracion->first()->host }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="port" class="col-md-2 col-form-label">Port</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="port" name="port" value="{{ $configuracion->first()->port }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="username" class="col-md-2 col-form-label">Username</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="username" name="username" value="{{ $configuracion->first()->username }}" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="password" class="col-md-2 col-form-label">Password</label>
                                    <div class="col-md-10">
                                        <input type="password" class="form-control" id="password" name="password" value="{{ $configuracion->first()->password }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Actualizar Configuración</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
