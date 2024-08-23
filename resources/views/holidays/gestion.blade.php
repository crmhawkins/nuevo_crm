@extends('layouts.app')

@section('titulo', 'Vacaciones')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-file-earmark-ruled"></i> Vacaciones</h3>
                    <p class="text-subtitle text-muted">Listado de vacaciones</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gestión de vacaciones</li>
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
                            <p for="">&nbsp;</p>
                            @if( $numberOfholidaysPetitions == 1)
                                <p for="pendant">Tienes <span style="color:red"><strong>{{ $numberOfholidaysPetitions }}</strong></span> petición pendiente de gestión</p>
                            @else
                                <p for="pendant">Tienes <span style="color:red"><strong>{{ $numberOfholidaysPetitions }}</strong></span> peticiones pendientes de gestión</p>
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
            @livewire('holidays-table')

        @else
            {{-- Contenido para dispositivos de escritorio --}}
            {{-- <livewire:users-table-view> --}}
            @livewire('holidays-table')
        @endif


    </div>
@endsection

@section('scripts')

    @include('partials.toast')

@endsection

