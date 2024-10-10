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
            @foreach ($clasificacion as $usuario => $referencias)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $usuario }}" role="tabpanel" aria-labelledby="tab-{{ $usuario }}-tab">
                    <div class="card-body">
                        @foreach ($referencias as $referenciaId => $cambios)
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Cliente</th>
                                        @foreach ($cambios as $propiedad => $detalles)
                                        <th>{{ ucfirst(str_replace('_', ' ', $propiedad)) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $kitdigital[$referenciaId]->cliente ?? 'ID: ' . $referenciaId }}</td>
                                        @foreach ($cambios as $propiedad => $detalles)
                                        @foreach ($detalles as $detalle)
                                            <td>
                                                {{ ($detalle['valor_antiguo'] ?: 'N/A'). ' - ' .$detalle['valor_nuevo'] }}
                                            </td>
                                        @endforeach
                                    @endforeach
                                    </tr>
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

