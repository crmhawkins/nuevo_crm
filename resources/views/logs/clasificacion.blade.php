@extends('layouts.app')

@section('titulo', 'Clasificación por Usuario')

@section('content')
<div class="page-heading card" style="box-shadow: none !important">
    <div class="page-title card-body">
        <div class="row justify-content-between">
            <div class="col-12">
                <h3>Clasificación por Usuario</h3>
                <p class="text-subtitle text-muted">Resultados clasificados por usuario</p>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <!-- Pestañas -->
        <div class="card-body">
            <ul class="nav nav-tabs" id="userTabs" role="tablist">
                @foreach ($clasificacion as $usuario => $cambios)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $usuario }}-tab" data-bs-toggle="tab" href="#tab-{{ $usuario }}" role="tab" aria-controls="tab-{{ $usuario }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $usuarios[$usuario]->name ?? 'Usuario Desconocido' }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>


        <div class="tab-content mt-3">
            @foreach ($clasificacion as $usuario => $cambios)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $usuario }}" role="tabpanel" aria-labelledby="tab-{{ $usuario }}-tab">
                    <div class="card-body">
                        @foreach ($cambios as $tipo => $detalles)
                            <h3 class="mt-3">{{ ucfirst($tipo) }}</h3>
                            <h5>{{count($detalles)}} registros</h5>
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>De</th>
                                        <th>A</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detalles as $detalle)
                                        <tr>
                                            <td>{{ $detalle['antiguo'] }}</td>
                                            <td>{{ $detalle['nuevo'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection

