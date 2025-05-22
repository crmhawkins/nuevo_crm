@extends('layouts.app')

@section('titulo', 'Tareas en revisión')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">

@endsection
@section('content')
    <div class="page-heading card" style="box-shadow: none !important" >
        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-12 col-md-4 order-md-1 order-last">
                    <h3>Tareas</h3>
                    <p class="text-subtitle text-muted">Listado de tareas en revisión</p>
                </div>
                <div class="col-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('tareas.index')}}">Tareas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tareas en revisión</li>
                        </ol>
                    </nav>
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
                    @else
                        {{-- Contenido para dispositivos de escritorio --}}
                        {{-- <livewire:users-table-view> --}}
                        @livewire('tasks-revision-table')
                    @endif
                </div>
            </div>

        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

