<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir nuevo registro</title>
    <link rel="stylesheet" href="{{ asset('assets/vendors/choices.js/choices.min.css') }}">
    <!-- Aquí puedes agregar otros enlaces CSS necesarios -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-d2e38ed8.css') }}" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="" style="overflow-x: hidden">
    <div id="app">
        <div id="loadingOverlay" style="display: block; position: fixed; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.5); z-index: 50000; cursor: pointer;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-black" role="status">
                    <span class="sr-only"></span>
                </div>
            </div>
        </div>
        <div class="css-96uzu9"></div>
        <div class="contenedor p-4">
                <div class="page-heading card" style="box-shadow: none !important">
                    <div class="page-title card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Añadir nuevo registro</h3>
                                <p class="text-subtitle text-muted">Formulario para añadir un nuevo registro de ayuda</p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">Todas las ayudas</li>
                                        <li class="breadcrumb-item active" aria-current="page">Añadir</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section mt-4">
                        <div class="card">
                            <div class="card-body">
                                <form method="POST" action="{{ route('kitDigital.storeComercial') }}" enctype="multipart/form-data" id="ayudaForm">
                                    @csrf
                                    <input type="hidden" name="admin_user_id" value="{{ $usuario }}" />

                                    <div class="row">
                                        <!-- Cliente -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="cliente">Empresa / Nombre de autónomo</label>
                                            <input type="text" class="form-control" id="cliente" name="cliente" value="{{ old('cliente') }}">
                                            @if ($errors->has('cliente'))
                                                <div class="alert alert-danger">{{ $errors->first('cliente') }}</div>
                                            @endif
                                        </div>
                                        <!-- CIF -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="nif">CIF / DNI</label>
                                            <input type="text" class="form-control" id="nif" name="nif" value="{{ old('nif') }}">
                                            @if ($errors->has('nif'))
                                                <div class="alert alert-danger">{{ $errors->first('nif') }}</div>
                                            @endif
                                        </div>
                                        <!-- Email -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                            @if ($errors->has('email'))
                                                <div class="alert alert-danger">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                        <!-- telefono -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="telefono">Telefono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}">
                                            @if ($errors->has('telefono'))
                                                <div class="alert alert-danger">{{ $errors->first('telefono') }}</div>
                                            @endif
                                        </div>
                                        <!-- direccion -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="direccion">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ old('direccion') }}">
                                            @if ($errors->has('direccion'))
                                                <div class="alert alert-danger">{{ $errors->first('direccion') }}</div>
                                            @endif
                                        </div>
                                        <!-- C.P -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="cp">C.P</label>
                                            <input type="text" class="form-control" id="cp" name="cp" value="{{ old('cp') }}">
                                            @if ($errors->has('cp'))
                                                <div class="alert alert-danger">{{ $errors->first('cp') }}</div>
                                            @endif
                                        </div>
                                        <!-- Ciudad -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="ciudad">Localidad</label>
                                            <input type="text" class="form-control" id="ciudad" name="ciudad" value="{{ old('ciudad') }}">
                                            @if ($errors->has('ciudad'))
                                                <div class="alert alert-danger">{{ $errors->first('ciudad') }}</div>
                                            @endif
                                        </div>

                                        <!-- Comercial -->
                                        <div class="col-12 col-md-6 form-group mt-2">
                                            <label class="form-label" for="comercial_id">Comercial</label>
                                            <select name="comercial_id" id="comercial_id" class="form-control">
                                                <option value="">Seleccione un comercial</option>
                                                @foreach($comerciales as $comercial)
                                                    <option value="{{ $comercial->id }}" {{ old('comercial_id') == $comercial->id ? 'selected' : '' }}>{{ $comercial->name }} {{ $comercial->surname }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('comercial_id'))
                                                <div class="alert alert-danger">{{ $errors->first('comercial_id') }}</div>
                                            @endif
                                        </div>

                                        <!-- Comentarios -->
                                        <div class="col-12 form-group">
                                            <label class="form-label" for="comentario">Comentario</label>
                                            <textarea class="form-control" rows="5" id="comentario" name="comentario">{{ old('comentario') }}</textarea>
                                        </div>
                                        <div class="g-recaptcha" data-sitekey="{{env('NOCAPTCHA_SITEKEY')}}"></div>

                                        <!-- Botón de acción -->
                                        <div class="col-12 mt-4">
                                            <button type="submit" class="btn btn-success btn-block w-100">Guardar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
        </div>
    </div>



<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" crossorigin="anonymous"></script>
<script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script>
    document.getElementById('ayudaForm').addEventListener('submit', function(event) {
        event.preventDefault();

        var response = grecaptcha.getResponse();
        if(response.length == 0) {
            alert("Debes verificar el CAPTCHA.");
        } else {
            this.submit();
        }
    });
   document.addEventListener("DOMContentLoaded", function() {
       var loader = document.getElementById('loadingOverlay');
       if (loader) {
               loader.style.display = 'none';
       }
   });

   document.addEventListener("DOMContentLoaded", function() {
       const rows = document.querySelectorAll("tr.clickable-row");

       // Agregar evento de clic a las filas
       rows.forEach(row => {
           row.addEventListener("click", () => {
               const href = row.dataset.href;
               if (href) {
                   window.location.href = href;
               }
           });
       });

       // Detener la propagación de los eventos de clic en los enlaces dentro de las filas
       const links = document.querySelectorAll("tr.clickable-row a");

       links.forEach(link => {
           link.addEventListener("click", (event) => {
               event.stopPropagation(); // Detiene la propagación del evento
           });
       });

       // Si tienes botones o cualquier otro elemento interactivo, repite el proceso anterior para ellos
       const buttons = document.querySelectorAll("tr.clickable-row button");
       buttons.forEach(button => {
           button.addEventListener("click", (event) => {
               event.stopPropagation();
           });
       });
   });
   document.addEventListener("DOMContentLoaded", function() {
       const rows = document.querySelectorAll("tr.clickable-row-sta");

       // Agregar evento de clic a las filas
       rows.forEach(row => {
           row.addEventListener("click", () => {
               const href = row.dataset.href;
               if (href) {
                   window.open(href, '_blank');
               }
           });
       });

       // Detener la propagación de los eventos de clic en los enlaces dentro de las filas
       const links = document.querySelectorAll("tr.clickable-row-sta a");

       links.forEach(link => {
           link.addEventListener("click", (event) => {
               event.stopPropagation(); // Detiene la propagación del evento
           });
       });

       // Si tienes botones o cualquier otro elemento interactivo, repite el proceso anterior para ellos
       const buttons = document.querySelectorAll("tr.clickable-row-sta button");
       buttons.forEach(button => {
           button.addEventListener("click", (event) => {
               event.stopPropagation();
           });
       });
   });
</script>

</body>
</html>
