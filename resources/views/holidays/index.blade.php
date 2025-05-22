@extends('layouts.app')

@section('titulo', 'Mis Vacaciones')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-6 order-md-1 order-last row">
                    <div class="col-auto">
                        <h3><i class="fa-solid fa-umbrella-beach"></i> Mis Vacaciones</h3>
                        <p class="text-subtitle text-muted">Listado de mis vacaciones</p>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-outline-secondary" href="{{route('holiday.create')}}">
                            <i class="fa-solid fa-plus"></i> Petición de vacaciones
                        </a>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mis Vacaciones</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg col-md-6 mt-4">
                <div class="card2">
                    <div class="card-body ">
                        <div class="col-12" style="text-align: center">
                                @if($userHolidaysQuantity)
                                    @if($userHolidaysQuantity->quantity == 1)
                                        <p for="have">Tienes <span style="color:green"><strong>{{$userHolidaysQuantity->quantity}}</strong></span> día de vacaciones</p>
                                    @endif
                                    @if($userHolidaysQuantity->quantity >1 )
                                        <p for="have">Tienes <span style="color:green"><strong>{{$userHolidaysQuantity->quantity}}</strong></span> días de vacaciones</p>
                                    @endif
                                @else
                                    <p for="have">No tienes días de vacaciones</p>
                                @endif
                                @if($numberOfHolidayPetitions)
                                    @if($numberOfHolidayPetitions == 1)
                                        <p for="pendant">Tienes <span style="color:orange"><strong>{{$numberOfHolidayPetitions}}</strong></span> petición pendiente</p>
                                    @endif
                                    @if($numberOfHolidayPetitions >1 )
                                        <p for="pendant">Tienes <span style="color:orange"><strong>{{$numberOfHolidayPetitions}}</strong></span> peticiones pendientes</p>
                                    @endif
                                @else
                                    <p for="pendant">No tienes peticiones pendientes</p>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-6 mt-4">
                <div class="card2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12" style="text-align: center">
                                <p for="status"><strong>ESTADOS</strong></p>
                                <p for="pendant">
                                    <i class="fa fa-square" aria-hidden="true" style="color:#FFDD9E"></i>&nbsp;&nbsp;PENDIENTE
                                    <i class="fa fa-square" aria-hidden="true" style="margin-left:5%;color:#C3EBC4"></i>&nbsp;&nbsp;ACEPTADA
                                    <i class="fa fa-square" aria-hidden="true" style="margin-left:5%;color:#FBC4C4"></i>&nbsp;&nbsp;DENEGADA
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    {{-- <livewire:users-table-view> --}}
                    @php
                        use Jenssegers\Agent\Agent;

                        $agent = new Agent();
                    @endphp
                    @if ($agent->isMobile())
                        {{-- Contenido para dispositivos móviles --}}
                        <div>
                            <span>Es movil</span>
                        </div>
                        @livewire('myholidays-table')

                    @else
                        {{-- Contenido para dispositivos de escritorio --}}
                        {{-- <livewire:users-table-view> --}}
                        @livewire('myholidays-table')
                    @endif
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')

    @include('partials.toast')

@endsection

