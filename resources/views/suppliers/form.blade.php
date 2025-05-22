<form action="{{$action}}" method="POST" class="form-primary">
    @csrf
    @if(isset($proveedor))
        @method('POST')
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Nombre del proveedor:</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" value="{{ old('name', isset($proveedor) ? $proveedor->name : '') }}" name="name">
                @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="cif">CIF:</label>
                <input type="text" class="form-control @error('cif') is-invalid @enderror" id="cif"value="{{ old('cif', isset($proveedor) ? $proveedor->cif : '') }}" name="cif">
                @error('cif')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" class="form-control @error('dni') is-invalid @enderror" id="dni" value="{{ old('dni', isset($proveedor) ? $proveedor->dni : '') }}" name="dni">
                @error('dni')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>

            <div class="form-group">
                <label for="work_activity">Actividad:</label>
                <input type="text" class="form-control @error('work_activity') is-invalid @enderror" id="work_activity" value="{{ old('work_activity', isset($proveedor) ? $proveedor->work_activity : '') }}" name="work_activity">
                @error('work_activity')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="mt-4 form-floating">
                <textarea class="form-control" placeholder="Escribe la anotación..."
                    id="floatingTextarea" name="note">{{ old('note', isset($proveedor) ? $proveedor->note : '') }}</textarea>
                <label for="floatingTextarea">Notas</label>
                @error('notes')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="address">Dirección:</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" value="{{ old('address', isset($proveedor) ? $proveedor->address : '') }}" name="address">
                @error('address')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="country">Pais:</label>
                <select class="choices form-select" name="country">
                    @if ($countries->count() > 0)
                        <option value="{{null}}">Seleccione un pais </option>
                        @foreach ( $countries as $country )
                            <option @if(old('country') == $country->name) {{'selected'}} @endif value="{{$country->name}}" >{{$country->name}}</option>
                        @endforeach
                    @else
                        <option value="{{null}}">No existen clientes todavia</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="city">Ciudad:</label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" value="{{ old('city', isset($proveedor) ? $proveedor->city : '') }}" name="city">
                @error('city')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="province">Provincia:</label>
                <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" value="{{ old('province', isset($proveedor) ? $proveedor->province : '') }}" name="province">
                @error('province')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="zipcode">Código postal:</label>
                <input type="text" class="form-control @error('zipcode') is-invalid @enderror" id="zipcode" value="{{ old('zipcode', isset($proveedor) ? $proveedor->zipcode : '') }}" name="zipcode">
                @error('zipcode')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                @enderror
            </div>

        </div>
    </div>


    <h3 class="mt-5 mb-2 text-center uppercase">Informacion de Contacto</h3>
    <hr class="mb-4">
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" value="{{ old('email', isset($proveedor) ? $proveedor->email : '') }}" name="email">
        @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="phone">Teléfono:</label>
        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" value="{{ old('phone', isset($proveedor) ? $proveedor->phone : '') }}" name="phone">
        @error('phone')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
        @enderror
    </div>
    <div class="form-group">
        <label for="fax">Fax:</label>
        <input type="text" class="form-control @error('fax') is-invalid @enderror" id="fax" value="{{ old('fax', isset($proveedor) ? $proveedor->fax : '') }}" name="fax">
        @error('fax')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
        @enderror
    </div>

    <h3 class="mt-5 mb-2 text-center uppercase">Redes Sociales - web</h3>
    <hr class="mb-4">

    <div class="form-group">
        <label for="web">Web:</label>
        <input type="text" class="form-control @error('web') is-invalid @enderror" id="web" value="{{ old('web', isset($proveedor) ? $proveedor->web : '') }}" name="web">
        @error('web')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
        @enderror
    </div>

    <h4 class="mt-5 mb-2 text-left uppercase">Redes Sociales</h4>
    <div class="row form-group">
        <div class="col-md-4 mb-3">
            <label for="facebook"><i class="fa-brands fa-facebook"></i> Facebook:</label>
            <input type="text" class="form-control @error('facebook') is-invalid @enderror" id="facebook" value="{{ old('facebook', isset($proveedor) ? $proveedor->facebook : '') }}" name="facebook">
            @error('facebook')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="twitter"><i class="fa-brands fa-x-twitter"></i> Twitter:</label>
            <input type="text" class="form-control @error('twitter') is-invalid @enderror" id="twitter" value="{{ old('twitter', isset($proveedor) ? $proveedor->twitter : '') }}" name="twitter">
            @error('twitter')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="linkedin"><i class="fa-brands fa-linkedin"></i> Linkedin:</label>
            <input type="text" class="form-control @error('linkedin') is-invalid @enderror" id="linkedin" value="{{ old('linkedin', isset($proveedor) ? $proveedor->linkedin : '') }}" name="linkedin">
            @error('linkedin')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="instagram"><i class="fa-brands fa-square-instagram"></i> Instagram:</label>
            <input type="text" class="form-control @error('instagram') is-invalid @enderror" id="instagram" value="{{ old('instagram', isset($proveedor) ? $proveedor->instagram : '') }}" name="instagram">
            @error('instagram')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
        <div class="col-md-4 mb-3">
            <label for="pinterest"><i class="fa-brands fa-pinterest"></i> Pinterest:</label>
            <input type="text" class="form-control @error('pinterest') is-invalid @enderror" id="pinterest" value="{{ old('pinterest', isset($proveedor) ? $proveedor->pinterest : '') }}" name="pinterest">
            @error('pinterest')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror
        </div>
    </div>

    <div class="form-group mt-5">
        <button type="submit" class="btn btn-primary w-100">
            {{ $buttonText }}
        </button>
    </div>
</form>
