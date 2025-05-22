@extends('layouts.app')

@section('titulo', 'Editar Estado de Email')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-md-6 order-md-1 order-last">
                <h3 class="display-6">Editar Estado de Email</h3>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.statusMail.update', $status->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nombre</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $status->name) }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="color" class="form-label">Color</label>
                                        <select class="form-select @error('color') is-invalid @enderror" id="color" name="color">
                                            <option value="primary" class="bg-primary text-white" {{ $status->color == 'primary' ? 'selected' : '' }}>Azul</option>
                                            <option value="secondary" class="bg-secondary text-white" {{ $status->color == 'secondary' ? 'selected' : '' }}>Gris</option>
                                            <option value="success" class="bg-success text-white" {{ $status->color == 'success' ? 'selected' : '' }}>Verde</option>
                                            <option value="danger" class="bg-danger text-white" {{ $status->color == 'danger' ? 'selected' : '' }}>Rojo</option>
                                            <option value="warning" class="bg-warning text-dark" {{ $status->color == 'warning' ? 'selected' : '' }}>Amarillo</option>
                                            <option value="info" class="bg-info text-dark" {{ $status->color == 'info' ? 'selected' : '' }}>Cyan</option>
                                            <option value="light" class="bg-light text-dark" {{ $status->color == 'light' ? 'selected' : '' }}>Blanco</option>
                                            <option value="dark" class="bg-dark text-white" {{ $status->color == 'dark' ? 'selected' : '' }}>Negro</option>
                                        </select>
                                        @error('color')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="other" class="form-label">Otro</label>
                                        <input type="text" class="form-control @error('other') is-invalid @enderror" id="other" name="other" value="{{ old('other', $status->other) }}">
                                        @error('other')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary w-100 fs-5 mt-4">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
