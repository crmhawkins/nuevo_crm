@extends('layouts.app')

@section('titulo', 'Ver Cliente')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/toastify/toastify.css')}}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')

    <div class="page-heading card ">

        {{-- Titulos --}}
        <div class="page-title card-body">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <div class="d-flex">
                        {{-- @if ($cliente->image == null)
                            <img class="rounded-circle img-fuild avatar-table" alt="avatar1" src="{{asset('assets/images/guest.webp')}}" />
                        @else
                            <img class="rounded-circle img-fuild avatar-table" alt="avatar1" src="{{asset('/storage/avatars/'.$cliente->image)}}" />
                        @endif --}}
                        <h2 class="ms-2 fs-2">{{$cliente->company ?? $cliente->name}}</h2>
                        <span class="badge bg-warning h_fit_content ms-2">{{$cliente->gestor->name}}</span>
                    </div>
                    <p class="text-subtitle text-muted">Información sobre el cliente {{$cliente->company ?? $cliente->name}}.</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$cliente->company ?? $cliente->name}}</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>

        <section class="section pt-4">
            <div class="card">
                <div class="card-body">

                    <div class="card">
                        <div class="card-content">
                            <div class="card-body" style="border:none">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card mb-4">
                                          <div class="card-body text-center" style="border:none">
                                            {{-- @if ($cliente->image == null)
                                                <img alt="avatar" class="rounded-circle img-fluid" style="width: 150px;" src="{{asset('assets/images/guest.webp')}}" />
                                            @else
                                                <img alt="avatar" class="rounded-circle img-fluid" style="width: 150px;" src="{{ asset('/storage/avatars/'.$cliente->image) }}" />
                                            @endif --}}
                                            <h5 class="my-3">{{$cliente->company ?? $cliente->name}}</h5>
                                            <p class="text-muted mb-1">{{$cliente->activity}}</p>
                                            <p class="text-m$clientelist-presupuestos-listuted mb-4">{{$cliente->identifier}}</p>
                                            <div class="d-flex justify-content-center mb-2">
                                              <button type="button" class="btn btn-outline-primary ms-1">Mensajes</button>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="mb-4 list-group" role="tablist">
                                            <a class="list-group-item list-group-item-action active"
                                                id="list-profile-list" data-bs-toggle="list" href="#list-profile"
                                                role="tab">Perfil</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-info-list" data-bs-toggle="list"
                                                href="#list-info" role="tab">Informacion de Contacto</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-presupuestos-list" data-bs-toggle="list"
                                                href="#list-presupuestos" role="tab">Presupuestos</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-presupuestos-list" data-bs-toggle="list"
                                                href="#list-facturas" role="tab">Facturas</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-dominios-list" data-bs-toggle="list"
                                                href="#list-dominios" role="tab">Dominios</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-contactos-list" data-bs-toggle="list"
                                                href="#list-contactos" role="tab">Contactos</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-estadisticas-list" data-bs-toggle="list"
                                                href="#list-estadisticas" role="tab">Estadisticas</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-newsletter-list" data-bs-toggle="list"
                                                href="#list-newsletter" role="tab">Newsletter</a>

                                        </div>
                                        <div class="card mb-4 mb-lg-0">
                                          <div class="card-body p-0">
                                            <ul class="list-group list-group-flush rounded-3">
                                            @if ($cliente->web)
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fas fa-globe fa-lg text-warning"></i>
                                                <p class="mb-0">{{$cliente->web}}</p>
                                              </li>
                                            @endif
                                              @if ($cliente->twitter)
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                    <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                                                    <p class="mb-0">{{$cliente->twitter}}</p>
                                                </li>
                                              @endif
                                              @if ($cliente->linkedin)
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                    <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                                                    <p class="mb-0">{{$cliente->linkedin}}</p>
                                                </li>
                                              @endif
                                              @if ($cliente->facebook)
                                                <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                    <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                                                    <p class="mb-0">{{$cliente->facebook}}</p>
                                                </li>
                                              @endif

                                            </ul>
                                          </div>
                                        </div>
                                      </div>

                                    <div class="col-12 col-sm-12 col-md-8 mt-1">
                                        <div class="tab-content text-justify" id="nav-tabContent">
                                            <div class="tab-pane show active" id="list-profile" role="tabpanel"
                                                aria-labelledby="list-profile-list">
                                                <div class="card-body" style="border:none">
                                                    <div class="row p-2">
                                                      <div class="col-sm-4">
                                                        <p class="mb-0">Nombre:</p>
                                                      </div>
                                                      <div class="col-sm-8">
                                                        <p class="text-muted mb-0">{{$cliente->name}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                      <div class="col-sm-4">
                                                        <p class="mb-0">Nombre de la empresa:</p>
                                                      </div>
                                                      <div class="col-sm-8">
                                                        <p class="text-muted mb-0">{{$cliente->company}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Gestor:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0"><a href="{{route('users.show',$cliente->gestor->id)}}">{{$cliente->gestor->name}}</a></p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">CIF:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->cif}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Marca:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->identifier}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Actividad:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->activity}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Dirección:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->address}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Pais:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->country}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Ciudad:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->city}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Provincia:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->province}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Codigo Postal:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->zipcode}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Creación de la Empresa:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->birthdate}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Notas:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->notes}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    @if ($cliente->client_id)
                                                        <div class="row p-2">
                                                            <div class="col-sm-4">
                                                            <p class="mb-0">Cliente Asociado:</p>
                                                            </div>
                                                            <div class="col-sm-8">
                                                            <p class="text-muted mb-0"><a href="{{route('clientes.show',$cliente->cliente->id)}}">{{$cliente->cliente->name}}</a></p>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                    @endif


                                                </div>
                                            </div>
                                            <div class="tab-pane" id="list-info" role="tabpanel"
                                                aria-labelledby="list-info-list">
                                                <div class="card-body">
                                                    <div class="row p-2">
                                                      <div class="col-sm-4">
                                                        <p class="mb-0">Telefono:</p>
                                                      </div>
                                                      <div class="col-sm-8">
                                                        <p class="text-muted mb-0">{{$cliente->phone}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                      <div class="col-sm-4">
                                                        <p class="mb-0">Fax:</p>
                                                      </div>
                                                      <div class="col-sm-8">
                                                        <p class="text-muted mb-0">{{$cliente->fax}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Email:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                          <p class="text-muted mb-0">{{$cliente->email}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Otros Telefonos:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <ul>
                                                                @foreach ($cliente->phones as $phone )
                                                                    <li>
                                                                        {{$phone->number}}
                                                                    </li>
                                                                @endforeach

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Otros Emails:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <ul>
                                                                @foreach ($cliente->emails as $email )
                                                                    <li>
                                                                        {{$email->email}}
                                                                    </li>
                                                                @endforeach

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row p-2">
                                                        <div class="col-sm-4">
                                                          <p class="mb-0">Otras Webs:</p>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <ul>
                                                                @foreach ($cliente->webs as $web )
                                                                    <li>
                                                                        {{$web->url}}
                                                                    </li>
                                                                @endforeach

                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="list-presupuestos" role="tabpanel"
                                                aria-labelledby="list-presupuestos-list">
                                                <h3 class="mb-2 fs-4 text-uppercase">Presupuestos del Cliente</h3>
                                                <hr class="border mb-4" >
                                                @if (count($cliente->presupuestos) > 0)
                                                    <div class="table-responsive">
                                                         <table class="table table-hover">
                                                            <thead class="header-table-other">
                                                                <th class="px-3" style="font-size:0.75rem">CAMPAÑA</th>
                                                                <th class="" style="font-size:0.75rem">ESTADO</th>
                                                                <th class="" style="font-size:0.75rem">TOTAL</th>
                                                                <th class="" style="font-size:0.75rem">GESTOR</th>
                                                                <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $cliente->presupuestos as $budget )
                                                                    <tr>
                                                                        <td>{{$budget->proyecto->name ?? ($budget->project_id ? 'Campaña borrada' : 'Sin campaña asignada')}}</td>
                                                                        <td>{{$budget->estadoPresupuesto->name ?? ($budget->budget_status_id ? 'Estado borrado' : 'Sin estado asignado')}}</td>
                                                                        <td>{{$budget->total}} €</td>
                                                                        <td>{{$budget->usuario->name ?? ($budget->admin_user_id ? 'Gestor borrado' : 'Sin gestor asignado')}}</td>
                                                                        <td class="">
                                                                            <a class="" href="{{route('presupuesto.show', $budget->id)}}"><img class="m-auto" src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar usuario"></a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                <div class="text-center py-4">
                                                    <h3 class="text-center fs-4">No se encontraron registros de <strong>Presupuestos</strong></h3>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="tab-pane" id="list-facturas" role="tabpanel"
                                                aria-labelledby="list-facturas-list">
                                                <h3 class="mb-2 fs-4 text-uppercase">Facturas del Cliente</h3>
                                                <hr class="border mb-4" >
                                                @if (count($cliente->facturas) > 0)
                                                    <div class="table-responsive">
                                                         <table class="table table-hover">
                                                            <thead class="header-table-other">
                                                                <th class="px-3" style="font-size:0.75rem">CAMPAÑA</th>
                                                                <th class="" style="font-size:0.75rem">ESTADO</th>
                                                                <th class="" style="font-size:0.75rem">TOTAL</th>
                                                                <th class="" style="font-size:0.75rem">GESTOR</th>
                                                                <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $cliente->facturas as $invoice )
                                                                    <tr>
                                                                        <td>{{$invoice->project->name ?? ($invoice->project_id ? 'Campaña borrada' : 'Sin campaña asignada')}}</td>
                                                                        <td>{{$invoice->invoiceStatus->name ?? ($invoice->invoice_status_id ? 'Estado borrado' : 'Sin estado asignado')}}</td>
                                                                        <td>{{$invoice->total}} €</td>
                                                                        <td>{{$invoice->adminUser->name ?? ($invoice->admin_user_id ? 'Gestor borrado' : 'Sin gestor asignado')}}</td>
                                                                        <td class="">
                                                                            <a class="" href="{{route('factura.show', $invoice->id)}}"><img class="m-auto" src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar factura"></a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                <div class="text-center py-4">
                                                    <h3 class="text-center fs-4">No se encontraron registros de <strong>Facturas</strong></h3>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="tab-pane" id="list-dominios" role="tabpanel"
                                                aria-labelledby="list-dominios-list">
                                                <h3 class="mb-2 fs-4 text-uppercase">Dominios del Cliente</h3>
                                                <hr class="border mb-4" >
                                                @if (count($cliente->dominios) > 0)
                                                    <div class="table-responsive">
                                                         <table class="table table-hover">
                                                            <thead class="header-table-other">
                                                                <th class="px-3" style="font-size:0.75rem">DOMINIO</th>
                                                                <th class="" style="font-size:0.75rem">CONTRARACIÓN</th>
                                                                <th class="" style="font-size:0.75rem">RENOVACIÓN</th>
                                                                <th class="" style="font-size:0.75rem">ESTADO</th>
                                                                <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $cliente->dominios as $dominio )
                                                                    <tr>
                                                                        <td>{{$dominio->dominio}}</td>
                                                                        <td>{{$dominio->date_start}}</td>
                                                                        <td>{{$dominio->date_end}}</td>
                                                                        <td>{{$dominio->estadoName->name ?? ($dominio->estado_id ? 'Estado borrado' : 'Sin estado asignado')}}</td>
                                                                        <td class="">
                                                                            <a class="" href="{{route('dominios.edit', $dominio->id)}}"><img class="m-auto" src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar factura"></a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                <div class="text-center py-4">
                                                    <h3 class="text-center fs-4">No se encontraron registros de <strong>Facturas</strong></h3>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="tab-pane" id="list-contactos" role="tabpanel"
                                                aria-labelledby="list-contactos-list">
                                                <h3 class="fs-5">Contactos Asociados</h3>
                                                <hr class="mb-4">

                                                @if ($cliente->contacto)

                                                    <table class="table table-striped table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    Nombre
                                                                </th>
                                                                <th>
                                                                    Teléfono
                                                                </th>
                                                                <th>
                                                                    Email
                                                                </th>
                                                                <th>
                                                                    Acciones
                                                                </th>
                                                            </tr>
                                                            <tbody>
                                                                @foreach ($cliente->contacto as $contacto)
                                                                    <tr>
                                                                        <td>
                                                                            {{$contacto->name}}
                                                                        </td>
                                                                        <td>
                                                                            {{$contacto->email}}
                                                                        </td>
                                                                        <td>
                                                                            {{$contacto->phone}}
                                                                        </td>
                                                                        <td>
                                                                            <button class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye w-5 h-5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></button>
                                                                        </td>
                                                                    </tr>

                                                                @endforeach
                                                            </tbody>
                                                        </thead>
                                                    </table>
                                                @else
                                                    <h5>No tienes contactos</h5>

                                                @endif
                                            </div>
                                            <div class="tab-pane" id="list-newsletter" role="tabpanel"
                                                aria-labelledby="list-newsletter-list">
                                                <h5>No tienes newsletter</h5>
                                            </div>
                                            <div class="tab-pane" id="list-estadisticas" role="tabpanel"
                                                aria-labelledby="list-estadisticas-list">
                                                <h5>No tienes estadisticas</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @php
        $usuario = $cliente;
    @endphp
    @include('partials.modalAvatar')

