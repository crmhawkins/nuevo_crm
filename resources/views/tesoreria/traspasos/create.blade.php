@extends('layouts.app')

@section('titulo', 'Crear Traspaso ')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')
 <div class="page-heading card" style="box-shadow: none !important" >
    <div class="page-title card-body">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Crear Traspaso</h3>
                <p class="text-subtitle text-muted">Formulario para crear un traspaso</p>
            </div>

            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('traspasos.index')}}">Traspasos</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Crear Traspaso</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="row">
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('traspasos.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-3 form-group mt-2" id="from_bank_id">
                                    <label class="form-label" for="from_bank_id">Banco de Origen:</label>
                                    <select class="form-control" id="from_bank_id" name="from_bank_id">
                                        <option value="">Seleccione</option>
                                        @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('from_bank_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-group mt-2" id="to_bank_id">
                                    <label class="form-label" for="to_bank_id">Banco de Destino:</label>
                                    <select class="form-control" id="to_bank_id" name="to_bank_id">
                                        <option value="">Seleccione</option>
                                        @foreach($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('to_bank_id')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-group mt-2">
                                    <label class="form-label" for="amount">Cantidad:</label>
                                    <input type="number" class="form-control" id="amount" name="amount" value="{{old('amount')}}" >
                                    @error('amount')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                                <div class="col-md-3 form-group mt-2">
                                    <label class="form-label" for="fecha">Fecha:</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" value="{{old('amfechaount')}}" >
                                    @error('fecha')
                                    <span class="text-danger">{{ $message }}</span>
                                    <style>.text-danger {color: red;}</style>
                                    @enderror
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            Acciones
                            <hr>
                        </div>
                        <button id="actualizar" class="btn btn-success btn-block mt-3">Crear Traspaso</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>

    <script>
        $('#actualizar').click(function(e){
        e.preventDefault(); // Esto previene que el enlace navegue a otra página.
        $('form').submit(); // Esto envía el formulario.
    });
    </script>
@endsection
