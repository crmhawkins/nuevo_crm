@extends('layouts.app')

@section('titulo', 'Plan General Contable')

@section('css')
<link rel="stylesheet" href="assets/vendors/simple-datatables/style.css">
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row justify-content-between">
                <div class="col-sm-12 col-md-4 order-md-1 order-last">
                    <h3><i class="bi bi-globe-americas"></i> Plan General Contable</h3>
                    <p class="text-subtitle text-muted">Listado de cuentas</p>
                </div>
                <div class="col-sm-12 col-md-4 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Plan General Contable</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">

                <div class="card-body">
                    {{-- <livewire:users-table-view> --}}
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Nombre</th>
                                    {{-- <th>Descripción</th> --}}
                                    <th>Nivel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($grupos as $grupo)
                                    <tr>
              
                                        <td><strong>{{ $grupo->numero }}</strong></td>
                                        <td><strong>{{ $grupo->nombre }}</strong></td>
                                        {{-- <td>{{ $grupo->descripcion }}</td> --}}
                                        <td>Grupo</td>
                                    </tr>
                                    @foreach ($grupo->subGrupos as $subGrupo)
                                        <tr>
                                            <td>{{ $subGrupo->numero }}</td>
                                            <td>{{ $subGrupo->nombre }}</td>
                                            {{-- <td>{{ $subGrupo->nombre }}</td> --}}
                                            <td>SubGrupo</td>
                                        </tr>
                                        @foreach ($subGrupo->cuentas as $cuenta)
                                            <tr>
                                                <td>{{ $cuenta->numero }}</td>
                                                <td>{{ $cuenta->nombre }}</td>
                                                {{-- <td>{{ $cuenta->nombre }}</td> --}}
                                                <td>Cuenta</td>
                                            </tr>
                                            @foreach ($cuenta->subCuentas as $subCuenta)
                                                <tr>
                                                    <td>{{ $subCuenta->numero }}</td>
                                                    <td>{{ $subCuenta->nombre }}</td>
                                                    {{-- <td>{{ $subCuenta->nombre }}</td> --}}
                                                    <td>SubCuenta</td>
                                                </tr>
                                                @foreach ($subCuenta->cuentasHijas as $cuentaHija)
                                                    <tr>
                                                        <td>{{ $cuentaHija->numero }}</td>
                                                        <td>{{ $cuentaHija->nombre }}</td>
                                                        {{-- <td>{{ $cuentaHija->nombre }}</td> --}}
                                                        <td>SubCuenta Hija</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                          </table>
                </div>
            </div>

        </section>

    </div>
@endsection

@section('scripts')


    @include('partials.toast')

@endsection
