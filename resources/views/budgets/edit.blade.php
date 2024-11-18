@extends('layouts.app')

@section('titulo', 'Editar Presupuesto')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body" >
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
                            <form id="update" action="{{route('presupuesto.update', $presupuesto->id)}}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="reference">Ref.:</label>
                                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" value="{{ $presupuesto->reference }}" name="reference">
                                            @error('reference')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="mb-2 text-left">Campañas</label>
                                            <div class="flex flex-row align-items-start mb-0">
                                                <select class=" form-select w-100 @error('project_id') is-invalid @enderror" name="project_id"  id="proyecto" @if($campanias != null )@if( $campanias->count() < 0){{'disabled'}} @endif @endif >
                                                            <option value="{{null}}">Seleccione una Campaña</option>
                                                            @foreach ( $campanias as $campania )
                                                                <option value="{{$campania->id}}"  @selected(old('project_id', $projectId ?? $presupuesto->project_id) == $campania->id) >{{$campania->name}}</option>
                                                            @endforeach
                                                </select>
                                                <button id="newCampania" type="button" class="btn btn-color-1 ml-3" style="height: fit-content"><i class="fa-solid fa-plus"></i></button>

                                            </div>
                                            @error('project_id')
                                                <p class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="payment_method_id">Forma de pago:</label>
                                            <div class="flex flex-row align-items-start mb-0">
                                                <select class=" form-select w-100 @error('payment_method_id') is-invalid @enderror" name="payment_method_id"  id="payment_method_id" >
                                                            <option value="{{null}}">Seleccione una forma de pago</option>
                                                            @foreach ( $formasPago as $formaPago )
                                                                <option @if(old('payment_method_id', $presupuesto->payment_method_id) == $formaPago->id) {{'selected'}} @endif value="{{$formaPago->id}}">{{$formaPago->name}}</option>
                                                            @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="budget_status_id">Estado:</label>
                                            <select class="form-control @error('budget_status_id') is-invalid @enderror" id="budget_status_id" name="budget_status_id">
                                                @foreach ( $estadoPresupuesto as $estado )
                                                    <option value="{{ $estado->id }}" {{ $presupuesto->budget_status_id == $estado->id ? 'selected' : '' }}>{{ $estado->name }} </option>

                                                @endforeach
                                            </select>
                                            @error('budget_status_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="description">Observaciones:</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ $presupuesto->description}}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="text-left mb-2">Cliente Asociado:</label>
                                            <div class="flex flex-row align-items-start">
                                                <select id="cliente" class=" w-100 form-select @error('client_id') is-invalid @enderror" name="client_id" >
                                                    @if ($clientes->count() > 0)
                                                    <option value="">Seleccione un Cliente</option>
                                                        @foreach ( $clientes as $cliente )
                                                            <option @if($presupuesto->client_id == $cliente->id) {{'selected'}} @endif data-id="{{$cliente->id}}" value="{{$cliente->id}}">{{$cliente->company ?? $cliente->name}}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="">No existen clientes todavia</option>
                                                    @endif
                                                </select>
                                                <button id="newClient" type="button" class="btn btn-color-1 ml-3" style="height: fit-content" @if(isset($petitionId)){{'disabled'}}@endif><i class="fa-solid fa-plus"></i></button>
                                            </div>
                                            @error('client_id')
                                                <p class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left">Gestor</label>
                                            <select class=" form-select w-100 @error('admin_user_id') is-invalid @enderror" name="admin_user_id" id="gestor">
                                                @if ($gestores->count() > 0)
                                                    @foreach ( $gestores as $gestor )
                                                        <option  {{ old('admin_user_id',$presupuesto->admin_user_id)  == $gestor->id ? 'selected' : '' }}  value="{{$gestor->id}}">{{$gestor->name}}</option>
                                                    @endforeach
                                                @else
                                                    <option value="{{null}}">No existen gestores todavia</option>
                                                @endif
                                            </select>
                                            @error('admin_user_id')
                                                <p class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </p>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="concept">Concepto:</label>
                                            <input type="text" class="form-control @error('concept') is-invalid @enderror" id="concept" value="{{ $presupuesto->concept }}" name="concept">
                                            @error('concept')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="creation_date">Fecha Creación:</label>
                                            <input type="date" class="form-control @error('creation_date') is-invalid @enderror" id="creation_date" value="{{ $presupuesto->creation_date }}" name="creation_date">
                                            @error('creation_date')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="note">Nota Interna:</label>
                                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ $presupuesto->note}}</textarea>
                                            @error('note')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 form-check form-switch d-flex justify-content-center mt-2">
                                    <div class="col-6 form-check form-switch d-flex justify-content-center mt-2">
                                        <input type="checkbox" class="form-check-input" id="no_sumatorio" name="no_sumatorio">
                                        <label class="form-check-label ml-2" for="no_sumatorio">No mostrar sumatorio</label>
                                    </div>
                                    <div class="col-6 form-check form-switch d-flex justify-content-center mt-2">
                                        <input type="checkbox" class="form-check-input" id="is_ceuta" name="is_ceuta" value="1" {{ old('ceuta',$presupuesto->is_ceuta) ? ' checked' : '' }}>
                                        <label class="form-check-label ml-2" for="is_ceuta">Ceuta</label>
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
                                                    $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit + $marginBenefitToAdd;
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
                                                            {{ $budgetConcept->units }}
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
                                                            <a id="deleteOwn" data-id="{{$budgetConcept->id}}" class="btn btn-danger destroyConceptOwn" data-concept-id="{{$budgetConcept->id}}" style="color:white" ><i class="fas fa-times"></i></a>
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
                                                value="{{ number_format((float)$presupuesto->iva_percentage, 2, '.', '')  }}" >
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
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                Acciones
                                <hr>
                            </div>
                            <a href="" id="actualizarPresupuesto" class="btn btn-success btn-block mb-3">Actualizar</a>
                            <a href="" id="aceptarPresupuesto" class="btn btn-primary btn-block mb-3">Aceptar</a>
                            <a href="" id="cancelarPresupuesto"class="btn btn-danger btn-block mb-3">Cancelar</a>
                            <form action="{{ route('presupuesto.duplicate', $presupuesto->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-block mb-3">Duplicar</button>
                            </form>
                            <a href="" id="generatePdf" class="btn btn-dark btn-block mb-3">Generar PDF</a>
                            <a href="" id="enviarEmail" data-id="{{$presupuesto->id}}" class="btn btn-dark btn-block mb-3">Enviar por email</a>
                            <a href="" id="generateInvoice" class="btn btn-dark btn-block mb-3">Generar factura</a>
                            <a href="" id="generateInvoicePartial" class="btn btn-dark btn-block mb-3">Generar factura parcial</a>
                            <a href="" id="generateTask" class="btn btn-dark btn-block mb-3">Generar tareas</a>
                            <a href="" id="deletePresupuesto" data-id="{{$presupuesto->id}}" class="btn btn-outline-danger btn-block mb-3">Eliminar</a>
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

        // Botón Actualizar presupuesto
        $('#actualizarPresupuesto').click(function(e) {
            e.preventDefault(); // Previene que el enlace navegue a otra página.

            // Obtener los datos del formulario
            var formData = $('#update').serialize();

            // Enviar la solicitud AJAX
            $.ajax({
                url: $('#update').attr('action'), // La URL del formulario
                type: 'POST', // El método HTTP
                data: formData,
                dataType: 'json', // Especifica que la respuesta será JSON
                success: function(response) {
                    // Manejar la respuesta JSON aquí
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualizado correctamente',
                            text: 'El presupuesto se ha actualizado exitosamente.',
                            showCancelButton: true,
                            confirmButtonText: 'Ir a la lista',
                            cancelButtonText: 'Seguir editando',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirigir a la lista (usar URL de la respuesta o una predeterminada)
                                window.location.href = response.redirect;
                            }
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Error al actualizar el presupuesto."
                        })
                    }
                },
                error: function(xhr, status, error) {
                    Toast.fire({
                            icon: "error",
                            title: "Error al actualizar el presupuesto."
                        })
                }
            });
        });


        // Boton Aceptar presupuesto
        $('#aceptarPresupuesto').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);

            $.ajax({
                url: '{{ route("presupuesto.aceptarPresupuesto")}}', // Asegúrate de que la URL es correcta
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

        $('#cancelarPresupuesto').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);

            $.ajax({
                url: '{{ route("presupuesto.cancelarPresupuesto")}}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: response.icon,
                        title: response.mensaje,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                        didClose: () => {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al cambiar el estado del presupuesto. Por favor, inténtalo de nuevo.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    console.error(xhr.responseText);
                }
            });

        });

        //Boton de generar factura
        $('#generateInvoice').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);

            $.ajax({
                url: '{{ route("presupuesto.generarFactura")}}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {
                    if (response.status) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Factura generada correctamente',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                            didClose: () => {
                                location.reload();
                            }
                        });
                    } else {
                        // Mostrar mensaje de error
                        Swal.fire({
                            icon: 'error',
                            title: response.mensaje,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                    }
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar la factura. Por favor, inténtalo de nuevo.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    console.error(xhr.responseText);
                }
            });
        });

        //Boton de generar tareas
        $('#generateTask').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);

            $.ajax({
                url: '{{ route("presupuesto.generarTarea")}}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {
                    if (response.status) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Tarea creada correctamente.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                            didClose: () => {
                                window.location.href = '/tasks';
                            }
                        });
                    } else {
                        // Mostrar mensaje de error
                        Swal.fire({
                            icon: 'error',
                            title: response.mensaje,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                    }
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar la tarea. Por favor, inténtalo de nuevo.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    console.error(xhr.responseText);
                }
            });
        });

        //Boton de generar tareas
        $('#generatePdf').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idPresupuesto = @json($presupuesto->id);
            const checksumatorio = $("#no_sumatorio").is(':checked');

            console.log(checksumatorio);

            $.ajax({
                url: '{{ route("presupuesto.generarPDF") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idPresupuesto,
                    sumatorio: checksumatorio
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                xhrFields: {
                    responseType: 'blob' // Necesario para manejar la descarga del archivo
                },
                success: function(response) {
                    // Crear una URL para el blob y forzar la descarga
                    const blob = new Blob([response], { type: 'application/pdf' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'presupuesto_' + idPresupuesto + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Pdf creado correctamente.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                        didClose: () => {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar el PDF. Por favor, inténtalo de nuevo.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    console.error(xhr.responseText);
                }
            });
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
                    title: "El concepto se ha elimino correctamente"
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
                    }, 0);
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
                    }, 0);
                },
                error: function(xhr, status, error) {
                    $('#loadingOverlay').css('display', 'none');

                    // Manejo de errores
                    console.error(error);
                }
            })
        })

        $('#generateInvoicePartial').click(function(e) {
            // Iniciar SweetAlert2 con opciones
            e.preventDefault();
            const porcentajeYaFacturado = @json($porcentaje); // Asegúrate de que esta variable tiene el porcentaje ya facturado.
            const maximoPermitido = 100 - porcentajeYaFacturado;
            Swal.fire({
                title: 'Facturar Parcialmente',
                text: 'Selecciona el porcentaje que deseas facturar:',
                icon: 'question',
                input: 'range', // Establece el tipo de input como un rango
                inputAttributes: {
                    min: 0, // Valor mínimo
                    max: maximoPermitido, // Valor máximo
                    step: 5 // Incrementos
                },
                inputValue: 0, // Valor inicial
                inputLabel: 'Porcentaje a facturar',
                showCancelButton: true, // Muestra el botón cancelar
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                preConfirm: (percentage) => {
                    return new Promise((resolve) => {
                        // Enviar datos al servidor o hacer algo antes de cerrar el diálogo
                        generatePartialInvoice(percentage).done(function(response) {
                            if (response.status) {
                                Swal.fire({
                                    title: '¡Factura generada!',
                                    toast: true,
                                    text: `La factura ha sido generada por el ${percentage}% del total.`,
                                    icon: 'success',
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    },
                                    didClose: () => {
                                        location.reload();
                                    }
                                });
                            } else {
                            // Mostrar mensaje de error
                                Swal.fire({
                                    icon: 'error',
                                    title: response.mensaje,
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.onmouseenter = Swal.stopTimer;
                                        toast.onmouseleave = Swal.resumeTimer;
                                    }
                                });
                            }
                        }).fail(function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Ocurrió un error al generar la factura. Por favor, inténtalo de nuevo.',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.onmouseenter = Swal.stopTimer;
                                    toast.onmouseleave = Swal.resumeTimer;
                                }
                            });
                            console.error(xhr.responseText);
                        });
                    });
                }
            });
        });

        $('#deletePresupuesto').on('click', function(e){
            e.preventDefault();
            let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
            botonAceptar(id);
        })
        $('#enviarEmail').on('click', function(e){
            e.preventDefault();
            let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
            botonEnviar(id);
        })

        $('#iva').on('change', function(){
            actualizarPrecios()
        })

        // Actualizar Precios
        const actualizarPrecios = () => {
            const conceptosPresupuesto = @json($budgetConcepts);
            let total = 0;
            let totalgross = 0;
            let iva = $('#iva').val();
            let descuento;

            conceptosPresupuesto.map((concepto) => {
                descuento += concepto.discount;
                total += concepto.total;
                totalgross += concepto.sale_price
            })
            const ivaTotal = (total*iva)/100
            $('#base').val(total)
            $('#gross').val(totalgross)
            $('#total').val(total+ivaTotal)
            $('#iva_total').val(ivaTotal)
            $('#budget_total').html(`<strong>${formatearNumero(total+ivaTotal)} €</strong>`)
            $('#gross').html(formatearNumero(totalgross) + ' €')
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

        function botonEnviar(id) {
            // Obtén el correo del cliente asociado desde el backend
            const defaultEmail = "{{ $clientes->where('id', $presupuesto->client_id)->first()->email }}";

            // Salta la alerta para pedir los correos y permitir la subida de archivos
            Swal.fire({
                title: "Enviar correo",
                html:
                    '<input id="swal-input1" class="swal2-input" value="' + defaultEmail + '" placeholder="Correo">' +
                    '<input id="swal-input2" class="swal2-input" placeholder="CC">' +
                    '<input id="swal-input3" class="swal2-input" placeholder="CC2">' +
                    '<input id="swal-files" type="file" multiple class="swal2-input">',
                showCancelButton: true,
                confirmButtonText: "Enviar",
                cancelButtonText: "Cancelar",
                preConfirm: () => {
                    const email = document.getElementById('swal-input1').value;
                    const cc = document.getElementById('swal-input2').value;
                    const cc2 = document.getElementById('swal-input3').value;
                    const files = document.getElementById('swal-files').files;

                    if (!email) {
                        Swal.showValidationMessage('El campo de correo es obligatorio');
                        return false;
                    }

                    return {
                        email: email,
                        cc: cc,
                        cc2: cc2,
                        files: files // Incluimos los archivos seleccionados
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;

                    // Preparar los archivos para el envío
                    const formData = new FormData();
                    formData.append('email', data.email);
                    formData.append('cc', data.cc);
                    formData.append('cc2', data.cc2);

                    // Añadir archivos al FormData
                    for (let i = 0; i < data.files.length; i++) {
                        formData.append('files[]', data.files[i]);
                    }

                    // Llamar a la función para enviar el correo con los archivos adjuntos
                    $.when(SendMail(id, formData)).then(function(data, textStatus, jqXHR) {
                        if (!data.status) {
                            // Si recibimos algún error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            });
                        } else {
                            // Todo ha ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            }).then(() => {
                                window.location.href = "{{ route('presupuestos.index') }}";
                            });
                        }
                    });
                }
            });
        }

        function SendMail(id, formData) {
            // Ruta de la petición
            const url = '/budget/send/' + id;

            // Petición AJAX para enviar el formulario con los archivos
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: formData,
                processData: false, // No procesar los datos (permite enviar archivos)
                contentType: false, // No establecer un tipo de contenido, para manejar correctamente los archivos
                dataType: "json"
            });
        }

        function botonAceptar(id){
            // Salta la alerta para confirmar la eliminacion
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar este presupuesto?",
                html: "<p>Esta acción es irreversible.</p>", // Corrige aquí
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
                // denyButtonText: `No Borrar`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Llamamos a la funcion para borrar el usuario
                    $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                        console.log(data)
                        if (!data.status) {
                            // Si recibimos algun error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            // Todo a ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            })
                            .then(() => {
                                window.location.href = "{{ route('presupuestos.index') }}";
                            })
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            // Ruta de la peticion
            const url = '{{route("presupuesto.delete")}}'
            // Peticion
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                },
                dataType: "json"
            });
        }

        // Función para enviar datos al servidor y generar la factura parcial
        function generatePartialInvoice(percentage) {
            return $.ajax({
                url: '{{ route("presupuesto.generarFacturaParcial") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: @json($presupuesto->id), // ID del presupuesto
                    percentage: percentage // Porcentaje para facturar
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                }
            });
        }

        actualizarPrecios()
        const selectedProjectId = @json(old('project_id', $presupuesto->project_id));
        const choicesOptions = { removeItemButton: true };

        // Inicialización de Choices.js para los selects existentes
        const proyectoSelect = new Choices('#proyecto');
        const clienteSelect = new Choices('#cliente');
        const gestorSelect = new Choices('#gestor');
        const paymentMethodSelect = new Choices('#payment_method_id');
        const budgetStatusSelect = new Choices('#budget_status_id');
                  // Ejecutar tanto en el cambio del select como cuando la página carga


        $('#cliente').on("change", function() {
            const clienteId = $(this).val();
            const previousValue = $('#proyecto').val();
            console.log(previousValue);
            if (clienteId) {
                $.ajax({
                    url: '{{ route("campania.postProjectsFromClient") }}',
                    type: 'POST',
                    data: { client_id: clienteId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        // Establece las nuevas opciones para el select de campañas
                        proyectoSelect.setChoices(
                            response.map((campania) => ({
                                value: campania.id,
                                label: campania.name,
                                selected: campania.id == previousValue // Establece el valor previamente seleccionado si está disponible
                            })),
                            'value',
                            'label',
                            true
                        );

                        // Si no se encuentra el valor anterior, asegúrate de seleccionar una opción por defecto (opcional)
                        if (!response.some(campania => campania.id == previousValue)) {
                            proyectoSelect.setChoiceByValue(response.length > 0 ? response[0].id : null);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error al cargar las campañas: ", error);
                    }
                });
                console.log($('#proyecto').val());

            }
        });


        // Dispara el cambio al cargar la página si hay un cliente seleccionado
        if ($('#cliente').val() !== '') {
            $('#cliente').trigger('change');
        }
    });

</script>
@endsection

