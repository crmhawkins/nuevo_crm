@extends('layouts.app')

@section('titulo', 'Suite')

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}" />

@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card bg-white shadow position-relative" style="z-index: 10;">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <div class="fw-bold">
                        Usuarios
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('suite.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Crear Usuario
                        </a>
                        <a href="{{ route('suite.edit') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i> Editar Usuario
                        </a>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Último Acceso</th>
                                    <th>Fecha de Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suites as $suite)
                                <tr>
                                    <td>{{ $suite->user }}</td>
                                    <td>
                                        {{ $suite->logged_at ? \Carbon\Carbon::parse($suite->logged_at)->format('d/m/Y H:i') : 'Nunca' }}
                                    </td>
                                    <td>{{ $suite->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
