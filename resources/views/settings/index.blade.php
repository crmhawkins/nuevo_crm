@extends('layouts.app')

@section('titulo', 'Configuración')

@section('css')
@endsection

@section('content')
    <div class="page-heading card" style="box-shadow: none !important">
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6">
                    <h3>Configuración</h3>
                    <p class="text-subtitle text-muted">Configuración de la empresa</p>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <nav aria-label="breadcrumb" class="breadcrumb-header">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Configuración</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            @if ($configuracion)
                <form action="{{ route('configuracion.update', $configuracion->id) }}" method="POST">
            @else
                <form action="{{ route('configuracion.store') }}" method="POST">
            @endif
                @csrf
                <div class="row">
                    <div class="col-12 col-lg-9">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="price_hour" class="form-label">Precio por Hora</label>
                                    <input type="number" step="0.01" class="form-control" id="price_hour" name="price_hour" value="{{ $configuracion->price_hour ?? '' }}" required>
                                </div>

                                <h5 class="mt-4">Fechas de Cambio de Horario</h5>
                                <div class="row g-3">
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_inicio_verano" class="form-label">Inicio Verano</label>
                                        <input type="date" class="form-control" id="fecha_inicio_verano" name="fecha_inicio_verano" value="{{ $configuracion->fecha_inicio_verano ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_fin_verano" class="form-label">Fin Verano</label>
                                        <input type="date" class="form-control" id="fecha_fin_verano" name="fecha_fin_verano" value="{{ $configuracion->fecha_fin_verano ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_inicio_invierno" class="form-label">Inicio Invierno</label>
                                        <input type="date" class="form-control" id="fecha_inicio_invierno" name="fecha_inicio_invierno" value="{{ $configuracion->fecha_inicio_invierno ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6 col-lg-3">
                                        <label for="fecha_fin_invierno" class="form-label">Fin Invierno</label>
                                        <input type="date" class="form-control" id="fecha_fin_invierno" name="fecha_fin_invierno" value="{{ $configuracion->fecha_fin_invierno ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-lg-6 ">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Horarios de Verano</h5>
                                    </div>
                                    <div class="card-body">
                                        @php
                                        $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes'];
                                        @endphp
                                        @foreach($dias as $dia)
                                            <h6 class="mt-4 mb-3">{{ ucfirst($dia) }}</h6>
                                            @php
                                                $horarioVerano = $configuracion ? $configuracion->horarios->where('tipo', 'verano')->where('dia', $dia)->first() : null;
                                                $horarioVeranoPartido = $configuracion ? $configuracion->horarios->where('tipo', 'verano')->where('dia', $dia)->skip(1)->first() : null;
                                            @endphp
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="inicio_verano_{{ $dia }}" class="form-label">Inicio</label>
                                                    <input type="time" class="form-control" id="inicio_verano_{{ $dia }}" name="horarios[verano][{{ $dia }}][0][inicio]" value="{{ $horarioVerano->inicio ?? '' }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fin_verano_{{ $dia }}" class="form-label">Fin</label>
                                                    <input type="time" class="form-control" id="fin_verano_{{ $dia }}" name="horarios[verano][{{ $dia }}][0][fin]" value="{{ $horarioVerano->fin ?? '' }}" required>
                                                </div>
                                                <div class="col-md-12 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="horario_partido_verano_{{ $dia }}" name="horarios[verano][{{ $dia }}][partido]" {{ $horarioVeranoPartido ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="horario_partido_verano_{{ $dia }}">Horario partido</label>
                                                    </div>
                                                </div>
                                                <input type="hidden" value="verano" >
                                            </div>
                                            <div id="horario_partido_verano_{{ $dia }}_div" style="{{ $horarioVeranoPartido ? '' : 'display: none;' }}">
                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-6">
                                                        <label for="inicio_partido_verano_{{ $dia }}" class="form-label">Inicio (Horario Partido)</label>
                                                        <input type="time" class="form-control" id="inicio_partido_verano_{{ $dia }}" name="horarios[verano][{{ $dia }}][1][inicio]" value="{{ $horarioVeranoPartido->inicio ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="fin_partido_verano_{{ $dia }}" class="form-label">Fin (Horario Partido)</label>
                                                        <input type="time" class="form-control" id="fin_partido_verano_{{ $dia }}" name="horarios[verano][{{ $dia }}][1][fin]" value="{{ $horarioVeranoPartido->fin ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Horarios de Invierno</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($dias as $dia)
                                            <h6 class="mt-4 mb-3">{{ ucfirst($dia) }}</h6>
                                            @php
                                                $horarioInvierno = $configuracion ? $configuracion->horarios->where('tipo', 'invierno')->where('dia', $dia)->first() : null;
                                                $horarioInviernoPartido = $configuracion ? $configuracion->horarios->where('tipo', 'invierno')->where('dia', $dia)->skip(1)->first() : null;
                                            @endphp
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label for="inicio_invierno_{{ $dia }}" class="form-label">Inicio</label>
                                                    <input type="time" class="form-control" id="inicio_invierno_{{ $dia }}" name="horarios[invierno][{{ $dia }}][0][inicio]" value="{{ $horarioInvierno->inicio ?? '' }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="fin_invierno_{{ $dia }}" class="form-label">Fin</label>
                                                    <input type="time" class="form-control" id="fin_invierno_{{ $dia }}" name="horarios[invierno][{{ $dia }}][0][fin]" value="{{ $horarioInvierno->fin ?? '' }}" required>
                                                </div>
                                                <div class="col-md-12 d-flex align-items-end">
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" id="horario_partido_invierno_{{ $dia }}" name="horarios[invierno][{{ $dia }}][partido]" {{ $horarioInviernoPartido ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="horario_partido_invierno_{{ $dia }}">Horario partido</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="horario_partido_invierno_{{ $dia }}_div" style="{{ $horarioInviernoPartido ? '' : 'display: none;' }}">
                                                <div class="row g-3 mt-2">
                                                    <div class="col-md-6">
                                                        <label for="inicio_partido_invierno_{{ $dia }}" class="form-label">Inicio (Horario Partido)</label>
                                                        <input type="time" class="form-control" id="inicio_partido_invierno_{{ $dia }}" name="horarios[invierno][{{ $dia }}][1][inicio]" value="{{ $horarioInviernoPartido->inicio ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="fin_partido_invierno_{{ $dia }}" class="form-label">Fin (Horario Partido)</label>
                                                        <input type="time" class="form-control" id="fin_partido_invierno_{{ $dia }}" name="horarios[invierno][{{ $dia }}][1][fin]" value="{{ $horarioInviernoPartido->fin ?? '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <h5>Acciones</h5>
                            </div>
                            <div class="card-body d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Guardar Configuración</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($dias as $dia)
        document.getElementById('horario_partido_verano_{{ $dia }}').addEventListener('change', function() {
            document.getElementById('horario_partido_verano_{{ $dia }}_div').style.display = this.checked ? '' : 'none';
        });
        document.getElementById('horario_partido_invierno_{{ $dia }}').addEventListener('change', function() {
            document.getElementById('horario_partido_invierno_{{ $dia }}_div').style.display = this.checked ? '' : 'none';
        });
    @endforeach
});
</script>
@endsection