@endsection

@section('scripts')
    <!-- toastify -->
    <script src="{{asset('assets/vendors/toastify/toastify.js')}}"></script>

    <!-- filepond -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Filepond: Basic
            FilePond.create( document.getElementById('inputFile'), {
                allowImagePreview: false,
                allowMultiple: false,
                allowFileEncode: false,
                required: false,
                storeAsFile: true,
                // server: '/user/avatar/1',
                // server: {
                //     process: async (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                //         console.log(options)
                //         console.log(transfer)
                //         console.log(fieldName)
                //         console.log(load)
                //         const id = 1;
                //         // const image = await toBase64(file)
                //         var frmdta = await new FormData();
                //         frmdta.append ( 'uploadfile', file);
                //         frmdta.append ( 'id', id);
                //         console.log(frmdta)
                //             addAvatar(frmdta)

                //     },
                // },
                server: {
                    process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                        // fieldName is the name of the input field
                        // file is the actual file object to send
                        const formData = new FormData();
                        formData.append(fieldName, file, file.name);
                        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                        const request = new XMLHttpRequest();
                        request.open('POST', '/user/avatar/1');

                        // Should call the progress method to update the progress to 100% before calling load
                        // Setting computable to false switches the loading indicator to infinite mode
                        request.upload.onprogress = (e) => {
                            progress(e.lengthComputable, e.loaded, e.total);
                        };

                        // Should call the load method when done and pass the returned server file id
                        // this server file id is then used later on when reverting or restoring a file
                        // so your server knows which file to return without exposing that info to the client
                        request.onload = function () {
                            if (request.status >= 200 && request.status < 300) {
                                // the load method accepts either a string (id) or an object
                                load(request.responseText);
                            } else {
                                // Can call the error method if something is wrong, should exit after
                                error('oh no');
                            }
                        };

                        request.send(formData);

                        $('#ModalAvatar'+{{$cliente->id}}).modal('hide');
                        location.reload();

                        // Should expose an abort method so the request can be cancelled
                        return {
                            abort: () => {
                                // This function is entered if the user has tapped the cancel button
                                request.abort();

                                // Let FilePond know the request has been cancelled
                                abort();
                            },
                        };
                    },
                }
            });
        })

    </script>
    @include('partials.toast')

    <script>
        function addAvatar(frmdta){
            console.log($('#inputFile'))
            $.when( postAvatar(frmdta) ).then(function( data, textStatus, jqXHR ) {
                // if (data.error) {
                //     Toast.fire({
                //         icon: "error",
                //         title: data.mensaje
                //     })
                // } else {
                //     Toast.fire({
                //         icon: "success",
                //         title: data.mensaje
                //     })

                //     setTimeout(() => {
                //         location.reload()
                //     }, 4000);
                // }
                console.log(jqXHR.status)
                const url ='/'+data
                const div = document.querySelector('.preview_image')
                $('.preview_image').attr("src", url);
                return jqXHR;
            });
        }
        function postAvatar(frmdta) {
            // console.log(await toBase64(image))
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: frmdta

            });
        }
        const toBase64 = file => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
        });
    </script>

@endsection
