@extends('layouts.app')

@section('titulo', 'Categoria de Servicios')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-bookmark-check"></i> Categoria de Servicios</h3>
                    <p class="text-subtitle text-muted">Listado de categoria de servicios</p>
                    {{-- {{$servicios->count()}} --}}
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Categoria de Servicios</li>
                        </ol>
                    </nav>
                </div>
            </div>
            {{-- <div class="row mt-3">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    @if($servicios->count() >= 0)
                        <a href="{{route('campania.create')}}" class="btn btn-primary"><i class="fa-solid fa-plus me-2 mx-auto"></i>  Crear categoria de servicio</a>
                    @endif
                </div>
            </div> --}}
        </div>

        <section class="section pt-4">
            <div class="card">

                <div class="card-body">
                    {{-- <livewire:services-table-view> --}}

                    @php
                        use Jenssegers\Agent\Agent;

                        $agent = new Agent();
                    @endphp

                    @if ($agent->isMobile())
                        {{-- Contenido para dispositivos móviles --}}
                        <div>
                            @if ($servicios->count() >= 0)
                                @foreach ($servicios as $servicio)
                                    <div class="card border-bottom">
                                        <div class="card-body" href="{{route('serviciosCategoria.edit',$servicio->id)}}">
                                            <h5 class="card-title">{{ $servicio->title }}</h5>
                                            <p class="card-text">{{ $servicio->terms }}</p>
                                            <p class="card-text">{{ $servicio->type }}</p>
                                            <!-- Más contenido de la tarjeta -->
                                        </div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-center">
                                    {{ $servicios->links() }}
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- Contenido para dispositivos de escritorio --}}
                        {{-- <livewire:services-table-view> --}}
                        @livewire('services-categories-table')

                    @endif

                </div>
            </div>

        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

