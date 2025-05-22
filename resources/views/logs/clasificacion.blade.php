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
        <!-- Formulario de selección de fecha -->
        <!-- Pestañas -->
        <div class="card-body align-items-center row">
            <div class="col-7">
                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                    @foreach ($clasificacion as $usuario => $referencias)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $usuario }}-tab" data-bs-toggle="tab" href="#tab-{{ $usuario }}" role="tab" aria-controls="tab-{{ $usuario }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $usuarios[$usuario]->name ?? 'Usuario Desconocido' }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-5">
                <form method="GET" action="{{ route('logs.clasificado') }}">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio', \Carbon\Carbon::today()->subDays(7)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-content mt-3">
            @foreach ($clasificacion as $usuario => $referencias)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $usuario }}" role="tabpanel" aria-labelledby="tab-{{ $usuario }}-tab">
                    <div class="card-body">
                        <div class="accordion" id="accordion-usuario-{{ $usuario }}">
                            @foreach ($referencias as $referenciaId => $cambios)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading-{{ $usuario }}-{{ $referenciaId }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $usuario }}-{{ $referenciaId }}" aria-expanded="false" aria-controls="collapse-{{ $usuario }}-{{ $referenciaId }}">
                                            Cliente: {{ $kitdigital[$referenciaId]->cliente ?? 'ID: ' . $referenciaId }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $usuario }}-{{ $referenciaId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $usuario }}-{{ $referenciaId }}" data-bs-parent="#accordion-usuario-{{ $usuario }}">
                                        <div class="accordion-body">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Propiedad</th>
                                                        <th>Valor antiguo</th>
                                                        <th>Valor nuevo</th>
                                                        <th>Fecha</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cambios as $propiedad => $detalles)
                                                        @foreach ($detalles as $detalle)
                                                            <tr>
                                                                <td>{{ ucfirst(str_replace('_', ' ', $propiedad)) }}</td>
                                                                <td>{{ $detalle['valor_antiguo'] ?: 'N/A' }}</td>
                                                                <td>{{ $detalle['valor_nuevo'] }}</td>
                                                                <td>{{ $detalle['created_at'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
