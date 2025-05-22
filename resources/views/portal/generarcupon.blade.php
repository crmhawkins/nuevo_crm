@extends('layouts.tempappPortal')

@section('content')
<div class="content">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm" style="border-radius: 0;">
                <div class="card-header" style="background-color: #007bff; color: white;">
                    <h3 class="text-center"><strong>Generar cupon</strong></h3>
                </div>
                <div class="card-body">
                    {{-- Mostrar usuario y pin si están en sesión --}}
                    @if(isset($cupon) && isset($discount))
                        <div class="alert alert-info">
                            <p><strong>Cupon:</strong> <span class="font-weight-semibold">{{ $cupon }}</span></p>
                            <p><strong>Descuento:</strong> <span class="font-weight-semibold">{{ $discount }} %</span></p>
                        </div>
                    @endif
                    <form action="{{ route('portal.generarCupon') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="discount"><strong>% de Descuento</strong></label>
                            <input type="number" name="discount" id="discount" class="form-control" placeholder="0-100" max="100" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg w-100">Generar nuevo cupón</button>
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
