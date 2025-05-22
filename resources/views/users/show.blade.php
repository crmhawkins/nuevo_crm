@extends('layouts.app')

@section('titulo', 'Crear Usuario')

@section('css')
    <link rel="stylesheet" href="{{asset('assets/vendors/toastify/toastify.css')}}">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
@endsection

@section('content')

    <div class="page-heading card" style="box-shadow: none !important" >
        <div class="page-title card-body">
            <div class="row">
                <div class="col-2">
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <div class="d-flex align-items-center mb-2">
                        <a href="javascript:history.back()" class="btn btn-volver p-2 w-auto mr-3"><i class="bi bi-arrow-left"></i></a>

                        {{-- @if ($usuario->image == null)
                            <img class="rounded-circle img-fuild avatar-table" alt="avatar1" src="{{asset('assets/images/guest.webp')}}" />
                            @else
                            <img class="rounded-circle img-fuild avatar-table" alt="avatar1" src="{{asset('/storage/avatars/'.$usuario->image)}}" />
                        @endif --}}
                        <h3 class="ms-2 text-uppercase display-5">{{$usuario->username}}</h3>
                        <span class="badge bg-warning h_fit_content ms-2">{{$usuario->posicion->name}}</span>
                    </div>
                    <p class="text-subtitle text-muted mb-3">Información sobre el usuario {{$usuario->name}} {{$usuario->surname}}.</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{route('users.index')}}">Usuarios</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$usuario->name}}</li>
                        </ol>
                    </nav>

                </div>
            </div>
        </div>

        <section class="section mt-4">
            <div class="card">
                <div class="card-body">

                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card mb-4">
                                          <div class="card-body text-center">
                                            @if ($usuario->image == null)
                                                <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{asset('assets/images/guest.webp')}}" />
                                            @else
                                                <img alt="avatar" class="rounded-circle img-fluid  m-auto" style="width: 150px;" src="{{ asset('/storage/avatars/'.$usuario->image) }}" />
                                            @endif
                                            <h5 class="my-3">{{$usuario->username}}</h5>
                                            <p class="text-muted mb-1">{{$usuario->departamento->name}}</p>
                                            <p class="text-muted mb-4">{{$usuario->acceso->name}}</p>
                                            <div class="d-flex justify-content-center mb-2">
                                              {{-- <button type="button" class="btn btn-primary">Follow</button> --}}
                                            @if ($usuario->image == null)
                                                <button data-bs-backdrop="false" data-bs-toggle="modal" data-bs-target="#ModalAvatar{{$usuario->id}}" type="button" class="btn btn-outline-primary ms-1">Añadir Avatar</button>
                                            @else
                                                <button data-bs-backdrop="false" data-bs-toggle="modal" data-bs-target="#ModalAvatar{{$usuario->id}}" class="btn btn-outline-primary ms-1">Cambiar Avatar</button>
                                            @endif
                                              <button type="button" class="btn btn-outline-primary ms-1">Message</button>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="list-group mb-4" role="tablist">
                                            <a class="list-group-item list-group-item-action active"
                                                id="list-profile-list" data-bs-toggle="list" href="#list-profile"
                                                role="tab">Perfil</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-tareas-list" data-bs-toggle="list"
                                                href="#list-tareas" role="tab">Tareas</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-estadisticas-list" data-bs-toggle="list"
                                                href="#list-estadisticas" role="tab">Estadisticas</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-notificaciones-list" data-bs-toggle="list"
                                                href="#list-notificaciones" role="tab">Notificaciones</a>
                                            <a class="list-group-item list-group-item-action"
                                                id="list-vacaciones-list" data-bs-toggle="list"
                                                href="#list-vacaciones" role="tab">Vacaciones</a>
                                        </div>
                                        <div class="card mb-4 mb-lg-0">
                                          <div class="card-body p-0">
                                            <ul class="list-group list-group-flush rounded-3">
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fas fa-globe fa-lg text-warning"></i>
                                                <p class="mb-0">https://mdbootstrap.com</p>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fab fa-github fa-lg" style="color: #333333;"></i>
                                                <p class="mb-0">mdbootstrap</p>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fab fa-twitter fa-lg" style="color: #55acee;"></i>
                                                <p class="mb-0">@mdbootstrap</p>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fab fa-instagram fa-lg" style="color: #ac2bac;"></i>
                                                <p class="mb-0">mdbootstrap</p>
                                              </li>
                                              <li class="list-group-item d-flex justify-content-between align-items-center p-3">
                                                <i class="fab fa-facebook-f fa-lg" style="color: #3b5998;"></i>
                                                <p class="mb-0">mdbootstrap</p>
                                              </li>
                                            </ul>
                                          </div>
                                        </div>
                                      </div>

                                    <div class="col-sm-12 col-lg-8 mt-1">
                                        <div class="tab-content text-justify" id="nav-tabContent">
                                            <div class="tab-pane show active" id="list-profile" role="tabpanel"
                                                aria-labelledby="list-profile-list">
                                                <div class="card-body">
                                                    <div class="row my-2">
                                                      <div class="col-sm-4">
                                                        <p class="mb-0">Nombre Completo</p>
                                                      </div>
                                                      <div class="col-sm-8">
                                                        <p class="text-muted mb-0">{{$usuario->name}} {{$usuario->surname}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                      <div class="col-sm-3">
                                                        <p class="mb-0">Email</p>
                                                      </div>
                                                      <div class="col-sm-9">
                                                        <p class="text-muted mb-0">{{$usuario->email}}</p>
                                                      </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Nivel de Acceso</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0">{{$usuario->acceso->name}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Departamento</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0">{{$usuario->departamento->name}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Cargo</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0">{{$usuario->posicion->name}}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Vacaciones</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0">0 dias restantes</p>
                                                        </div>
                                                    </div>
                                                    <hr>

                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Productividad</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0">90%</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row my-2">
                                                        <div class="col-sm-3">
                                                          <p class="mb-0">Estado de animo</p>
                                                        </div>
                                                        <div class="col-sm-9">
                                                          <p class="text-muted mb-0"></p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="list-tareas" role="tabpanel"
                                                aria-labelledby="list-tareas-list">
                                                <h3 class="mb-2 fs-4 text-uppercase">Tareas</h3>
                                                <hr class="border mb-4" >
                                                @if (count($usuario->tareas) > 0)
                                                    <div class="table-responsive">
                                                         <table class="table table-hover">
                                                            <thead class="header-table-other">
                                                                <th class="px-3" style="font-size:0.75rem">TITULO</th>
                                                                <th class="" style="font-size:0.75rem">CLIENTE</th>
                                                                <th class="" style="font-size:0.75rem">ESTIMADO</th>
                                                                <th class="" style="font-size:0.75rem">REAL</th>
                                                                <th class="" style="font-size:0.75rem">ESTADO</th>
                                                                <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $usuario->tareas as $tarea )
                                                                    <tr class="clickable-row" data-href="{{route('tarea.edit', $tarea->id)}}">
                                                                        <td>{{$tarea->title}}</td>
                                                                        <td>{{$tarea->presupuesto->cliente->company ?? $tarea->presupuesto->cliente->name ?? 'Cliente Borrado'}}</td>
                                                                        <td>{{$tarea->estimated_time}}</td>
                                                                        <td>{{$tarea->real_time}}</td>
                                                                        <td>{{$tarea->estado->name}}</td>
                                                                        <td class="">
                                                                            <a class="" href="{{route('tarea.edit', $tarea->id)}}"><img class="m-auto" src="{{asset('assets/icons/edit.svg')}}" alt="Editar Tarea"></a>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4">
                                                        <h3 class="text-center fs-4">No se encontraron registros de <strong>Tareas</strong></h3>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="tab-pane" id="list-estadisticas" role="tabpanel"
                                                aria-labelledby="list-estadisticas-list">
                                                <h5>No tienes estadisticas</h5>

                                            </div>
                                            <div class="tab-pane" id="list-notificaciones" role="tabpanel"
                                                aria-labelledby="list-notificaciones-list">
                                                <h5>No tienes notificaciones</h5>
                                            </div>
                                            <div class="tab-pane" id="list-vacaciones" role="tabpanel"
                                                aria-labelledby="list-vacaciones-list">
                                                <h3 class="mb-2 fs-4 text-uppercase">Vacaciones</h3>
                                                <hr class="border mb-4" >
                                                <div class="card">
                                                        <div class="row justify-center my-4">
                                                        <div class="col-auto">
                                                            @if($usuario->vacacionesDias->first())
                                                                @if($usuario->vacacionesDias->first()->quantity == 1)
                                                                    <p for="have">Tienes <span style="color:green"><strong>{{$usuario->vacacionesDias->first()->quantity}}</strong></span> día de vacaciones</p>
                                                                @endif
                                                                @if($usuario->vacacionesDias->first()->quantity >1 )
                                                                    <p for="have">Tienes <span style="color:green"><strong>{{$usuario->vacacionesDias->first()->quantity}}</strong></span> días de vacaciones</p>
                                                                @endif
                                                            @else
                                                                <p for="have">No tienes días de vacaciones</p>
                                                            @endif
                                                        </div>
                                                        <div class="col-auto">
                                                            @if($usuario->vacaciones->where('holidays_status_id',3))
                                                                @if(count($usuario->vacaciones->where('holidays_status_id',3)) == 1)
                                                                    <p for="pendant">Tienes <span style="color:orange"><strong>{{count($usuario->vacaciones->where('holidays_status_id',3))}}</strong></span> petición pendiente</p>
                                                                @endif
                                                                @if(count($usuario->vacaciones->where('holidays_status_id',3)) >1 )
                                                                    <p for="pendant">Tienes <span style="color:orange"><strong>{{count($usuario->vacaciones->where('holidays_status_id',3))}}</strong></span> peticiones pendientes</p>
                                                                @endif
                                                            @else
                                                                <p for="pendant">No tienes peticiones pendientes</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                @if (count($usuario->vacaciones) > 0)
                                                    <div class="table-responsive">
                                                         <table class="table table-hover">
                                                            <thead class="header-table-other">
                                                                <th class="px-3" style="font-size:0.75rem">DÍA/S PEDIDOS</th>
                                                                <th class="" style="font-size:0.75rem">MEDIO DÍA</th>
                                                                <th class="" style="font-size:0.75rem">DÍAS EN TOTAL</th>
                                                                <th class="" style="font-size:0.75rem">ESTADO</th>
                                                                <th class="" style="font-size:0.75rem">FECHA DE PETICIÓN</th>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ( $usuario->vacaciones as $vacacion )
                                                                    @if($vacacion->holidays_status_id == 3)
                                                                        <tr class="table-warning" style="background-color:#FFDD9E">
                                                                    @endif
                                                                    @if($vacacion->holidays_status_id == 1)
                                                                        <tr class="table-success" style="background-color:#C3EBC4">
                                                                    @endif
                                                                    @if($vacacion->holidays_status_id == 2)
                                                                        <tr class="table-danger" style="background-color:#FBC4C4">
                                                                    @endif
                                                                    <td>{{ Carbon\Carbon::parse($vacacion->from)->format('d/m/Y') . ' - ' .  Carbon\Carbon::parse($vacacion->to)->format('d/m/Y') }}</td>
                                                                    @if($vacacion->half_day)
                                                                        <td><i class="fas fa-check"></i></td>
                                                                    @else
                                                                        <td><i class="fas fa-times"></i></td>
                                                                    @endif
                                                                    <td>{{ $vacacion->total_days }}</td>
                                                                    @if($vacacion->holidays_status_id == 1)
                                                                        <td>Aceptada</td>
                                                                    @elseif($vacacion->holidays_status_id == 2)
                                                                        <td>Denegada</td>
                                                                    @elseif($vacacion->holidays_status_id == 3)
                                                                        <td>Pendiente</td>
                                                                    @endif
                                                                    <td>{{ Carbon\Carbon::parse($vacacion->created_at)->format('d/m/Y H:i:s') }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        <a class="btn btn-outline-primary w-100" href="{{route('holiday.create')}}">
                                                            <i class="fa-solid fa-plus"></i> Petición de vacaciones
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="text-center py-4">
                                                        <h3 class="text-center fs-4">No se encontraron registros de <strong>Vacaciones</strong></h3>
                                                    </div>
                                                @endif
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

                        $('#ModalAvatar'+{{$usuario->id}}).modal('hide');
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
