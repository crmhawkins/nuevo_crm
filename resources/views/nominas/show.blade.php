@extends('layouts.app')

@section('titulo', 'Ver Nomina')

@section('css')

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-globe-americas"></i> Nominas</h3>
                    <p class="text-subtitle text-muted">Ver nomina</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{(Auth::user()->id = $nomina->admin_user_id) ? route('nominas.index_user',Auth::user()->id) : route('nominas.index')}}">Nominas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ver nomina</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Detalles de la Nómina</h3>
                    <!-- Mostrar detalles de la nómina -->
                    <div class="d-flex justify-between my-4">
                        <p><strong>Usuario Asociado:</strong> {{ $nomina->usuario->name }} {{ $nomina->usuario->surname }}</p>
                        <p><strong>Fecha:</strong> {{\Carbon\Carbon::parse($nomina->fecha)->format('d/m/Y')  }}</p>
                    </div>

                    <!-- IFrame para visualizar el PDF de la nómina -->
                    <div class="iframe-container" style="overflow: hidden; padding-top: 56.25%; position: relative;">
                        <iframe src="{{ asset('storage/' . $nomina->archivo) }}" frameborder="0" style="border: none; height: 100%; width: 100%; position: absolute; top: 0; left: 0;" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection

