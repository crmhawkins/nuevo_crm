@extends('layouts.appPortal')

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center"><strong>Cambiar PIN</strong></h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form action="{{ route('portal.setPin') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="pin">Nuevo PIN</label>
                            <input type="password" name="pin" id="pin" class="form-control @error('pin') is-invalid @enderror"  >
                            @error('pin')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">El PIN debe tener entre 4 y 6 dígitos.</small>
                            <!-- Mostrar mensaje de error para el campo "pin" -->
                        </div>

                        <div class="form-group mb-3">
                            <label for="pin_confirmation">Confirmar PIN</label>
                            <input type="password" name="pin_confirmation" id="pin_confirmation" class="form-control @error('pin_confirmation') is-invalid @enderror" >

                            <!-- Mostrar mensaje de error para el campo "pin_confirmation" -->
                            @error('pin_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cambiar PIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@include('partials.toast')
@endsection
