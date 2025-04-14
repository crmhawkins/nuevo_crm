@extends('layouts.tempappPortal')

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm" style="border-radius: 0;">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h3 class="text-center"><strong>Generar Usuario Temporal</strong></h3>
                </div>
                <div class="card-body">
                    {{-- Mostrar usuario y pin si están en sesión --}}
                    @if(isset($userNumber) && isset($pin))
                        <div class="alert alert-info">
                            <p><strong>Usuario:</strong> <span class="font-weight-semibold">{{ $userNumber }}</span></p>
                            <p><strong>PIN:</strong> <span class="font-weight-semibold">{{ $pin }}</span></p>
                        </div>
                    @endif

                    {{-- Botón para generar usuario temporal --}}
                    <form action="{{ route('portal.generarUser') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg w-100">Generar Usuario Temporal</button>
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
