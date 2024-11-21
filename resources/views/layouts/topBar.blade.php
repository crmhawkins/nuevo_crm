<nav id="topbar" class="navbar">
    <header class="top-burguer">
        <a href="#" class="burger-btn d-block d-lg-none">
            <i class="bi bi-list"></i>
        </a>
    </header>
    <ul class="topbar-menu d-flex align-items-center gap-3">
        <li class="dropdown notification-list">
            <a href="{{ route('admin.emailConfig.settings') }}" type="button" class="nav-link position-relative">
                <i class="fas fa-cogs"></i>
            </a>
        </li>
        <li class="dropdown notification-list">
            <a href="{{ route('admin.emails.index') }}" type="button" class="nav-link position-relative">
                <i class="fa-regular fa-envelope"></i>
                <span class="position-absolute top-10 start-80 translate-middle px-2 bg-info rounded-pill">
                  <span class="text-white countCorreos" style="font-size: 0.85rem">0</span>
                </span>
            </a>

            {{-- <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            </a> --}}
            {{-- <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 fs-16 fw-semibold">Messages</h6>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="text-dark text-decoration-underline">
                                <small>Clear All</small>
                            </a>
                        </div>
                    </div>
                </div>

                <div style="max-height: 300px;" data-simplebar="init">
                    <div class="simplebar-wrapper" style="margin: 0px;">
                        <div class="simplebar-height-auto-observer-wrapper">
                            <div class="simplebar-height-auto-observer"></div>
                        </div>
                        <div class="simplebar-mask">
                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                    <div class="simplebar-content" style="padding: 0px;">

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-1.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Cristina Pride <small class="fw-normal text-muted float-end ms-1">1 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-2.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Sam Garret <small class="fw-normal text-muted float-end ms-1">2 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Yeah everything is fine</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-3.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Karen Robinson <small class="fw-normal text-muted float-end ms-1">2 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Wow that's great</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-4.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Sherry Marshall <small class="fw-normal text-muted float-end ms-1">3 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-5.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Shawn Millard <small class="fw-normal text-muted float-end ms-1">4 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Yeah everything is fine</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                    </div>
                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                    </div>
                    <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                    </div>
                </div>

                <!-- All-->
                <a href="javascript:void(0);" class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                    View All
                </a>

            </div> --}}
        </li>

        <!-- Campana de notificaciones -->

        <li class="dropdown notification-list">
            <a href="#" type="button" class="nav-link position-relative" id="btnAbrirAlertas">
                <i class="fa-regular fa-bell"></i>
                <span class="position-absolute top-10 start-80 translate-middle px-2 bg-danger rounded-pill">
                    <span class="text-white countAlertas" style="font-size: 0.85rem">0</span>
                </span>
            </a>
        </li>
{{--
      <!-- Modal para seleccionar el stage -->
        <div class="modal fade" id="alertsModal" tabindex="-1" aria-labelledby="alertsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alertsModalLabel">Alertas por Etapa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="alertsList" class="list-group">
                            <!-- Aquí se mostrarán las alertas agrupadas por `stage_id` -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segundo modal para mostrar las alertas específicas de un stage -->
        <div class="modal fade" id="specificAlertsModal" tabindex="-1" aria-labelledby="specificAlertsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="specificAlertsModalLabel">Alertas Específicas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="specificAlertsList" class="list-group">
                            <!-- Aquí se mostrarán las alertas específicas del stage seleccionado -->
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- <li class="d-none d-sm-inline-block">
            <div class="nav-link" >
                <i id="light-dark-mode" class="bi @if($isDarkMode) bi-brightness-high @else bi-moon @endif" style="cursor: pointer;"></i>
            </div>
        </li> --}}
        <li class="d-none d-sm-inline-block">
            <div class="nav-link" >
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-arrow-right-from-bracket "></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </li>

        {{-- <li class="dropdown">
            <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="account-user-avatar">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/User-avatar.svg/2048px-User-avatar.svg.png" alt="user-image" width="32" class="rounded-circle">
                </span>
                <span class="d-lg-block d-none">
                    <h5 class="my-0 fw-normal">Thomson <i class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome!</h6>
                </div>

                <!-- item-->
                <a href="pages-profile.html" class="dropdown-item">
                    <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                    <span>My Account</span>
                </a>

                <!-- item-->
                <a href="pages-profile.html" class="dropdown-item">
                    <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                    <span>Settings</span>
                </a>

                <!-- item-->
                <a href="pages-faq.html" class="dropdown-item">
                    <i class="ri-customer-service-2-line fs-18 align-middle me-1"></i>
                    <span>Support</span>
                </a>

                <!-- item-->
                <a href="auth-lock-screen.html" class="dropdown-item">
                    <i class="ri-lock-password-line fs-18 align-middle me-1"></i>
                    <span>Lock Screen</span>
                </a>

                <!-- item-->
                <a href="auth-logout-2.html" class="dropdown-item">
                    <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li> --}}

    </ul>

    <script>
        var APP_URL = '{{ url('/') }}';
        var alertasAgrupadasPorStageId = {};  // Objeto para almacenar las alertas agrupadas
        var mapeoMensajes = {
            1: 'Alerta Peticion - Tienes peticiones pendientes',
            2: 'Presupuesto Pendiente de Confirmar',
            3: 'Presupuesto Pendiente de Aceptar',
            4: 'Presupuesto Aceptado',
            5: 'Presupuesto Finalizado',
            6: 'Presupuesto Facturado',
            7: 'Presupuesto Cancelado',
            8: 'Crear Factura',
            9: 'Factura Fuera de Plazo',
            10: 'Tesoreria Descubierta',
            11: 'Tarea Nueva',
            12: 'Un cliente ha descargado el presupuesto',
            13: 'Productividad',
            14: 'Tarea Revision Antes Previsto',
            15: 'Alerta recordatorio',
            16: 'Peticion vacaciones',
            17: 'Vacaciones Aceptadas',
            18: 'Vacaciones Denegadas',
            19: 'Alerta Respuestas',
            20: 'Mensaje',
            21: 'Presupuesto no abierto tras 48 horas',
            22: 'Horas Trabajadas del Mes',
            23: 'Alerta puntualidad',
            24: 'Alerta 3 veces tarde',
            25: 'Alerta Comercial',
            26: 'Alerta Cobrar Comercial',
            27: 'Alerta General',
            28: 'Alerta Encuesta Satisfaccion',
            29: 'Nueva acta pendiente',
            31: 'Aviso Jornada Laboral',
            33: 'Dominio a punto de expirar',
            40: 'Aviso de Tarea - Se está sobrepasando las horas estimadas',
            41: 'Tarea en Revision',
            42: 'Alerta pospuesta 3 veces'
        };

        // Función para obtener las alertas usando fetch
        function obtenerCorreos() {
            fetch("{{ route('admin.emails.unread') }}", {  // Cambia esto con la ruta de tu backend
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(response => response.json())
            .then(count => {

                console.log('Correos sin leer:', count);
                actualizarContadorCorreos(count);
            })
            .catch(error => console.error('Error al obtener los correos:', error));
        }
        function obtenerAlertas() {
            fetch("{{ route('user.alerts') }}", {  // Cambia esto con la ruta de tu backend
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(response => response.json())
            .then(alertas => {

                console.log('Alertas recibidas:', alertas);
                agruparAlertas(alertas);
                actualizarContadorAlertas();
            })
            .catch(error => console.error('Error al obtener las alertas:', error));
        }

        // Función para agrupar alertas por stage_id
        function agruparAlertas(alertas) {
            alertasAgrupadasPorStageId = {};  // Reiniciar el agrupamiento

            alertas.forEach(alerta => {
                var stage_id = alerta.stage_id;

                if (!alertasAgrupadasPorStageId.hasOwnProperty(stage_id)) {
                    alertasAgrupadasPorStageId[stage_id] = [];
                }
                alertasAgrupadasPorStageId[stage_id].push(alerta);
            });
        }
        // Función para mostrar el número total de alertas en el icono de la campana
        function actualizarContadorCorreos(count) {
            const correoCountSpan = document.querySelector('.countCorreos');
            let totalCorreos = count;

            correoCountSpan.textContent = totalCorreos;

        }
        function actualizarContadorAlertas() {
            const alertCountSpan = document.querySelector('.countAlertas');
            let totalAlertas = 0;

            for (const stage_id in alertasAgrupadasPorStageId) {
                totalAlertas += alertasAgrupadasPorStageId[stage_id].length;
            }

            alertCountSpan.textContent = totalAlertas;

            if (window.enRutaEspecifica && totalAlertas > 0) {
                mostrarTiposDeAlertas();
            }

        }
        // Función para mostrar los tipos de alertas agrupadas
        function mostrarTiposDeAlertas() {
            let htmlContent = "<ul>";

            for (const stage_id in mapeoMensajes) {
                if (alertasAgrupadasPorStageId.hasOwnProperty(stage_id) && alertasAgrupadasPorStageId[stage_id].length > 0) {
                    let mensaje = mapeoMensajes[stage_id];

                    if (parseInt(stage_id) === 33) {
                        alertasAgrupadasPorStageId[stage_id].forEach(alerta => {
                            if (alerta['dominio'] && alerta['dominio'].dominio != null) {
                                htmlContent += `<li>${mensaje} - Dominio: ${alerta['dominio'].dominio} - <button onclick="mostrarAlertaEspecifica(${stage_id})">Ver</button></li>`;
                            }
                        });
                    } else {
                        htmlContent += `<li class="li-flex"> <div class="li-mensaje">${mensaje}</div> <div class="li-boton"> <button class="btn-pop" onclick="mostrarAlertaEspecifica(${stage_id})">Ver</button></div></li>`;
                    }
                }
            }

            htmlContent += "</ul>";

            if (htmlContent === "<ul></ul>") {
                htmlContent = "<p>No hay alertas pendientes.</p>";
            }

            Swal.fire({
                title: 'Listado de Alertas',
                html: htmlContent,
                customClass: {
                    popup: 'popupaletas',
                    title: 'tituloalertas',
                    content: 'contenidoalerta'
                },
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: true
            });
        }
        // Función para mostrar alertas específicas de un stage
        function mostrarAlertaEspecifica(stage_id) {
            let alertasEspecificas = alertasAgrupadasPorStageId[stage_id];
            let htmlContent = "<ul>";

            alertasEspecificas.forEach((alerta, index) => {
                let mensajeDetalle;
                switch (stage_id) {
                    case 1:
                        mensajeDetalle = "Tienes una petición pendiente de " + alerta['client'];
                        botonposponer = true;
                        break;

                    case 2:
                        mensajeDetalle = "Tienes el presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " pendiente de confirmar";
                        botonposponer = true;
                        break;

                    case 3:
                        mensajeDetalle = "Tienes el presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " pendiente de aceptar";
                        botonposponer = true;
                        break;

                    case 4:
                        mensajeDetalle = alerta['description'];
                        botonposponer = true;
                        break;

                    case 5:
                        mensajeDetalle = "Tienes el presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " finalizado";
                        botonposponer = true;
                        break;

                    case 6:
                        mensajeDetalle = "Tienes el presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " facturado";
                        botonposponer = false;
                        break;

                    case 7:
                        mensajeDetalle = "Tienes el presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " cancelado";
                        botonposponer = false;
                        break;

                    case 8:
                        mensajeDetalle = "El presupuesto " + alerta['presupuesto'] + " - " + alerta['cliente'] + " que ha sido finalizado";
                        botonposponer = true;
                        break;

                    case 9:
                        mensajeDetalle = "La factura " + alerta['factura'] + " está fuera de plazo";
                        botonposponer = true;
                        break;

                    case 10:
                        mensajeDetalle = "Tesorería descubierta";
                        botonposponer = false;
                        break;

                    case 11:
                        mensajeDetalle = "Tarea: " + alerta['tarea'];
                        botonposponer = false;
                        break;

                    case 12:
                        mensajeDetalle = alerta['budget_send_client'] + " ha descargado el presupuesto. Los términos del presupuesto " + alerta['budget_send'] + " han sido aceptados (Llamar al cliente)";
                        botonposponer = true;
                        break;

                    case 13:
                        mensajeDetalle = alerta['description'];
                        botonposponer = false;
                        break;

                    case 14:
                        mensajeDetalle = "Felicidades has terminado una tarea antes del tiempo previsto";
                        botonposponer = false;
                        break;

                    case 15:
                        mensajeDetalle = alerta['remitente'] + ": " + alerta['nota'];
                        botonposponer = false;
                        break;

                    case 16:
                        mensajeDetalle = "Tienes una petición de vacaciones de " + alerta['usuario'];
                        botonposponer = false;
                        break;

                    case 17:
                        mensajeDetalle = "Vacaciones Aceptadas " + alerta['fecha'];
                        botonposponer = false;
                        break;

                    case 18:
                        mensajeDetalle = "Vacaciones Denegadas " + alerta['fecha'];
                        botonposponer = false;
                        break;

                    case 19:
                        mensajeDetalle = "Alerta de " + alerta['remitente'] + ": " + alerta['nota'];
                        botonposponer = false;
                        break;

                    case 20:
                        mensajeDetalle = alerta['remitente']+"," + alerta['nota'];
                        botonposponer = false;
                        break;

                    case 21:
                        mensajeDetalle = "El presupuesto " + alerta['presupuesto'] + " no ha sido abierto por el cliente tras 48 horas (Llamar al cliente)";
                        botonposponer = true;
                        break;

                    case 22:
                        mensajeDetalle = "La horas trabajadas de este mes son :" + alerta['horas'] + " acepta si esta conforme";
                        botonposponer = false;
                        break;
                    case 23:
                        mensajeDetalle = alerta['description'];
                        botonposponer = false;
                        break;

                    case 24:
                        mensajeDetalle = "Aviso de Puntualidad: " + alerta['nota'];
                        botonposponer = false;
                        break;

                    case 25:
                        mensajeDetalle = "El comercial " + alerta['comercial'] + " ha creado una petición para el cliente " + alerta['cliente'];
                        botonposponer = false;
                        break;

                    case 28:
                        mensajeDetalle = "Alerta de Encuesta de Satisfacción: " + alerta['descripcion'];
                        botonposponer = false;
                        break;

                    case 29:
                        mensajeDetalle = "Nueva acta pendiente: " + alerta['description'];
                        botonposponer = false;
                        break;

                    case 31:
                        mensajeDetalle = "Aviso de Jornada Laboral: " + alerta['description'];
                        botonposponer = false;
                        break;

                    case 33:
                        mensajeDetalle = "El dominio " + alerta['dominio'].dominio + " está a punto de expirar";
                        botonposponer = true;
                        break;

                    case 40:
                        mensajeDetalle = alerta['description'];
                        botonposponer = true;
                        break;
                    case 41:
                        mensajeDetalle = "Tarea en Revision: La tarea con ID " + alerta['reference_id'] + " está en revisión";
                        botonposponer = false;
                        break;
                    case 42:
                        mensajeDetalle =  alerta['description'];
                        botonposponer = false;
                        break;

                    default:
                    alertas.shift();
                    mostrarTiposDeAlertas();
                    break;
                }
                if(botonposponer){
                    htmlContent += `
                        <li class="li-flex">
                            <div class="li-mensaje">${mensajeDetalle}</div>
                            <div class="li-boton">
                                <button class="btn-pop" onclick="posponerAlerta(${alerta['id']},${stage_id},${index})" title="Posponer"><i class="fa-regular fa-clock"></i></button>
                                <button class="btn-pop" onclick="manejarAlertaEspecifica(${stage_id}, ${index})" title="Ok"><i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                        </li>`;
                }else{
                    htmlContent += `
                        <li class="li-flex">
                            <div class="li-mensaje">${mensajeDetalle}</div>
                            <div class="li-boton">
                                <button class="btn-pop" onclick="manejarAlertaEspecifica(${stage_id}, ${index})" title="Ok"><i class="fa-solid fa-arrow-right"></i></button>
                            </div>
                        </li>`;
                }
                });

            htmlContent += "</ul>";

            Swal.fire({
                title: `${mapeoMensajes[stage_id]}`,
                html: htmlContent,
                customClass: {
                    popup: 'popupaletas',
                    title: 'tituloalertas',
                    content: 'contenidoalerta'
                },
                showCloseButton: true,
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: true
            });
        }

        // Función para manejar una alerta específica
        function manejarAlertaEspecifica(stage_id, index) {
            let alertaSeleccionada = alertasAgrupadasPorStageId[stage_id][index];
            switch (stage_id) {
                case 1:

                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/petition/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 2:

                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 3:
                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 4:
                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 5:
                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 6:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 7:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 8:

                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 9:

                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/treasury", '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 10:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 11:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 12:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 13:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 14:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 15:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 16:
                var id = alertaSeleccionada['id'];
                var reference_id = alertaSeleccionada['reference_id'];
                var status = 2; //Resuelto
                $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                    if (jqXHR.responseText != 503) {
                        $.ajax({
                            url: `/holidays/getDate/${reference_id}`, // Ajusta esta ruta según tu backend
                            type: 'post',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}' // Incluye el token CSRF si es necesario
                            },
                            success: function(response) {
                                // Suponiendo que `response` incluye la fecha en `response.fecha_inicio`
                                const fechaInicio = response.fecha_inicio; // Ajusta el nombre del campo según corresponda
                                if (fechaInicio) {
                                    // Abre una nueva ventana con el calendario, pasando la fecha en el parámetro `fecha`
                                    window.open(`/holidays/petitions?fecha=${fechaInicio}`, '_blank');
                                } else {
                                    swal(
                                        'Error',
                                        'Fecha no encontrada en la petición de vacaciones',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                swal(
                                    'Error',
                                    'No se pudo obtener la fecha de la petición de vacaciones',
                                    'error'
                                );
                            }
                        });
                    eliminarAlertaDOM(stage_id, index);
                    } else {
                    swal(
                        'Error',
                        'Error al realizar la peticion',
                        'error'
                    );
                    }
                    });
                break;

                case 17:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 18:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 19:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 20:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 21:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/budget/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 22:
                console.log('Mostrando alerta 22:', alertaSeleccionada);

                    Swal.fire({
                            title: "Horas Trabajadas del Mes",
                            text: "La horas trabajadas de este mes son :" + alertaSeleccionada['horas'] +
                                " acepta si esta conforme",
                            type: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Aceptar',
                            cancelButtonText: "Rechazar",
                            allowEscapeKey: false,
                            allowEnterKey: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.value) {
                                var id = alertaSeleccionada['id'];
                                var status = 2; //Resuelto
                                $.when(updateStatusAlertAndAcceptHours(id, status)).then(function(data, textStatus, jqXHR) {
                                    if (jqXHR.responseText != 503) {
                                        Swal.fire(
                                            'Éxito',
                                            'Aceptadas las hora de trabajo del mes.',
                                            'success'
                                        );
                                        eliminarAlertaDOM(stage_id, index);
                                    } else {
                                        Swal.fire(
                                            'Error',
                                            'Error al realizar la peticion',
                                            'error'
                                        );
                                    }
                                });
                            } else {
                                var id = alertaSeleccionada['id'];
                                var status = 2; //Resuelto
                                $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                                    if (jqXHR.responseText != 503) {
                                        eliminarAlertaDOM(stage_id, index);
                                        Swal.fire({
                                            title: 'Razones para no estar conforme',
                                            type: 'question',
                                            html: '<label for="description"></label><textarea id="description" rows="4" cols="50" required></textarea>',
                                            allowEscapeKey: false,
                                            allowEnterKey: false,
                                            allowOutsideClick: false,
                                            preConfirm: function(value) {
                                                return new Promise(function(resolve) {
                                                    var text = $("#description").val();

                                                    $(".swal2-messages").remove();
                                                    $("#swal2-content").append(
                                                        "<div class='swal2-messages'>"
                                                    );

                                                    if (value) {
                                                        $(".swal2-messages").empty();
                                                        if (text) {
                                                            //Crear alerta para enviarla al MegaFullAdmin
                                                            $.when(responseAlert(id,
                                                                text)).then(
                                                                function(data,
                                                                    textStatus,
                                                                    jqXHR) {
                                                                    if (jqXHR
                                                                        .responseText !=
                                                                        503) {
                                                                        swal(
                                                                            'Éxito',
                                                                            'Respuesta enviada.',
                                                                            'success'
                                                                        );
                                                                    } else {
                                                                        swal(
                                                                            'Error',
                                                                            'Error al crear la alerta',
                                                                            'error'
                                                                        );
                                                                    }
                                                                });
                                                        } else {
                                                            swal.enableButtons();
                                                            $(".swal2-messages").append(
                                                                "<span style='color:red;font-size: 20px;'>El campo está vacio</span>"
                                                            );
                                                        }
                                                    }

                                                });
                                            },
                                        });
                                    } else {
                                        swal(
                                            'Error',
                                            'Error al realizar la peticion',
                                            'error'
                                        );
                                    }
                                });
                            }
                        });
                    break;

                case 23:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;
                case 24:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 25:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/petition/edit/" + alertaSeleccionada['reference_id'] , '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 28:

                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/clients/surveys/" + alertaSeleccionada['reference_id'] + "/get", '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 29:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open( "/crm-activity-meeting-view/" + alertaSeleccionada['reference_id'], '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 31:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                case 33:


                    var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                        window.open("dominios/edit/" + alertaSeleccionada['dominio'].id, '_blank');
                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });

                    break;

                case 40:

                var id = alertaSeleccionada['id'];
                var status = 2;
                $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                            if (jqXHR.responseText != 503) {
                            window.open( "/task/edit/"+ alertaSeleccionada['reference_id'], '_blank');
                            eliminarAlertaDOM(stage_id, index);
                            } else {
                            swal(
                                'Error',
                                'Error al realizar la peticion',
                                'error'
                            );
                            }
                        });
                    break;

                case 41:

                var id = alertaSeleccionada['id'];
                var status = 2;
                $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                            if (jqXHR.responseText != 503) {
                            window.open( "/task/edit/"+ alertaSeleccionada['reference_id'], '_blank');
                            eliminarAlertaDOM(stage_id, index);
                            } else {
                            swal(
                                'Error',
                                'Error al realizar la peticion',
                                'error'
                            );
                            }
                        });
                    break;

                case 42:
                var id = alertaSeleccionada['id'];
                    var status = 2; //Resuelto
                    $.when(updateStatusAlert(id, status)).then(function(data, textStatus, jqXHR) {
                        if (jqXHR.responseText != 503) {
                            switch (alertaSeleccionada['stage']) {
                                case "1":
                                    window.open( "/petition/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "2":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "3":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "4":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "5":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "8":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "9":
                                    window.open( "/treasury", '_blank');
                                    break;

                                case "21":
                                    window.open( "/budget/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;

                                case "33":
                                    window.open("dominios/edit/" + alertaSeleccionada['referencia'] , '_blank');
                                    break;
                            }

                        eliminarAlertaDOM(stage_id, index);
                        } else {
                        swal(
                            'Error',
                            'Error al realizar la peticion',
                            'error'
                        );
                        }
                    });
                    break;

                default:
                    mostrarTiposDeAlertas();
                    break;


            }
        }

        // Función para eliminar la alerta del DOM
        function eliminarAlertaDOM(stage_id, index) {
            const alertCountSpan = document.querySelector('.countAlertas');
            let totalAlertas = 0;
            alertasAgrupadasPorStageId[stage_id].splice(index, 1);
            for (const stage_id in alertasAgrupadasPorStageId) {
                totalAlertas += alertasAgrupadasPorStageId[stage_id].length;
            }
            alertCountSpan.textContent = totalAlertas;
            mostrarAlertaEspecifica(stage_id);
        }



        function updateStatusAlert(id, status) {
            return $.ajax({
            type: "POST",
            url: '/alert/update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                'id': id,
                'status': status
            },
            dataType: "json"
            });
        }

        function posponerAlerta(id,stage_id, index) {
            eliminarAlertaDOM(stage_id, index);
            return $.ajax({
            type: "POST",
            url: '/alert/postpone',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            data: {
                'id': id,
            },
            dataType: "json"
            });
        }

        function updateStatusAlertAndAcceptHours(id, status) {
            return $.ajax({
                type: "POST",
                url: 'dashboard/updateStatusAlertAndAcceptHours',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                    'status': status
                },
                dataType: "json"
            });
        }

        function responseAlert(id, text) {
            return $.ajax({
                type: "POST",
                url: 'dashboard/responseAlert',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                    'texto': text
                },
                dataType: "json"
            });
        }


       document.addEventListener('DOMContentLoaded', (event) => {
            console.log('DOM completamente cargado y analizado');
            const alertButton = document.getElementById('btnAbrirAlertas');
            if (alertButton) {
                alertButton.addEventListener('click', function() {
                    console.log('Botón de alertas clicado');
                    mostrarTiposDeAlertas();
                });
            } else {
                console.error('No se encontró el botón con el ID btnAbrirAlertas');
            }

            // Llamada inicial para cargar alertas
            obtenerAlertas();
            obtenerCorreos();
        });
    </script>
    <style>
        .li-flex {
            display: flex;
            justify-content: space-between;
        }

        .li-mensaje {
            flex: 0 0 80%; /* Asigna el 80% del espacio disponible */
        }

        .li-boton {
            flex: 0 0 20%; /* Asigna el 20% del espacio restante */
            display: flex;
            justify-content: space-around; /* Distribuye uniformemente los botones dentro del contenedor */
        }
        .tituloalertas {
            font-size: 24px;
            color: #3078d6; /* Cambia el color del título */
            font-weight: bold; /* Hace el título en negrita */
        }

        .popupaletas {
            background-color: #ffffff; /* Cambia el color de fondo del popup */
            width: 900px; /* Aumenta el ancho del popup */
            max-height: 700px;     /* Altura máxima de 700px */
            overflow-y: auto;      /* Habilita el scroll si se excede la altura */
            border-radius: 10px;   /* Bordes redondeados */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Sombra suave */
            background-color: white; /* Fondo blanco */
        }

        .contenidoalerta ul li {
        margin-bottom: 10px;
        list-style-type: none;
        padding: 5px;
        border-bottom: 1px solid #ffffff;
        }

        .btn-pop {
            background-color: #4CAF50; /* Color de fondo */
            color: white;             /* Color del texto */
            padding: 10px 20px;       /* Relleno: arriba-abajo, izquierda-derecha */
            border: none;             /* Sin borde */
            border-radius: 5px;       /* Bordes redondeados */
            cursor: pointer;          /* Cambiar el cursor a una mano */
            font-size: 16px;          /* Tamaño de fuente */
        }

        .btn-pop:hover {
            background-color: #45a049; /* Color de fondo al pasar el mouse */
        }

        .li-flex {
            display: flex;
            justify-content: space-between; /* Distribuye el espacio entre los elementos */
            align-items: center;            /* Alinea los elementos verticalmente */
            padding: 5px;                   /* Agrega algo de relleno */
        }

    </style>
</nav>


<script>
    // document.getElementById('light-dark-mode').addEventListener('click', function() {
    //     const body = document.body;
    //     body.classList.toggle('dark-mode');
    //     const isDarkMode = body.classList.contains('dark-mode');

    //     // Guardar preferencia en la base de datos
    //     fetch('/save-theme-preference', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //         },
    //         body: JSON.stringify({ is_dark: isDarkMode })
    //     }).then(response => {
    //         if (response.ok) {
    //             console.log('Preferencia de tema guardada.');
    //             console.log(isDarkMode)
    //             console.log(this)
    //             window.location.reload();
    //         } else {
    //             console.error('Error al guardar la preferencia de tema.');
    //         }
    //     });
    // });

</script>
