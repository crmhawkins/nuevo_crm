@extends('layouts.app')

@section('titulo', 'Tipos de iva')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-6 order-md-1 order-last row">
                    <div class="col-auto">
                        <h3><i class="bi bi-currency-euro"></i> Cierre de años </h3>
                        <p class="text-subtitle text-muted">Listado de cierres de año</p>
                    </div>
                    <div class="col-auto">
                        <a class="btn btn-outline-secondary" href="{{route('cierre.create')}}">
                            <i class="fa-solid fa-plus"></i> Crear o Editar Cierres
                        </a>
                    </div>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Cierres</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">

                <div class="card-body">
                    @php
                        use Jenssegers\Agent\Agent;

                        $agent = new Agent();
                    @endphp
                    @if ($agent->isMobile())
                        <div>
                            <span>Es movil</span>
                        </div>
                        @livewire('cierres-table')

                    @else

                        @livewire('cierres-table')
                    @endif
                </div>
            </div>

        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection
