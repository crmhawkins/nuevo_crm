@extends('layouts.app')

@section('titulo', 'Editar Presupuesto')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Presupuesto</h3>
                    <p class="text-subtitle text-muted">Formulario para editar un presupuesto</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('presupuestos.index')}}">Presupuestos</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar presupuesto</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="row">
                <div class="col-9">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{route('presupuesto.update', $presupuesto->id)}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="reference">Ref.:</label>
                                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" value="{{ $presupuesto->reference }}" name="reference">
                                            @error('reference')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="projetc_id">Campaña:</label>
                                            <input type="text" class="form-control @error('projetc_id') is-invalid @enderror" id="projetc_id" value="{{ $presupuesto->proyecto->name }}" name="projetc_id">
                                            @error('projetc_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="payment_method_id">Forma de pago:</label>
                                            <input type="text" class="form-control @error('payment_method_id') is-invalid @enderror" id="payment_method_id" value="{{ $presupuesto->metodoPago->name }}" name="payment_method_id">
                                            @error('payment_method_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="budget_status_id">Estado:</label>
                                            <select class="form-control @error('budget_status_id') is-invalid @enderror" id="budget_status_id" name="budget_status_id">
                                                @foreach ( $estadoPresupuesto as $estado )
                                                    <option value="{{ $estado->id }}" {{ $presupuesto->budget_status_id == $estado->id ? 'selected' : '' }}>{{ $estado->name }}</option>

                                                @endforeach
                                            </select>
                                            @error('budget_status_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="description">Observaciones:</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ $presupuesto->description}}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label for="client_id">Cliente:</label>
                                            <input type="text" class="form-control @error('client_id') is-invalid @enderror" id="client_id" value="{{ $presupuesto->cliente->name }}" name="client_id">
                                            @error('client_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="admin_user_id">Gestor:</label>
                                            <input type="text" class="form-control @error('admin_user_id') is-invalid @enderror" id="admin_user_id" value="{{ $presupuesto->usuario->name }}" name="admin_user_id">
                                            @error('admin_user_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="concept">Concepto:</label>
                                            <input type="text" class="form-control @error('concept') is-invalid @enderror" id="concept" value="{{ $presupuesto->concept }}" name="concept">
                                            @error('concept')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="creation_date">Fecha Creación:</label>
                                            <input type="date" class="form-control @error('creation_date') is-invalid @enderror" id="creation_date" value="{{ $presupuesto->creation_date }}" name="creation_date">
                                            @error('creation_date')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="note">Nota Interna:</label>
                                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ $presupuesto->note}}</textarea>
                                            @error('note')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <hr class="mt-3 mb-3">
                                <div class="row">
                                    <div class="col-12">
                                        <h3 class="text-center text-uppercase fs-5 mb-3">Conceptos</h3>
                                        <div class="d-inline-block m-auto text-center w-100">
                                            <button id="btnPropio" type="button" class="btn btn-dark">Propio</button>
                                            <button id="btnProveedor" type="button" class="btn btn-secondary">Proveedor</button>
                                        </div>
                                    </div>
                                </div>
                                <table id="conceptsTable" class="table dt_custom_budget_concepts table-hover table-striped table-bordered mt-4" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Concepto</th>
                                            <!--<th>Descripcion</th>-->
                                            <th>Unidades</th>
                                            <th>Precio/Unidad</th>
                                            <th>SUBTOTAL</th>
                                            <th>DTO</th>
                                            <th>TOTAL</th>
                                            <th>ACCIONES</th>
                                            <th hidden></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($budgetConcepts)
                                            @foreach($budgetConcepts as $budgetConcept)
                                            <?php
                                                $subtotalOwn = $budgetConcept->units*$budgetConcept->sale_price;
                                                $subtotalSupplier = $budgetConcept->total_no_discount;
                                                $subtotal = 0;
                                                if ($budgetConcept->concept_type_id == 1 ){
                                                    $subtotal = $subtotalSupplier;
                                                }
                                                if ($budgetConcept->concept_type_id == 2 ){
                                                    $subtotal = $subtotalOwn;
                                                }
                                                if ($budgetConcept->concept_type_id == 1 ){
                                                    $purchasePriceWithoutMarginBenefit = $budgetConcept->purchase_price;
                                                    $benefitMargin = $budgetConcept->benefit_margin;
                                                    $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
                                                    $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;
                                                }
                                            ?>
                                                <tr class="budgetRow" data-child-value="{{$budgetConcept->concept}}">
                                                    <td class="details-control">
                                                        @if($budgetConcept->concept_type_id == 2)
                                                            <a href="{{route('budgetConcepts.editTypeOwn', $budgetConcept->id)}}" class="btn btn-success">
                                                                <i class="fas fa-arrow-down" style="color:white;"></i>
                                                            </a>
                                                        @else
                                                            <a href="{{route('budgetConcepts.editTypeSupplier', $budgetConcept->id)}}" class="btn btn-success">
                                                                <i class="fas fa-arrow-down" style="color:white;"></i>
                                                            </a>
                                                        @endif

                                                    </td>
                                                    <td hidden >{{ $budgetConcept->id }}</td>
                                                    <td>{{ $budgetConcept->title }}</td>
                                                    <!--<td>{{ $budgetConcept->concept }}</td>-->
                                                    <td >
                                                        @if($budgetConcept->concept_type_id == 1)
                                                            @if($budgetConcept->purchase_price != null)
                                                                {{ $budgetConcept->units }}
                                                            @endif
                                                        @else
                                                            {{ $budgetConcept->units }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetPriceRow" >
                                                        @if($budgetConcept->concept_type_id == 1)
                                                            @if( $purchasePriceWithMarginBenefit != null)
                                                                 {{  round(
                                                                        (number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units / 100 * number_format((float)$budgetConcept->benefit_margin, 2, '.', ''))
                                                                        + (number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units), 2) }}

                                                                 <!--
                                                                     {{  round((number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units / 100 * number_format((float)$budgetConcept->benefit_margin, 2, '.', '')) + (number_format((float)$budgetConcept->purchase_price, 2, '.', '') / $budgetConcept->units), 2) }}
                                                                 -->
                                                            @endif
                                                        @else
                                                            {{ number_format((float)$budgetConcept->sale_price, 2, '.', '')  }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetSubtotalRow">
                                                        @if($budgetConcept->concept_type_id == 1)
                                                            @if($budgetConcept->purchase_price != null)
                                                                {{ number_format((float)$subtotalSupplier, 2, '.', '')  }}
                                                            @endif
                                                        @else
                                                            {{ number_format((float)$subtotalOwn, 2, '.', '')  }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetDiscountRow">
                                                        @if(!$budgetConcept->discount)
                                                            <input type="number" data-id-budget="{{ $presupuesto->id }}" data-id="{{ $budgetConcept->id }}" class="form-control discountInput" style="width:80px" name="discount[{{ $budgetConcept->id }}]" min="0" max="100"  value="0" data-subtotal="{{$subtotal}}">
                                                        @else
                                                            <input type="number" data-id-budget="{{ $presupuesto->id }}" data-id="{{ $budgetConcept->id }}" class="form-control discountInput" style="width:80px" name="discount[{{ $budgetConcept->id }}]" min="0" max="100" value="{{ $budgetConcept->discount }}" data-subtotal="{{$subtotal}}">
                                                        @endif
                                                    </td>
                                                    <td class="conceptTotal"> {{ number_format((float)$budgetConcept->total, 2, '.', '')  }}</td>
                                                    <td>
                                                        @if($budgetConcept->concept_type_id == 1)
                                                            {{-- <a class="btn btn-success" href="{{ route('admin.budget_concepts.editTypeSupplier',$budgetConcept->id) }}"><i class="fas fa-pencil-alt"></i></a> --}}
                                                        @else
                                                            {{-- <a class="btn btn-success" href="{{ route('admin.budget_concepts.editTypeOwn',$budgetConcept->id) }}"><i class="fas fa-pencil-alt"></i></a> --}}
                                                        @endif
                                                        @if($budgetConcept->concept_type_id == 1)
                                                            <a class="btn btn-danger destroyConceptSupplier" data-concept-id="{{$budgetConcept->id}}" style="color:white" ><i class="fas fa-times"></i></a>
                                                        @else
                                                            <a id="deleteOwn" data-id="{{$budgetConcept->id}}" class="btn btn-danger destroyConceptOwn" data-concept-id="{{$budgetConcept->id}}" style="color:white" ><i class="fas fa-times"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                                {{-- Boton --}}
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 form-group">
                                    <div class="container">
                                        <table class="table display responsive no-wrap">
                                          <thead class="thead-dark">
                                            <tr>
                                              <th>Bruto</th>
                                              <!--<th>Retenciones %</th>-->
                                              <th>Descuento</th>
                                              <th>Base</th>
                                              <th>% IVA</th>
                                              <th>IVA</th>
                                              <th>TOTAL</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                                <td><span id="gross">{{ number_format((float)$presupuesto->gross, 2, '.', '')  }}</span></td>
                                                <!--<td><input id ="withhold" type="number" class="form-control" style="width:80px" id="gross" name="gross" min="0" max="1000" value="0" ></td>-->
                                                <td id="discount_summary_amount">{{ number_format((float)$presupuesto->discount, 2, '.', '')  }}</td>
                                                <td id="base_amount"> {{ number_format((float)$presupuesto->base, 2, '.', '')  }}</td>
                                                <td>
                                                    <input type="number" class="form-control" style="width:80px" id="iva" name="iva_percentage" min="0" max="100"
                                                    @if($presupuesto->iva_percentage == null) value="21"
                                                    @else value="{{ number_format((float)$budget->iva_percentage, 2, '.', '')  }}"
                                                    @endif >
                                                </td>
                                                <td id="iva_amount">{{ number_format((float)$presupuesto->iva, 2, '.', '')  }}</td>
                                                <td id="budget_total"><strong>{{ number_format((float)$presupuesto->total, 2, '.', '')  }} €</strong></td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>

                                      <input type="hidden" id="base" name="base" value="0">
                                      <input type="hidden" id="gross" name="gross" value="0">
                                      <input type="hidden" id="total" name="total" value="0">
                                      <input type="hidden" id="iva_total" name="iva" value="0">
                                      @if($presupuesto->budget_status_id == null || $presupuesto->budget_status_id == 1 || $presupuesto->budget_status_id == 2 )
                                        <input type="hidden" id="thisbudgetstatus"  value="0">
                                      @else
                                        <input type="hidden" id="thisbudgetstatus"  value="{{ $presupuesto->budget_status_id}}" data-status-name="{{$thisBudgetStatus->name}}">
                                      @endif
                                      <br>
                                      <!--
                                      <div class="container">
                                        <button type="button" id="test" class="btn btn-info ">
                                            Facturar parcialmente 50%
                                        </button>
                                     </div>
                                    -->
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
                <div class="col-3">
                    <div class="card p-3">
                        <div class="card-title">
                            Acciones
                            <hr>
                        </div>
                        <div class="card-body">
                            <a href="" id="actualizarPresupuesto" class="btn btn-success btn-block">Actualizar Presupuesto</a>
                            <a href="" id="aceptarPresupuesto" class="btn btn-primary btn-block mt-3">Aceptar Presupuesto</a>
                            <a href="" class="btn btn-danger btn-block mt-3">Cancelar Presupuesto</a>
                            <a href="" class="btn btn-secondary btn-block mt-3">Duplicar Presupuesto</a>
                            <a href="" class="btn btn-dark btn-block mt-3">Generar PDF</a>
                            <a href="" class="btn btn-dark btn-block mt-3">Enviar por email</a>
                            <a href="" class="btn btn-dark btn-block mt-3">Generar factura</a>
                            <a href="" class="btn btn-dark btn-block mt-3">Generar factura parcial</a>
                            <a href="" class="btn btn-dark btn-block mt-3">Generar tareas</a>
                            <a href="" class="btn btn-outline-danger btn-block mt-3">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

    @include('partials.toast')

@endsection


@section('scripts')
<script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js" integrity="sha512-rMGGF4wg1R73ehtnxXBt5mbUfN9JUJwbk21KMlnLZDJh7BkPmeovBuddZCENJddHYYMkCh9hPFnPmS9sspki8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script type="text/javascript">
    var urlTemplate = "{{ route('campania.createFromBudget', ['cliente' => 'CLIENTE_ID']) }}";
    var urlTemplateCliente = "{{ route('cliente.createFromBudget') }}";

</script>

<script>
    $(document).ready(function() {

        // Boton de Concepto Propio
        $('#btnPropio').click(function(){
            console.log('Click en Boton Propio');
            const idPresupuesto = @json($presupuesto->id);
            var baseUrl = "{{ route('budgetConcepts.createTypeOwn', ['budget' => 'PLACEHOLDER']) }}";
            var finalUrl = baseUrl.replace('PLACEHOLDER', idPresupuesto);

            console.log(idPresupuesto);
            window.open(finalUrl, '_self');

        });

        // Boton de Concepto Proveedor
        $('#btnProveedor').click(function(){
            console.log('Click en Boton Propio');
            const idPresupuesto = @json($presupuesto->id);
            var baseUrl = "{{ route('budgetConcepts.createTypeSupplier', ['budget' => 'PLACEHOLDER']) }}";
            var finalUrl = baseUrl.replace('PLACEHOLDER', idPresupuesto);

            console.log(idPresupuesto);
            window.open(finalUrl, '_self');
        });

        // Boton Actualizar presupuesto
        $('#actualizarPresupuesto').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('form').submit(); // Esto envía el formulario.
        });

        // Boton Aceptar presupuesto
        $('#aceptarPresupuesto').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);

            $.ajax({
                url: '{{ route("presupuesto.aceptarPresupuesto") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response, status) {
                    console.log(response)

                },
                error: function(xhr, status, error) {
                    // Manejo de errores
                    console.error(error);
                }
            });
            const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                    didClose: () => {
                        // Recargar la página una vez que la alerta se cierra
                        location.reload();
                    }
                });
                // Lanzamos la alerta
                Toast.fire({
                    icon: "success",
                    title: "El presupuesto se ha actualizado correctamente a su estado Aceptado"
                });
                return;
        });

        // Boton eliminar concepto propio
        $('#deleteOwn').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = $(this).attr('data-id');

            $.ajax({
                url: '{{ route("budgetConcepts.delete") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response, status) {
                    console.log(response)

                },
                error: function(xhr, status, error) {
                    // Manejo de errores
                    console.error(error);
                }
            });
            const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                    didClose: () => {
                        // Recargar la página una vez que la alerta se cierra
                        location.reload();
                    }
                });
                // Lanzamos la alerta
                Toast.fire({
                    icon: "success",
                    title: "El concepto se ha elimino correctamente a su estado Aceptado"
                });
                return;
        });

        // Boton añadir campaña
        $('#newCampania').click(function(){
            var clientId = $('select[name="client_id"]').val();
            console.log(clientId)
            if (clientId == '' || clientId == null) {
                // Alerta Toast de error
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                // Lanzamos la alerta
                Toast.fire({
                    icon: "error",
                    title: "Por favor, selecciona un cliente."
                });
                return;
            }

            // Abrimos pestaña para crear campaña
            var finalUrl = urlTemplate.replace('CLIENTE_ID', clientId);
            window.open(finalUrl, '_self');
        });

        // Boton añadir cliente
        $('#newClient').click(function(){
            // Abrimos pestaña para crear campaña
            window.open(urlTemplateCliente, '_self');
        });

        // Boton Cliente
        $('#cliente').on( "change", function(){
            var clienteId = $(this).val();
            $.ajax({
                url: '{{ route("campania.postProjectsFromClient") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    client_id: clienteId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {
                    console.log(response);

                    var select = $('#proyecto'); // Reemplaza 'tuSelect' con el ID de tu select
                    select.empty(); // Limpia las opciones actuales
                    if (response.length === 0) {
                        select.attr("disabled", true)
                        select.append($('<option></option>').attr('value', null).text('No hay campaña de este cliente'));
                    }else{
                        $.each(response, function(key, value) {
                            select.append($('<option></option>').attr('value', value.id).text(value.name));
                        });
                        select.attr("disabled", false)
                    }
                },
                error: function(xhr, status, error) {
                    // Manejo de errores
                    console.error(error);
                }
            });

        })

        // Boton eliminar concepto
        $('.destroyConceptOwn').on('click', function(){
            $('#loadingOverlay').css('display', 'block');

            const id = $(this).attr('data-concept-id');
            $.ajax({
                url: '{{ route("budgetConcepts.delete") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {

                    setTimeout(() => {
                        $('#loadingOverlay').css('display', 'none');
                        location.reload()
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    $('#loadingOverlay').css('display', 'none');

                    // Manejo de errores
                    console.error(error);
                }
            })
        })

        // Input descuento concepto
        $('.discountInput').on('change', function(){
            $('#loadingOverlay').css('display', 'block');

            const id = $(this).attr('data-id');
            const idBudget = $(this).attr('data-id-budget');
            const descuento = $(this).val();
            $.ajax({
                url: '{{ route("budgetConcepts.discountUpdate") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    idBudget: idBudget,
                    idConcept: id,
                    discount: descuento
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {

                    setTimeout(() => {
                        $('#loadingOverlay').css('display', 'none');
                        location.reload()
                    }, 2000);
                },
                error: function(xhr, status, error) {
                    $('#loadingOverlay').css('display', 'none');

                    // Manejo de errores
                    console.error(error);
                }
            })
        })

        // Actualizar Precios
        const actualizarPrecios = () => {
            const conceptosPresupuesto = @json($budgetConcepts);
            let total = 0;
            let iva;
            let descuento;
            console.log($('#total').val())

            conceptosPresupuesto.map((concepto) => {
                console.log(concepto.total)

                descuento += concepto.discount;
                total += concepto.total;

            })

            const ivaTotal = (total*21)/100
            console.log(ivaTotal)
            $('#base').val(total)
            $('#gross').val(total)
            $('#total').val(total+ivaTotal)
            $('#iva_total').val(total+21/100)
            $('#budget_total').html(`<strong>${formatearNumero(total+ivaTotal)} €</strong>`)
            $('#gross').html(formatearNumero(total) + ' €')
            $('#base_amount').html(formatearNumero(total) + ' €')
            $('#iva_amount').html(formatearNumero(ivaTotal) + ' €')

        }

        // Formatear los numeros
        function formatearNumero(numero) {
            return numero.toLocaleString('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        }

        // actualizarPrecios()

    });
</script>
@endsection

