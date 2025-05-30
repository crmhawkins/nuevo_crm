<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="name">Nombre del departamento</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="fecha" value="{{ old('fecha', isset($department) ? $department->name : '') }}" name="name">
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="phone">Telefono</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="fecha" value="{{ old('fecha', isset($department) ? $department->phone : '') }}" name="phone">
        @error('phone')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    {{-- Boton --}}
    <div class="form-group mt-5">
        <button type="submit" class="btn btn-success w-100 text-uppercase">
            {{ $buttonText }}
        </button>
    </div>
</form>
