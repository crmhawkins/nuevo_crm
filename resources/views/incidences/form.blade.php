<form action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="flex flex-col mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="titulo">Gestor</label>
        <div class="form-group flex flex-row align-items-center mb-0">
            <select class="choices w-100 form-select @error('gestor_id') is-invalid @enderror" name="gestor_id">
                @if ($usuarios->count() > 0)
                    <option value="{{null}}">--- Seleccione un usuario ---</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ (isset($incidencia) && $incidencia->gestor_id == $usuario->id) ? 'selected' : '' }}>
                            {{ $usuario->name.' '.$usuario->surname }}
                        </option>
                    @endforeach
                @else
                    <option value="">No existen usuarios todavía</option>
                @endif
            </select>
        </div>
        @error('gestor_id')
            <p class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </p>
        @enderror
    </div>

    <div class="flex flex-col mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="admin_user_id">Admin User</label>
        <div class="form-group flex flex-row align-items-center mb-0">
            <select class="choices w-100 form-select @error('admin_user_id') is-invalid @enderror" name="admin_user_id">
                @if ($usuarios->count() > 0)
                    <option value="{{null}}">--- Seleccione un usuario ---</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ (isset($incidencia) && $incidencia->admin_user_id == $usuario->id) ? 'selected' : '' }}>
                            {{ $usuario->name.' '.$usuario->surname }}
                        </option>
                    @endforeach
                @else
                    <option value="">No existen usuarios todavía</option>
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
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="titulo">Título</label>
        <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo" value="{{ old('titulo', isset($incidencia) ? $incidencia->titulo: '') }}" name="titulo">
        @error('titulo')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="descripcion">Descripción</label>
        <textarea type="text" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion">{{old('descripcion', isset($incidencia) ? $incidencia->descripcion: '') }}</textarea>
        @error('descripcion')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="client_id">Cliente</label>
        <select class="choices w-100 form-select @error('client_id') is-invalid @enderror" name="client_id">
            @if ($clientes->count() > 0)
                <option value="{{null}}">--- Seleccione un cliente ---</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ (isset($incidencia) && $incidencia->client_id == $cliente->id) ? 'selected' : '' }}>
                        {{ $cliente->name }}
                    </option>
                @endforeach
            @else
                <option value="">No existen clientes todavía</option>
            @endif
        </select>
        @error('client_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="budget_id">Presupuesto</label>
        <select class="choices w-100 form-select @error('budget_id') is-invalid @enderror" name="budget_id">
            @if ($presupuestos->count() > 0)
                <option value="{{null}}">--- Seleccione un presupuesto ---</option>
                @foreach ($presupuestos as $presupuesto)
                    <option value="{{ $presupuesto->id }}" {{ (isset($incidencia) && $incidencia->budget_id == $presupuesto->id) ? 'selected' : '' }}>
                        {{ $presupuesto->reference }}
                    </option>
                @endforeach
            @else
                <option value="">No existen presupuestos todavía</option>
            @endif
        </select>
        @error('budget_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="supplier_id">Proveedor</label>
        <select class="choices w-100 form-select @error('supplier_id') is-invalid @enderror" name="supplier_id">
            @if ($suppliers->count() > 0)
                <option value="{{null}}">--- Seleccione un proveedor ---</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ (isset($incidencia) && $incidencia->supplier_id == $supplier->id) ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            @else
                <option value="">No existen proveedores todavía</option>
            @endif
        </select>
        @error('supplier_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <label class="mb-2 text-left uppercase" style="font-weight: bold" for="status">Estado</label>
        <select class="choices w-100 form-select @error('status') is-invalid @enderror" name="status">
            @if ($estados->count() > 0)
                <option value="{{null}}">--- Seleccione un estado ---</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->id }}" {{ (isset($incidencia) && $incidencia->status == $estado->id) ? 'selected' : '' }}>
                        {{ $estado->name }}
                    </option>
                @endforeach
            @else
                <option value="">No existen estados todavía</option>
            @endif
        </select>
        @error('status')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="form-group mb-4">
        <button type="submit" class="btn btn-primary">{{ $buttonText }}</button>
    </div>
</form>
