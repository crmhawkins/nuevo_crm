@extends('layouts.app')

@section('title', 'Mis Vacaciones')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendors/simple-datatables/style.css') }}">
@endsection

@section('content')
<div class="page-heading card border-0">

    {{-- Titles --}}
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-12 col-md-6 order-md-1">
                <h3><i class="fa-solid fa-umbrella-beach"></i> Mis Vacaciones</h3>
                <p class="text-muted">Petición de días</p>
            </div>
            <div class="col-12 col-md-6 order-md-2">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('holiday.index') }}">Mis Vacaciones</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Petición de días</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg col-md-6 mt-4">
            <div class="card">
                <div class="card-body text-center">
                    @if ($userHolidaysQuantity->quantity)
                        <p>Tienes <span class="text-success"><strong>{{ $userHolidaysQuantity->quantity }}</strong></span> {{ Str::plural('día', $userHolidaysQuantity->quantity) }} de vacaciones</p>
                    @else
                        <p>No tienes días de vacaciones</p>
                    @endif
                    @if ($numberOfHolidayPetitions)
                        <p>Tienes <span class="text-warning"><strong>{{ $numberOfHolidayPetitions }}</strong></span> {{ Str::plural('petición', $numberOfHolidayPetitions) }} pendiente{{ $numberOfHolidayPetitions > 1 ? 's' : '' }}</p>
                    @else
                        <p>No tienes peticiones pendientes</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg col-md-6 mt-4">
            <div class="card">
                <div class="card-body text-center">
                    <p><strong>ESTADOS</strong></p>
                    <p>
                        <i class="fa fa-square" style="color:#FFDD9E"></i> PENDIENTE
                        <i class="fa fa-square" style="margin-left:10%; color:#C3EBC4"></i> ACEPTADA
                        <i class="fa fa-square" style="margin-left:10%; color:#FBC4C4"></i> DENEGADA
                    </p>
                </div>
            </div>
        </div>
    </div>

    <section class="section pt-4">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('holiday.store') }}" enctype="multipart/form-data" data-callback="formCallback">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label">Desde</label>
                            <input type="date" name="from_date" class="form-control" id="from_date" />
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label">Hasta</label>
                            <input type="date" name="to_date" class="form-control" id="to_date" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Medio día</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="half_day" name="half_day" value="1">
                                <label class="form-check-label" for="half_day">Sí</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-success">Realizar Petición</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
@include('partials.toast')
@endsection
