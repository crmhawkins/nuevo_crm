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
                @foreach ($clasificacion as $usuario => $referencias)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $usuario }}-tab" data-bs-toggle="tab" href="#tab-{{ $usuario }}" role="tab" aria-controls="tab-{{ $usuario }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $usuarios[$usuario]->name ?? 'Usuario Desconocido' }}
                        </a>
                    </li>
                @endforeach
            </ul>
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
                                            Referencia: {{ $kitdigital[$referenciaId]->cliente ?? 'ID: ' . $referenciaId }}
                                        </button>
                                    </h2>
                                    <div id="collapse-{{ $usuario }}-{{ $referenciaId }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $usuario }}-{{ $referenciaId }}" data-bs-parent="#accordion-usuario-{{ $usuario }}">
                                        <div class="accordion-body">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Propiedad</th>
                                                        <th>De</th>
                                                        <th>A</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($cambios as $propiedad => $detalles)
                                                        @foreach ($detalles as $detalle)
                                                            <tr>
                                                                <td>{{ ucfirst(str_replace('_', ' ', $propiedad)) }}</td>
                                                                <td>{{ $detalle['valor_antiguo'] ?: 'N/A' }}</td>
                                                                <td>{{ $detalle['valor_nuevo'] }}</td>
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
