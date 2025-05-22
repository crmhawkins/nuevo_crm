<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($contrato))
        @method('PUT')
    @endif
    <h3 class="mb-2 text-left uppercase">Usuario Asociado</h3>
    <div class="flex flex-col mb-4">
        <div class="form-group flex flex-row align-items-center mb-0">
            <select class="choices w-100 form-select @error('admin_user_id') is-invalid @enderror" name="admin_user_id">
                @if ($usuarios->count() > 0)
                    <option value="{{null}}">--- Seleccione un usuario ---</option>
                    @foreach ($usuarios as $usuario)
                        <option data-id="{{ $usuario->id }}" value="{{ $usuario->id }}" {{  (isset($contrato) && $contrato->admin_user_id == $usuario->id) ? 'selected' : '' }}>{{ $usuario->name.' '.$usuario->surname }}</option>
                    @endforeach
                @else
                    <option value="">No existen usuarios todavia</option>
                @endif
            </select>
        </div>
        @error('admin_user_id')
            <p class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </p>
        @enderror
    </div>
    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="fecha">Fecha</label>
        <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" value="{{ old('fecha', isset($contrato) ? \Carbon\Carbon::parse($contrato->fecha)->format('Y-m-d') : '') }}" name="fecha">
        @error('fecha')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="mb-4">
        <label for="archivo" class="mb-2 text-left uppercase" style="font-weight: bold">Archivo de Nómina (PDF)</label>
        <input type="file" class="form-control" id="archivo" name="archivo" >
        @error('archivo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
        @if(isset($contrato) && $contrato->archivo)
            <div class="mt-2">
                <a href="{{ asset('storage/' . $contrato->archivo) }}" target="_blank">Ver archivo actual</a>
            </div>
        @endif
    </div>

    {{-- Boton --}}
    <div class="form-group mt-5">
        <button type="submit" class="btn btn-success w-100 text-uppercase">
            {{ $buttonText }}
        </button>
    </div>
</form>
