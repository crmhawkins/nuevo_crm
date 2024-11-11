@extends('layouts.app')

@section('titulo', 'Editar factura')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />

@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Editar Factura</h3>
                    <p class="text-subtitle text-muted">Formulario para editar una factura</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('facturas.index')}}">Facturas</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar factura</li>
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
                            <form action="{{route('factura.update', $factura->id)}}" method="POST">
                                @csrf
                                <div class="row d-flex">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="reference">Ref.:</label>
                                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" value="{{ $factura->reference }}" name="reference" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="text-left mb-2">Presupuesto <span id="budgetLinkContainer"></span><a id="budgetAssignedClient" target="_blank" href="{{ route('presupuesto.edit', $factura->budget_id) }}"><i class="fas fa-external-link-alt"></i></a></label>
                                            <input type="text" class="form-control @error('budget_id') is-invalid @enderror" id="budget_id" value="{{ $factura->budget->concept ??  ($factura->budget_id ? 'Presupuesto borrado' : 'Sin presupuesto asignado') }}" name="budget_id" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="text-left mb-2">Cliente <span id="budgetLinkContainer"></span><a id="budgetAssignedClient" target="_blank" href="{{ route('clientes.edit', $factura->client_id) }}"><i class="fas fa-external-link-alt"></i></a></label>
                                            <input type="text" class="form-control @error('client_id') is-invalid @enderror" id="client_id" value="{{ $factura->cliente->name ??  ($factura->client_id ? 'Cliente borrado' : 'Sin cliente asignado') }}" name="client_id" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="text-left mb-2">Campaña <span id="budgetLinkContainer"></span><a id="budgetAssignedClient" target="_blank" href="{{ route('campania.edit', $factura->project_id) }}"><i class="fas fa-external-link-alt"></i></a></label>
                                            <input type="text" class="form-control @error('project_id') is-invalid @enderror" id="project_id" value="{{ $factura->project->name ?? ($factura->project_id ? 'Campaña borrada' : 'Sin campaña asignada') }}" name="project_id" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="text-left mb-2">Gestor:</label>
                                            <input type="text" class="form-control @error('admin_user_id') is-invalid @enderror" id="admin_user_id" value="{{ $factura->adminUser->name ?? ($factura->admin_user_id ? 'Gestor borrado' : 'Sin gestor asignado') }}" name="admin_user_id" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="text-left mb-2" for="payment_method_id">Forma de pago:</label>
                                            <input type="text" class="form-control @error('payment_method_id') is-invalid @enderror" id="payment_method_id" value="{{ $factura->paymentMethod->name }}" name="payment_method_id" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="concept">Concepto:</label>
                                            <input type="text" class="form-control @error('concept') is-invalid @enderror" id="concept" value="{{ $factura->concept }}" name="concept" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="invoice_status_id">Estado:</label>
                                            <select class="form-control @error('invoice_status_id') is-invalid @enderror" id="invoice_status_id" name="invoice_status_id">
                                                @foreach ( $invoiceStatuses as $estado )
                                                    <option value="{{ $estado->id }}" {{ $factura->invoice_status_id == $estado->id ? 'selected' : '' }}>{{ $estado->name }}</option>

                                                @endforeach
                                            </select>
                                            @error('invoice_status_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="observations">Observaciones:</label>
                                            <textarea class="form-control @error('observations') is-invalid @enderror" id="observations" name="observations">{{ $factura->observations}}</textarea>
                                            @error('observations')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="note">Nota Interna:</label>
                                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note">{{ $factura->note}}</textarea>
                                            @error('note')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="created_at">Fecha Creación:</label>
                                            <input type="date" class="form-control @error('created_at') is-invalid @enderror" id="created_at" value="{{ Carbon\Carbon::parse($factura->created_at)->format('Y-m-d') }}" name="created_at">
                                            @error('created_at')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group mb-3">
                                            <label class="mb-2 text-left" for="paid_date">Fecha de Cobro:</label>
                                            <input type="date" class="form-control @error('paid_date') is-invalid @enderror" id="paid_date" value="{{ $factura->paid_date }}" name="paid_date">
                                            @error('paid_date')
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
                                    </div>
                                </div>
                                <table id="conceptsTable" class="table dt_custom_budget_concepts table-hover table-striped table-bordered mt-4" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Unidades</th>
                                            <th>Precio/Unidad</th>
                                            <th>SUBTOTAL</th>
                                            <th>DTO</th>
                                            <th>TOTAL</th>
                                            <th hidden></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($invoice_concepts)
                                            @foreach($invoice_concepts as $concept)
                                            <?php
                                                $subtotalOwn = $concept->units*$concept->sale_price;
                                                $subtotalSupplier = $concept->total_no_discount;
                                                $subtotal = 0;
                                                if ($concept->concept_type_id == 1 ){
                                                    $subtotal = $subtotalSupplier;
                                                }
                                                if ($concept->concept_type_id == 2 ){
                                                    $subtotal = $subtotalOwn;
                                                }
                                                if ($concept->concept_type_id == 1 ){
                                                    $purchasePriceWithoutMarginBenefit = $concept->purchase_price;
                                                    $benefitMargin = $concept->benefit_margin;
                                                    $marginBenefitToAdd  =  ($purchasePriceWithoutMarginBenefit*$benefitMargin)/100;
                                                    $purchasePriceWithMarginBenefit  =  $purchasePriceWithoutMarginBenefit+ $marginBenefitToAdd;
                                                }
                                            ?>
                                                <tr class="budgetRow" data-child-value="{{$concept->concept}}">

                                                    <td>{{ $concept->title }}</td>
                                                    <td >
                                                        @if($concept->concept_type_id == 1)
                                                            @if($concept->purchase_price != null)
                                                                {{ $concept->units }}
                                                            @endif
                                                        @else
                                                            {{ $concept->units }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetPriceRow" >
                                                        @if($concept->concept_type_id == 1)
                                                            @if( $purchasePriceWithMarginBenefit != null)
                                                                {{  round(
                                                                        (number_format((float)$concept->purchase_price, 2, '.', '') / $concept->units / 100 * number_format((float)$concept->benefit_margin, 2, '.', ''))
                                                                        + (number_format((float)$concept->purchase_price, 2, '.', '') / $concept->units), 2)
                                                                }}
                                                            @endif
                                                        @else
                                                            {{ number_format((float)$concept->sale_price, 2, '.', '')  }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetSubtotalRow">
                                                        @if($concept->concept_type_id == 1)
                                                            @if($concept->purchase_price != null)
                                                                {{ number_format((float)$subtotalSupplier, 2, '.', '')  }}
                                                            @endif
                                                        @else
                                                            {{ number_format((float)$subtotalOwn, 2, '.', '')  }}
                                                        @endif
                                                    </td>
                                                    <td class="budgetDiscountRow">
                                                        {{ $concept->discount ?? '0 ' }}%
                                                    </td>
                                                    <td class="conceptTotal"> {{ number_format((float)$concept->total, 2, '.', '')  }}</td>

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
                                              <th>Descuento</th>
                                              <th>Base</th>
                                              <th>% IVA</th>
                                              <th>IVA</th>
                                              <th>TOTAL</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                                <td><span id="gross">{{ number_format((float)$factura->gross, 2, '.', '')  }}</span></td>
                                                <td id="discount_summary_amount">{{ number_format((float)$factura->discount, 2, '.', '')  }}</td>
                                                <td id="base_amount"> {{ number_format((float)$factura->base, 2, '.', '')  }}</td>
                                                <td id="iva_percentage">{{ number_format((float)$factura->iva_percentage, 0, '.', '')  }}</td>
                                                <td id="iva_amount">{{ number_format((float)$factura->iva, 2, '.', '')  }}</td>
                                                <td id="budget_total"><strong>{{ number_format((float)$factura->total, 2, '.', '')  }} €</strong></td>
                                            </tr>
                                          </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-12 form-check form-switch d-flex justify-content-center">
                                    <input type="hidden" name="show_summary" value="0">
                                    <input  @if($factura->show_summary) {{ 'checked' }} @endif type="checkbox" value="1" class="form-check-input" id="show_summary" name="show_summary">
                                    <label class="form-check-label ml-2" for="show_summary">Mostrar sumatorio</label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card-body p-3">
                        <div class="card-title">
                            Acciones
                            <hr>
                        </div>
                        <div class="card-body">
                            <a href="" id="actualizarfactura" class="btn btn-success btn-block mb-2">Actualizar</a>
                            <a href="" id="facturaCobrada" class="btn btn-primary btn-block mb-2">Cobrada</a>
                            <a href="" id="generatePdf" class="btn btn-dark btn-block mb-2">Generar PDF</a>
                            <a href="" id="SendPDF" data-id="{{$factura->id}}" class="btn btn-dark btn-block mb-2">Enviar PDF</a>
                            <a href="" id="electronica" data-id="{{$factura->id}}" class="btn btn-info btn-block mb-2">Electronica</a>
                            <a href="" id="rectificar" class="btn btn-danger btn-block mb-2">Abonado (N)</a>
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


<script>
    $(document).ready(function() {

        // Boton Actualizar factura
        $('#actualizarfactura').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.
            $('form').submit(); // Esto envía el formulario.
        });

        // Boton Aceptar presupuesto
        $('#facturaCobrada').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idFactura = @json($factura->id);

            $.ajax({
                url: '{{ route("factura.cobrada")}}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idFactura
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
                    title: "La factura se ha actualizado correctamente a su estado Cobrada"
                });
                return;
        });

        //Boton de generar tareas
        $('#generatePdf').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idFactura = @json($factura->id);
            $.ajax({
                url: '{{ route("factura.generarPDF") }}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idFactura
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
                    link.download = 'factura_' + idFactura + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
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

        $('#electronica').click(function(e) {
            e.preventDefault(); // Evita la navegación predeterminada del enlace

            const idFactura = @json($factura->id);
            $.ajax({
                url: '{{ route("factura.electronica") }}', // Verifica que esta URL sea correcta
                type: 'POST',
                data: {
                    id: idFactura
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                xhrFields: {
                    responseType: 'blob' // Necesario para manejar la descarga del archivo
                },
                success: function(response) {
                    // Crea una URL para el blob y fuerza la descarga
                    const blob = new Blob([response], { type: 'application/xsig' });
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'factura_' + idFactura + '_' + new Date().toISOString().slice(0, 10) + '.xsig';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Factura electrónica generada correctamente.',
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
                },
                error: function(xhr) {
                    // Manejo de errores
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al generar la factura electrónica. Por favor, inténtalo de nuevo.',
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
        $('#rectificar').click(function(e){
            e.preventDefault(); // Esto previene que el enlace navegue a otra página.

            const idFactura = @json($factura->id);

            $.ajax({
                url: '{{ route("factura.rectificada")}}', // Asegúrate de que la URL es correcta
                type: 'POST',
                data: {
                    id: idFactura
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Obtén el token CSRF
                },
                success: function(response) {
                    if (response.status) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Factura rectificada correctamente.',
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
                                window.location.href = "{{ url('invoice/edit') }}/" + response.id;
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

        $('#SendPDF').on('click', function(e) {
            e.preventDefault();
            let id = $(this).data('id'); // Obtén el ID del presupuesto
            botonEnviar(id);
        });

        function botonEnviar(id) {
            // Obtén el correo del cliente asociado desde el backend
            const defaultEmail = "{{ $factura->cliente->email ?? '' }}"; // Asegúrate de que aquí estás obteniendo el correo del cliente correctamente

            // Salta la alerta para pedir el correo
            Swal.fire({
                title: "Enviar correo",
                html: '<input id="swal-input1" class="swal2-input" value="' + defaultEmail + '" placeholder="Correo">',
                showCancelButton: true,
                confirmButtonText: "Enviar",
                cancelButtonText: "Cancelar",
                preConfirm: () => {
                    const email = document.getElementById('swal-input1').value;

                    if (!email) {
                        Swal.showValidationMessage('El campo de correo es obligatorio');
                        return false;
                    }

                    return {
                        id: id,
                        email: email
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;

                    // Crear un objeto FormData solo con el ID y el correo
                    const formData = new FormData();
                    formData.append('email', data.email);
                    formData.append('id', data.id);

                    // Llamar a la función para enviar el correo
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
            const url = '/invoice/sendInvoicePDF';

            // Petición AJAX para enviar el correo
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: formData,
                processData: false, // No procesar los datos
                contentType: false, // No establecer un tipo de contenido
                dataType: "json"
            });
        }


    });
</script>
@endsection

